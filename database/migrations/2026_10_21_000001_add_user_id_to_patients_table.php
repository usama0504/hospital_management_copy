<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

/**
 * Patient portal: har patient ka (optional) login account.
 * patients.user_id -> users.id. User delete ho to patient record bacha rehta hai.
 * Saath hi 'patient' role bana dete hain (bina permissions ke).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('patients', 'user_id')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->unique()->after('id')
                    ->constrained('users')->nullOnDelete();
            });
        }

        if (Schema::hasTable('roles')) {
            Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('patients', 'user_id')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};
