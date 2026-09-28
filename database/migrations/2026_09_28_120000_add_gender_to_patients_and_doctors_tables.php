<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullable rakha hai taake purane records break na hon
        Schema::table('patients', function (Blueprint $table) {
            $table->string('gender', 10)->nullable()->after('phone');
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->string('gender', 10)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('gender');
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
