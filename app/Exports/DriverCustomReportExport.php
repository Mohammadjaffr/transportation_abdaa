<?php

namespace App\Exports;

use App\Models\PreparationStu;
use App\Models\Driver;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DriverCustomReportExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $driverId;
    protected $from;
    protected $to;
    protected $showNames;

    public function __construct($driverId, $from, $to, $showNames = false)
    {
        $this->driverId  = $driverId;
        $this->from      = $from;
        $this->to        = $to;
        $this->showNames = (bool) $showNames;
    }

    public function title(): string
    {
        $driver = Driver::find($this->driverId);
        return 'تقرير ' . ($driver?->Name ?? $this->driverId);
    }

    public function headings(): array
    {
        if ($this->showNames) {
            return ['السائق', 'الفترة من', 'الفترة إلى', 'اسم الطالب الغائب', 'التاريخ'];
        }

        return ['السائق', 'الفترة من', 'الفترة إلى', 'إجمالي الحضور', 'إجمالي الغياب'];
    }

    public function collection()
    {
        $driver = Driver::find($this->driverId);

        $presentCount = PreparationStu::where('driver_id', $this->driverId)
            ->whereBetween('Date', [$this->from, $this->to])
            ->whereIn('type', ['morning', 'leave'])
            ->where('Atend', true)
            ->count();

        $absentQuery = PreparationStu::with('student:id,Name')
            ->where('driver_id', $this->driverId)
            ->whereBetween('Date', [$this->from, $this->to])
            ->whereIn('type', ['morning', 'leave'])
            ->where('Atend', false);

        $absentCount = (clone $absentQuery)->count();

        if ($this->showNames) {
            $rows = new Collection();

            $absentees = $absentQuery->orderBy('Date')->get(['student_id', 'Date']);
            foreach ($absentees as $row) {
                $rows->push([
                    $driver?->Name,
                    $this->from,
                    $this->to,
                    optional($row->student)->Name,
                    $row->Date,
                ]);
            }

            if ($rows->isEmpty()) {
                $rows->push([
                    $driver?->Name,
                    $this->from,
                    $this->to,
                    'لا يوجد غياب',
                    '-',
                ]);
            }

            return $rows;
        }

        return collect([[
            $driver?->Name,
            $this->from,
            $this->to,
            $presentCount,
            $absentCount,
        ]]);
    }
}