<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
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

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $applySearch = function ($query) use ($search) {
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }
        };

        // Agar user Doctor hai, toh sirf uske appointment wale patients show hon
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            $doctor = Doctor::where('email', $user->email)->first();

            if ($doctor) {
                $patientIds = Appointment::where('doctor_id', $doctor->id)
                    ->where('status', '!=', 'Cancelled')
                    ->pluck('patient_id')
                    ->unique();

                $patients = Patient::whereIn('id', $patientIds)
                    ->when($search, $applySearch)
                    ->latest()
                    ->paginate(10)
                    ->withQueryString();
            } else {
                // Empty pagination instance return karein taake frontend par links() error na de
                $patients = Patient::where('id', 0)->paginate(10)->withQueryString();
            }
        } else {
            // Admin ya Receptionist ke liye sab patients show hon
            $patients = Patient::when($search, $applySearch)
                ->latest()
                ->paginate(10)
                ->withQueryString();
        }

        return Inertia::render('Patients/Index', [
            'patients' => $patients,
            'filters' => ['search' => $search],
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

    public function store(StorePatientRequest $request)
    {
        if (!$this->userHasRole(['admin', 'receptionist'])) {
            abort(403, 'Unauthorized action.');
        }

        Patient::create($request->validated());

        return redirect()->route('patients.index')->with('success', 'Patient added successfully.');
    }

    public function show(Patient $patient)
    {
        $user = Auth::user();
        $isDoctor = $this->userHasRole('doctor');

        // Doctor sirf apne patients ki details dekh sakta hai
        if ($isDoctor) {
            $doctor = Doctor::where('email', $user->email)->first();

            $hasAccess = $doctor && Appointment::where('doctor_id', $doctor->id)
                ->where('patient_id', $patient->id)
                ->where('status', '!=', 'Cancelled')
                ->exists();

            if (!$hasAccess) {
                abort(403, 'You can only view your own patients.');
            }
        }

        $patient->load([
            'appointments' => fn($q) => $q->with('doctor.department')->latest('appointment_date'),
            'prescriptions' => fn($q) => $q->with(['doctor', 'items'])->latest('prescribed_date'),
        ]);

        // Billing information doctor ko nahi dikhayi jaati
        if (!$isDoctor) {
            $patient->load(['bills' => fn($q) => $q->with('doctor')->latest('bill_date')]);
        }

        return Inertia::render('Patients/Show', [
            'patient' => $patient,
            'canManagePatients' => $this->userHasRole(['admin', 'receptionist']),
            'canViewBills' => !$isDoctor,
        ]);
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

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        if (!$this->userHasRole(['admin', 'receptionist'])) {
            abort(403, 'Unauthorized action.');
        }

        $patient->update($request->validated());

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
