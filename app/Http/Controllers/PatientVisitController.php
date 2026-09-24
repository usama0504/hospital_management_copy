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

    public function store(Request $request)
    {
        // Store par check
        if ($this->checkIfDoctor()) {
            abort(403, 'Doctors are not authorized to perform this action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
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
        $appointmentEnd = $appointmentStart->copy()->addMinutes(30);

        if ($appointmentStart->isPast()) {
            return back()->withInput()->withErrors([
                'appointment_date' => 'You cannot book a past appointment time.'
            ]);
        }

        $dayOfWeek = $appointmentStart->format('l');

        $availability = DoctorAvailability::where('doctor_id', $doctor->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$availability) {
            return back()->withInput()->withErrors([
                'appointment_date' => 'Doctor is not available on this day.'
            ]);
        }

        $availabilityStart = Carbon::parse(
            $appointmentStart->format('Y-m-d') . ' ' . $availability->start_time
        );

        $availabilityEnd = Carbon::parse(
            $appointmentStart->format('Y-m-d') . ' ' . $availability->end_time
        );

        if ($appointmentStart < $availabilityStart || $appointmentEnd > $availabilityEnd) {
            return back()->withInput()->withErrors([
                'appointment_date' => 'Selected time is outside the doctor\'s working hours.'
            ]);
        }

        if ($availabilityStart->diffInMinutes($appointmentStart) % 30 !== 0) {
            return back()->withInput()->withErrors([
                'appointment_date' => 'Please select a valid 30-minute time slot.'
            ]);
        }

        $overlap = Appointment::where('doctor_id', $doctor->id)
            ->where('status', '!=', 'Cancelled')
            ->get()
            ->contains(function ($existing) use ($appointmentStart, $appointmentEnd) {
                $existingStart = Carbon::parse($existing->appointment_date);
                $existingEnd = $existingStart->copy()->addMinutes(30);

                return $appointmentStart < $existingEnd &&
                    $appointmentEnd > $existingStart;
            });

        if ($overlap) {
            return back()->withInput()->withErrors([
                'appointment_date' => 'This doctor already has an appointment during this time.'
            ]);
        }
        // ----------------------------------------------------

        $billId = null;

        DB::transaction(function () use ($validated, &$billId) {
            $patient = Patient::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
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
