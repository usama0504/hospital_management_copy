<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Bill;
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

        // Aaj ka din aur current time nikal lein
        $currentDay = Carbon::now()->format('l'); // Maslan: Thursday
        $currentTime = Carbon::now()->format('H:i:s'); // Maslan: 11:28:42

        // Sirf wahi doctors fetch hon:
        // 1. Jinki availability aaj ke din ho
        // 2. Jinka end_time abhi ke current time se aage ka ho (yani waqt khatam na hua ho)
        $doctors = Doctor::whereHas('availabilities', function ($query) use ($currentDay, $currentTime) {
            $query->where('day_of_week', $currentDay)
                ->where('end_time', '>', $currentTime); // Agar 11:00 baje the toh end_time 11:00 se zyada hona chahiye
        })->get();

        return Inertia::render('PatientVisit/CreateVisitWizard', [
            'doctors' => $doctors
        ]);
    }

    public function store(Request $request)
    {
        // Store par check
        if ($this->checkIfDoctor()) {
            abort(403, 'Doctors are not authorized to perform this action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',

            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date', // Yeh datetime-local format hai (e.g. 2026-09-18 14:30:00)
            'status' => 'required|string',

            'amount' => 'required|numeric|min:0',
            'bill_status' => 'required|string',
            'bill_date' => 'required|date',
        ]);

        // --- ACCURATE TIME & DAY CHECK FOR DATETIME-LOCAL ---
        $appointmentDateTime = Carbon::parse($validated['appointment_date']);
        $dayOfWeek = $appointmentDateTime->format('l'); // Maslan: Thursday, Friday
        $appointmentTime = $appointmentDateTime->format('H:i:s'); // Maslan: 14:30:00

        // Check karein ke doctor is din aur is exact time par available hai ya nahi
        $isSlotAvailable = \App\Models\DoctorAvailability::where('doctor_id', $validated['doctor_id'])
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $appointmentTime)
            ->where('end_time', '>=', $appointmentTime)
            ->exists();

        if (!$isSlotAvailable) {
            return back()->withErrors([
                'appointment_date' => 'Doctor is not available at this time. Please select a time within their working hours.'
            ])->withInput();
        }
        // ----------------------------------------------------

        $billId = null;

        DB::transaction(function () use ($validated, &$billId) {
            $patient = Patient::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'dob' => $validated['dob'],
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
