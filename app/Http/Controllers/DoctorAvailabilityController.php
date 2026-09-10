<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use Inertia\Inertia;

class DoctorAvailabilityController extends Controller
{
    public function index($doctor_id = null)
    {
        $user = Auth::user();

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
            $targetDoctorId = $doctor_id ?? Auth::id();
        }

        $availabilities = DoctorAvailability::where('doctor_id', $targetDoctorId)->get();

        return Inertia::render('Doctors/Availability', [
            'availabilities' => $availabilities,
            'targetDoctorId' => $targetDoctorId
        ]);
    }

    // Naya time slot save karne ke liye
    public function store(Request $request)
    {
        $request->validate([
            'day_of_week' => ['required', 'string'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
        ]);

        $doctor = Doctor::where('email', Auth::user()->email)->first();

        if (!$doctor) {
            abort(403, 'Access Denied: Doctor profile not found.');
        }

        $doctor->availabilities()->create([
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return back()->with('success', 'Availability slot added successfully!');
    }

    public function destroy($id)
    {
        $availability = DoctorAvailability::findOrFail($id);
        $availability->delete();

        return back()->with('success', 'Availability slot removed successfully!');
    }
}