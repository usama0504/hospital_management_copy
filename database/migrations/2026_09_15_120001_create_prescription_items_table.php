<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('prescription_items')) {
            Schema::create('prescription_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prescription_id')->constrained()->onDelete('cascade');
                $table->string('medicine_name');
                $table->string('dosage')->nullable();       // e.g. "500mg"
                $table->string('frequency')->nullable();    // e.g. "1-0-1" (morning-afternoon-night)
                $table->string('duration')->nullable();     // e.g. "5 days"
                $table->text('instructions')->nullable();   // e.g. "After meals"
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
