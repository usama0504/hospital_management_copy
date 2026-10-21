<?php

use App\Models\Bill;
use App\Models\Patient;
use App\Models\Payment;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    // Nakli number: asli number sirf .env mein hota hai.
    config(['payments.manual_jazzcash' => [
        'enabled' => true,
        'number' => '03000000000',
        'name' => 'Test Hospital',
    ]]);
});

function manualBill(Patient $patient, float $amount = 1500, string $status = 'Unpaid'): Bill
{
    return Bill::factory()->create([
        'patient_id' => $patient->id,
        'amount' => $amount,
        'status' => $status,
    ]);
}

function manualPayment(Bill $bill, string $txn = 'TXN000000001'): Payment
{
    return Payment::create([
        'bill_id' => $bill->id,
        'patient_id' => $bill->patient_id,
        'gateway' => Payment::GATEWAY_JAZZCASH_MANUAL,
        'reference' => 'PAY-' . strtoupper(substr(md5($txn), 0, 14)),
        'transaction_id' => $txn,
        'amount' => $bill->amount,
        'currency' => 'PKR',
        'status' => Payment::PENDING,
    ]);
}

// ----------------------------------------------------------------- patient side

test('the jazzcash page shows the hospital number and the amount', function () {
    [$user, $patient] = portalPatient();
    $bill = manualBill($patient, 2500);

    $this->actingAs($user)->get("/portal/bills/{$bill->id}/jazzcash")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Portal/ManualPayment')
            ->where('account.number', '03000000000')
            ->where('bill.id', $bill->id)
            ->etc());
});

test('the jazzcash option is hidden and blocked when it is not configured', function () {
    config(['payments.manual_jazzcash.enabled' => false]);

    [$user, $patient] = portalPatient();
    $bill = manualBill($patient);

    $this->actingAs($user)->get('/portal/bills')
        ->assertInertia(fn (Assert $page) => $page->where('payments.manual', false)->etc());

    $this->actingAs($user)->get("/portal/bills/{$bill->id}/jazzcash")->assertNotFound();
    $this->actingAs($user)->post("/portal/bills/{$bill->id}/jazzcash", ['transaction_id' => 'ABC123456'])->assertNotFound();
});

test('it is also off when the number is empty', function () {
    config(['payments.manual_jazzcash.number' => null]);

    [$user, $patient] = portalPatient();
    $bill = manualBill($patient);

    $this->actingAs($user)->get("/portal/bills/{$bill->id}/jazzcash")->assertNotFound();
});

test('submitting a transaction id creates a pending payment and leaves the bill unpaid', function () {
    [$user, $patient] = portalPatient();
    $bill = manualBill($patient, 1800);

    $this->actingAs($user)
        ->post("/portal/bills/{$bill->id}/jazzcash", [
            'transaction_id' => ' ab 123456 ',
            'sender_number' => '0300 1234567',
            'amount' => 1, // ignore hona chahiye
        ])
        ->assertRedirect(route('portal.bills'))
        ->assertSessionHas('success');

    $payment = Payment::first();

    expect($payment->gateway)->toBe('jazzcash_manual');
    expect($payment->status)->toBe('pending');
    expect($payment->transaction_id)->toBe('AB123456');
    expect((float) $payment->amount)->toBe(1800.0);
    expect($payment->patient_id)->toBe($patient->id);
    expect($bill->fresh()->status)->toBe('Unpaid');
});

test('the same transaction id cannot be used twice, even in another format', function () {
    [$user, $patient] = portalPatient();
    $first = manualBill($patient);
    $second = manualBill($patient);

    $this->actingAs($user)->post("/portal/bills/{$first->id}/jazzcash", ['transaction_id' => 'AB123456']);

    $this->actingAs($user)
        ->post("/portal/bills/{$second->id}/jazzcash", ['transaction_id' => 'ab 123456'])
        ->assertSessionHasErrors('transaction_id');

    expect(Payment::count())->toBe(1);
});

test('a malformed transaction id is rejected', function () {
    [$user, $patient] = portalPatient();
    $bill = manualBill($patient);

    $this->actingAs($user)->post("/portal/bills/{$bill->id}/jazzcash", ['transaction_id' => '12'])
        ->assertSessionHasErrors('transaction_id');
    $this->actingAs($user)->post("/portal/bills/{$bill->id}/jazzcash", ['transaction_id' => '<script>1234'])
        ->assertSessionHasErrors('transaction_id');

    expect(Payment::count())->toBe(0);
});

test('a bill cannot have two pending jazzcash submissions', function () {
    [$user, $patient] = portalPatient();
    $bill = manualBill($patient);

    $this->actingAs($user)->post("/portal/bills/{$bill->id}/jazzcash", ['transaction_id' => 'AAAA111111']);
    $this->actingAs($user)
        ->post("/portal/bills/{$bill->id}/jazzcash", ['transaction_id' => 'BBBB222222'])
        ->assertSessionHas('error');

    expect(Payment::count())->toBe(1);
});

