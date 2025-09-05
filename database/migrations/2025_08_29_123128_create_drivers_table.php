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
            $table->string('Name', 100);
            $table->string('Phone', 20);
            $table->string('LicenseNo', 50);
            $table->string('Ownership', 50)->nullable();
            $table->string('Wing', 50)->nullable();
            $table->boolean('CheckUp')->nullable();
            $table->boolean('Behavior')->nullable();
            $table->boolean('Form')->nullable();
            $table->boolean('Fitnes')->nullable();


            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
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