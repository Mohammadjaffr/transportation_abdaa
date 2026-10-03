<?php

namespace App\Services;

use App\Models\Student;
use App\Models\AttendanceSmsNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceSmsService
{
    public function __construct(
        protected HttpSmsService $smsService
    ) {}

    /**
     * إرسال إشعار الغياب لأولياء الأمور المسموح لهم باستقبال الإشعارات
     */
    public function sendAbsence(Student $student, string $type, string $date): array
    {
        // التحقق من تفعيل الـ SMS من الإعدادات
        if (!config('attendance.sms_enabled', true)) {
            return [
                'success' => true,
                'sent' => 0,
                'failed' => 0,
                'skipped' => 1,
                'reason' => 'sms_disabled',
            ];
        }

        $apiKey = config('services.httpsms.api_key');
        $fromPhone = config('services.httpsms.from_phone');

        if (empty($apiKey) || empty($fromPhone)) {
            Log::warning('Attendance SMS skipped: SMS configuration is missing.', [
                'student_id' => $student->id,
            ]);

            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'skipped' => 1,
                'reason' => 'missing_configuration',
            ];
        }

        $guardians = $student->guardians()
            ->where('guardians.is_active', true)
            ->wherePivot('receive_notifications', true)
            ->whereNotNull('guardians.phone')
            ->where('guardians.phone', '!=', '')
            ->get();

        $guardians = $guardians->unique(function ($guardian) {
            return preg_replace('/[^0-9]/', '', $guardian->phone);
        });

        if ($guardians->isEmpty()) {
            return [
                'success' => true,
                'sent' => 0,
                'failed' => 0,
                'skipped' => 1,
                'reason' => 'no_recipients',
            ];
        }

        $tripText = match ($type) {
            'morning' => 'رحلة الذهاب',
            'leave'   => 'رحلة العودة',
            default   => 'الرحلة',
        };

        try {
            $dateText = Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable $e) {
            $dateText = $date;
        }

        $dayText = \Carbon\Carbon::parse($date)
            ->locale('ar')
            ->translatedFormat('l');

        if (trim($student->Sex) === 'أنثى') {

            $message =
                "تنبيه متابعة من مدرسة الإبداع:\n"
                . "تم تسجيل الطالبة {$student->Name} غائبة في {$tripText} "
                . "يوم {$dayText} بتاريخ {$dateText}.\n"
                . "وذلك ضمن متابعة المدرسة اليومية لسلامة وانتظام بناتنا الطالبات، "
                . "ونقدر تعاونكم الدائم معنا.";
        } else {

            $message =
                "تنبيه متابعة من مدرسة الإبداع:\n"
                . "تم تسجيل الطالب {$student->Name} غائبًا في {$tripText} "
                . "يوم {$dayText} بتاريخ {$dateText}.\n"
                . "وذلك ضمن متابعة المدرسة اليومية لسلامة وانتظام أبنائنا الطلاب، "
                . "ونقدر تعاونكم الدائم معنا.";
        }
        $sent = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($guardians as $guardian) {
            $normalizedPhone = preg_replace('/[^0-9]/', '', $guardian->phone);

            // التحقق من منع التكرار
            $notification = AttendanceSmsNotification::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'guardian_id' => $guardian->id,
                    'date' => $dateText,
                    'type' => $type,
                    'event' => 'absence',
                ],
                [
                    'phone' => $normalizedPhone,
                    'status' => 'pending',
                ]
            );

            // إذا أرسلت بنجاح من قبل، لا ترسل مجدداً
            if ($notification->status === 'sent') {
                $skipped++;
                continue;
            }

            // تحديث الحالة إلى جاري الإرسال
            $notification->update(['status' => 'sending', 'attempts' => $notification->attempts + 1]);

            try {
                $result = $this->smsService->send(
                    $normalizedPhone,
                    $message,
                    $apiKey,
                    $fromPhone
                );

                if ($result['success'] ?? false) {
                    $notification->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'provider_message_id' => $result['message_id'] ?? null,
                    ]);
                    $sent++;
                } else {
                    $notification->update([
                        'status' => 'failed',
                        'last_error' => $result['error'] ?? 'Unknown error',
                    ]);
                    $failed++;
                    Log::warning('Attendance SMS failed.', [
                        'student_id'  => $student->id,
                        'guardian_id' => $guardian->id,
                        'error'       => $result['error'] ?? 'Unknown error',
                    ]);
                }
            } catch (\Throwable $e) {
                $notification->update([
                    'status' => 'failed',
                    'last_error' => substr($e->getMessage(), 0, 500),
                ]);
                $failed++;
                Log::error('Attendance SMS exception.', [
                    'student_id'  => $student->id,
                    'guardian_id' => $guardian->id,
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => true,
            'sent'    => $sent,
            'failed'  => $failed,
            'skipped' => $skipped,
        ];
    }
}
