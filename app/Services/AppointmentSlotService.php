<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Appointment slot ke sare rules ek hi jagah:
 * past time, doctor ka din/working hours, 30-minute slot, aur overlap.
 *
 * Pehle ye code web, API, public booking aur patient-visit controllers mein
 * copy-paste tha (aur API mein tha hi nahi). Ab sab yahin se guzarte hain.
 */
class AppointmentSlotService
{
    public const SLOT_MINUTES = 30;

    /**
     * Doctor ki row lock karta hai taake do log ek hi waqt ek hi slot book na
     * kar saken. DB::transaction() ke ANDAR call karein, check se pehle.
     */
    public function lockDoctor(int $doctorId): void
    {
        Doctor::whereKey($doctorId)->lockForUpdate()->first();
    }

    /**
     * Agar slot theek hai to null, warna error message.
     */
    public function error(int $doctorId, Carbon $start, ?int $ignoreAppointmentId = null): ?string
    {
        $end = $start->copy()->addMinutes(self::SLOT_MINUTES);

        if ($start->isPast()) {
            return 'You cannot book a past appointment time.';
        }

        $availability = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day_of_week', $start->format('l'))
            ->where('is_active', true)
            ->first();

        if (! $availability) {
            return 'Doctor is not available on this day.';
        }

        $day = $start->format('Y-m-d');
        $availabilityStart = Carbon::parse($day . ' ' . $availability->start_time);
        $availabilityEnd = Carbon::parse($day . ' ' . $availability->end_time);

        if ($start < $availabilityStart || $end > $availabilityEnd) {
            return "Selected time is outside the doctor's working hours.";
        }

        if (((int) round($availabilityStart->diffInMinutes($start, true))) % self::SLOT_MINUTES !== 0) {
            return 'Please select a valid 30-minute time slot.';
        }

        // Overlap database se check hota hai (pehle saari appointments memory
        // mein load hoti thi). Do slots overlap karte hain agar ek ka start
        // doosre ke (start - 30min, end) ke darmiyan ho.
        $overlap = Appointment::where('doctor_id', $doctorId)
            ->where('status', '!=', 'Cancelled')
            ->when($ignoreAppointmentId, fn ($q) => $q->where('id', '!=', $ignoreAppointmentId))
            ->where('appointment_date', '>', $start->copy()->subMinutes(self::SLOT_MINUTES))
            ->where('appointment_date', '<', $end)
            ->exists();

        if ($overlap) {
            return 'This doctor already has an appointment during this time.';
        }

        return null;
    }

    /**
     * Error ho to ValidationException phenkta hai: web par form ke saath wapas,
     * API par 422 JSON.
     *
     * @throws ValidationException
     */
    public function assertAvailable(
        int $doctorId,
        Carbon $start,
        string $field = 'appointment_date',
        ?int $ignoreAppointmentId = null,
    ): void {
        if ($message = $this->error($doctorId, $start, $ignoreAppointmentId)) {
            throw ValidationException::withMessages([$field => $message]);
        }
    }
}
