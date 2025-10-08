<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Wing;
use App\Models\Region;
use App\Models\Teacher;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;
    

    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null;
        }

        return new Student([
            'Name'         => trim($row['alasm'] ?? ''),
            'Grade'        => trim($row['alsf'] ?? ''),
            'Sex'          => trim($row['algns'] ?? ''),
            'Phone'        => trim($row['alhatf'] ?? ''),
            'wing_id'      => Wing::where('Name', trim($row['algnah'] ?? ''))->value('id'),
            'Division'     => trim($row['alshaab'] ?? ''),
            'region_id'    => Region::where('Name', trim($row['almntk'] ?? ''))->value('id'),
            'Stu_position' => trim($row['almokf'] ?? ''),
            'teacher_id'   => Teacher::where('Name', trim($row['almaalm'] ?? ''))->value('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'alasm'     => ['required','string'],
            'alsf'      => ['required','string'],
            'algns'     => ['required','string', Rule::in(['ذكر','أنثى'])],
            'alhatf'    => ['required'],
            'almokf'    => ['required','string'],

            'algnah'    => ['required', Rule::exists('wings','Name')],
            'almntk'    => ['nullable', Rule::exists('regions','Name')],
            'almaalm'   => ['nullable', Rule::exists('teachers','Name')],

            'alshaab'   => ['required','string', Rule::in(['أ','ب','ج','د','ه','و','ز'])],
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'alasm'     => 'الاسم',
            'alsf'      => 'الصف',
            'algns'     => 'الجنس',
            'alhatf'    => 'الهاتف',
            'almokf'    => 'الموقف',
            'algnah'    => 'الجناح',
            'almntk'    => 'المنطقة',
            'almaalm'   => 'المعلم',
            'alshaab'   => 'الشعبة',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'alasm.required'     => 'الاسم مطلوب',
            'alsf.required'      => 'الصف مطلوب',

            'algns.required'     => 'الجنس مطلوب',
            'algns.in'           => 'الجنس يجب أن يكون إما "ذكر" أو "أنثى". (القيمة المدخلة: :input)',

            'alhatf.required'    => 'الهاتف مطلوب',
            'almokf.required'    => 'الموقف مطلوب',

            'algnah.required'    => 'الجناح مطلوب',
            'algnah.exists'      => 'اسم الجناح غير صحيح',

            'almntk.exists'      => 'اسم المنطقة غير صحيح',
            'almaalm.exists'     => 'اسم المعلم غير صحيح',

            'alshaab.required'   => 'الشعبة مطلوبة',
            'alshaab.in'         => 'الشعبة يجب أن تكون إما "أ" أو "ب" أو "ج". (القيمة المدخلة: :input)',
        ];
    }
}