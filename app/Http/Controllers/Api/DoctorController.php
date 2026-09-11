<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    // 1. Saare doctors ki list dekhne ke liye
    public function index()
    {
        $doctors = Doctor::latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }

    // 2. Naya doctor create karna (Sath hi User account & Role assign karna)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:doctors,email',
            'phone' => 'required|string',
            'specialization' => 'required|string',
            'password' => 'required|string|min:6', // Admin password set karega
        ]);

        // 1. User create karein taake doctor login kar sakay
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Role assign karein aur account direct approve kar dein
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('doctor');
        }
        $user->forceFill(['is_approved' => true])->save();

        // 2. Doctors table mein user_id ke sath entry karein
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'specialization' => $request->specialization,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Doctor added successfully and can now log in.',
            'data' => $doctor
        ], 201);
    }

    // 3. Specific doctor details dekhne ke liye
    public function show(Doctor $doctor)
    {
        return response()->json([
            'status' => 'success',
            'data' => $doctor
        ]);
    }

    // 4. Doctor aur linked User details update karne ke liye
    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $doctor->id,
            'phone' => 'required|string',
            'specialization' => 'required|string',
        ]);

        $doctor->update($request->all());

        // Agar doctors table mein user_id link hai toh user table ko bhi update karein
        if ($doctor->user_id) {
            $user = User::find($doctor->user_id);
            if ($user) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Doctor updated successfully.',
            'data' => $doctor
        ]);
    }

    // 5. Doctor aur uska linked user account delete karne ke liye
    public function destroy(Doctor $doctor)
    {
        if ($doctor->user_id) {
            User::where('id', $doctor->user_id)->delete();
        }

        $doctor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Doctor deleted successfully.'
        ]);
    }
}