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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('CardNo', 30)->unique();
            $table->string('Name', 200);
            $table->string('Phone', 30);
            $table->string('LicenseNo', 30);
            $table->string('Ownership', 30);
            $table->string('Wing', 50);
            $table->boolean('CheckUp')->nullable();
            $table->boolean('Behavior')->nullable();
            $table->boolean('Form')->nullable();
            $table->boolean('Fitnes')->nullable();
            $table->integer('BusNo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
