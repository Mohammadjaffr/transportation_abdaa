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
        Schema::create('attendance_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('guardian_id')->nullable();
            $table->date('date');
            $table->string('type'); // morning, leave
            $table->string('channel'); // sms, whatsapp
            $table->string('event'); // absence
            $table->string('phone')->nullable();
            $table->string('status')->default('pending'); // pending, sending, sent, failed
            $table->integer('attempts')->default(0);
            $table->string('provider_message_id')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Idempotency unique constraint
            $table->unique(
                ['student_id', 'guardian_id', 'date', 'type', 'channel', 'event'],
                'attn_notif_unique_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_notifications');
    }
};
