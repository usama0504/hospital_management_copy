<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Doctor profile page par pehle hardcoded "4.9 (Patient Rated)" dikhta tha —
 * asal data nahi tha. Ye table real patient reviews store karti hai taake
 * rating asal (average) data se calculate ho.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->string('patient_name');
            $table->unsignedTinyInteger('rating'); // 1 se 5 tak
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true); // future moderation ke liye
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};