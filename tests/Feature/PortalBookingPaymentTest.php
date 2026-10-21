<?php

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

function portalUnpaidBill(Patient $patient, float $amount = 1500, string $status = 'Unpaid'): Bill
{
    return Bill::factory()->create([
        'patient_id' => $patient->id,
        'amount' => $amount,
        'status' => $status,
    ]);
}

function portalBookingPayload($test, array $override = []): array
{
    return array_merge([
        'department_id' => $test->doctor->department_id,
        'doctor_id' => $test->doctor->id,
        'appointment_date' => $test->monday->format('Y-m-d'),
        'appointment_time' => '10:00',
        'notes' => 'Headache for two days',
    ], $override);
}

// ================================================================ Booking

describe('portal booking', function () {
    beforeEach(function () {
        Mail::fake();

        $this->monday = now()->next('Monday')->setTime(10, 0);
        $this->doctor = Doctor::factory()->create(['consultation_fee' => 1500]);

        DoctorAvailability::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_active' => true,
        ]);
    });

    test('a patient can open the booking page, staff cannot', function () {
        [$user] = portalPatient();

        $this->actingAs($user)->get('/portal/appointments/create')->assertOk();
        $this->actingAs(userWithRole('receptionist'))->get('/portal/appointments/create')->assertForbidden();
    });

    test('a patient can book a slot and a consultation bill is created', function () {
        [$user, $patient] = portalPatient();

        $this->actingAs($user)
            ->post('/portal/appointments', portalBookingPayload($this))
            ->assertRedirect(route('portal.appointments'));

        $appointment = Appointment::first();

        expect($appointment->patient_id)->toBe($patient->id);
        expect($appointment->doctor_id)->toBe($this->doctor->id);
        expect($appointment->status)->toBe('Pending');

        $bill = Bill::where('appointment_id', $appointment->id)->first();

        expect($bill)->not->toBeNull();
        expect((float) $bill->amount)->toBe(1500.0);
        expect($bill->status)->toBe('Unpaid');
    });

    test('the booking always belongs to the logged in patient', function () {
        [$user, $patient] = portalPatient();
        $other = Patient::factory()->create();

        $this->actingAs($user)->post('/portal/appointments', portalBookingPayload($this, [
            'patient_id' => $other->id,
        ]));

        expect(Appointment::first()->patient_id)->toBe($patient->id);
    });

    test('a taken slot cannot be booked again', function () {
        [$user] = portalPatient();

        Appointment::factory()->create([
            'doctor_id' => $this->doctor->id,
            'appointment_date' => $this->monday,
            'status' => 'Pending',
        ]);

        $this->actingAs($user)
            ->post('/portal/appointments', portalBookingPayload($this))
            ->assertSessionHasErrors('appointment_time');

        expect(Appointment::count())->toBe(1);
    });

    test('times outside working hours are rejected', function () {
        [$user] = portalPatient();

        $this->actingAs($user)
            ->post('/portal/appointments', portalBookingPayload($this, ['appointment_time' => '20:00']))
            ->assertSessionHasErrors('appointment_time');

        expect(Appointment::count())->toBe(0);
    });

    test('past dates are rejected', function () {
        [$user] = portalPatient();

        $this->actingAs($user)
            ->post('/portal/appointments', portalBookingPayload($this, [
                'appointment_date' => now()->subDay()->format('Y-m-d'),
            ]))
            ->assertSessionHasErrors('appointment_date');
    });

    test('the doctor must belong to the chosen department', function () {
        [$user] = portalPatient();
        $otherDoctor = Doctor::factory()->create();

        $this->actingAs($user)
            ->post('/portal/appointments', portalBookingPayload($this, [
                'department_id' => $otherDoctor->department_id,
            ]))
            ->assertSessionHasErrors('doctor_id');
    });
});

// ================================================================ Payments

