<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
       protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'dob',
        'address',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}