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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('Name', 100);
            $table->string('Grade', 20);
            $table->string('Sex', 20);
            $table->string('Phone', 20);
            $table->string('Picture', 100);
            $table->string('Stu_position', 200);
            $table->foreignId('wing_id')->constrained('wings')->onDelete('cascade');
            $table->string('Division', 20)->nullable();
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};