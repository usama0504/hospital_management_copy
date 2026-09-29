<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DoctorController@store hamesha 'user_id' bhejta tha, lekin doctors table
 * mein ye column kabhi tha hi nahi — is wajah se woh chupke se drop ho
 * jata tha aur Doctor.user_id hamesha NULL rehta tha. Matlab admin ke
 * DoctorController@update wala "email ko User account se sync karo" wala
 * hissa kabhi chalta hi nahi tha, aur agar kabhi Doctor ka email dashboard
 * se badal jaye to uska login account purane email par hi phansa reh jata.
 *
 * Ye migration column add karti hai aur maujooda doctors ko unke email se
 * match karke, unke User account se link (backfill) bhi kar deti hai.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('doctors', 'user_id')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')
                    ->constrained('users')->nullOnDelete();
            });
        }

        // Backfill: jo doctors pehle se maujood hain, unhe email ke zariye
        // unke User account se link kar dein (agar match mil jaye).
        $doctorsWithoutUser = DB::table('doctors')->whereNull('user_id')->orderBy('id')->get();

        foreach ($doctorsWithoutUser as $doctor) {
            $user = DB::table('users')->where('email', $doctor->email)->first();

            if ($user) {
                DB::table('doctors')->where('id', $doctor->id)->update(['user_id' => $user->id]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('doctors', 'user_id')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};