<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Naye reviews ab pehle "pending" rahenge, admin approve kare tab public par aayenge.
        // Pehle se maujood reviews approved hi rehte hain (sirf default badal raha hai).
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->change();
        });

        // Admin ke liye read/unread track karna (Safe check ke sath taake duplicate column error na aaye)
        if (!Schema::hasColumn('contact_messages', 'read_at')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('message');
            });
        }
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('is_approved')->default(true)->change();
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }
};