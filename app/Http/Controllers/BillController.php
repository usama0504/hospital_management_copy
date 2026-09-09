<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    // Doctor check helper function
    private function checkDoctor()
    {
        $user = Auth::user();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('doctor')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->checkDoctor();

        $bills = Bill::with('patient','doctor')->latest()->paginate(10);
        return view('bills.index', compact('bills'));
    }

    public function create()
    {
        $this->checkDoctor();

        $patients = Patient::all();
        $doctors = Doctor::all();

        return view('bills.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $this->checkDoctor();

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'bill_date' => 'required|date',
        ]);

        Bill::create($request->all());
        return redirect()->route('bills.index')->with('success', 'Bill created successfully.');
    }

    public function edit(Bill $bill)
    {
        $this->checkDoctor();

        $patients = Patient::all();
        $doctors = Doctor::all();

        return view('bills.edit', compact('bill', 'patients', 'doctors'));
    }

    public function update(Request $request, Bill $bill)
    {
        $this->checkDoctor();

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'bill_date' => 'required|date',
        ]);

        $bill->update($request->all());
        return redirect()->route('bills.index')->with('success', 'Bill updated successfully.');
    }

    public function destroy(Bill $bill)
    {
        $this->checkDoctor();

        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Bill deleted successfully.');
    }

    public function receipt(Bill $bill)
    {
        $this->checkDoctor();

        $bill->load(['patient', 'doctor']);

        return view('bills.receipt', compact('bill'));
    }
}