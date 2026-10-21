<?php

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;

// Ye tests tab chalte hain jab `composer require dompdf/dompdf` ho chuka ho.
$noDompdf = ! class_exists(\Dompdf\Dompdf::class);

function pdfPrescription(array $attributes = []): Prescription
{
    $prescription = Prescription::factory()->create($attributes);

    PrescriptionItem::create([
        'prescription_id' => $prescription->id,
        'medicine_name' => 'Paracetamol',
        'dosage' => '500mg',
        'frequency' => '1-0-1',
        'duration' => '5 days',
        'instructions' => 'After meals',
    ]);

    return $prescription;
}

test('guests cannot download a prescription pdf', function () {
    $prescription = pdfPrescription();

    $this->get("/prescriptions/{$prescription->id}/pdf")->assertRedirect('/login');
})->skip($noDompdf, 'dompdf is not installed');

test('admin can download a prescription as a pdf', function () use ($noDompdf) {
    $prescription = pdfPrescription();

    $response = $this->actingAs(userWithRole('admin'))->get("/prescriptions/{$prescription->id}/pdf");

    $response->assertOk()->assertHeader('content-type', 'application/pdf');
    expect(substr($response->getContent(), 0, 4))->toBe('%PDF');
})->skip($noDompdf, 'dompdf is not installed');

test('the prescribing doctor can download the pdf but other doctors cannot', function () {
    [$owner, $ownerDoctor] = doctorWithUser();
    [$other] = doctorWithUser();

    $prescription = pdfPrescription(['doctor_id' => $ownerDoctor->id]);

    $this->actingAs($owner)->get("/prescriptions/{$prescription->id}/pdf")->assertOk();
    $this->actingAs($other)->get("/prescriptions/{$prescription->id}/pdf")->assertForbidden();
})->skip($noDompdf, 'dompdf is not installed');

test('a patient can download their own prescription pdf from the portal only', function () {
    $user = userWithRole('patient');
    $patient = Patient::factory()->create(['email' => $user->email, 'user_id' => $user->id]);

    $mine = pdfPrescription(['patient_id' => $patient->id]);
    $theirs = pdfPrescription();

    $response = $this->actingAs($user)->get("/portal/prescriptions/{$mine->id}/pdf");

    $response->assertOk()->assertHeader('content-type', 'application/pdf');
    expect(substr($response->getContent(), 0, 4))->toBe('%PDF');

    $this->actingAs($user)->get("/portal/prescriptions/{$theirs->id}/pdf")->assertNotFound();
})->skip($noDompdf, 'dompdf is not installed');
