<?php

namespace App\Imports;

use App\Models\Region;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Wing;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements SkipsEmptyRows, SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use SkipsFailures;

    /**
     * دالة لتنظيف النصوص (إزالة المسافات وتصحيح التنسيق)
     */
    private function normalize($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // تحويل القيمة إلى نص أولاً (معالجة الأرقام من Excel)
        $value = (string) $value;

        // إزالة المسافات الزائدة
        return trim(preg_replace('/\s+/', ' ', $value));
    }

    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null;
        }

        // تنظيف القيم
        $wingName = $this->normalize($row['algnah'] ?? null);
        $regionName = $this->normalize($row['almntk'] ?? null);
        $teacherName = $this->normalize($row['almaalm'] ?? null);

        // البحث عن العلاقات
        $wing = Wing::where('Name', $wingName)->first();
        $region = $regionName ? Region::where('Name', $regionName)->first() : null;
        $teacher = $teacherName ? Teacher::where('Name', $teacherName)->first() : null;

        /*
        // خيار اختياري: إنشاء السجلات إذا لم تكن موجودة
        $wing   = Wing::firstOrCreate(['Name' => $wingName]);
        $region = $regionName ? Region::firstOrCreate(['Name' => $regionName]) : null;
        $teacher = $teacherName ? Teacher::firstOrCreate(['Name' => $teacherName]) : null;
        */

        return new Student([
            'Name' => $this->normalize($row['alasm'] ?? ''),
            'Grade' => $this->normalize($row['alsf'] ?? ''),
            'Sex' => $this->normalize($row['algns'] ?? '') ?: 'غير محدد', // قيمة افتراضية إذا كان فارغاً
            'Phone' => $this->normalize($row['alhatf'] ?? '') ?: 'غير محدد', // قيمة افتراضية إذا كان فارغاً
            'wing_id' => $wing?->id,
            'Division' => $this->normalize($row['alshaab'] ?? ''),
            'region_id' => $region?->id,
            'Stu_position' => $this->normalize($row['almokf'] ?? ''),
            'teacher_id' => $teacher?->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'alasm' => ['required'],
            'alsf' => ['required'],
            'algns' => ['nullable', Rule::in(['ذكر', 'انثى'])],
            'alhatf' => ['nullable'],
            'almokf' => ['nullable'],

            'algnah' => ['required', Rule::exists('wings', 'Name')],
            'almntk' => ['nullable', Rule::exists('regions', 'Name')],
            'almaalm' => ['nullable'],

            'alshaab' => ['required', Rule::in(['أ', 'ب', 'ج', 'د', 'ه', 'و', 'ز'])],
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'alasm' => 'الاسم',
            'alsf' => 'الصف',
            'algns' => 'الجنس',
            'alhatf' => 'الهاتف',
            'almokf' => 'الموقف',
            'algnah' => 'الجناح',
            'almntk' => 'المنطقة',
            'almaalm' => 'المعلم',
            'alshaab' => 'الشعبة',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'alasm.required' => 'الاسم مطلوب',
            'alsf.required' => 'الصف مطلوب',

            'algns.required' => 'الجنس مطلوب',
            'algns.in' => 'الجنس يجب أن يكون إما "ذكر" أو "أنثى". (القيمة المدخلة: :input)',


            'algnah.required' => 'الجناح مطلوب',
            'algnah.exists' => 'اسم الجناح غير صحيح',

            'almntk.exists' => 'اسم المنطقة غير صحيح',
            'almaalm.exists' => 'اسم المعلم غير صحيح',

            'alshaab.required' => 'الشعبة مطلوبة',
            'alshaab.in' => 'الشعبة يجب أن تكون إحدى القيم المسموحة. (القيمة المدخلة: :input)',
        ];
    }
}