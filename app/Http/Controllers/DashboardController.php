<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Bill;

class DashboardController extends Controller
{
    public function index()
    {
        $patientsCount = Patient::count();
        $doctorsCount = Doctor::count();
        $appointmentsCount = Appointment::count();
        $pendingBillsCount = Bill::whereIn('status', ['Pending', 'Unpaid'])->count();
        $paidBillsCount = Bill::where('status', 'Paid')->count();

        $recentAppointments = Appointment::with(['patient', 'doctor'])
            ->latest()
            ->take(5)
            ->get();

        $recentPatients = Patient::latest()->take(5)->get();

        return view('dashboard', compact(
            'patientsCount',
            'doctorsCount',
            'appointmentsCount',
            'pendingBillsCount',
            'paidBillsCount',
            'recentAppointments',
            'recentPatients'
        ));
    }
}