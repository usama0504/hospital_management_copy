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
    // Constructor mein middleware ya authorization bhi laga sakte hain
    public function __construct()
    {
        // Misal ke taur par: Admin aur Receptionist hi create/store/edit/update kar sakte hain
        // Lekin destroy (delete) sirf Admin kar sake, iske liye hum method mein bhi check laga sakte hain
    }

    private function userHasRole($roles): bool
    {
        $user = Auth::user();

        if (!$user || !method_exists($user, 'hasRole')) {
            return false;
        }

        return $user->hasRole($roles);
    }

    public function index()
    {
        $user = Auth::user();

        // Agar user Doctor hai, toh sirf uske appointment wale patients show hon
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            $doctor = Doctor::where('email', $user->email)->first();

            if ($doctor) {
                $patientIds = Appointment::where('doctor_id', $doctor->id)
                                        ->pluck('patient_id')
                                        ->unique();

                $patients = Patient::whereIn('id', $patientIds)->latest()->paginate(10);
            } else {
                // Empty pagination instance return karein taake frontend par links() error na de
                $patients = Patient::where('id', 0)->paginate(10);
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
        // Check karein ke sirf Admin ya Receptionist hi create page access kar sakein
        if (!$this->userHasRole(['admin', 'receptionist'])) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Patients/Create');
    }

    public function store(Request $request)
    {
        if (!$this->userHasRole(['admin', 'receptionist'])) {
            abort(403, 'Unauthorized action.');
        }

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
        if (!$this->userHasRole(['admin', 'receptionist'])) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Patients/Edit', [
            'patient' => $patient
        ]);
    }

    public function update(Request $request, Patient $patient)
    {
        if (!$this->userHasRole(['admin', 'receptionist'])) {
            abort(403, 'Unauthorized action.');
        }

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
        // 🛑 IMPORTANT: Sirf Admin hi patient delete kar sakta hai, Receptionist nahi!
        if (!$this->userHasRole('admin')) {
            abort(403, 'Unauthorized action. Only admins can delete patients.');
        }

        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }
}