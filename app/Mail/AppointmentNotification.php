<?php

namespace App\Mail;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AppointmentNotification extends Mailable
{
    /**
     * @param string $event 'received' | 'confirmed' | 'rescheduled' | 'cancelled'
     */
    public function __construct(public Appointment $appointment, public string $event)
    {
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->event) {
            'received' => 'We received your appointment request',
            'confirmed' => 'Your appointment is confirmed',
            'rescheduled' => 'Your appointment has been rescheduled',
            'cancelled' => 'Your appointment has been cancelled',
            default => 'Appointment update',
        };

        return new Envelope(subject: $subject . ' - ' . config('app.name'));
    }

    public function content(): Content
    {
        $appointment = $this->appointment;

        return new Content(
            markdown: 'emails.appointment',
            with: [
                'event' => $this->event,
                'patientName' => $appointment->patient?->name ?? 'Patient',
                'doctorName' => $appointment->doctor?->name,
                'when' => Carbon::parse($appointment->appointment_date)->format('l, d M Y \a\t h:i A'),
                'clinic' => config('app.name'),
            ],
        );
    }
}
