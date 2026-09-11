<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;

class BillController extends Controller
{
    // Doctor check helper function for API
    private function checkDoctor(Request $request)
    {
        $user = $request->user();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            abort(response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action.'
            ], 403));
        }
    }

    // 1. List of Bills
    public function index(Request $request)
    {
        $this->checkDoctor($request);

        $bills = Bill::with('patient', 'doctor')->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $bills
        ]);
    }

    // 2. Create New Bill
    public function store(Request $request)
    {
        $this->checkDoctor($request);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'bill_date' => 'required|date',
        ]);

        $bill = Bill::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Bill created successfully.',
            'data' => $bill
        ], 201);
    }

    // 3. Show Single Bill Details
    public function show(Request $request, Bill $bill)
    {
        $this->checkDoctor($request);

        $bill->load(['patient', 'doctor']);

        return response()->json([
            'status' => 'success',
            'data' => $bill
        ]);
    }

    // 4. Update Bill
    public function update(Request $request, Bill $bill)
    {
        $this->checkDoctor($request);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'bill_date' => 'required|date',
        ]);

        $bill->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Bill updated successfully.',
            'data' => $bill
        ]);
    }

    // 5. Delete Bill
    public function destroy(Request $request, Bill $bill)
    {
        $this->checkDoctor($request);

        $bill->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bill deleted successfully.'
        ]);
    }

    // 6. Bill Receipt API
    public function receipt(Request $request, Bill $bill)
    {
        $this->checkDoctor($request);

        $bill->load(['patient', 'doctor']);

        return response()->json([
            'status' => 'success',
            'receipt' => $bill
        ]);
    }
}