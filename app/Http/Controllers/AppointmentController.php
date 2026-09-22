<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\DoctorAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    private function getAuthenticatedDoctor()
    {
        $user = Auth::user();

        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('doctor')) {
            return null;
        }

        return Doctor::where('email', $user->email)->first();
    }

    public function index(Request $request)
    {
        $doctor = $this->getAuthenticatedDoctor();

        $appointments = Appointment::with(['patient', 'doctor.department'])
            ->when($doctor, function ($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('patient', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest('appointment_date')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    public function create()
    {
        $doctor = $this->getAuthenticatedDoctor();

        if ($doctor) {
            $doctors = Doctor::with(['department', 'availabilities'])
                ->where('id', $doctor->id)
                ->get();
        } else {
            $doctors = Doctor::with(['department', 'availabilities'])->get();
        }

        return Inertia::render('Appointments/Create', [
            'patients' => Patient::all(),
            'departments' => Department::where('status', true)->get(),
            'doctors' => $doctors,
        ]);
    }

    public function store(Request $request)
    {
        $doctor = $this->getAuthenticatedDoctor();
        $doctorId = $doctor ? $doctor->id : $request->doctor_id;

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'department_id' => 'required|exists:departments,id',
            'appointment_date' => 'required|date',
            'status' => 'required|string',
            'doctor_id' => $doctor ? 'nullable' : 'required|exists:doctors,id',
        ]);

        $selectedDoctor = Doctor::findOrFail($doctorId);

        if ((int) $selectedDoctor->department_id !== (int) $request->department_id) {
            return back()->withInput()->withErrors([
                'doctor_id' => 'Selected doctor does not belong to this department.'
            ]);
        }

        $appointmentStart = Carbon::parse($request->appointment_date);
        $appointmentEnd = $appointmentStart->copy()->addMinutes(30);

        if ($appointmentStart->isPast()) {
            return back()->withInput()->withErrors([
                'appointment_date' => 'You cannot book a past appointment time.'
            ]);
        }

        $dayOfWeek = $appointmentStart->format('l');

        $availability = DoctorAvailability::where('doctor_id', $doctorId)
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

        if (
            $appointmentStart < $availabilityStart ||
            $appointmentEnd > $availabilityEnd
        ) {
            return back()->withInput()->withErrors([
                'appointment_date' => 'Selected time is outside the doctor\'s working hours.'
            ]);
        }

        $minutesFromStart = $availabilityStart->diffInMinutes($appointmentStart);

        if ($minutesFromStart % 30 !== 0) {
            return back()->withInput()->withErrors([
                'appointment_date' => 'Please select a valid 30-minute time slot.'
            ]);
        }

        $overlap = Appointment::where('doctor_id', $doctorId)
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

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $doctorId,
            'appointment_date' => $request->appointment_date,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment created successfully.');
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load('doctor');

        $departments = Department::where('status', true)->get();

        $doctors = Doctor::with(['department', 'availabilities'])->get();

        return Inertia::render('Appointments/Edit', [
            'appointment' => $appointment,
            'patients' => Patient::all(),
            'departments' => $departments,
            'doctors' => $doctors,
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'department_id' => 'required|exists:departments,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'status' => 'required|string',
        ]);

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
                'appointment_date' => 'You cannot select a past appointment time.'
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

        if (
            $appointmentStart < $availabilityStart ||
            $appointmentEnd > $availabilityEnd
        ) {
            return back()->withInput()->withErrors([
                'appointment_date' => 'Selected time is outside the doctor\'s working hours.'
            ]);
        }

        $minutesFromStart = $availabilityStart->diffInMinutes($appointmentStart);

        if ($minutesFromStart % 30 !== 0) {
            return back()->withInput()->withErrors([
                'appointment_date' => 'Please select a valid 30-minute time slot.'
            ]);
        }

        $overlap = Appointment::where('doctor_id', $doctor->id)
            ->where('status', '!=', 'Cancelled')
            ->where('id', '!=', $appointment->id)
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

        $appointment->update([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    public function calendar()
    {
        $doctor = $this->getAuthenticatedDoctor();

        $appointments = Appointment::with(['patient', 'doctor'])
            ->when($doctor, function ($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            })
            ->get();

        return Inertia::render('Appointments/Calendar', [
            'appointments' => $appointments,
        ]);
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'appointment_id' => 'nullable|exists:appointments,id',
        ]);

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->format('l');

        $availability = DoctorAvailability::where('doctor_id', $request->doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$availability) {
            return response()->json([
                'slots' => []
            ]);
        }

        $start = Carbon::parse(
            $request->date . ' ' . $availability->start_time
        );

        $end = Carbon::parse(
            $request->date . ' ' . $availability->end_time
        );

        $appointments = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('appointment_date', $request->date)
            ->where('status', '!=', 'Cancelled')
            ->when($request->appointment_id, function ($query) use ($request) {
                $query->where('id', '!=', $request->appointment_id);
            })
            ->pluck('appointment_date');

        $now = Carbon::now();
        $slots = [];

        while ($start->copy()->addMinutes(30) <= $end) {
            $slotStart = $start->copy();
            $slotEnd = $slotStart->copy()->addMinutes(30);

            if ($date->isToday() && $slotStart <= $now) {
                $start->addMinutes(30);
                continue;
            }

            $booked = $appointments->contains(function ($appointment) use ($slotStart, $slotEnd) {
                $appointmentStart = Carbon::parse($appointment);
                $appointmentEnd = $appointmentStart->copy()->addMinutes(30);

                return $slotStart < $appointmentEnd &&
                    $slotEnd > $appointmentStart;
            });

            if (!$booked) {
                $slots[] = $slotStart->format('H:i');
            }

            $start->addMinutes(30);
        }

        return response()->json([
            'slots' => $slots
        ]);
    }
}
