<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pehle check karein ke agar 'bills' table mein appointment_id column nahi hai, toh add kardein
        Schema::table('bills', function (Blueprint $table) {
            if (!Schema::hasColumn('bills', 'appointment_id')) {
                $table->foreignId('appointment_id')->nullable()->after('patient_id');
            }
        });

        // 2. Appointments table ki foreign key update karein
        Schema::table('appointments', function (Blueprint $table) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'appointments' 
                AND COLUMN_NAME = 'patient_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            foreach ($foreignKeys as $fk) {
                try {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                } catch (\Throwable $e) {}
            }
            
            $table->foreign('patient_id')
                  ->references('id')->on('patients')
                  ->cascadeOnDelete();
        });

        // 3. Bills table ki foreign keys update karein
        Schema::table('bills', function (Blueprint $table) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME, COLUMN_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'bills' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            foreach ($foreignKeys as $fk) {
                if (in_array($fk->COLUMN_NAME, ['patient_id', 'appointment_id'])) {
                    try {
                        $table->dropForeign($fk->CONSTRAINT_NAME);
                    } catch (\Throwable $e) {}
                }
            }

            $table->foreign('patient_id')
                  ->references('id')->on('patients')
                  ->cascadeOnDelete();

            $table->foreign('appointment_id')
                  ->references('id')->on('appointments')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            try {
                $table->dropForeign(['patient_id']);
            } catch (\Throwable $e) {}
        });

        Schema::table('bills', function (Blueprint $table) {
            try {
                $table->dropForeign(['patient_id']);
                $table->dropForeign(['appointment_id']);
            } catch (\Throwable $e) {}
        });
    }
};