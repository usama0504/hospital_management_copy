<?php

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

// API par bhi wahi role/permission rules lagte hain jo web par hain.

test('guests cannot use the protected api', function () {
    $this->getJson('/api/patients')->assertUnauthorized();
});

test('only admin can list, approve or delete users via the api', function () {
    $target = User::factory()->create();

    foreach (['receptionist', 'doctor'] as $role) {
        Sanctum::actingAs(userWithRole($role));

        $this->getJson('/api/users')->assertForbidden();
        $this->patchJson("/api/users/{$target->id}/approve")->assertForbidden();
        $this->deleteJson("/api/users/{$target->id}")->assertForbidden();
    }

    expect(User::find($target->id))->not->toBeNull();

    Sanctum::actingAs(userWithRole('admin'));
    $this->getJson('/api/users')->assertOk();
});

test('non-admins cannot create, edit or delete doctors via the api', function () {
    $doctor = Doctor::factory()->create();

    foreach (['receptionist', 'doctor'] as $role) {
        Sanctum::actingAs(userWithRole($role));

        $this->postJson('/api/doctors', [
            'name' => 'Dr X',
            'email' => 'drx@example.com',
            'phone' => '03001234567',
            'specialization' => 'General',
            'password' => 'password123',
        ])->assertForbidden();
        $this->putJson("/api/doctors/{$doctor->id}", [])->assertForbidden();
        $this->deleteJson("/api/doctors/{$doctor->id}")->assertForbidden();
    }

    expect(Doctor::find($doctor->id))->not->toBeNull();
});

test('only admin can delete patients via the api', function () {
    $patient = Patient::factory()->create();

    Sanctum::actingAs(userWithRole('receptionist'));
    $this->deleteJson("/api/patients/{$patient->id}")->assertForbidden();

    Sanctum::actingAs(userWithRole('doctor'));
    $this->deleteJson("/api/patients/{$patient->id}")->assertForbidden();

    expect(Patient::find($patient->id))->not->toBeNull();

    Sanctum::actingAs(userWithRole('admin'));
    $this->deleteJson("/api/patients/{$patient->id}")->assertSuccessful();
});

test('doctors cannot create patients or bills via the api', function () {
    Sanctum::actingAs(userWithRole('doctor'));

    $this->postJson('/api/patients', [
        'name' => 'A', 'email' => 'a@example.com', 'phone' => '0300',
    ])->assertForbidden();
    $this->getJson('/api/bills')->assertForbidden();
});

test('receptionist can create a patient via the api', function () {
    Sanctum::actingAs(userWithRole('receptionist'));

    $this->postJson('/api/patients', [
        'name' => 'Ali',
        'email' => 'ali@example.com',
        'phone' => '03001234567',
    ])->assertSuccessful();

    $this->assertDatabaseHas('patients', ['email' => 'ali@example.com']);
});

test('api patient update ignores fields that are not validated', function () {
    $patient = Patient::factory()->create(['email' => 'old@example.com']);

    Sanctum::actingAs(userWithRole('receptionist'));

    $this->putJson("/api/patients/{$patient->id}", [
        'name' => 'New Name',
        'email' => 'old@example.com',
        'phone' => '03001234567',
        'id' => 99999,
    ])->assertSuccessful();

    expect($patient->fresh()->id)->toBe($patient->id);
});

test('registering as a doctor links the doctor profile by user_id', function () {
    test()->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    $this->postJson('/api/register', [
        'name' => 'Dr Link',
        'email' => 'drlink@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'doctor',
    ])->assertSuccessful();

    $user = User::where('email', 'drlink@example.com')->first();

    expect(Doctor::where('user_id', $user->id)->exists())->toBeTrue();
});

test('passwords shorter than 8 characters are rejected at registration', function () {
    test()->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    $this->postJson('/api/register', [
        'name' => 'Short',
        'email' => 'short@example.com',
        'password' => 'abc123',
        'password_confirmation' => 'abc123',
        'role' => 'receptionist',
    ])->assertUnprocessable();
});
