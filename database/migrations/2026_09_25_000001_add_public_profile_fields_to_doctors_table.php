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
        Schema::table('doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('doctors', 'photo_url')) {
                $table->string('photo_url')->nullable()->after('specialization');
            }
            if (!Schema::hasColumn('doctors', 'experience_years')) {
                $table->unsignedSmallInteger('experience_years')->nullable()->after('photo_url');
            }
            if (!Schema::hasColumn('doctors', 'bio')) {
                $table->text('bio')->nullable()->after('experience_years');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['photo_url', 'experience_years', 'bio']);
        });
    }
};