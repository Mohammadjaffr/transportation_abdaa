<?php

namespace App\Exports;

use App\Models\preparationDriver;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PreparationDriverStuExport implements FromCollection,WithHeadings
{
    public function collection()
    {
        return PreparationDriver::with(['driver', 'region'])
            ->get()
            ->map(function ($item) {
                return [
                    'السائق'  => $item->driver?->Name ?? '-',
                    'المنطقة' => $item->region?->Name ?? '-',
                    'اسم بديل' => $item->Alternative_name ?? '-',
                    'التاريخ' => $item->Month,
                    'الحضور'  => $item->Atend ? 'حاضر' : 'غائب',
                ];
            });
    }

    public function headings(): array
    {
        return ['السائق', 'المنطقة', 'اسم بديل', 'التاريخ', 'الحضور'];
    }
}