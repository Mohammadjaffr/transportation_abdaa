<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation
{
    public function model(array $row)
    {
        return new Student([
            'Name'         => $row['الاسم'],
            'Grade'        => $row['الصف'],
            'Sex'          => $row['النوع'],
            'Phone'        => $row['الهاتف'],
            'Stu_position' => $row['الموقف'],
            'wing_id'      => $row['الجناح'],
            'Division'     => $row['الشعبة'] ?? null,
            'region_id'    => $row['المنطقة'] ?? null,
            'teacher_id'   => $row['المعلم'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'الاسم'   => 'required|string',
            'الصف'    => 'required|string',
            'النوع'   => 'required|string',
            'الهاتف'  => 'required|',
            'الموقف'  => 'required|string',
            'الجناح'  => 'required|exists:wings,id',
            'الشعبة'  => 'nullable|string',
            'المنطقة' => 'nullable|string',
            'المعلم'  => 'nullable|string',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }
}