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
    // Logged-in user doctor hai to uska Doctor record wapis karta hai, warna null
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
            // Doctor ko sirf apni likhi hui prescriptions dikhein
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

        // Sirf doctor aur admin naya prescription likh sakte hain
        if (!(method_exists($user, 'hasRole') && ($user->hasRole('doctor') || $user->hasRole('admin')))) {
            abort(403, 'Unauthorized action.');
        }

        $doctor = $this->currentDoctor();

        $patients = Patient::all();
        $doctors = $doctor ? collect([$doctor]) : Doctor::all();

        // Agar kisi appointment se "Write Prescription" click hua ho, to us appointment ko pre-fill karein
        $appointment = null;
        if ($request->filled('appointment_id')) {
            $appointment = Appointment::with('patient', 'doctor')->find($request->appointment_id);
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

        if (!(method_exists($user, 'hasRole') && ($user->hasRole('doctor') || $user->hasRole('admin')))) {
            abort(403, 'Unauthorized action.');
        }

        $doctor = $this->currentDoctor();

        $rules = [
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
        ];

        // Admin ke liye doctor select karna zaroori hai, doctor khud login hai to apna id use hoga
        if (!$doctor) {
            $rules['doctor_id'] = 'required|exists:doctors,id';
        }

        $validated = $request->validate($rules);

        $prescription = Prescription::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $doctor ? $doctor->id : $validated['doctor_id'],
            'appointment_id' => $validated['appointment_id'] ?? null,
            'prescribed_date' => $validated['prescribed_date'],
            'diagnosis' => $validated['diagnosis'],
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('prescriptions.index')->with('success', 'Prescription created successfully.');
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
            'items.*.id' => 'nullable|exists:prescription_items,id',
            'items.*.medicine_name' => 'required|string',
            'items.*.dosage' => 'nullable|string',
            'items.*.frequency' => 'nullable|string',
            'items.*.duration' => 'nullable|string',
            'items.*.instructions' => 'nullable|string',
        ]);

        $prescription->update([
            'patient_id' => $validated['patient_id'],
            'prescribed_date' => $validated['prescribed_date'],
            'diagnosis' => $validated['diagnosis'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Simple replace strategy: purane items hata kar naye bana dete hain
        $prescription->items()->delete();
        foreach ($validated['items'] as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('prescriptions.index')->with('success', 'Prescription updated successfully.');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();

        return redirect()->route('prescriptions.index')->with('success', 'Prescription deleted successfully.');
    }

    // Doctor sirf apni prescriptions edit/view kar sake, admin har cheez.
    // Receptionist ko sirf "show" (read-only receipt) pe allowReceptionist=true ke sath access milta hai.
    private function authorizeAccess(Prescription $prescription, bool $allowReceptionist = false)
    {
        $user = Auth::user();

        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return;
        }

        $doctor = $this->currentDoctor();

        if ($doctor && $prescription->doctor_id === $doctor->id) {
            return;
        }

        if ($allowReceptionist && method_exists($user, 'hasRole') && $user->hasRole('receptionist')) {
            return;
        }

        abort(403, 'Unauthorized action.');
    }
}
