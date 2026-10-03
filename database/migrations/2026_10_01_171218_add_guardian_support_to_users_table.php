<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. إضافة guardian_id
        |--------------------------------------------------------------------------
        |
        | Nullable حتى لا نتأثر بحسابات Admin و Driver الحالية.
        |
        */
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('guardian_id')->nullable()->after('driver_id')->constrained('guardians')->nullOnDelete();
        });

        DB::statement("
            ALTER TABLE users
            MODIFY role
            ENUM('admin', 'driver', 'guardian')
            NOT NULL
            DEFAULT 'admin'
        ");
    }


    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['guardian_id']);
            $table->dropColumn('guardian_id');
        });
    }
};