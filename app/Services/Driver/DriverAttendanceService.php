<?php

namespace App\Services\Driver;

use App\Models\Student;
use App\Models\PreparationStu;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DriverAttendanceService
{
    public function getStudents($driverId, $search = '')
    {
        return Student::with('region')
            ->where('driver_id', $driverId)
            ->when($search, function($query) use ($search) {
                $query->where('Name', 'like', '%' . $search . '%');
            })
            ->get();
    }

    public function getAttendanceRecords($driverId, $date, $type)
    {
        return PreparationStu::where('driver_id', $driverId)
            ->where('Date', $date)
            ->where('type', $type)
            ->get()
            ->keyBy('student_id');
    }

    public function markAttendance($driverId, $studentId, $date, $type, $status)
    {
        // تم نقل هذا الشرط لملف الـ Component بدلاً من هنا لمنع التكرار، 
        // لكننا سنتركه كإجراء أمني إضافي.
        if ($this->isLocked($type, $date)) {
            return false;
        }

        $student = Student::where('id', $studentId)->where('driver_id', $driverId)->firstOrFail();

        PreparationStu::updateOrCreate(
            [
                'student_id' => $student->id,
                'driver_id' => $driverId,
                'Date' => $date,
                'type' => $type,
            ],
            [
                'Atend' => $status,
                'region_id' => $student->region_id,
            ]
        );

        return true;
    }

    public function markAllPresent($driverId, $students, $date, $type)
    {
        if ($this->isLocked($type, $date)) {
            return false;
        }

        foreach ($students as $stu) {
            PreparationStu::updateOrCreate(
                [
                    'student_id' => $stu->id,
                    'driver_id' => $driverId,
                    'Date' => $date,
                    'type' => $type,
                ],
                [
                    'Atend' => true,
                    'region_id' => $stu->region_id,
                ]
            );
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