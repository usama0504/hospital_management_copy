<?php

use App\Models\Bill;
use App\Models\Doctor;
use App\Models\Patient;

function validBill(array $overrides = []): array
{
    return array_merge([
        'patient_id' => Patient::factory()->create()->id,
        'doctor_id' => Doctor::factory()->create()->id,
        'amount' => 1500,
        'status' => 'Unpaid',
        'bill_date' => '2026-10-01',
    ], $overrides);
}

test('guests are sent to the login page', function () {
    $this->get('/bills')->assertRedirect('/login');
});

test('admin and receptionist can open the bills page', function (string $role) {
    $this->actingAs(userWithRole($role))->get('/bills')->assertOk();
})->with(['admin', 'receptionist']);

test('doctors cannot open or create bills', function () {
    [$user] = doctorWithUser();

    $this->actingAs($user)->get('/bills')->assertForbidden();
    $this->actingAs($user)->post('/bills', validBill())->assertForbidden();
});

test('receptionist can create a bill', function () {
    $data = validBill();

    $this->actingAs(userWithRole('receptionist'))
        ->post('/bills', $data)
        ->assertRedirect(route('bills.index'));

    $this->assertDatabaseHas('bills', ['patient_id' => $data['patient_id'], 'status' => 'Unpaid']);
});

test('bill status must be Unpaid, Paid or Pending', function () {
    $this->actingAs(userWithRole('receptionist'))
        ->post('/bills', validBill(['status' => 'Whatever']))
        ->assertSessionHasErrors('status');
});

test('negative amounts are rejected', function () {
    $this->actingAs(userWithRole('receptionist'))
        ->post('/bills', validBill(['amount' => -5]))
        ->assertSessionHasErrors('amount');
});

test('a bill cannot be created for a deleted patient', function () {
    $patient = Patient::factory()->create();
    $patient->delete();

    $this->actingAs(userWithRole('receptionist'))
        ->post('/bills', validBill(['patient_id' => $patient->id]))
        ->assertSessionHasErrors('patient_id');
});

test('only admin can delete a bill, and it is a soft delete', function () {
    $bill = Bill::factory()->create();

    $this->actingAs(userWithRole('receptionist'))->delete("/bills/{$bill->id}")->assertForbidden();
    $this->assertNotSoftDeleted($bill);

    $this->actingAs(userWithRole('admin'))->delete("/bills/{$bill->id}");
    $this->assertSoftDeleted($bill);
});
