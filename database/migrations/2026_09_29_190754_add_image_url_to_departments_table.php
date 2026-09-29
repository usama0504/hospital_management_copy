<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Har department ki apni alag picture ho sakti hai (public website ke
 * "Our Departments" page aur department detail page par dikhane ke liye).
 * Pehle sirf ek generic icon dikhta tha, sab departments ke liye same style.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};