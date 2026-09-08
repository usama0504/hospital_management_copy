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
        $bills = Bill::query();

        if ($from) {
            $appointments = $appointments->whereDate('appointment_date', '>=', $from);
            $bills = $bills->whereDate('bill_date', '>=', $from);
        }
        if ($to) {
            $appointments = $appointments->whereDate('appointment_date', '<=', $to);
            $bills = $bills->whereDate('bill_date', '<=', $to);
        }

        $appointmentsCount = $appointments->count();
        $pendingBillsCount = $bills->whereIn('status', ['Pending', 'Unpaid'])->count();
        $paidBillsCount = $bills->where('status', 'Paid')->count();

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
