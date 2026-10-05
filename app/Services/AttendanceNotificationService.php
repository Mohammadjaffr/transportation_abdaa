<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Setting;
use App\Models\AttendanceNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceNotificationService
{
    public function __construct(
        protected HttpSmsService $httpSmsService,
        protected WhatsAppService $whatsAppService
    ) {}

    public function notifyAbsence(Student $student, string $type, string $date): array
    {
        $channelSetting = Setting::where('key', 'attendance_notification_channel')->value('value') ?? 'whatsapp';

        if ($channelSetting === 'disabled') {
            return ['status' => 'disabled'];
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
            return ['status' => 'no_recipients'];
        }

        $message = $this->buildMessage($student, $type, $date);

        $results = [
            'sms' => ['sent' => 0, 'failed' => 0],
            'whatsapp' => ['sent' => 0, 'failed' => 0],
        ];

        foreach ($guardians as $guardian) {
            $normalizedPhone = preg_replace('/[^0-9]/', '', $guardian->phone);

            if (in_array($channelSetting, ['sms', 'both'])) {
                $this->processChannel('sms', $guardian, $student, $type, $date, $normalizedPhone, $message, $results['sms']);
            }

            if (in_array($channelSetting, ['whatsapp', 'both'])) {
                $this->processChannel('whatsapp', $guardian, $student, $type, $date, $normalizedPhone, $message, $results['whatsapp']);
            }
        }

        return $results;
    }

    protected function processChannel(string $channel, $guardian, Student $student, string $type, string $date, string $phone, string $message, array &$counts): void
    {
        try {
            $dateText = Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable $e) {
            $dateText = $date;
        }

        $notification = AttendanceNotification::firstOrCreate(
            [
                'student_id' => $student->id,
                'guardian_id' => $guardian->id,
                'date' => $dateText,
                'type' => $type,
                'channel' => $channel,
                'event' => 'absence',
            ],
            [
                'phone' => $phone,
                'status' => 'pending',
            ]
        );

        if ($notification->status === 'sent') {
            return; // Idempotency
        }

        $notification->update(['status' => 'sending', 'attempts' => $notification->attempts + 1]);

        try {
            $success = false;
            $messageId = null;
            $error = null;

            if ($channel === 'sms') {
                $apiKey = config('services.httpsms.api_key');
                $fromPhone = config('services.httpsms.from_phone');
                if (empty($apiKey) || empty($fromPhone)) {
                    $error = 'missing_configuration';
                } else {
                    $result = $this->httpSmsService->send($phone, $message, $apiKey, $fromPhone);
                    if ($result['success'] ?? false) {
                        $success = true;
                        $messageId = $result['message_id'] ?? null;
                    } else {
                        $error = $result['error'] ?? 'Unknown error';
                    }
                }
            } elseif ($channel === 'whatsapp') {
                $result = $this->whatsAppService->sendText($phone, $message);
                if (!isset($result['error'])) {
                    $success = true;
                } else {
                    $error = $result['message'] ?? 'Unknown WhatsApp error';
                }
            }

            if ($success) {
                $notification->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'provider_message_id' => $messageId,
                ]);
                $counts['sent']++;
            } else {
                $notification->update([
                    'status' => 'failed',
                    'last_error' => substr($error, 0, 500),
                ]);
                $counts['failed']++;
                Log::warning("Attendance Notification $channel failed.", [
                    'student_id'  => $student->id,
                    'guardian_id' => $guardian->id,
                    'error'       => $error,
                ]);
            }
        } catch (\Throwable $e) {
            $notification->update([
                'status' => 'failed',
                'last_error' => substr($e->getMessage(), 0, 500),
            ]);
            $counts['failed']++;
            Log::error("Attendance Notification $channel exception.", [
                'student_id'  => $student->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    protected function buildMessage(Student $student, string $type, string $date): string
    {
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

        $dayText = Carbon::parse($date)->locale('ar')->translatedFormat('l');

        if (trim($student->Sex) === 'أنثى' || trim($student->Sex) === 'انثى') {
            return "تنبيه متابعة من مدرسة الإبداع:\n"
                . "تم تسجيل الطالبة {$student->Name} غائبة في {$tripText} "
                . "يوم {$dayText} بتاريخ {$dateText}.\n"
                . "وذلك ضمن متابعة المدرسة اليومية لسلامة وانتظام بناتنا الطالبات، "
                . "ونقدر تعاونكم الدائم معنا.";
        } else {
            return "تنبيه متابعة من مدرسة الإبداع:\n"
                . "تم تسجيل الطالب {$student->Name} غائبًا في {$tripText} "
                . "يوم {$dayText} بتاريخ {$dateText}.\n"
                . "وذلك ضمن متابعة المدرسة اليومية لسلامة وانتظام أبنائنا الطلاب، "
                . "ونقدر تعاونكم الدائم معنا.";
        }
    }
}
