<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('gateway', 40);
            $table->string('reference', 40)->unique();   // hamari taraf ka transaction reference
            $table->decimal('amount', 10, 2);            // hamesha bill se, kabhi browser se nahi
            $table->string('currency', 3)->default('PKR');
            $table->string('status', 20)->default('pending')->index(); // pending | paid | failed
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
