<?php

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Sanctum\Sanctum;

// ---------------------------------------------------------------- access

test('guests are sent to the login page for the portal', function () {
    $this->get('/portal')->assertRedirect('/login');
});

test('a patient can open every portal page', function () {
    [$user] = portalPatient();

    foreach (['/portal', '/portal/appointments', '/portal/prescriptions', '/portal/bills'] as $url) {
        $this->actingAs($user)->get($url)->assertOk();
    }
});

test('patients are kept out of the staff area', function () {
    [$user] = portalPatient();

    foreach (['/dashboard', '/patients', '/doctors', '/appointments', '/prescriptions', '/reports'] as $url) {
        $this->actingAs($user)->get($url)->assertRedirect(route('portal.dashboard'));
    }
});

test('patients cannot use the staff api', function () {
    [$user] = portalPatient();

    Sanctum::actingAs($user);

    $this->getJson('/api/patients')->assertForbidden();
    $this->getJson('/api/doctors')->assertForbidden();
    $this->getJson('/api/appointments')->assertForbidden();
});

test('staff cannot open the patient portal', function (string $role) {
    $this->actingAs(userWithRole($role))->get('/portal')->assertForbidden();
})->with(['admin', 'doctor', 'receptionist']);

test('logging in as a patient lands on the portal', function () {
    [$user] = portalPatient();
    $user->forceFill(['is_approved' => true])->save();

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('portal.dashboard'));
});

// ------------------------------------------------------------ own data only

test('a patient only sees their own prescriptions, appointments and bills', function () {
    [$user, $patient] = portalPatient();
    $other = Patient::factory()->create();

    Prescription::factory()->count(2)->create(['patient_id' => $patient->id]);
    Prescription::factory()->count(3)->create(['patient_id' => $other->id]);

    Appointment::factory()->create(['patient_id' => $patient->id]);
    Appointment::factory()->count(2)->create(['patient_id' => $other->id]);

    Bill::factory()->create(['patient_id' => $patient->id]);
    Bill::factory()->count(2)->create(['patient_id' => $other->id]);

    $this->actingAs($user)->get('/portal/prescriptions')
        ->assertInertia(fn (Assert $page) => $page->component('Portal/Prescriptions')->has('prescriptions.data', 2)->etc());

    $this->actingAs($user)->get('/portal/appointments')
        ->assertInertia(fn (Assert $page) => $page->component('Portal/Appointments')->has('appointments.data', 1)->etc());

    $this->actingAs($user)->get('/portal/bills')
        ->assertInertia(fn (Assert $page) => $page->component('Portal/Bills')->has('bills.data', 1)->etc());
});

test('a patient can open their own prescription but not someone elses', function () {
    [$user, $patient] = portalPatient();

    $mine = Prescription::factory()->create(['patient_id' => $patient->id]);
    $theirs = Prescription::factory()->create(); // kisi aur patient ki

    $this->actingAs($user)->get("/portal/prescriptions/{$mine->id}")->assertOk();
    $this->actingAs($user)->get("/portal/prescriptions/{$theirs->id}")->assertNotFound();
});

// ---------------------------------------------------------------- cancel

test('a patient can cancel their own upcoming appointment', function () {
    Mail::fake();
    [$user, $patient] = portalPatient();

    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'Pending',
        'appointment_date' => now()->addDays(2)->setTime(10, 0),
    ]);

    $this->actingAs($user)->patch("/portal/appointments/{$appointment->id}/cancel")
        ->assertSessionHas('success');

    expect($appointment->fresh()->status)->toBe('Cancelled');
});

test('a patient cannot cancel a past appointment', function () {
    Mail::fake();
    [$user, $patient] = portalPatient();

    $appointment = Appointment::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'Scheduled',
        'appointment_date' => now()->subDay()->setTime(10, 0),
    ]);

    $this->actingAs($user)->patch("/portal/appointments/{$appointment->id}/cancel")
        ->assertSessionHas('error');

    expect($appointment->fresh()->status)->toBe('Scheduled');
});

test('a patient cannot cancel someone elses appointment', function () {
    Mail::fake();
    [$user] = portalPatient();

    $theirs = Appointment::factory()->create(['status' => 'Pending']);

    $this->actingAs($user)->patch("/portal/appointments/{$theirs->id}/cancel")->assertNotFound();

    expect($theirs->fresh()->status)->toBe('Pending');
});

// ---------------------------------------------------------------- invite

test('receptionist can invite a patient to the portal', function () {
    Notification::fake();

    $patient = Patient::factory()->create(['email' => 'new.patient@example.com']);

    $this->actingAs(userWithRole('receptionist'))
        ->post("/patients/{$patient->id}/portal-invite")
        ->assertSessionHas('success');

    $user = User::where('email', 'new.patient@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->hasRole('patient'))->toBeTrue();
    expect($patient->fresh()->user_id)->toBe($user->id);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('inviting twice reuses the same account', function () {
    Notification::fake();

    $patient = Patient::factory()->create(['email' => 'twice@example.com']);
    $admin = userWithRole('admin');

    $this->actingAs($admin)->post("/patients/{$patient->id}/portal-invite");
    $this->actingAs($admin)->post("/patients/{$patient->id}/portal-invite");

    expect(User::where('email', 'twice@example.com')->count())->toBe(1);
});

test('doctors cannot invite patients', function () {
    [$doctorUser] = doctorWithUser();
    $patient = Patient::factory()->create();

    $this->actingAs($doctorUser)->post("/patients/{$patient->id}/portal-invite")->assertForbidden();
});

test('guest booking patients with a placeholder email cannot be invited', function () {
    Notification::fake();

    $patient = Patient::factory()->create(['email' => 'guest.03001234567@careplus.local']);

    $this->actingAs(userWithRole('admin'))
        ->post("/patients/{$patient->id}/portal-invite")
        ->assertSessionHas('error');

    expect($patient->fresh()->user_id)->toBeNull();
    Notification::assertNothingSent();
});

test('an email already used by another account cannot be linked to a patient', function () {
    Notification::fake();

    $staff = User::factory()->create(['email' => 'taken@example.com']);
    $patient = Patient::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs(userWithRole('admin'))
        ->post("/patients/{$patient->id}/portal-invite")
        ->assertSessionHas('error');

    expect($patient->fresh()->user_id)->toBeNull();
    expect($staff->fresh()->hasRole('patient'))->toBeFalse();
});
