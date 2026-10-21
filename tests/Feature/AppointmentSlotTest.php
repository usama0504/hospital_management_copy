<?php

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Patient;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Mail::fake();

    $this->monday = now()->next('Monday')->setTime(10, 0);
    $this->doctor = Doctor::factory()->create();

    DoctorAvailability::create([
        'doctor_id' => $this->doctor->id,
        'day_of_week' => 'Monday',
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'is_active' => true,
    ]);

    $this->patient = Patient::factory()->create();
});

function slotPayload($test, $when): array
{
    return [
        'patient_id' => $test->patient->id,
        'department_id' => $test->doctor->department_id,
        'doctor_id' => $test->doctor->id,
        'appointment_date' => $when->format('Y-m-d H:i:s'),
        'status' => 'Scheduled',
    ];
}

test('a valid slot can be booked from the dashboard', function () {
    $this->actingAs(userWithRole('receptionist'))
        ->post('/appointments', slotPayload($this, $this->monday))
        ->assertRedirect(route('appointments.index'));

    $this->assertDatabaseCount('appointments', 1);
});

test('an overlapping slot is rejected', function () {
    $admin = userWithRole('admin');

    $this->actingAs($admin)->post('/appointments', slotPayload($this, $this->monday));

    $this->actingAs($admin)
        ->post('/appointments', slotPayload($this, $this->monday->copy()->addMinutes(15)))
        ->assertSessionHasErrors('appointment_date');

    $this->assertDatabaseCount('appointments', 1);
});

test('the slot right after an existing one is allowed', function () {
    $admin = userWithRole('admin');

    $this->actingAs($admin)->post('/appointments', slotPayload($this, $this->monday));
    $this->actingAs($admin)->post('/appointments', slotPayload($this, $this->monday->copy()->addMinutes(30)));

    $this->assertDatabaseCount('appointments', 2);
});

test('a cancelled appointment does not block its slot', function () {
    Appointment::factory()->create([
        'doctor_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'appointment_date' => $this->monday,
        'status' => 'Cancelled',
    ]);

    $this->actingAs(userWithRole('admin'))
        ->post('/appointments', slotPayload($this, $this->monday))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('appointments', 2);
});

test('times outside working hours, odd slots and past times are rejected', function () {
    $admin = userWithRole('admin');

    foreach ([
        $this->monday->copy()->setTime(8, 0),   // working hours se pehle
        $this->monday->copy()->setTime(16, 45), // end time cross karta hai
        $this->monday->copy()->setTime(10, 10), // 30-minute slot nahi
        now()->subDay()->setTime(10, 0),        // guzra hua waqt
    ] as $when) {
        $this->actingAs($admin)
            ->post('/appointments', slotPayload($this, $when))
            ->assertSessionHasErrors('appointment_date');
    }

    $this->assertDatabaseCount('appointments', 0);
});

test('a day the doctor does not work is rejected', function () {
    $tuesday = $this->monday->copy()->addDay();

    $this->actingAs(userWithRole('admin'))
        ->post('/appointments', slotPayload($this, $tuesday))
        ->assertSessionHasErrors('appointment_date');
});

test('the api applies the same slot rules as the dashboard', function () {
    Sanctum::actingAs(userWithRole('receptionist'));

    $payload = [
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'appointment_date' => $this->monday->format('Y-m-d H:i:s'),
        'status' => 'Scheduled',
    ];

    $this->postJson('/api/appointments', $payload)->assertCreated();

    // Wahi slot dobara -> overlap
    $this->postJson('/api/appointments', $payload)->assertUnprocessable();

    // Working hours ke bahar
    $this->postJson('/api/appointments', array_merge($payload, [
        'appointment_date' => $this->monday->copy()->setTime(20, 0)->format('Y-m-d H:i:s'),
    ]))->assertUnprocessable();

    $this->assertDatabaseCount('appointments', 1);
});

test('editing an appointment does not conflict with itself', function () {
    $admin = userWithRole('admin');

    $this->actingAs($admin)->post('/appointments', slotPayload($this, $this->monday));
    $appointment = Appointment::first();

    $this->actingAs($admin)
        ->put("/appointments/{$appointment->id}", array_merge(
            slotPayload($this, $this->monday),
            ['status' => 'Completed']
        ))
        ->assertSessionHasNoErrors();

    expect($appointment->fresh()->status)->toBe('Completed');
});

test('a doctor is matched to their profile by user_id, not by email', function () {
    [$user, $doctor] = doctorWithUser();

    // email badalne par bhi doctor ka access nahi toot'ta
    $user->update(['email' => 'changed@example.com']);

    Appointment::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $this->patient->id]);

    $this->actingAs($user->fresh())->get('/appointments')->assertOk();
});
