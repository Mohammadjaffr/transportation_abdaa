<?php

namespace App\Services\Driver;

use App\Models\Student;
use App\Models\PreparationStu;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Services\AttendanceNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DriverAttendanceService
{
    public function __construct(
        protected AttendanceNotificationService $attendanceNotificationService
    ) {}
    public function getStudents($driverId, $search = '')
    {
        return Student::with('region')
            ->where('driver_id', $driverId)
            ->when($search, function ($query) use ($search) {
                $query->where('Name', 'like', '%' . $search . '%');
            })
            ->get();
    }
    public function saveBatchAttendance($driverId, $students, array $attendance, $date, $type): bool
    {
        // التأكد أن التحضير ما زال مفتوحًا
        if ($this->isLocked($type, $date)) {
            return false;
        }

        // مصفوفة لجمع الطلاب الغائبين الذين يجب إرسال رسائل لهم
        $absentStudentsToNotify = [];

        try {
            DB::transaction(function () use ($driverId, $students, $attendance, $date, $type, &$absentStudentsToNotify) {
                foreach ($students as $student) {

                    // حماية: الطالب يجب أن يكون تابعًا للسائق.
                    if ((int) $student->driver_id !== (int) $driverId) {
                        continue;
                    }

                    // يجب أن تكون حالة الطالب محددة.
                    if (!array_key_exists($student->id, $attendance)) {
                        throw new \RuntimeException('حالة الطالب غير محددة.');
                    }

                    $status = $attendance[$student->id];

                    // نقبل فقط true أو false.
                    if (!is_bool($status)) {
                        throw new \RuntimeException('حالة تحضير غير صحيحة.');
                    }

                    // الحفظ في قاعدة البيانات
                    $preparation = PreparationStu::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'driver_id'  => $driverId,
                            'Date'       => $date,
                            'type'       => $type,
                        ],
                        [
                            'Atend'      => $status,
                            'region_id'  => $student->region_id,
                        ]
                    );

                    // إذا كان الطالب "غائباً" (false) 
                    // وتأكدنا أن هذا التسجيل جديد أو تم تغيير حالته للتو إلى غائب (لتجنب تكرار الرسائل)
                    if ($status === false && ($preparation->wasRecentlyCreated || $preparation->wasChanged('Atend'))) {
                        $absentStudentsToNotify[] = $student;
                    }
                }
            });

            // =================================================================
            // إرسال رسائل الغياب (خارج الـ Transaction لكي لا نؤخر قاعدة البيانات)
            // =================================================================
            foreach ($absentStudentsToNotify as $studentToNotify) {
                // استدعاء خدمة الإشعارات (SMS + WhatsApp)
                $this->attendanceNotificationService->notifyAbsence($studentToNotify, $type, $date);
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('Failed to save batch attendance', [
                'driver_id' => $driverId,
                'date'      => $date,
                'type'      => $type,
                'error'     => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function getAttendanceRecords($driverId, $date, $type)
    {
        return PreparationStu::where('driver_id', $driverId)
            ->where('Date', $date)
            ->where('type', $type)
            ->get()
            ->keyBy('student_id');
    }

    public function markAttendance(
        $driverId,
        $studentId,
        $date,
        $type,
        $status
    ) {
        /*
     * حماية إضافية من التحضير خارج الفترة.
     */
        if (
            $this->isLocked(
                $type,
                $date
            )
        ) {

            return false;
        }


        /*
     * لا يستطيع السائق تحضير طالب
     * غير تابع له.
     */
        $student = Student::where(
            'id',
            $studentId
        )
            ->where(
                'driver_id',
                $driverId
            )
            ->firstOrFail();


        /*
     * حفظ سجل الحضور أولًا.
     */
        $record =
            PreparationStu::updateOrCreate(
                [
                    'student_id' =>
                    $student->id,

                    'driver_id' =>
                    $driverId,

                    'Date' =>
                    $date,

                    'type' =>
                    $type,
                ],
                [
                    'Atend' =>
                    (bool) $status,

                    'region_id' =>
                    $student->region_id,
                ]
            );


        /*
    |--------------------------------------------------------------------------
    | هل نرسل SMS؟
    |--------------------------------------------------------------------------
    |
    | نرسل فقط:
    |
    | 1. إذا السجل جديد.
    | أو
    | 2. تغيرت حالة Atend.
    |
    | والأهم: فقط للغياب
    |
    */

        $shouldNotify =
            ($record->wasRecentlyCreated
            || $record->wasChanged('Atend')) && !$record->Atend;


        if ($shouldNotify) {

            $this
                ->attendanceNotificationService
                ->notifyAbsence(
                    $student,
                    $type,
                    $date
                );
        }


        return true;
    }

  public function markAllPresent(
    $driverId,
    $students,
    $date,
    $type
) {
    if (
        $this->isLocked(
            $type,
            $date
        )
    ) {

        return false;
    }


    foreach ($students as $student) {

        $record =
            PreparationStu::updateOrCreate(
                [
                    'student_id' =>
                        $student->id,

                    'driver_id' =>
                        $driverId,

                    'Date' =>
                        $date,

                    'type' =>
                        $type,
                ],
                [
                    'Atend' => true,

                    'region_id' =>
                        $student->region_id,
                ]
            );


        /*
         * الرسائل ملغية هنا لأن الحاضر لا يرسل له رسالة غياب.
         */
    }


    return true;
}
    // الدالة المحدثة للتحقق من الفترات الزمنية
    public function isLocked($type, $date)
    {
        $targetDate = Carbon::parse($date)->startOfDay();
        $today = Carbon::today();

        // منع تحضير الأيام السابقة أو القادمة
        if ($targetDate->lessThan($today) || $targetDate->greaterThan($today)) {
            return true;
        }

        $now = Carbon::now();

        // جلب أوقات البداية والنهاية بناءً على نوع الرحلة
        if ($type === 'morning') {
            $startTimeStr = Setting::where('key', 'morning_start')->value('value') ?? '07:00';
            $endTimeStr   = Setting::where('key', 'morning_end')->value('value') ?? '09:00';
        } else {
            $startTimeStr = Setting::where('key', 'leave_start')->value('value') ?? '13:00';
            $endTimeStr   = Setting::where('key', 'leave_end')->value('value') ?? '16:00';
        }

        try {
            // تحويل النصوص إلى أوقات للتمكن من مقارنتها
            $startTime = Carbon::createFromTimeString($startTimeStr);
            $endTime   = Carbon::createFromTimeString($endTimeStr);
        } catch (\Exception $e) {
            // في حال وجود خطأ في صيغة الوقت في قاعدة البيانات، يتم إغلاق التحضير احترازياً
            return true;
        }

        // يكون "مغلقاً" إذا كان الوقت الحالي (خارج) الفترة المسموحة
        return !$now->between($startTime, $endTime);
    }

    // الدالة المحدثة لإظهار الرسائل بناءً على الفترات الزمنية
    public function getLockMessage($type)
    {
        if ($type === 'morning') {
            $startTimeStr = Setting::where('key', 'morning_start')->value('value') ?? '07:00';
            $endTimeStr   = Setting::where('key', 'morning_end')->value('value') ?? '09:00';

            try {
                $startFormatted = Carbon::parse($startTimeStr)->format('h:i A');
                $endFormatted   = Carbon::parse($endTimeStr)->format('h:i A');
                return "التحضير لرحلة الذهاب متاح فقط بين ($startFormatted) و ($endFormatted).";
            } catch (\Exception $e) {
                return "تم إغلاق تحضير رحلة الذهاب حالياً.";
            }
        } else {
            $startTimeStr = Setting::where('key', 'leave_start')->value('value') ?? '13:00';
            $endTimeStr   = Setting::where('key', 'leave_end')->value('value') ?? '16:00';

            try {
                $startFormatted = Carbon::parse($startTimeStr)->format('h:i A');
                $endFormatted   = Carbon::parse($endTimeStr)->format('h:i A');
                return "التحضير لرحلة العودة متاح فقط بين ($startFormatted) و ($endFormatted).";
            } catch (\Exception $e) {
                return "تم إغلاق تحضير رحلة العودة حالياً.";
            }
        }
    }

    public function getCounters($students, $records)
    {
        $present = 0;
        $absent = 0;
        $total = $students->count();

        foreach ($students as $student) {
            if ($records->has($student->id)) {
                if ($records[$student->id]->Atend) {
                    $present++;
                } else {
                    $absent++;
                }
            }
        }

        $pending = $total - ($present + $absent);

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'pending' => max(0, $pending),
        ];
    }
}