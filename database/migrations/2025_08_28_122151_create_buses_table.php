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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->integer('BusNo')->unique();
            $table->string('BusType', 50);
            $table->string('Model', 30);
            $table->integer('SeatsNo');
            $table->string('CustomsNo', 30)->nullable();
            $table->integer('StudentsNo')->default(0);
            $table->integer('LocNo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
