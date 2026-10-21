<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Register form se bane doctors ki user_id set nahi hoti thi (sirf email se
 * match hota tha). Ab sab jagah user_id use hoti hai, isliye jin doctors ki
 * user_id khali hai unhe email se match karke link kar dete hain.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('doctors')->whereNull('user_id')->orderBy('id')->get()->each(function ($doctor) {
            $user = DB::table('users')->where('email', $doctor->email)->first();

            if ($user) {
                DB::table('doctors')->where('id', $doctor->id)->update(['user_id' => $user->id]);
            }
        });
    }

    public function down(): void
    {
        // Data backfill hai, wapas karne ki zaroorat nahi.
    }
};
