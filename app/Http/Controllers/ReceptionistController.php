<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoctorAvailability;
use Carbon\Carbon;
use Inertia\Inertia;

class ReceptionistController extends Controller
{
    // Aaj ke din ke hisab se doctors ki availability dikhane ke liye
    public function todayAvailability()
    {
        // Aaj ka din nikalne ke liye (maslan: Thursday, Friday)
        $currentDay = Carbon::now()->format('l'); 

        // Sirf aaj ke din ki active availabilities fetch karein sath mein doctor ki details
        $availabilities = DoctorAvailability::with('doctor')
            ->where('day_of_week', $currentDay)
            ->where('is_active', true)
            ->get();

        return Inertia::render('Receptionist/TodayAvailability', [
            'availabilities' => $availabilities,
            'currentDay' => $currentDay
        ]);
    }
}