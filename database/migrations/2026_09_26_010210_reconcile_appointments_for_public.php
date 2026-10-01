<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * The appointments table was created with patient_name/patient_phone
 * (both NOT NULL) and a DATE-only appointment_date. But the dashboard
 * AppointmentController (and the Appointment model's $fillable) has always
 * expected a patient_id foreign key and a full datetime appointment_date —
 * so creating an appointment from the dashboard fails with "Field
 * 'patient_name' doesn't have a default value", and any stored time gets
 * silently truncated by the DATE column. This migration is defensive
 * (checks before every change) so it is safe to run regardless of what
 * has already been applied.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('appointments', 'patient_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->foreignId('patient_id')->nullable()->after('id')
                    ->constrained('patients')->cascadeOnDelete();
            });
        }

        // Legacy columns: keep them (older rows may still use them for
        // display), but they must not block an insert that only sets
        // patient_id, so make sure they are all nullable.
        foreach (['patient_name', 'patient_phone', 'appointment_time'] as $col) {
            if (Schema::hasColumn('appointments', $col)) {
                Schema::table('appointments', function (Blueprint $table) use ($col) {
                    $table->string($col)->nullable()->change();
                });
            }
        }
        if (Schema::hasColumn('appointments', 'appointment_time')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->time('appointment_time')->nullable()->change();
            });
        }

        // appointment_date must hold date AND time (dashboard code does
        // Carbon::parse($appointment->appointment_date) and compares times).
        $col = collect(DB::select("SHOW COLUMNS FROM appointments WHERE Field = 'appointment_date'"))->first();
        if ($col && stripos($col->Type, 'datetime') === false) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dateTime('appointment_date')->change();
            });
        }
    }

    public function down(): void
    {
        // Intentionally left as-is: reverting would reintroduce the bug.
    }
};