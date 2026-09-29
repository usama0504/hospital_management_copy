<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;
use App\Models\Department;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'gender',
        'specialization',
        'department_id',
        'consultation_fee',
        'photo_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}