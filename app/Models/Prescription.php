<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Prescription extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'prescribed_date',
        'diagnosis',
        'notes',
    ];

    protected $casts = [
        'prescribed_date' => 'date',
    ];

    // Audit log: kisne kya create/update/delete/restore kiya (sirf badli hui values).
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logExcept(['diagnosis', 'notes'])
            ->logOnlyDirty();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class)->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
