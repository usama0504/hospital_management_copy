<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:doctor,receptionist',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // New self-registrations always start as pending until an admin approves them.
        $user->forceFill(['is_approved' => false])->save();

        // Only doctor/receptionist can be self-selected at registration.
        // Admin accounts must always be assigned manually by an existing admin.
        $user->assignRole($request->role);

        // Agar role 'doctor' hai toh doctors table mein bhi entry kar dein
        if ($request->role === 'doctor') {
            Doctor::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => 'N/A', // Default value kyunki register form mein phone nahi hota
                'specialization' => 'General Practitioner', // Default specialty (baad mein edit ho sakti hai)
            ]);
        }

        return redirect()->route('login')->with('success',
            'Registration submitted! Your account is pending admin approval. You will be able to log in once approved.'
        );
    }

    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (! $user->is_approved) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Your account is pending admin approval. Please wait until an admin approves your registration.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}