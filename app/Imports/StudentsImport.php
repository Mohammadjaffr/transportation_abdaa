<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Maatwebsite\Excel\Concerns\WithChunkReading;
// use Maatwebsite\Excel\Concerns\WithBatchInserts;
// use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        @dd($row);
        return new Student([
            'Name'         => $row['الاسم'],
            'Grade'        => $row['الصف'],
            'Sex'          => $row['النوع'],
            'Phone'        => $row['الهاتف'],
            'wing_id'      => $row['الجناح'],
            'Division'     => $row['الشعبة'] ?? null,
            'region_id'    => $row['المنطقة'] ?? null,
            'Stu_position' => $row['الموقف'],
            'teacher_id'   => $row['المعلم\ة'] ?? null,
        ]);
    }

    // public function rules(): array
    // {
    //     return [
    //         'الاسم'   => 'required|string',
    //         'الصف'    => 'required|string',
    //         'النوع'   => 'required|string',
    //         'الهاتف'  => 'required|',
    //         'الموقف'  => 'required|string',
    //         'الجناح'  => 'required|exists:wings,id',
    //         'الشعبة'  => 'nullable|string',
    //         'المنطقة' => 'nullable|string',
    //         'المعلم\ة'  => 'nullable|string',
    //     ];
    // }

    // public function chunkSize(): int
    // {
    //     return 500;
    // }

    // public function batchSize(): int
    // {
    //     return 500;
    // }
}