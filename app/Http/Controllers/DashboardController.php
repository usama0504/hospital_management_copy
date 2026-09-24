<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Department;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $patientsCount = Patient::count();
        $doctorsCount = Doctor::count();

        $appointmentsCount = Appointment::where('status', '!=', 'Cancelled')->count();

        $operationsCount = 0;

        $totalEarnings = Bill::where('status', 'Paid')
            ->where(function ($query) {
                $query->whereNull('appointment_id')
                    ->orWhereHas('appointment', function ($query) {
                        $query->where('status', '!=', 'Cancelled');
                    });
            })->sum('amount');

        $pendingBillsCount = Bill::whereIn('status', ['Pending', 'Unpaid'])
            ->where(function ($query) {
                $query->whereNull('appointment_id')
                    ->orWhereHas('appointment', function ($query) {
                        $query->where('status', '!=', 'Cancelled');
                    });
            })->count();

        $paidBillsCount = Bill::where('status', 'Paid')
            ->where(function ($query) {
                $query->whereNull('appointment_id')
                    ->orWhereHas('appointment', function ($query) {
                        $query->where('status', '!=', 'Cancelled');
                    });
            })->count();

        $recentBills = Bill::with('patient')
            ->where(function ($query) {
                $query->whereNull('appointment_id')
                    ->orWhereHas('appointment', function ($query) {
                        $query->where('status', '!=', 'Cancelled');
                    });
            })->latest()->take(5)->get();

        $recentAppointments = Appointment::where('status', '!=', 'Cancelled')
            ->with(['patient', 'doctor'])->latest()->take(5)->get();

        $recentPatients = Patient::latest()->take(5)->get();

        // FIX: Pehle ye "popular" doctors nahi the, bas pehle 4 doctors (Doctor::take(4))
        // dikhaye ja rahe the. Ab genuinely appointment count ke hisaab se top doctors nikaalte hain.
        $popularDoctors = Doctor::withCount('appointments')
            ->with('department')
            ->orderByDesc('appointments_count')
            ->take(4)
            ->get();

        $trendLabels = [];
        $appointmentsTrend = [];
        $revenueTrend = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);

            $trendLabels[] = $day->format('D');

            $appointmentsTrend[] = Appointment::whereDate(
                'appointment_date',
                $day->toDateString()
            )->where('status', '!=', 'Cancelled')->count();

            $revenueTrend[] = (float) Bill::whereDate(
                'bill_date',
                $day->toDateString()
            )
                ->where('status', 'Paid')
                ->where(function ($query) {
                    $query->whereNull('appointment_id')
                        ->orWhereHas('appointment', function ($query) {
                            $query->where('status', '!=', 'Cancelled');
                        });
                })->sum('amount');
        }

        $statusBreakdown = Appointment::selectRaw(
            'status, COUNT(*) as total'
        )->groupBy('status')->pluck('total', 'status');

        $doctorLoad = Appointment::where('status', '!=', 'Cancelled')
            ->selectRaw('doctor_id, COUNT(*) as total')->groupBy('doctor_id')->orderByDesc('total')
            ->take(5)->with('doctor')->get()->map(fn($row) => [
                'doctor' => $row->doctor?->name ?? 'N/A',
                'total' => $row->total,
            ]);

        $departmentStatistics = Department::with([
            'doctors:id,department_id,name'
        ])
            ->withCount('doctors')
            ->get()
            ->map(function ($department) {
                $doctorIds = $department->doctors->pluck('id');

                // Har doctor ke apne appointment counts nikal lein (department ke
                // andar doctor-wise performance chart banane ke liye)
                $appointmentCountsByDoctor = Appointment::whereIn('doctor_id', $doctorIds)
                    ->where('status', '!=', 'Cancelled')
                    ->selectRaw('doctor_id, COUNT(*) as total')
                    ->groupBy('doctor_id')
                    ->pluck('total', 'doctor_id');

                $doctorsBreakdown = $department->doctors->map(function ($doctor) use ($appointmentCountsByDoctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->name,
                        'appointments_count' => $appointmentCountsByDoctor[$doctor->id] ?? 0,
                    ];
                })->sortByDesc('appointments_count')->values();

                $appointmentsCount = $doctorsBreakdown->sum('appointments_count');

                return [
                    'name' => $department->name,
                    'doctors_count' => $department->doctors_count,
                    'appointments_count' => $appointmentsCount,
                    'doctors' => $doctorsBreakdown,
                ];
            })
            ->sortByDesc('appointments_count')
            ->values();

        return Inertia::render('Dashboard', [
            'patientsCount' => $patientsCount,
            'doctorsCount' => $doctorsCount,
            'appointmentsCount' => $appointmentsCount,
            'operationsCount' => $operationsCount,

            'totalEarnings' => $totalEarnings,
            'pendingBillsCount' => $pendingBillsCount,
            'paidBillsCount' => $paidBillsCount,

            'recentBills' => $recentBills,
            'recentAppointments' => $recentAppointments,
            'recentPatients' => $recentPatients,
            'popularDoctors' => $popularDoctors,

            'trendLabels' => $trendLabels,
            'appointmentsTrend' => $appointmentsTrend,
            'revenueTrend' => $revenueTrend,

            'statusBreakdown' => $statusBreakdown,
            'doctorLoad' => $doctorLoad,
            'departmentStatistics' => $departmentStatistics,
        ]);
    }
}
