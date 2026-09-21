<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bill;
use Carbon\Carbon;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    // Helper: Check if current user is a doctor and get their record
    private function getAuthenticatedDoctor()
    {
        $user = Auth::user();
        if (method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            return Doctor::where('email', $user->email)->first();
        }
        return null;
    }

    public function index(Request $request)
    {
        $doctor = $this->getAuthenticatedDoctor();
        $search = $request->input('search');
        $status = $request->input('status');

        $appointments = Appointment::with(['patient', 'doctor'])
            ->when($doctor, fn($query) => $query->where('doctor_id', $doctor->id))
            ->when($search, function ($query) use ($search) {
                $query->whereHas('patient', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->when($status, fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'filters' => ['search' => $search, 'status' => $status],
        ]);
    }

    public function create()
    {
        $patients = Patient::all();
        $doctor = $this->getAuthenticatedDoctor();

        if ($doctor) {
            $doctors = collect([$doctor]);
        } else {
            $currentDay = Carbon::now()->format('l');
            $doctorIds = DoctorAvailability::where('day_of_week', $currentDay)
                ->where('is_active', true)
                ->pluck('doctor_id');

            $doctors = Doctor::whereIn('id', $doctorIds)->get();
        }

        return Inertia::render('Appointments/Create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $doctor = $this->getAuthenticatedDoctor();
        $doctorId = $doctor ? $doctor->id : $request->doctor_id;

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date',
            'status' => 'required|string',
            'doctor_id' => $doctor ? 'nullable' : 'required|exists:doctors,id',
        ]);

        $appointmentDate = Carbon::parse($request->appointment_date);
        $dayOfWeek = $appointmentDate->format('l');
        // FIX: sirf din nahi, exact TIME bhi check karein — pehle sirf day_of_week
        // match hota tha, jisse doctor ki working-hours se bahar bhi appointment
        // book ho jaata tha.
        $appointmentTime = $appointmentDate->format('H:i:s');

        $isAvailable = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $appointmentTime)
            ->where('end_time', '>=', $appointmentTime)
            ->exists();

        if (!$isAvailable) {
            return back()->withInput()->with('error', "Doctor is not available at this time on {$dayOfWeek}. Please choose a time within their working hours.");
        }

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $doctorId,
            'appointment_date' => $request->appointment_date,
            'status' => $request->status,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully.');
    }

    public function edit(Appointment $appointment)
    {
        return Inertia::render('Appointments/Edit', [
            'appointment' => $appointment,
            'patients' => Patient::all(),
            'doctors' => Doctor::all(),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'status' => 'required|string',
        ]);

        // FIX: $request->all() ki jagah $validated use karein — sirf allowed
        // fields update hon, koi extra/unexpected field mass-assign na ho.
        $appointment->update($validated);

        if ($validated['status'] === 'Cancelled') {
            Bill::where('appointment_id', $appointment->id)->delete();
        }

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully.');
    }

    public function calendar()
    {
        $doctor = $this->getAuthenticatedDoctor();

        $appointments = Appointment::with(['patient', 'doctor'])
            ->when($doctor, fn($query) => $query->where('doctor_id', $doctor->id))
            ->get();

        $statusColors = [
            'Scheduled' => '#f59e0b',
            'Completed' => '#10b981',
            'Cancelled' => '#f43f5e',
        ];

        $events = $appointments->map(fn($appointment) => [
            'id' => $appointment->id,
            'title' => ($appointment->patient?->name ?? 'Patient') . ' — Dr. ' . ($appointment->doctor?->name ?? 'N/A'),
            'start' => Carbon::parse($appointment->appointment_date)->toIso8601String(),
            'color' => $statusColors[$appointment->status] ?? '#6b7280',
            'extendedProps' => [
                'status' => $appointment->status,
                'patient' => $appointment->patient?->name,
                'doctor' => $appointment->doctor?->name,
            ],
        ]);

        return Inertia::render('Appointments/Calendar', compact('events'));
    }
}
