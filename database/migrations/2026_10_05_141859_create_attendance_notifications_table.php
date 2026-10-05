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

            // العلاقات (Foreign Keys)
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained('guardians')->cascadeOnDelete();

            // تفاصيل الإشعار
            $table->date('date');
            $table->string('type', 20); // morning | leave
            $table->string('channel', 20); // sms | whatsapp
            $table->string('event', 20)->default('absence'); // نوع الحدث (غياب)
            $table->string('phone', 25);

            // حالة الإرسال والتتبع
            $table->string('status', 20)->default('pending'); // pending | sending | sent | failed
            $table->unsignedInteger('attempts')->default(0);
            $table->string('provider_message_id', 191)->nullable();
            $table->string('last_error', 500)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            /*
             * منع إرسال نفس الإشعار مرتين:
             * (نفس الطالب + نفس ولي الأمر + نفس التاريخ والرحلة + نفس القناة والحدث)
             */
            $table->unique(
                ['student_id', 'guardian_id', 'date', 'type', 'channel', 'event'],
                'attn_notif_unique_idx'
            );

            // فهارس (Indexes) لتحسين سرعة الاستعلامات (Search & Filter)
            $table->index(['student_id', 'date', 'type'], 'attn_student_date_type_idx');
            $table->index(['guardian_id', 'status'], 'attn_guardian_status_idx');
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
