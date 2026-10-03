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
        Schema::create('attendance_sms_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('type'); // morning, leave
            $table->string('event')->default('absence');
            $table->string('phone');
            $table->string('status')->default('pending'); // pending, sending, sent, failed
            $table->integer('attempts')->default(0);
            $table->string('provider_message_id')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate messages for the same absence event
            $table->unique(['student_id', 'guardian_id', 'date', 'type', 'event'], 'attendance_sms_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sms_notifications');
    }
};
