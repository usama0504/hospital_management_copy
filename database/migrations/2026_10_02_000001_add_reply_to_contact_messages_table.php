<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('contact_messages', 'reply')) {
                $table->text('reply')->nullable();
            }
            if (! Schema::hasColumn('contact_messages', 'replied_at')) {
                $table->timestamp('replied_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['reply', 'replied_at']);
        });
    }
};
