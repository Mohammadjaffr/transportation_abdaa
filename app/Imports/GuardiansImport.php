<?php

namespace App\Imports;

use App\Models\Guardian;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuardiansImport implements SkipsEmptyRows, SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
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

        return new Guardian([
            'name' => $this->normalize($row['alasm'] ?? ''),
            'phone' => $this->normalize($row['rkm_alhatf'] ?? ''),
            'national_id' => $this->normalize($row['rkm_alhoaa'] ?? ''),
            'address' => $this->normalize($row['alaanoan'] ?? ''),
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'alasm' => ['required', 'string', 'max:150'],
            'rkm_alhatf' => ['required', 'string', 'max:20'],
            'rkm_alhoaa' => ['nullable', 'string', 'max:50'],
            'alaanoan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'alasm' => 'الاسم',
            'rkm_alhatf' => 'رقم الهاتف',
            'rkm_alhoaa' => 'رقم الهوية',
            'alaanoan' => 'العنوان',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'alasm.required' => 'الاسم مطلوب',
            'rkm_alhatf.required' => 'رقم الهاتف مطلوب',
        ];
    }
}
