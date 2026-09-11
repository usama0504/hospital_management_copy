<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\DoctorAvailability;

class DoctorAvailabilityController extends Controller
{
    // 1. Get Availabilities (Role-based filtering)
    public function index(Request $request, $doctor_id = null)
    {
        $user = $request->user();
        $isDoctorUser = false;

        if ($user) {
            if (method_exists($user, 'hasRole')) {
                $isDoctorUser = $user->hasRole('doctor');
            }

            if (!$isDoctorUser) {
                $doctor = Doctor::where('email', $user->email)->first();
                $isDoctorUser = !empty($doctor);
            }
        }

        if ($isDoctorUser) {
            $doctor = Doctor::where('email', $user->email)->first();
            $targetDoctorId = $doctor ? $doctor->id : $user->id;
        } else {
            $targetDoctorId = $doctor_id ?? $user->id;
        }

        $availabilities = DoctorAvailability::where('doctor_id', $targetDoctorId)->get();

        return response()->json([
            'status' => 'success',
            'target_doctor_id' => $targetDoctorId,
            'data' => $availabilities
        ]);
    }

    // Naya time slot save karne ke liye (Admin ya Doctor dono ke liye)
    public function store(Request $request)
    {
        $request->validate([
            'day_of_week' => ['required', 'string'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'doctor_id' => ['nullable', 'exists:doctors,id'], // Admin ke liye optional doctor_id
        ]);

        $user = $request->user();
        $targetDoctorId = null;

        // 1. Check karein ke kya user ke pas 'admin' role hai aur usne request mein doctor_id di hai
        if (method_exists($user, 'hasRole') && $user->hasRole('admin') && $request->filled('doctor_id')) {
            $targetDoctorId = $request->doctor_id;
        } else {
            // 2. Agar doctor khud login hai toh uski apni email se doctor profile find karein
            $doctor = Doctor::where('email', $user->email)->first();

            if (!$doctor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access Denied: Doctor profile not found for this user.'
                ], 403);
            }
            $targetDoctorId = $doctor->id;
        }

        // 3. Availability create karein
        $availability = DoctorAvailability::create([
            'doctor_id' => $targetDoctorId,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Availability slot added successfully!',
            'data' => $availability
        ], 201);
    }
    
    // 3. Delete Availability Slot
    public function destroy($id)
    {
        $availability = DoctorAvailability::findOrFail($id);
        $availability->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Availability slot removed successfully!'
        ]);
    }
}