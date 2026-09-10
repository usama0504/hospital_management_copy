<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Agar user Doctor hai, toh sirf uski apni appointments show hon
        if (method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            $doctor = Doctor::where('email', $user->email)->first();

            if ($doctor) {
                $appointments = Appointment::where('doctor_id', $doctor->id)
                    ->with('patient', 'doctor')
                    ->latest()
                    ->paginate(10);
            } else {
                $appointments = collect();
            }
        } else {
            // Admin ya Receptionist ke liye sab appointments show hon
            $appointments = Appointment::with('patient', 'doctor')->latest()->paginate(10);
        }

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $patients = Patient::all();
        $user = Auth::user();

        if (method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            $doctors = Doctor::where('email', $user->email)->get();
        } else {
            // Sirf un doctors ko layein jinki aaj ke din availability active hai
            $currentDay = \Carbon\Carbon::now()->format('l'); // Aaj ka din (e.g., Thursday)

            $doctorIds = \App\Models\DoctorAvailability::where('day_of_week', $currentDay)
                ->where('is_active', true)
                ->pluck('doctor_id');

            $doctors = Doctor::whereIn('id', $doctorIds)->get();
        }

        return view('appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $doctorId = $request->doctor_id;

        if (method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            $doctor = Doctor::where('email', $user->email)->first();
            if ($doctor) {
                $doctorId = $doctor->id;
            }
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date',
            'status' => 'required|string',
        ]);

        if (! (method_exists($user, 'hasRole') && $user->hasRole('doctor'))) {
            $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
            ]);
            $doctorId = $request->doctor_id;
        }

        $appointmentDate = \Carbon\Carbon::parse($request->appointment_date);
        $dayOfWeek = $appointmentDate->format('l'); // Maslan: Monday, Tuesday, etc.

        $isAvailable = \App\Models\DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->exists();

        if (!$isAvailable) {
            return back()->withInput()->with('error', 'Doctor is not available on ' . $dayOfWeek . '!');
        }

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $doctorId,
            'appointment_date' => $request->appointment_date,
            'status' => $request->status,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully.');
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::all();
        $doctors = Doctor::all();
        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $appointment->update($request->all());
        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully.');
    }
}
