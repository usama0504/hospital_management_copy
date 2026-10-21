<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Staff (admin/receptionist) kisi patient ko portal ke liye invite karta hai.
 *
 * Patient khud register karke kisi ka bhi record claim nahi kar sakta
 * (warna koi doosre ki email likh kar uski medical history dekh leta).
 * Isliye account staff banata hai aur patient ko password set karne ka link
 * email hota hai; email ka link kholna hi uski email ownership ka saboot hai.
 */
class PatientPortalController extends Controller
{
    public function invite(Patient $patient)
    {
        $email = strtolower(trim((string) $patient->email));

        // Public booking guests ko banawati email milti hai; unhe invite nahi kar sakte.
        if ($email === '' || str_ends_with($email, '@careplus.local')) {
            return back()->with('error', 'This patient has no real email address. Add one first, then send the invitation.');
        }

        if ($patient->user_id) {
            $user = $patient->user;
        } else {
            if (User::where('email', $email)->exists()) {
                return back()->with('error', 'This email is already used by another account, so it cannot be linked to this patient.');
            }

            $user = DB::transaction(function () use ($patient, $email) {
                $user = User::create([
                    'name' => $patient->name,
                    'email' => $email,
                    'password' => Hash::make(Str::random(40)),
                ]);

                $user->forceFill([
                    'is_approved' => true,
                    'email_verified_at' => now(),
                ])->save();

                $user->assignRole('patient');

                $patient->user_id = $user->id;
                $patient->save();

                return $user;
            });
        }

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Portal invitation sent to ' . $user->email . '. The link lets the patient set a password.');
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->with('error', 'An invitation was just sent. Please wait a minute before sending another.');
        }

        return back()->with('error', 'Could not send the invitation email. Please try again.');
    }
}
