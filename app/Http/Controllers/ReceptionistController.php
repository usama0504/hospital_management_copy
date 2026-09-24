<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoctorAvailability;
use Carbon\Carbon;
use Inertia\Inertia;

class ReceptionistController extends Controller
{
    // Aaj ke din ke hisab se doctors ki availability dikhane ke liye

    // Aaj ke din ke hisab se doctors ki availability dikhane ke liye
    public function todayAvailability()
    {
        // Aaj ka din aur current time nikalne ke liye
        $currentDay = Carbon::now()->format('l'); // Maslan: Thursday
        $currentTime = Carbon::now()->format('H:i:s'); // Maslan: 11:28:42

        // Sirf wahi availabilities fetch hon jinka end_time abhi ke time se aage ka ho
        $availabilities = DoctorAvailability::with('doctor')
            ->where('day_of_week', $currentDay)
            ->where('is_active', true)
            ->where('end_time', '>', $currentTime) // Yeh check karega ke waqt baqi hai ya nahi
            ->get();

        return Inertia::render('Receptionist/TodayAvailability', [
            'availabilities' => $availabilities,
            'currentDay' => $currentDay
        ]);
    }
}
