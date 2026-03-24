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

    public function isLocked($type, $date)
    {
        $targetDate = Carbon::parse($date)->startOfDay();
        $today = Carbon::today();
        
        if ($targetDate->lessThan($today)) {
            return true;
        }
        if ($targetDate->greaterThan($today)) {
            return true;
        }

        $lockTime = Cache::rememberForever('attendance_lock_' . $type, function () use ($type) {
            return Setting::where('key', 'attendance_lock_' . $type)->value('value') ?? config('attendance.locks.' . $type);
        });

        if (!$lockTime) return false;

        $now = Carbon::now();
        $cutoff = Carbon::createFromFormat('H:i', $lockTime);

        return $now->greaterThan($cutoff);
    }

    public function getLockMessage($type)
    {
        $lockTime = Cache::rememberForever('attendance_lock_' . $type, function () use ($type) {
            return Setting::where('key', 'attendance_lock_' . $type)->value('value') ?? config('attendance.locks.' . $type);
        });
        
        if ($type === 'morning') {
            return "تم إغلاق تحضير رحلة الذهاب (الحد الأقصى: $lockTime).";
        }
        return "تم إغلاق تحضير رحلة العودة (الحد الأقصى: $lockTime).";
    }

    public function getCounters($students, $records)
    {
        $present = 0;
        $absent = 0;
        $total = $students->count();

        // Correct implementation:
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
