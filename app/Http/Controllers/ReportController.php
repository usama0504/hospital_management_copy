<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Bill;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $patientsCount = Patient::count();
        $doctorsCount = Doctor::count();
        
        $appointments = Appointment::query();
        $billsQuery = Bill::query();

        if ($from) {
            $appointments = $appointments->whereDate('appointment_date', '>=', $from);
            $billsQuery = $billsQuery->whereDate('bill_date', '>=', $from);
        }
        if ($to) {
            $appointments = $appointments->whereDate('appointment_date', '<=', $to);
            $billsQuery = $billsQuery->whereDate('bill_date', '<=', $to);
        }

        $appointmentsCount = $appointments->count();
        
        // Clone queries for separate status counts so they don't override each other
        $pendingBillsCount = (clone $billsQuery)->whereIn('status', ['Pending', 'Unpaid'])->count();
        $paidBillsCount = (clone $billsQuery)->where('status', 'Paid')->count();

        return view('reports.index', compact(
            'patientsCount',
            'doctorsCount',
            'appointmentsCount',
            'pendingBillsCount',
            'paidBillsCount',
            'from',
            'to'
        ));
    }
}