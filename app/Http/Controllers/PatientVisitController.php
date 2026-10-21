<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\DoctorAvailability;
use App\Services\AppointmentSlotService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PatientVisitController extends Controller
{
    // Helper function to check if the authenticated user is a doctor
    private function checkIfDoctor()
    {
        $user = Auth::user();

        // Check if user has 'doctor' role using Spatie or direct property
        $hasDoctorRole = method_exists($user, 'hasRole') && $user->hasRole('doctor');
        $isDoctorProp = isset($user->role) && $user->role === 'doctor';

        return $hasDoctorRole || $isDoctorProp;
    }

    // Wizard Form Show karne ke liye
    public function create()
    {
        // Agar user doctor hai toh usay access deny kar dein
        if ($this->checkIfDoctor()) {
            abort(403, 'Doctors are not authorized to create patient visits.');
        }

        // AppointmentController@create jaisa hi data: department -> date -> doctor -> slots
        return Inertia::render('PatientVisit/CreateVisitWizard', [
            'departments' => Department::where('status', true)->get(),
            'doctors' => Doctor::with(['department', 'availabilities'])->get(),
        ]);
    }

    public function store(Request $request, AppointmentSlotService $slots)
    {
        // Store par check
        if ($this->checkIfDoctor()) {
            abort(403, 'Doctors are not authorized to perform this action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',

            'department_id' => 'required|exists:departments,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'status' => 'required|string',

            'amount' => 'required|numeric|min:0',
            'bill_status' => 'required|string',
            'bill_date' => 'required|date',
        ]);

        // --- Appointment jaisi hi validation (AppointmentController@store) ---
        $doctor = Doctor::findOrFail($validated['doctor_id']);

        if ((int) $doctor->department_id !== (int) $validated['department_id']) {
            return back()->withInput()->withErrors([
                'doctor_id' => 'Selected doctor does not belong to this department.'
            ]);
        }

        $appointmentStart = Carbon::parse($validated['appointment_date']);

        $billId = null;

        DB::transaction(function () use ($validated, &$billId, $slots, $appointmentStart) {
            // Lock + slot check pehle, phir patient/appointment/bill banao.
            $slots->lockDoctor((int) $validated['doctor_id']);
            $slots->assertAvailable((int) $validated['doctor_id'], $appointmentStart);

            $patient = Patient::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'address' => $validated['address'] ?? null,
                'dob' => $validated['dob'] ?? null,
            ]);

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $validated['doctor_id'],
                'appointment_date' => $validated['appointment_date'],
                'status' => $validated['status'],
            ]);

            $bill = Bill::create([
                'patient_id' => $patient->id,
                'appointment_id' => $appointment->id,
                'doctor_id' => $validated['doctor_id'],
                'amount' => $validated['amount'],
                'status' => $validated['bill_status'],
                'bill_date' => $validated['bill_date'],
            ]);

            $billId = $bill->id;
        });

        return redirect()->route('bills.receipt', $billId)->with('success', 'Patient visit, appointment and bill registered successfully.');
    }
}
