<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Result ignore karte hain: chahe email maujood ho ya nahi, jawab ek jaisa hota hai,
        // taake koi yeh andaza na laga sake ke kaun sa email registered hai.
        Password::sendResetLink($request->only('email'));

        return back()->with('success', 'If that email address is registered, we have sent a password reset link to it.');
    }

    public function showResetForm(Request $request, string $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Your password has been reset. You can now log in.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
