<?php

namespace App\Exports;

use App\Models\PreparationStu;
use App\Models\Driver;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DriverReportExport implements FromCollection, WithHeadings
{
    protected $driverId;
    protected $date;

    public function __construct($driverId, $date)
    {
        $this->driverId = $driverId;
        $this->date     = $date;
    }

    public function collection()
    {
        $driver   = Driver::findOrFail($this->driverId);
        $students = $driver->students;

        $data = [];
        $countMorningPresent = 0;
        $countMorningAbsent  = 0;
        $countLeavePresent   = 0;
        $countLeaveAbsent    = 0;

        foreach ($students as $stu) {
            $morning = PreparationStu::where('student_id',$stu->id)
                        ->where('Date',$this->date)
                        ->where('type','morning')
                        ->first();

            $leave   = PreparationStu::where('student_id',$stu->id)
                        ->where('Date',$this->date)
                        ->where('type','leave')
                        ->first();

            // قيم الحالة
            $morningStatus = $morning ? ($morning->Atend ? 'حاضر' : 'غائب') : 'لم يسجل';
            $leaveStatus   = $leave ? ($leave->Atend ? 'حاضر' : 'غائب') : 'لم يسجل';

            // إحصائيات
            if ($morning) {
                $morning->Atend ? $countMorningPresent++ : $countMorningAbsent++;
            }
            if ($leave) {
                $leave->Atend ? $countLeavePresent++ : $countLeaveAbsent++;
            }

            $data[] = [
                'student' => $stu->Name,
                'driver'  => $driver->Name,
                'date'    => $this->date,
                'morning' => $morningStatus,
                'leave'   => $leaveStatus,
            ];
        }

        // إضافة الإحصائيات كصفوف إضافية
        $data[] = [
            'student' => '---',
            'driver'  => 'الإحصائيات',
            'date'    => $this->date,
            'morning' => "حضور: $countMorningPresent / غياب: $countMorningAbsent",
            'leave'   => "حضور: $countLeavePresent / غياب: $countLeaveAbsent",
        ];

        return collect($data);
    }

    public function headings(): array
    {
        return ['الطالب', 'السائق', 'التاريخ', 'الصباحي', 'الانصراف'];
    }
}