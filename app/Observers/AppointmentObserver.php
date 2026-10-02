<?php

namespace App\Observers;

use App\Mail\AppointmentNotification;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;

class AppointmentObserver
{
    // Transaction (jaise PatientVisitController) commit hone ke baad hi email jaye.
    public bool $afterCommit = true;

    public function created(Appointment $appointment): void
    {
        $event = in_array($appointment->status, ['Pending'], true) ? 'received' : 'confirmed';

        // Naya appointment pehle se cancelled/completed ho to email ki zaroorat nahi.
        if (in_array($appointment->status, ['Cancelled', 'Completed'], true)) {
            return;
        }

        $this->notify($appointment, $event);
    }

    public function updated(Appointment $appointment): void
    {
        if ($appointment->wasChanged('status')) {
            match ($appointment->status) {
                'Cancelled' => $this->notify($appointment, 'cancelled'),
                'Scheduled', 'Approved' => $this->notify($appointment, 'confirmed'),
                default => null, // Pending / Completed par email nahi
            };

            return;
        }

        if ($appointment->wasChanged('appointment_date') && $appointment->status !== 'Cancelled') {
            $this->notify($appointment, 'rescheduled');
        }
    }

    private function notify(Appointment $appointment, string $event): void
    {
        $email = $appointment->patient?->email;

        // Public form par email na dene wale guests ko banawati "@careplus.local" address milta hai.
        if (! $email || str_ends_with(strtolower($email), '@careplus.local')) {
            return;
        }

        try {
            Mail::to($email)->send(new AppointmentNotification($appointment, $event));
        } catch (\Throwable $e) {
            // Email fail hone par appointment ka kaam na ruke.
            report($e);
        }
    }
}
