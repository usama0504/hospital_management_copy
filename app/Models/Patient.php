<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
       protected $fillable = [
        'name',
        'email',
        'phone',
        'dob',
        'address',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}