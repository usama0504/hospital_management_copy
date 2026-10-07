<?php

namespace App\Models;

use App\Observers\AppointmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[ObservedBy([AppointmentObserver::class])]
class Appointment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'status',
        'notes',
    ];

    // Audit log: kisne kya create/update/delete/restore kiya (sirf badli hui values).
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logExcept(['notes'])
            ->logOnlyDirty();
    }

    // Patient relation: ek appointment ka ek patient hota hai (withTrashed: delete hone par bhi naam dikhe)
    public function patient()
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    // Doctor relation: ek appointment ka ek doctor hota hai
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Ek appointment se ek prescription likhi ja sakti hai
    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }
    public function bill()
    {
        return $this->hasOne(Bill::class);
    }
}