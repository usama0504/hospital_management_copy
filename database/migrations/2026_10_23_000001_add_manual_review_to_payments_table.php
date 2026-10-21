<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Manual JazzCash payment: patient transaction ID likhta hai, staff check karke
 * approve ya reject karta hai.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Ek hi transaction ID do baar use nahi ho sakti (NULL baar baar chalta hai)
            $table->string('transaction_id', 60)->nullable()->unique()->after('reference');
            $table->string('sender_number', 30)->nullable()->after('transaction_id');
            $table->foreignId('reviewed_by')->nullable()->after('paid_at')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('review_note')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropUnique(['transaction_id']);
            $table->dropColumn(['transaction_id', 'sender_number', 'reviewed_at', 'review_note']);
        });
    }
};
