<?php

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Prescription;

function prescriptionData(int $patientId): array
{
    return [
        'patient_id' => $patientId,
        'prescribed_date' => '2026-10-01',
        'diagnosis' => 'Seasonal flu',
        'items' => [
            ['medicine_name' => 'Paracetamol', 'dosage' => '500mg', 'frequency' => '3x daily', 'duration' => '3 days'],
        ],
    ];
}

test('receptionist cannot create prescriptions', function () {
    $patient = Patient::factory()->create();

    $this->actingAs(userWithRole('receptionist'))
        ->post('/prescriptions', prescriptionData($patient->id))
        ->assertForbidden();
});

test('doctor can prescribe to their own patient', function () {
    [$user, $doctor] = doctorWithUser();
    $patient = Patient::factory()->create();
    Appointment::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);

    $this->actingAs($user)
        ->post('/prescriptions', prescriptionData($patient->id))
        ->assertRedirect(route('prescriptions.index'));

    $prescription = Prescription::where('patient_id', $patient->id)->firstOrFail();
    expect($prescription->doctor_id)->toBe($doctor->id)
        ->and($prescription->items)->toHaveCount(1);
});

test('doctor cannot prescribe to a patient who is not theirs', function () {
    [$user] = doctorWithUser();
    $stranger = Patient::factory()->create();

    $this->actingAs($user)
        ->post('/prescriptions', prescriptionData($stranger->id))
        ->assertForbidden();

    expect(Prescription::count())->toBe(0);
});

test('a prescription needs at least one medicine', function () {
    [$user, $doctor] = doctorWithUser();
    $patient = Patient::factory()->create();
    Appointment::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);

    $this->actingAs($user)
        ->post('/prescriptions', ['items' => []] + prescriptionData($patient->id))
        ->assertSessionHasErrors('items');
});

test('only admin can delete a prescription, and it is a soft delete', function () {
    [$user, $doctor] = doctorWithUser();
    $prescription = Prescription::factory()->create(['doctor_id' => $doctor->id]);

    $this->actingAs($user)->delete("/prescriptions/{$prescription->id}")->assertForbidden();

    $this->actingAs(userWithRole('admin'))->delete("/prescriptions/{$prescription->id}");
    $this->assertSoftDeleted($prescription);
});
