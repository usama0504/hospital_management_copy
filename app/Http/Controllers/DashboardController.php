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
        
        $operationsCount = 0; 

        // Real Billing Calculations from Database
        $totalEarnings = Bill::sum('amount') ?? 0;
        $pendingBillsCount = Bill::whereIn('status', ['Pending', 'Unpaid'])->count();
        $paidBillsCount = Bill::where('status', 'Paid')->count();
        
        // Agar recent bills ki list bhi dashboard par dikhani ho
        $recentBills = Bill::with('patient')->latest()->take(5)->get();

        $recentAppointments = Appointment::with(['patient', 'doctor'])
            ->latest()
            ->take(5)
            ->get();

        $recentPatients = Patient::latest()->take(5)->get();
        $popularDoctors = Doctor::take(4)->get();

        return view('dashboard', compact(
            'patientsCount',
            'doctorsCount',
            'appointmentsCount',
            'operationsCount',
            'totalEarnings',
            'pendingBillsCount',
            'paidBillsCount',
            'recentBills',
            'recentAppointments',
            'recentPatients',
            'popularDoctors'
        ));
    }
}