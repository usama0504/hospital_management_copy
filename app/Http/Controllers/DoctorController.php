<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::latest()->paginate(10);
        return view('doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('doctors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:doctors,email',
            'phone' => 'required|string',
            'specialization' => 'required|string',
            'password' => 'required|string|min:6', // Admin password set karega
        ]);

        // 1. User create karein taake doctor login kar sakay
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Role assign karein aur account direct approve kar dein
        $user->assignRole('doctor');
        $user->forceFill(['is_approved' => true])->save();

        // 2. Doctors table mein user_id ke sath entry karein
        Doctor::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'specialization' => $request->specialization,
        ]);

        return redirect()->route('doctors.index')->with('success', 'Doctor added successfully and can now log in.');
    }

    public function edit(Doctor $doctor)
    {
        return view('doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $doctor->id,
            'phone' => 'required|string',
            'specialization' => 'required|string',
        ]);

        $doctor->update($request->all());

        // Agar doctors table mein user_id link hai aur user table update karna ho toh wo bhi kar sakte hain
        if ($doctor->user_id) {
            $user = User::find($doctor->user_id);
            if ($user) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
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