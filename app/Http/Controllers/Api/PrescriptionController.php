<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    private function currentDoctor()
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            return Doctor::where('email', $user->email)->first();
        }

        return null;
    }

    // 1. Saari prescriptions ki list (ya doctor ke liye sirf uski apni)
    public function index()
    {
        $doctor = $this->currentDoctor();

        if ($doctor) {
            $prescriptions = Prescription::where('doctor_id', $doctor->id)
                ->with('patient', 'doctor', 'items')
                ->latest('prescribed_date')
                ->paginate(10);
        } else {
            $prescriptions = Prescription::with('patient', 'doctor', 'items')
                ->latest('prescribed_date')
                ->paginate(10);
        }

        return response()->json([
            'status' => 'success',
            'data' => $prescriptions,
        ]);
    }

    // 2. Nayi prescription save karne ke liye (medicines ke sath)
    public function store(Request $request)
    {
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

        return response()->json([
            'status' => 'success',
            'message' => 'Prescription created successfully.',
            'data' => $prescription->load('patient', 'doctor', 'items'),
        ], 201);
    }

    // 3. Single prescription (medicines ke sath) dikhane ke liye
    public function show(Prescription $prescription)
    {
        return response()->json([
            'status' => 'success',
            'data' => $prescription->load('patient', 'doctor', 'appointment', 'items'),
        ]);
    }

    // 4. Prescription update karne ke liye
    public function update(Request $request, Prescription $prescription)
    {
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

        $prescription->update([
            'patient_id' => $validated['patient_id'],
            'prescribed_date' => $validated['prescribed_date'],
            'diagnosis' => $validated['diagnosis'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $prescription->items()->delete();
        foreach ($validated['items'] as $item) {
            $prescription->items()->create($item);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Prescription updated successfully.',
            'data' => $prescription->load('patient', 'doctor', 'items'),
        ]);
    }

    // 5. Prescription delete karne ke liye
    public function destroy(Prescription $prescription)
    {
        $prescription->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Prescription deleted successfully.',
        ]);
    }
}
