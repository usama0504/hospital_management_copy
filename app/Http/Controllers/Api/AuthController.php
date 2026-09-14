<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. Register API
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

        // New self-registrations always start as pending approval.
        $user->forceFill(['is_approved' => false])->save();

        // Assign role
        $user->assignRole($request->role);

        // Agar role 'doctor' hai toh doctors table mein bhi entry kar dein
        if ($request->role === 'doctor') {
            Doctor::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => 'N/A',
                'specialization' => 'General Practitioner',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Registration submitted! Your account is pending admin approval. You will be able to log in once approved.'
        ], 201);
    }

    // 2. Login API (Sanctum Token Generation)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check credentials manually for API
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.'
            ], 401);
        }

        // Check if user is approved by admin
        if (! $user->is_approved) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is pending admin approval. Please wait until an admin approves your registration.'
            ], 403);
        }

        // Generate Sanctum Token (Chabi)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames() // Ya aapka role nikalne ka jo tareeqa ho
            ]
        ]);
    }

    // 3. Logout API (Revoke Token)
    public function logout(Request $request)
    {
        // Current user ka active token delete/revoke kar do
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.'
        ]);
    }
}