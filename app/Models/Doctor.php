<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'specialization',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    
    public function availabilities()
    {
        return $this->hasMany(DoctorAvailability::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
