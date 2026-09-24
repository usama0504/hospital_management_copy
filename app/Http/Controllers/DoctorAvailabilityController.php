<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use Inertia\Inertia;

class DoctorAvailabilityController extends Controller
{
    public function index(Request $request, $doctor_id = null)
    {

        $doctor_id = $doctor_id ?? $request->query('doctor_id');

        $user = Auth::user();

        $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('admin');
        $doctor  = Doctor::where('email', $user->email)->first();

        if ($isAdmin) {
            $targetDoctorId = $doctor_id ?? (Doctor::first()?->id);

            if (!$targetDoctorId) {
                abort(404, 'No doctors found.');
            }

            $targetDoctor = Doctor::find($targetDoctorId);

            if (!$targetDoctor) {
                abort(404, 'Doctor not found.');
            }
        } elseif ($doctor) {
            $targetDoctorId = $doctor->id;
            $targetDoctor = $doctor;
        } else {
            abort(403, 'Access Denied: You are not authorized to view this page.');
        }

        $availabilities = DoctorAvailability::where('doctor_id', $targetDoctorId)->get();

        return Inertia::render('Doctors/Availability', [
            'availabilities' => $availabilities,
            'targetDoctorId' => $targetDoctorId,
            'doctorName'     => $targetDoctor->name,
            'isAdmin'        => $isAdmin,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('admin');

        if ($isAdmin) {
            $request->validate([
                'day_of_week' => ['required', 'string'],
                'start_time'  => ['required'],
                'end_time'    => ['required', 'after:start_time'],
                'doctor_id'   => ['required', 'exists:doctors,id'],
            ]);

            $doctor = Doctor::find($request->doctor_id);
        } else {
            $request->validate([
                'day_of_week' => ['required', 'string'],
                'start_time'  => ['required'],
                'end_time'    => ['required', 'after:start_time'],
            ]);

            $doctor = Doctor::where('email', $user->email)->first();
        }

        if (!$doctor) {
            abort(403, 'Access Denied: Doctor profile not found.');
        }

        $overlap = DoctorAvailability::where('doctor_id', $doctor->id)->where('day_of_week', $request->day_of_week)->where('is_active', true)->where(function ($query) use ($request) {
            $query->where('start_time', '<', $request->end_time)->where('end_time', '>', $request->start_time);
        })->exists();
        if ($overlap) {
            return back()->withErrors(['start_time' => 'This time slot overlaps with an existing shift.']);
        }

        $doctor->availabilities()->create([
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => true,
        ]);

        return back()->with('success', 'Availability slot added successfully!');
    }

    public function toggle($id)
    {
        $user = Auth::user();
        $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('admin');
        $availability = DoctorAvailability::findOrFail($id);
        if ($isAdmin) {
            $availability->update(['is_active' => !$availability->is_active,]);
            return back()->with('success', 'Availability status updated successfully!');
        }
        $doctor = Doctor::where('email', $user->email)->first();
        if (!$doctor || $availability->doctor_id !== $doctor->id) {
            abort(403, 'Access Denied: This is not your slot.');
        }
        $availability->update(['is_active' => !$availability->is_active,]);
        return back()->with('success', 'Availability status updated successfully!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('admin');

        $availability = DoctorAvailability::findOrFail($id);

        if ($isAdmin) {
            $availability->delete();
            return back()->with('success', 'Availability slot removed successfully!');
        }

        $doctor = Doctor::where('email', $user->email)->first();

        if (!$doctor || $availability->doctor_id !== $doctor->id) {
            abort(403, 'Access Denied: This is not your slot.');
        }

        $availability->delete();

        return back()->with('success', 'Availability slot removed successfully!');
    }
}
