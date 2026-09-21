<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Bill;
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

        // Real Billing Calculations from Database
        $totalEarnings = Bill::sum('amount') ?? 0;
        $pendingBillsCount = Bill::whereIn('status', ['Pending', 'Unpaid'])->count();
        $paidBillsCount = Bill::where('status', 'Paid')->count();

        // Agar recent bills ki list bhi dashboard par dikhani ho
        $recentBills = Bill::with('patient')->latest()->take(5)->get();

        $recentAppointments = Appointment::where('status', '!=', 'Cancelled')->with(['patient', 'doctor'])
            ->latest()
            ->take(5)
            ->get();

        $recentPatients = Patient::latest()->take(5)->get();
        $popularDoctors = Doctor::take(4)->get();

        // Pichle 7 dinon ka appointments aur revenue trend (Line Chart ke liye)
        $trendLabels = [];
        $appointmentsTrend = [];
        $revenueTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i); // now() ki jagah today() use karein
            $trendLabels[] = $day->format('D');
            $appointmentsTrend[] = Appointment::whereDate('appointment_date', $day->toDateString())
                ->where('status', '!=', 'Cancelled')
                ->count();
            $revenueTrend[] = (float) Bill::whereDate('bill_date', $day->toDateString())->sum('amount');
        }

        // Appointment status breakdown (Doughnut Chart ke liye)
        $statusBreakdown = Appointment::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Doctor-wise appointment load, top 5 (Bar Chart ke liye)
        $doctorLoad = Appointment::where('status', '!=', 'Cancelled')
            ->selectRaw('doctor_id, COUNT(*) as total')
            ->groupBy('doctor_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('doctor')
            ->get()
            ->map(fn($row) => [
                'doctor' => $row->doctor?->name ?? 'N/A',
                'total' => $row->total,
            ]);

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
        ]);
    }
}
