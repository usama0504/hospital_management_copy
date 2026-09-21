<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'status',
    ];

    // Patient relation: ek appointment ka ek patient hota hai
    public function patient()
    {
        return $this->belongsTo(Patient::class);
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
