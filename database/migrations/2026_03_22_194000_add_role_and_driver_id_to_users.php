<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'driver'])->default('admin')->after('password');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['role', 'driver_id']);
        });
    }
};
