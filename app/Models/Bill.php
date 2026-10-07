<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;

class Bill extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'amount',
        'status',
        'bill_date',
    ];

    protected $casts = [
        'bill_date' => 'date',
    ];

    // Audit log: kisne kya create/update/delete/restore kiya (sirf badli hui values).
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    // withTrashed: patient delete ho jaye tab bhi purane bill par naam dikhe
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
}
