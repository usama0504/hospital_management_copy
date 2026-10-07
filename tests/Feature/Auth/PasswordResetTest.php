<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('forgot password and reset password screens can be rendered', function () {
    $this->get('/forgot-password')->assertOk();
    $this->get('/reset-password/some-token')->assertOk();
});

test('a reset link is sent to a registered email', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHas('success');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('an unknown email gets the same answer and nothing is sent', function () {
    Notification::fake();

    $this->post('/forgot-password', ['email' => 'nobody@example.com'])
        ->assertSessionHas('success');

    Notification::assertNothingSent();
});

test('the password can be reset with a valid token', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasNoErrors()->assertRedirect(route('login'));

        return true;
    });

    $this->post('/login', ['email' => $user->email, 'password' => 'new-password']);
    $this->assertAuthenticatedAs($user);
});

test('an invalid token cannot reset the password', function () {
    $user = User::factory()->create();

    $this->post('/reset-password', [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertSessionHasErrors('email');

    $this->post('/login', ['email' => $user->email, 'password' => 'new-password']);
    $this->assertGuest();
});

test('the new password must be confirmed', function () {
    $this->post('/reset-password', [
        'token' => 'x',
        'email' => 'a@example.com',
        'password' => 'new-password',
        'password_confirmation' => 'different',
    ])->assertSessionHasErrors('password');
});
