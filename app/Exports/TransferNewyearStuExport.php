<?php

namespace App\Exports;

use App\Models\StudentRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransferNewyearStuExport implements FromCollection, WithHeadings
{
    protected $yearId;

    public function __construct($yearId)
    {
        $this->yearId = $yearId;
    }

    public function collection()
    {
        return StudentRecord::with(['student', 'wing', 'region', 'teacher', 'driver', 'schoolYear'])
            ->where('school_year_id', $this->yearId) // 🔍 فلترة حسب السنة
            ->get()
            ->map(function ($record) {
                return [
                    'ID'        => $record->student?->id,
                    'الاسم'      => $record->student?->Name,
                    'الصف'       => $record->Grade,
                    'الهاتف'     => $record->Phone,
                    'الحالة'     => $record->status,
                    'المعلم'     => $record->teacher?->Name,
                    'السائق'     => $record->driver?->Name,
                    'الجناح'     => $record->wing?->Name,
                    'المنطقة'    => $record->region?->Name,
                    'السنة'      => $record->schoolYear?->year,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'الاسم',
            'الصف',
            'الهاتف',
            'الحالة',
            'المعلم',
            'السائق',
            'الجناح',
            'المنطقة',
            'السنة',
        ];
    }
}
