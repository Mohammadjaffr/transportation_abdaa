<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DistributedStudentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Student::with(['region', 'driver'])
            ->whereNotNull('region_id')
            ->whereNotNull('driver_id')
            ->whereNotNull('Stu_position')
            ->get()
            ->map(function ($student) {
                return [
                    'الاسم' => $student->Name,
                    'المنطقة' => $student->region?->Name ?? 'غير محدد',
                    'السائق' => $student->driver?->Name ?? 'غير محدد',
                    'الموقف' => $student->Stu_position,
                ];
            });
    }

    public function headings(): array
    {
        return ['الاسم', 'المنطقة', 'السائق', 'الموقف'];
    }
}