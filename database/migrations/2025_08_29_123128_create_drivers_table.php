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
            $table->string('Name', 50);
            $table->string('IDNo', 11);
            $table->string('Phone', length: 15);
            $table->string('LicenseNo', length: 8)->nullable()->unique();
            $table->string('Picture', 20)->nullable();
            $table->string('Bus_type', 10);
            $table->string('No_Passengers', 10);
            $table->string('Ownership', 10);
            $table->foreignId('wing_id')->constrained('wings')->onDelete('cascade');
            $table->boolean('CheckUp')->nullable(); 
            $table->boolean('Behavior')->nullable(); 
            $table->boolean('Form')->nullable(); 
            $table->boolean('Fitnes')->nullable(); 
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
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