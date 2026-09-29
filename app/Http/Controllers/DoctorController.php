<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function show(Doctor $doctor)
    {
        $user = Auth::user();
        $isDoctor = $user && method_exists($user, 'hasRole') && $user->hasRole('doctor');

        $doctor->load(['department', 'availabilities']);

        $stats = [
            'total_appointments' => $doctor->appointments()->count(),
            'total_patients' => $doctor->appointments()->distinct('patient_id')->count('patient_id'),
            'upcoming_appointments' => $doctor->appointments()
                ->where('status', '!=', 'Cancelled')
                ->where('appointment_date', '>=', now())
                ->count(),
        ];

        // Doctor role wale user ko dusre doctors ke patients ki list nahi dikhate
        $recentAppointments = $isDoctor
            ? []
            : $doctor->appointments()->with('patient')->latest('appointment_date')->limit(10)->get();

        return Inertia::render('Doctors/Show', [
            'doctor' => $doctor,
            'stats' => $stats,
            'recentAppointments' => $recentAppointments,
            'showAppointments' => !$isDoctor,
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
            'gender' => 'required|in:Male,Female,Other',
            'specialization' => 'required|string',
            'password' => 'required|string|min:6', // Admin password set karega
            'department_id' => 'nullable|exists:departments,id',
            'consultation_fee' => 'nullable|numeric|min:0',
            'photo_url' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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

        // 2. Photo upload karein (agar admin ne image di hai)
        $photoUrl = null;

        if ($request->hasFile('photo_url')) {
            $path = $request->file('photo_url')->store('doctors', 'public');
            $photoUrl = Storage::url($path);
        }

        // 3. Doctors table mein entry karein
        Doctor::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'specialization' => $validated['specialization'],
            'department_id' => $validated['department_id'] ?? null,
            'consultation_fee' => $validated['consultation_fee'] ?? 0,
            'photo_url' => $photoUrl,
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
            'gender' => 'required|in:Male,Female,Other',
            'specialization' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'consultation_fee' => 'nullable|numeric|min:0',
            'photo_url' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // FIX: $request->all() ki jagah sirf validated fields update karein
        // (mass-assignment se bachne ke liye). 'photo' khud DB column nahi
        // hai, is liye pehle update array se nikal dein.
        $data = collect($validated)->except('photo_url')->toArray();

        // Agar admin ne nayi photo di hai, to purani photo storage se
        // delete kar ke nayi save kar dein.
        if ($request->boolean('remove_photo') && $doctor->photo_url) {
            Storage::disk('public')->delete(
                str_replace('/storage/', '', $doctor->photo_url)
            );

            $data['photo_url'] = null;
        }
        if ($request->hasFile('photo_url')) {
            if ($doctor->photo_url) {
                Storage::disk('public')->delete(
                    str_replace('/storage/', '', $doctor->photo_url)
                );
            }
            $data['photo_url'] = Storage::url(
                $request->file('photo_url')->store('doctors', 'public')
            );
        }

        $doctor->update($data);

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