test('a patient cannot submit payment for someone elses bill or an already paid bill', function () {
    [$user, $patient] = portalPatient();
    $theirs = manualBill(Patient::factory()->create());
    $paid = manualBill($patient, 1500, 'Paid');

    $this->actingAs($user)->post("/portal/bills/{$theirs->id}/jazzcash", ['transaction_id' => 'AAAA111111'])->assertNotFound();
    $this->actingAs($user)->post("/portal/bills/{$paid->id}/jazzcash", ['transaction_id' => 'BBBB222222'])
        ->assertSessionHas('error');

    expect(Payment::count())->toBe(0);
});

test('the portal bills page flags a bill that is under review', function () {
    [$user, $patient] = portalPatient();
    $bill = manualBill($patient);
    manualPayment($bill);

    $this->actingAs($user)->get('/portal/bills')
        ->assertInertia(fn (Assert $page) => $page
            ->where('bills.data.0.has_pending_payment', true)
            ->etc());
});

// ----------------------------------------------------------------- staff side

test('only staff with manage bills can see the review page', function () {
    $this->actingAs(userWithRole('admin'))->get('/payments')->assertOk();
    $this->actingAs(userWithRole('receptionist'))->get('/payments')->assertOk();

    [$doctorUser] = doctorWithUser();
    $this->actingAs($doctorUser)->get('/payments')->assertForbidden();

    [$patientUser] = portalPatient();
    $this->actingAs($patientUser)->get('/payments')->assertRedirect(route('portal.dashboard'));
});

test('approving a payment marks the payment and the bill as paid', function () {
    $patient = Patient::factory()->create();
    $bill = manualBill($patient);
    $payment = manualPayment($bill);
    $staff = userWithRole('receptionist');

    $this->actingAs($staff)->post("/payments/{$payment->id}/approve")->assertSessionHas('success');

    $payment->refresh();

    expect($payment->status)->toBe('paid');
    expect($payment->paid_at)->not->toBeNull();
    expect($payment->reviewed_by)->toBe($staff->id);
    expect($bill->fresh()->status)->toBe('Paid');
});

test('rejecting a payment keeps the bill unpaid and stores the reason', function () {
    [$user, $patient] = portalPatient();
    $bill = manualBill($patient);
    $payment = manualPayment($bill);
    $staff = userWithRole('admin');

    $this->actingAs($staff)->post("/payments/{$payment->id}/reject", ['note' => 'Amount not received'])
        ->assertSessionHas('success');

    expect($payment->fresh()->status)->toBe('failed');
    expect($payment->fresh()->review_note)->toBe('Amount not received');
    expect($bill->fresh()->status)->toBe('Unpaid');

    // patient wajah dekh sakta hai aur dobara bhej sakta hai
    $this->actingAs($user)->get('/portal/bills')
        ->assertInertia(fn (Assert $page) => $page
            ->where('bills.data.0.rejection_note', 'Amount not received')
            ->where('bills.data.0.has_pending_payment', false)
            ->etc());

    $this->actingAs($user)->post("/portal/bills/{$bill->id}/jazzcash", ['transaction_id' => 'NEWTXN123456'])
        ->assertSessionHas('success');
});

test('rejecting needs a reason', function () {
    $payment = manualPayment(manualBill(Patient::factory()->create()));

    $this->actingAs(userWithRole('admin'))->post("/payments/{$payment->id}/reject", ['note' => ''])
        ->assertSessionHasErrors('note');

    expect($payment->fresh()->status)->toBe('pending');
});

test('a reviewed payment cannot be reviewed again', function () {
    $bill = manualBill(Patient::factory()->create());
    $payment = manualPayment($bill);
    $admin = userWithRole('admin');

    $this->actingAs($admin)->post("/payments/{$payment->id}/reject", ['note' => 'No money']);
    $this->actingAs($admin)->post("/payments/{$payment->id}/approve")->assertSessionHas('error');

    expect($payment->fresh()->status)->toBe('failed');
    expect($bill->fresh()->status)->toBe('Unpaid');
});

test('approval is blocked when the bill amount changed after the patient paid', function () {
    $bill = manualBill(Patient::factory()->create(), 1500);
    $payment = manualPayment($bill);

    $bill->update(['amount' => 2500]);

    $this->actingAs(userWithRole('admin'))->post("/payments/{$payment->id}/approve")
        ->assertSessionHas('error');

    expect($payment->fresh()->status)->toBe('pending');
    expect($bill->fresh()->status)->toBe('Unpaid');
});

test('doctors and patients cannot approve payments', function () {
    $bill = manualBill(Patient::factory()->create());
    $payment = manualPayment($bill);

    [$doctorUser] = doctorWithUser();
    [$patientUser] = portalPatient();

    $this->actingAs($doctorUser)->post("/payments/{$payment->id}/approve")->assertForbidden();
    $this->actingAs($patientUser)->post("/payments/{$payment->id}/approve")->assertRedirect(route('portal.dashboard'));

    expect($payment->fresh()->status)->toBe('pending');
    expect($bill->fresh()->status)->toBe('Unpaid');
});
