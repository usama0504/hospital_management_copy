<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Agar user Doctor hai, toh sirf uske appointment wale patients show hon
        if (method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            // Doctor ko user ki email ke zariye find karein
            $doctor = Doctor::where('email', $user->email)->first();

            if ($doctor) {
                // Appointments table se is doctor ki related patient IDs nikal lein
                $patientIds = Appointment::where('doctor_id', $doctor->id)
                                        ->pluck('patient_id')
                                        ->unique();

                $patients = Patient::whereIn('id', $patientIds)->latest()->paginate(10);
            } else {
                $patients = collect(); // Agar doctor profile nahi mili
            }
        } else {
            // Admin ya Receptionist ke liye sab patients show hon
            $patients = Patient::latest()->paginate(10);
        }

        return Inertia::render('Patients/Index', [
            'patients' => $patients
        ]);
    }

    public function create()
    {
        return Inertia::render('Patients/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email',
            'phone' => 'required|string',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
        ]);

        Patient::create($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient added successfully.');
    }

    public function edit(Patient $patient)
    {
        return Inertia::render('Patients/Edit', [
            'patient' => $patient
        ]);
    }

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

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }
}