<?php

use App\Models\Doctor;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('registration screen can be rendered', function () {
    $this->get('/register')->assertOk();
});

test('a new receptionist registers as pending and is not logged in', function () {
    $this->post('/register', [
        'name' => 'Sara Reception',
        'email' => 'sara@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'receptionist',
    ])->assertRedirect(route('login'));

    $user = User::where('email', 'sara@example.com')->firstOrFail();

    expect((bool) $user->is_approved)->toBeFalse()
        ->and($user->hasRole('receptionist'))->toBeTrue();
    $this->assertGuest();
});

test('a new doctor also gets a doctor profile', function () {
    $this->post('/register', [
        'name' => 'Dr Ali',
        'email' => 'ali@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'doctor',
    ]);

    expect(Doctor::where('email', 'ali@example.com')->exists())->toBeTrue();
});

test('nobody can register themselves as admin', function () {
    $this->post('/register', [
        'name' => 'Sneaky',
        'email' => 'sneaky@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
    ])->assertSessionHasErrors('role');

    expect(User::where('email', 'sneaky@example.com')->exists())->toBeFalse();
});