describe('portal payments', function () {
    test('the bills page tells the ui that online payment is available', function () {
        [$user] = portalPatient();

        $this->actingAs($user)->get('/portal/bills')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Portal/Bills')
                ->where('payments.enabled', true)
                ->where('payments.sandbox', true)
                ->etc());
    });

    test('paying a bill starts a pending payment with the amount taken from the bill', function () {
        [$user, $patient] = portalPatient();
        $bill = portalUnpaidBill($patient, 2500);

        // browser se bheja gaya 'amount' ignore hota hai
        $response = $this->actingAs($user)->post("/portal/bills/{$bill->id}/pay", ['amount' => 1]);

        $payment = Payment::first();

        $response->assertRedirect(route('portal.payments.sandbox', $payment));

        expect($payment->bill_id)->toBe($bill->id);
        expect($payment->patient_id)->toBe($patient->id);
        expect((float) $payment->amount)->toBe(2500.0);
        expect($payment->status)->toBe('pending');
        expect($payment->gateway)->toBe('sandbox');
        expect($bill->fresh()->status)->toBe('Unpaid');
    });

    test('a successful sandbox payment marks the bill as paid', function () {
        [$user, $patient] = portalPatient();
        $bill = portalUnpaidBill($patient);

        $this->actingAs($user)->post("/portal/bills/{$bill->id}/pay");
        $payment = Payment::first();

        $this->actingAs($user)->get("/portal/payments/{$payment->id}/sandbox")->assertOk();

        $this->actingAs($user)
            ->post("/portal/payments/{$payment->id}/sandbox", ['outcome' => 'paid'])
            ->assertRedirect(route('portal.bills'))
            ->assertSessionHas('success');

        expect($bill->fresh()->status)->toBe('Paid');
        expect($payment->fresh()->status)->toBe('paid');
        expect($payment->fresh()->paid_at)->not->toBeNull();
    });

    test('a failed payment leaves the bill unpaid', function () {
        [$user, $patient] = portalPatient();
        $bill = portalUnpaidBill($patient);

        $this->actingAs($user)->post("/portal/bills/{$bill->id}/pay");
        $payment = Payment::first();

        $this->actingAs($user)
            ->post("/portal/payments/{$payment->id}/sandbox", ['outcome' => 'failed'])
            ->assertSessionHas('error');

        expect($bill->fresh()->status)->toBe('Unpaid');
        expect($payment->fresh()->status)->toBe('failed');
    });

    test('a finished payment cannot be completed again', function () {
        [$user, $patient] = portalPatient();
        $bill = portalUnpaidBill($patient);

        $this->actingAs($user)->post("/portal/bills/{$bill->id}/pay");
        $payment = Payment::first();

        $this->actingAs($user)->post("/portal/payments/{$payment->id}/sandbox", ['outcome' => 'failed']);
        $this->actingAs($user)
            ->post("/portal/payments/{$payment->id}/sandbox", ['outcome' => 'paid'])
            ->assertNotFound();

        expect($bill->fresh()->status)->toBe('Unpaid');
        expect($payment->fresh()->status)->toBe('failed');
    });

    test('a patient cannot pay someone elses bill', function () {
        [$user] = portalPatient();
        $theirs = portalUnpaidBill(Patient::factory()->create());

        $this->actingAs($user)->post("/portal/bills/{$theirs->id}/pay")->assertNotFound();

        expect(Payment::count())->toBe(0);
    });

    test('a patient cannot touch someone elses payment', function () {
        [$user] = portalPatient();
        $otherPatient = Patient::factory()->create();
        $bill = portalUnpaidBill($otherPatient);

        $payment = Payment::create([
            'bill_id' => $bill->id,
            'patient_id' => $otherPatient->id,
            'gateway' => 'sandbox',
            'reference' => 'PAY-TESTREF000001',
            'amount' => $bill->amount,
            'currency' => 'PKR',
            'status' => 'pending',
        ]);

        $this->actingAs($user)->get("/portal/payments/{$payment->id}/sandbox")->assertNotFound();
        $this->actingAs($user)
            ->post("/portal/payments/{$payment->id}/sandbox", ['outcome' => 'paid'])
            ->assertNotFound();

        expect($bill->fresh()->status)->toBe('Unpaid');
    });

    test('an already paid bill cannot be paid again', function () {
        [$user, $patient] = portalPatient();
        $bill = portalUnpaidBill($patient, 1500, 'Paid');

        $this->actingAs($user)->post("/portal/bills/{$bill->id}/pay")->assertSessionHas('error');

        expect(Payment::count())->toBe(0);
    });

    test('a bill with nothing to pay is rejected', function () {
        [$user, $patient] = portalPatient();
        $bill = portalUnpaidBill($patient, 0);

        $this->actingAs($user)->post("/portal/bills/{$bill->id}/pay")->assertSessionHas('error');

        expect(Payment::count())->toBe(0);
    });

    test('the sandbox gateway is switched off in production', function () {
        [$user, $patient] = portalPatient();
        $bill = portalUnpaidBill($patient);

        app()->detectEnvironment(fn () => 'production');

        $this->actingAs($user)->post("/portal/bills/{$bill->id}/pay")->assertSessionHas('error');

        expect(Payment::count())->toBe(0);
        expect($bill->fresh()->status)->toBe('Unpaid');
    });
});
