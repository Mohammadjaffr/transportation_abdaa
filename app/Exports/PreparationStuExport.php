<?php

namespace App\Exports;

use App\Models\PreparationStu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PreparationStuExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return PreparationStu::with(['student', 'driver', 'region'])
            ->get()
            ->map(function ($item) {
                return [
                    'الطالب'   => $item->student?->Name ?? '-',
                    'السائق'   => $item->driver?->Name ?? '-',
                    'المنطقة'  => $item->region?->Name ?? '-',
                    'التاريخ'  => $item->Year,
                    'الحضور'   => $item->Atend ? 'حاضر' : 'غائب',
                ];
            });
    }

    public function headings(): array
    {
        return ['الطالب', 'السائق', 'المنطقة', 'التاريخ', 'الحضور'];
    }
}