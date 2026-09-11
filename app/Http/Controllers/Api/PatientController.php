<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // 1. List of Patients (Role-based: Doctor sees only their patients, Admin/Receptionist sees all)
    public function index(Request $request)
    {
        $user = $request->user();

        if (method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            $doctor = Doctor::where('email', $user->email)->first();

            if ($doctor) {
                $patientIds = Appointment::where('doctor_id', $doctor->id)
                    ->pluck('patient_id')
                    ->unique();

                $patients = Patient::whereIn('id', $patientIds)->latest()->paginate(10);
            } else {
                $patients = collect();
            }
        } else {
            $patients = Patient::latest()->paginate(10);
        }

        return response()->json([
            'status' => 'success',
            'data' => $patients
        ]);
    }

    // 2. Store New Patient
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email',
            'phone' => 'required|string',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
        ]);

        $patient = Patient::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Patient added successfully.',
            'data' => $patient
        ], 201);
    }

    // 3. Show Single Patient Details
    public function show(Patient $patient)
    {
        return response()->json([
            'status' => 'success',
            'data' => $patient
        ]);
    }

    // 4. Update Patient
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email,' . $patient->id,
            'phone' => 'required|string',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
        ]);

        $patient->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Patient updated successfully.',
            'data' => $patient
        ]);
    }

    // 5. Delete Patient
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Patient deleted successfully.'
        ]);
    }
}