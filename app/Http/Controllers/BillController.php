<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Patient;
use App\Models\Doctor;
use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\UpdateBillRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Models\Appointment;
use Inertia\Inertia;

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

    public function index(Request $request)
    {
        $this->checkDoctor();

        $search = $request->input('search');
        $status = $request->input('status');

        $bills = Bill::with('patient', 'doctor')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('patient', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->when($status, fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Bills/Index', [
            'bills' => $bills,
            'filters' => ['search' => $search, 'status' => $status],
        ]);
    }

    public function create()
    {
        $this->checkDoctor();

        $patients = Patient::all();
        $doctors = Doctor::all();
        $appointments = Appointment::with('patient', 'doctor')->latest()->get(); // <-- Yeh add karein

        return Inertia::render('Bills/Create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'appointments' => $appointments // <-- Pass karein
        ]);
    }

    public function store(StoreBillRequest $request)
    {
        $this->checkDoctor();

        Bill::create($request->validated());

        return redirect()->route('bills.index')->with('success', 'Bill created successfully.');
    }

    public function edit(Bill $bill)
    {
        $this->checkDoctor();

        $patients = Patient::all();
        $doctors = Doctor::all();

        return Inertia::render('Bills/Edit', [
            'bill' => $bill,
            'patients' => $patients,
            'doctors' => $doctors
        ]);
    }

    public function update(UpdateBillRequest $request, Bill $bill)
    {
        $this->checkDoctor();

        $bill->update($request->validated());

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

        return Inertia::render('Bills/Receipt', [
            'bill' => $bill
        ]);
    }
}