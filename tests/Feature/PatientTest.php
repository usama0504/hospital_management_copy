<?php

use App\Models\Bill;
use App\Models\Patient;

function validPatient(array $overrides = []): array
{
    return array_merge([
        'name' => 'Ayesha Khan',
        'email' => 'ayesha@example.com',
        'phone' => '03001234567',
        'gender' => 'Female',
        'dob' => '1995-05-20',
        'address' => 'Bahawalpur',
    ], $overrides);
}

test('receptionist can add a patient', function () {
    $this->actingAs(userWithRole('receptionist'))
        ->post('/patients', validPatient())
        ->assertRedirect(route('patients.index'));

    $this->assertDatabaseHas('patients', ['email' => 'ayesha@example.com']);
});

test('patient form validates required fields and gender', function () {
    $this->actingAs(userWithRole('receptionist'))
        ->post('/patients', ['name' => '', 'gender' => 'Robot'])
        ->assertSessionHasErrors(['name', 'email', 'phone', 'gender']);
});

test('patient email must be unique', function () {
    Patient::factory()->create(['email' => 'ayesha@example.com']);

    $this->actingAs(userWithRole('receptionist'))
        ->post('/patients', validPatient())
        ->assertSessionHasErrors('email');
});

test('a patient can keep their own email when updating', function () {
    $patient = Patient::factory()->create(['email' => 'ayesha@example.com']);

    $this->actingAs(userWithRole('receptionist'))
        ->put("/patients/{$patient->id}", validPatient(['name' => 'Ayesha K.']))
        ->assertSessionHasNoErrors();

    expect($patient->fresh()->name)->toBe('Ayesha K.');
});

test('doctors cannot manage patients', function () {
    [$user] = doctorWithUser();

    $this->actingAs($user)->post('/patients', validPatient())->assertForbidden();
});

test('only admin can delete a patient', function () {
    $patient = Patient::factory()->create();

    $this->actingAs(userWithRole('receptionist'))
        ->delete("/patients/{$patient->id}")
        ->assertForbidden();

    $this->assertNotSoftDeleted($patient);
});

test('deleting a patient is a soft delete and can be restored from the trash', function () {
    $admin = userWithRole('admin');
    $patient = Patient::factory()->create();

    $this->actingAs($admin)->delete("/patients/{$patient->id}");
    $this->assertSoftDeleted($patient);

    $this->actingAs($admin)->get('/trash?type=patients')->assertOk();

    $this->actingAs($admin)
        ->patch("/trash/patients/{$patient->id}/restore")
        ->assertSessionHasNoErrors();

    $this->assertNotSoftDeleted($patient);
});

test('old bills still show the patient after the patient is deleted', function () {
    $patient = Patient::factory()->create(['name' => 'Old Patient']);
    $bill = Bill::factory()->create(['patient_id' => $patient->id]);

    $patient->delete();

    expect($bill->fresh()->patient?->name)->toBe('Old Patient');
});
