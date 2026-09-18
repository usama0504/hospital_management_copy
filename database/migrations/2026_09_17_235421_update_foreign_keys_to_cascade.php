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
        // 1. Bills table mein appointment_id column add karein agar already nahi hai
        //    (ye sab drivers — sqlite aur mysql dono — pe safe hai)
        if (!Schema::hasColumn('bills', 'appointment_id')) {
            Schema::table('bills', function (Blueprint $table) {
                $table->foreignId('appointment_id')->nullable()->after('patient_id');
            });
        }

        // 2. Foreign keys ko cascade banane wala hissa SIRF MySQL/MariaDB par chalayein.
        //    SQLite ALTER TABLE se foreign keys change karna support nahi karta,
        //    aur information_schema.KEY_COLUMN_USAGE bhi MySQL-only hai (ye SQLite
        //    par crash ho rahi thi). SQLite (local/dev DB) par ye migration
        //    safely skip ho jayegi — koi error nahi aayega.
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        // Appointments.patient_id ki foreign key cascade banayein
        Schema::table('appointments', function (Blueprint $table) {
            try {
                $table->dropForeign(['patient_id']);
            } catch (\Throwable $e) {
                // Foreign key exist nahi karti ya pehle se drop ho chuki — ignore karein
            }
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('patient_id')
                  ->references('id')->on('patients')
                  ->cascadeOnDelete();
        });

        // Bills ki patient_id aur appointment_id dono foreign keys cascade banayein
        Schema::table('bills', function (Blueprint $table) {
            try {
                $table->dropForeign(['patient_id']);
            } catch (\Throwable $e) {}
            try {
                $table->dropForeign(['appointment_id']);
            } catch (\Throwable $e) {}
        });

        Schema::table('bills', function (Blueprint $table) {
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
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            try {
                $table->dropForeign(['patient_id']);
            } catch (\Throwable $e) {}
        });

        Schema::table('bills', function (Blueprint $table) {
            try {
                $table->dropForeign(['patient_id']);
            } catch (\Throwable $e) {}
            try {
                $table->dropForeign(['appointment_id']);
            } catch (\Throwable $e) {}
        });
    }
};