<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Patient;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with('patient')->latest()->paginate(10);
        return view('bills.index', compact('bills'));
    }

    public function create()
    {
        $patients = Patient::all();
        return view('bills.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'bill_date' => 'required|date',
        ]);

        Bill::create($request->all());
        return redirect()->route('bills.index')->with('success', 'Bill created successfully.');
    }

    public function edit(Bill $bill)
    {
        $patients = Patient::all();
        return view('bills.edit', compact('bill', 'patients'));
    }

    public function update(Request $request, Bill $bill)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'bill_date' => 'required|date',
        ]);

        $bill->update($request->all());
        return redirect()->route('bills.index')->with('success', 'Bill updated successfully.');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Bill deleted successfully.');
    }
}
