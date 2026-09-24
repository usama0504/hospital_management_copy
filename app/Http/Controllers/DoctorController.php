<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('department')->latest()->paginate(10);

        return Inertia::render('Doctors/Index', [
            'doctors' => $doctors
        ]);
    }

    public function create()
    {
        return Inertia::render('Doctors/Create', [
            'departments' => Department::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:doctors,email',
            'phone' => 'required|string',
            'specialization' => 'required|string',
            'password' => 'required|string|min:6', // Admin password set karega
            'department_id' => 'nullable|exists:departments,id',
            'consultation_fee' => 'nullable|numeric|min:0',
        ]);

        // 1. User create karein taake doctor login kar sakay
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Role assign karein aur account direct approve kar dein
        $user->assignRole('doctor');
        $user->forceFill(['is_approved' => true])->save();

        // 2. Doctors table mein entry karein
        Doctor::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'specialization' => $validated['specialization'],
            'department_id' => $validated['department_id'] ?? null,
            'consultation_fee' => $validated['consultation_fee'] ?? 0,
        ]);

        return redirect()->route('doctors.index')->with('success', 'Doctor added successfully and can now log in.');
    }

    public function edit(Doctor $doctor)
    {
        return Inertia::render('Doctors/Edit', [
            'doctor' => $doctor,
            'departments' => Department::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $doctor->id,
            'phone' => 'required|string',
            'specialization' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'consultation_fee' => 'nullable|numeric|min:0',
        ]);

        // FIX: $request->all() ki jagah sirf validated fields update karein
        // (mass-assignment se bachne ke liye)
        $doctor->update($validated);

        // Agar doctors table mein user_id link hai aur user table update karna ho toh wo bhi kar sakte hain
        if ($doctor->user_id) {
            $user = User::find($doctor->user_id);
            if ($user) {
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);
            }
        }

        return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        // Optional: Agar aap chahte hain ke doctor delete hone par user account bhi delete ho jaye
        if ($doctor->user_id) {
            User::where('id', $doctor->user_id)->delete();
        }

        $doctor->delete();

        return redirect()->route('doctors.index')->with('success', 'Doctor deleted successfully.');
    }
}
