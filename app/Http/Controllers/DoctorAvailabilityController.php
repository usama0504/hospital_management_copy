<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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

        // --- CURRENT TIME & DAY LOGIC ---
        $currentDay = Carbon::now()->format('l'); // Aaj ka din (maslan: Thursday)
        $currentTime = Carbon::now()->format('H:i:s'); // Current time

        // Hum availabilities fetch karte waqt check lagayenge:
        // Agar aaj ka din hai, toh sirf wohi slots ayein jinka end_time abhi se aage ka ho.
        // Agar koi purana din hai (maslan Monday), toh woh apni marzi se dikha sakte hain ya sirf aaj ke liye filter kar sakte hain.
        $availabilities = DoctorAvailability::where('doctor_id', $targetDoctorId)
            ->where(function ($query) use ($currentDay, $currentTime) {
                // Agar din aaj ka nahi hai, toh saare slots dikhao
                $query->where('day_of_week', '!=', $currentDay)
                      // Lekin agar din AAJ hi ka hai, toh sirf wahi slots dikhao jinka time abhi baqi hai
                      ->orWhere(function ($q) use ($currentDay, $currentTime) {
                          $q->where('day_of_week', $currentDay)
                            ->where('end_time', '>', $currentTime);
                      });
            })
            ->get();
        // ----------------------------------------------------

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

        $doctor->availabilities()->create([
            'day_of_week' => $request->day_of_week,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
        ]);

        return back()->with('success', 'Availability slot added successfully!');
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
