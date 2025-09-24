<?php
namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Student::with(['wing','region','teacher'])->get();
    }

    public function map($student): array
    {
        return [
            $student->Name,
            $student->Grade,
            $student->Sex,
            $student->Phone,
            $student->wing?->Name,
            $student->Division,
            $student->region?->Name,
            $student->Stu_position,
            $student->teacher?->Name,
        ];
    }

    public function headings(): array
    {
        return [
            'الاسم',
            'الصف',
            'النوع',
            'الهاتف',
            'الجناح',
            'الشعبة',
            'المنطقة',
            'الموقف',
            'المعلم/ة',
        ];
    }
}