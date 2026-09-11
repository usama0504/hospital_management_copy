<?php

namespace App\Http\Controllers\Api; // Agar aapne Api folder mein rakha hai

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // 1. Saari appointments ki list dikhane ke liye
    public function index()
    {
        $user = Auth::user();

        if (method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            $doctor = Doctor::where('email', $user->email)->first();

            if ($doctor) {
                $appointments = Appointment::where('doctor_id', $doctor->id)->with('patient', 'doctor')->latest()->paginate(10);
            } else {
                $appointments = collect();
            }
        } else {
            $appointments = Appointment::with('patient', 'doctor')->latest()->paginate(10);
        }

        // Inertia ki jagah JSON response bhej rahe hain
        return response()->json([
            'status' => 'success',
            'data' => $appointments
        ]);
    }

    // 2. Form ke liye zaroori data (Patients aur Doctors) dene ke liye
    public function createData()
    {
        $patients = Patient::all();
        $user = Auth::user();

        if (method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            $doctors = Doctor::where('email', $user->email)->get();
        } else {
            $currentDay = Carbon::now()->format('l');

            $doctorIds = DoctorAvailability::where('day_of_week', $currentDay)
                ->where('is_active', true)
                ->pluck('doctor_id');

            $doctors = Doctor::whereIn('id', $doctorIds)->get();
        }

        return response()->json([
            'status' => 'success',
            'patients' => $patients,
            'doctors' => $doctors
        ]);
    }

    // 3. Nayi appointment save karne ke liye
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

        $appointmentDate = Carbon::parse($request->appointment_date);
        $dayOfWeek = $appointmentDate->format('l');

        $isAvailable = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->exists();

        if (!$isAvailable) {
            return response()->json([
                'status' => 'error',
                'message' => 'Doctor is not available on ' . $dayOfWeek . '!'
            ], 422);
        }

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $doctorId,
            'appointment_date' => $request->appointment_date,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment created successfully.',
            'data' => $appointment
        ], 201);
    }

    // 4. Single appointment show karne ke liye (Edit ke waqt data lane ke liye)
    public function show(Appointment $appointment)
    {
        return response()->json([
            'status' => 'success',
            'appointment' => $appointment->load('patient', 'doctor')
        ]);
    }

    // 5. Appointment update karne ke liye
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $appointment->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment updated successfully.',
            'data' => $appointment
        ]);
    }

    // 6. Appointment delete karne ke liye
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment deleted successfully.'
        ]);
    }
}