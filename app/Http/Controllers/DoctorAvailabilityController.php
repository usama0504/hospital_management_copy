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
        $targetDoctorId = null;

        $doctor = Doctor::where('email', $user->email)->first();

        if ($doctor) {
            $targetDoctorId = $doctor->id;
        } else {
            $targetDoctorId = $doctor_id;
        }

        if (!$targetDoctorId) {
            $firstDoctor = Doctor::first();
            $targetDoctorId = $firstDoctor ? $firstDoctor->id : 1;
        }

        $availabilities = DoctorAvailability::where('doctor_id', $targetDoctorId)->get();

        return Inertia::render('Doctors/Availability', [
            'availabilities' => $availabilities,
            'targetDoctorId' => $targetDoctorId,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'day_of_week' => ['required', 'string'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
        ]);

        $doctor = Doctor::where('email', Auth::user()->email)->first();

        if (!$doctor) {
            $user = Auth::user();
            $isAdmin = is_callable([$user, 'hasRole'])
                && call_user_func([$user, 'hasRole'], 'admin');

            if ($request->has('doctor_id') && $isAdmin) {
                $doctor = Doctor::findOrFail($request->doctor_id);
            } else {
                abort(403, 'Access Denied: Doctor profile not found.');
            }
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