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

        return view('dashboard', compact(
            'patientsCount',
            'doctorsCount',
            'appointmentsCount',
            'pendingBillsCount',
            'paidBillsCount'
        ));
    }
}
