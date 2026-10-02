<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Patient extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

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

    // Audit log: kisne kya create/update/delete/restore kiya (sirf badli hui values).
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

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