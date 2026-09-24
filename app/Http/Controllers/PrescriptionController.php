<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PrescriptionController extends Controller
{
    // Logged-in doctor ka Doctor record return karta hai
    private function currentDoctor()
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            return Doctor::where('email', $user->email)->first();
        }

        return null;
    }

    public function index()
    {
        $doctor = $this->currentDoctor();

        if ($doctor) {
            // Doctor ko sirf apni prescriptions dikhein
            $prescriptions = Prescription::where('doctor_id', $doctor->id)
                ->with('patient', 'doctor')
                ->latest('prescribed_date')
                ->paginate(10);
        } else {
            // Admin / Receptionist ko sab prescriptions dikhein
            $prescriptions = Prescription::with('patient', 'doctor')
                ->latest('prescribed_date')
                ->paginate(10);
        }

        return Inertia::render('Prescriptions/Index', [
            'prescriptions' => $prescriptions,
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        // Sirf doctor prescription create kar sakta hai
        if (!(method_exists($user, 'hasRole') && $user->hasRole('doctor'))) {
            abort(403, 'Only doctors can create prescriptions.');
        }

        $doctor = $this->currentDoctor();

        if (!$doctor) {
            abort(403, 'Doctor profile not found.');
        }

        // Sirf current doctor ke patients
        $patientIds = Appointment::where('doctor_id', $doctor->id)
            ->pluck('patient_id')
            ->unique();

        $patients = Patient::whereIn('id', $patientIds)->get();

        // Doctor khud selected hoga
        $doctors = collect([$doctor]);

        // Appointment se "Write Prescription" click hua ho
        $appointment = null;

        if ($request->filled('appointment_id')) {
            $appointment = Appointment::with('patient', 'doctor')
                ->where('doctor_id', $doctor->id)
                ->find($request->appointment_id);
        }

        return Inertia::render('Prescriptions/Create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'selectedAppointment' => $appointment,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Sirf doctor prescription create kar sakta hai
        if (!(method_exists($user, 'hasRole') && $user->hasRole('doctor'))) {
            abort(403, 'Only doctors can create prescriptions.');
        }

        $doctor = $this->currentDoctor();

        if (!$doctor) {
            abort(403, 'Doctor profile not found.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'prescribed_date' => 'required|date',
            'diagnosis' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_name' => 'required|string',
            'items.*.dosage' => 'nullable|string',
            'items.*.frequency' => 'nullable|string',
            'items.*.duration' => 'nullable|string',
            'items.*.instructions' => 'nullable|string',
        ]);

        // Check: patient current doctor ka patient hai ya nahi
        $patientAllowed = Appointment::where('doctor_id', $doctor->id)
            ->where('patient_id', $validated['patient_id'])
            ->exists();

        if (!$patientAllowed) {
            abort(403, 'You can only create prescriptions for your own patients.');
        }

        // Agar appointment select ki gayi hai to woh bhi current doctor ki honi chahiye
        if (!empty($validated['appointment_id'])) {
            $appointmentAllowed = Appointment::where('id', $validated['appointment_id'])
                ->where('doctor_id', $doctor->id)
                ->where('patient_id', $validated['patient_id'])
                ->exists();

            if (!$appointmentAllowed) {
                abort(403, 'Invalid appointment selected.');
            }
        }

        $prescription = Prescription::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $doctor->id,
            'appointment_id' => $validated['appointment_id'] ?? null,
            'prescribed_date' => $validated['prescribed_date'],
            'diagnosis' => $validated['diagnosis'],
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription created successfully.');
    }

    public function show(Prescription $prescription)
    {
        $this->authorizeAccess($prescription, allowReceptionist: true);

        $prescription->load('patient', 'doctor', 'items');

        return Inertia::render('Prescriptions/Show', [
            'prescription' => $prescription,
        ]);
    }

    public function edit(Prescription $prescription)
    {
        $this->authorizeAccess($prescription);

        $prescription->load('items');

        $doctor = $this->currentDoctor();

        $patients = Patient::all();
        $doctors = $doctor ? collect([$doctor]) : Doctor::all();

        return Inertia::render('Prescriptions/Edit', [
            'prescription' => $prescription,
            'patients' => $patients,
            'doctors' => $doctors,
        ]);
    }

    public function update(Request $request, Prescription $prescription)
    {
        $this->authorizeAccess($prescription);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'prescribed_date' => 'required|date',
            'diagnosis' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_name' => 'required|string',
            'items.*.dosage' => 'nullable|string',
            'items.*.frequency' => 'nullable|string',
            'items.*.duration' => 'nullable|string',
            'items.*.instructions' => 'nullable|string',
        ]);

        // Doctor ke liye check: selected patient uska patient hona chahiye
        $doctor = $this->currentDoctor();

        if ($doctor) {
            $patientAllowed = Appointment::where('doctor_id', $doctor->id)
                ->where('patient_id', $validated['patient_id'])
                ->exists();

            if (!$patientAllowed) {
                abort(403, 'You can only use your own patients.');
            }
        }

        $prescription->update([
            'patient_id' => $validated['patient_id'],
            'prescribed_date' => $validated['prescribed_date'],
            'diagnosis' => $validated['diagnosis'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Purane items delete karke naye items create
        $prescription->items()->delete();

        foreach ($validated['items'] as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription updated successfully.');
    }

    public function destroy(Prescription $prescription)
    {
        // Route middleware bhi admin-only hai,
        // lekin controller level par bhi authorization check
        $this->authorizeAccess($prescription);

        $user = Auth::user();

        if (!(method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            abort(403, 'Only admins can delete prescriptions.');
        }

        $prescription->delete();

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription deleted successfully.');
    }

    // Admin: all prescriptions
    // Doctor: sirf apni prescriptions
    // Receptionist: sirf show/view jab allowReceptionist=true ho
    private function authorizeAccess(Prescription $prescription, bool $allowReceptionist = false)
    {
        $user = Auth::user();

        // Admin ko complete access
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return;
        }

        // Doctor ko sirf apni prescriptions ka access
        $doctor = $this->currentDoctor();

        if ($doctor && $prescription->doctor_id === $doctor->id) {
            return;
        }

        // Receptionist sirf view kar sakta hai
        if ($allowReceptionist && method_exists($user, 'hasRole') && $user->hasRole('receptionist')) {
            return;
        }

        abort(403, 'Unauthorized action.');
    }
}
