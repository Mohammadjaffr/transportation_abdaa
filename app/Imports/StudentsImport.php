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

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        return new Student([
            'Name'         => $row['name'],
            'Grade'        => $row['grad'],
            'Sex'          => $row['sex'],
            'Phone'        => $row['phone'],
            'wing_id'      => Wing::where('Name', trim($row['wing'] ?? ''))->value('id'),
            'Division'     => $row['division'] ?? null,
            'region_id'    => Region::where('Name', trim($row['region'] ?? ''))->value('id'),
            'Stu_position' => $row['stu_position'],
            'teacher_id'   => Teacher::where('Name', trim($row['teacher'] ?? ''))->value('id'),
        ]);
    }

   public function rules(): array
{
    return [
        'name'         => ['required','string'],
        'grad'         => ['required','string'],
        'sex'          => ['required','string', Rule::in(['ذكر','أنثى'])],   
        'phone'        => ['required'],
        'stu_position' => ['required','string'],

        'wing'   => ['required', Rule::exists('wings','Name')],
        'region' => ['nullable', Rule::exists('regions','Name')],
        'teacher'=> ['nullable', Rule::exists('teachers','Name')],

        'division'     => ['required','string', Rule::in(['أ','ب','ج'])],   
    ];
}


    public function customValidationAttributes()
    {
        return [
        'name.required'         => 'الاسم مطلوب',
        'grad.required'         => 'الصف مطلوب',
        'sex.required'          => 'النوع مطلوب',
        'sex.in'                => 'النوع يجب أن يكون إما "ذكر" أو "أنثى"',
        'phone.required'        => 'الهاتف مطلوب',
        'stu_position.required' => 'الموقف مطلوب',

        'wing.required'         => 'الجناح مطلوب',
        'wing.exists'           => 'اسم الجناح غير صحيح',
        'region.exists'         => 'اسم المنطقة غير صحيح',
        'teacher.exists'        => 'اسم المعلم غير صحيح',

        'division.required'     => 'الشعبة مطلوبة',
        'division.in'           => 'الشعبة يجب أن تكون إما "أ" أو "ب" أو "ج"',
    ];
    }

    public function customValidationMessages()
    {
          return [
        'name.required'         => 'الاسم مطلوب',
        'grad.required'         => 'الصف مطلوب',

        'sex.required'          => 'النوع مطلوب',
        'sex.in'                => 'النوع يجب أن يكون إما "ذكر" أو "أنثى". (القيمة المدخلة: :input)',

        'phone.required'        => 'الهاتف مطلوب',
        'stu_position.required' => 'الموقف مطلوب',

        'wing.required'         => 'الجناح مطلوب',
        'wing.exists'           => 'اسم الجناح غير صحيح',

        'region.exists'         => 'اسم المنطقة غير صحيح',
        'teacher.exists'        => 'اسم المعلم غير صحيح',

        'division.required'     => 'الشعبة مطلوبة',
        'division.in'           => 'الشعبة يجب أن تكون إما "أ" أو "ب" أو "ج". (القيمة المدخلة: :input)',
    ];
    }
}