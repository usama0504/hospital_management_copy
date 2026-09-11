<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    // 1. Appointments ki list dekhne ke liye
    public function index(Request $request)
    {
        $user = $request->user();

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

        return response()->json([
            'status' => 'success',
            'data' => $appointments
        ]);
    }

    // 2. Nayi appointment store karne ke liye (API)
    public function store(Request $request)
    {
        $user = $request->user();
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
        $dayOfWeek = $appointmentDate->format('l');

        $isAvailable = \App\Models\DoctorAvailability::where('doctor_id', $doctorId)
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

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $appointment->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment updated successfully.',
            'data' => $appointment
        ]);
    }

    // 4. Appointment Delete (Cancel) karne ke liye
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment deleted/cancelled successfully.'
        ]);
    }
}