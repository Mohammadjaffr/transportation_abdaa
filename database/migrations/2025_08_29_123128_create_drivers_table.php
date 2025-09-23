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
            $table->string('Phone', 9);
            $table->string('LicenseNo', length: 8)->nullable()->unique();
            $table->string('Picture', 20)->nullable();
            $table->string('Ownership', 10);
            $table->foreignId('wing_id')->constrained('wings')->onDelete('cascade');
            $table->boolean('CheckUp')->nullable(); //الفحص
            $table->boolean('Behavior')->nullable(); //السلوك
            $table->boolean('Form')->nullable(); //الاستمارة
            $table->boolean('Fitnes')->nullable(); //اللياقة
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