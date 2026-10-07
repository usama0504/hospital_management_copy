<?php

use App\Mail\AppointmentNotification;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

test('a pending appointment sends a "received" email to the patient', function () {
    $patient = Patient::factory()->create(['email' => 'p@example.com']);

    Appointment::factory()->create(['patient_id' => $patient->id, 'status' => 'Pending']);

    Mail::assertSent(AppointmentNotification::class, fn ($mail) => $mail->hasTo('p@example.com') && $mail->event === 'received');
});

test('a scheduled appointment sends a "confirmed" email', function () {
    $patient = Patient::factory()->create(['email' => 'p@example.com']);

    Appointment::factory()->create(['patient_id' => $patient->id, 'status' => 'Scheduled']);

    Mail::assertSent(AppointmentNotification::class, fn ($mail) => $mail->event === 'confirmed');
});

test('cancelling an appointment sends a "cancelled" email', function () {
    $patient = Patient::factory()->create(['email' => 'p@example.com']);
    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'status' => 'Scheduled']);

    $appointment->update(['status' => 'Cancelled']);

    Mail::assertSent(AppointmentNotification::class, fn ($mail) => $mail->event === 'cancelled');
});

test('changing the time sends a "rescheduled" email', function () {
    $patient = Patient::factory()->create(['email' => 'p@example.com']);
    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'status' => 'Scheduled']);

    $appointment->update(['appointment_date' => now()->addDays(5)->setTime(11, 30)]);

    Mail::assertSent(AppointmentNotification::class, fn ($mail) => $mail->event === 'rescheduled');
});

test('completing an appointment does not send an email', function () {
    $patient = Patient::factory()->create(['email' => 'p@example.com']);
    $appointment = Appointment::factory()->create(['patient_id' => $patient->id, 'status' => 'Scheduled']);
    Mail::fake(); // pehle wali "confirmed" email count se hata do

    $appointment->update(['status' => 'Completed']);

    Mail::assertNothingSent();
});

test('guests without a real email address are not emailed', function () {
    $patient = Patient::factory()->create(['email' => 'guest.03001234567@careplus.local']);

    Appointment::factory()->create(['patient_id' => $patient->id]);

    Mail::assertNothingSent();
});

test('the email shows the doctor and the appointment time', function () {
    $doctor = Doctor::factory()->create(['name' => 'Imran Sheikh']);
    $patient = Patient::factory()->create(['name' => 'Zainab']);
    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'appointment_date' => '2030-01-15 10:30:00',
    ]);

    $mailable = new AppointmentNotification($appointment, 'confirmed');

    $mailable->assertSeeInHtml('Zainab');
    $mailable->assertSeeInHtml('Dr. Imran Sheikh');
    $mailable->assertSeeInHtml('15 Jan 2030');
});
