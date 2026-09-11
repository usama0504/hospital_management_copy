<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        
        // Clone queries for separate status counts
        $pendingBillsCount = (clone $billsQuery)->whereIn('status', ['Pending', 'Unpaid'])->count();
        $paidBillsCount = (clone $billsQuery)->where('status', 'Paid')->count();

        return response()->json([
            'status' => 'success',
            'filters' => [
                'from' => $from,
                'to' => $to,
            ],
            'data' => [
                'patients_count' => $patientsCount,
                'doctors_count' => $doctorsCount,
                'appointments_count' => $appointmentsCount,
                'pending_bills_count' => $pendingBillsCount,
                'paid_bills_count' => $paidBillsCount,
            ]
        ]);
    }
}