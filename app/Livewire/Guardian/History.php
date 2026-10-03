<?php

namespace App\Livewire\Guardian;

use Livewire\Component;

use App\Models\PreparationStu;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

class History extends Component
{
    public $selectedStudentId = null;

    public $month;


    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount()
    {
        /*
         * الشهر الحالي
         */
        $this->month =
            Carbon::now()->format('Y-m');


        /*
         * ولي الأمر الحالي
         */
        $guardian =
            Auth::user()->guardian;


        /*
         * نختار أول ابن تلقائيًا
         */
        $firstStudent =
            $guardian
                ->students()
                ->orderBy('Name')
                ->first();


        $this->selectedStudentId =
            $firstStudent?->id;
    }


    /*
    |--------------------------------------------------------------------------
    | عند تغيير الطالب
    |--------------------------------------------------------------------------
    */

    public function updatedSelectedStudentId($value)
    {
        /*
         * لا يوجد طالب محدد
         */
        if (empty($value)) {

            $this->selectedStudentId = null;

            return;
        }


        /*
         * مهم جدًا:
         * نتأكد أن الطالب من أبناء ولي الأمر الحالي.
         */
        $guardian =
            Auth::user()->guardian;


        $belongsToGuardian =
            $guardian
                ->students()
                ->whereKey(
                    (int) $value
                )
                ->exists();


        if (!$belongsToGuardian) {

            abort(
                403,
                'لا تملك صلاحية الوصول إلى بيانات هذا الطالب.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | عند تغيير الشهر
    |--------------------------------------------------------------------------
    */

    public function updatedMonth($value)
    {
        /*
         * حماية من أي قيمة شهر غير صحيحة
         */
        try {

            Carbon::createFromFormat(
                'Y-m',
                $value
            );

        } catch (\Throwable $e) {

            $this->month =
                Carbon::now()->format('Y-m');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $guardian =
            Auth::user()->guardian;


        /*
        |--------------------------------------------------------------------------
        | أبناء ولي الأمر فقط
        |--------------------------------------------------------------------------
        */

        $students =
            $guardian
                ->students()
                ->orderBy('Name')
                ->get();


        $student = null;

        $records = collect();


        /*
         * الإحصائيات الافتراضية
         */
        $stats = [

            'total' => 0,

            'present' => 0,

            'absent' => 0,

            'morning' => 0,

            'leave' => 0,
        ];


        if ($this->selectedStudentId) {

            /*
            |--------------------------------------------------------------------------
            | الحماية الأساسية
            |--------------------------------------------------------------------------
            |
            | لا نستخدم:
            |
            | Student::findOrFail(...)
            |
            | بل نبحث من داخل علاقة ولي الأمر.
            |
            */

            $student =
                $guardian
                    ->students()
                    ->whereKey(
                        (int)
                        $this->selectedStudentId
                    )
                    ->first();


            /*
             * لو حاول شخص تغيير ID يدويًا
             */
            if (!$student) {

                abort(
                    403,
                    'لا تملك صلاحية الوصول إلى بيانات هذا الطالب.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | الفترة الزمنية
            |--------------------------------------------------------------------------
            */

            try {

                $date =
                    Carbon::createFromFormat(
                        'Y-m',
                        $this->month
                    );

            } catch (\Throwable $e) {

                $date =
                    Carbon::now();

                $this->month =
                    $date->format('Y-m');
            }


            $startDate =
                $date
                    ->copy()
                    ->startOfMonth()
                    ->toDateString();


            $endDate =
                $date
                    ->copy()
                    ->endOfMonth()
                    ->toDateString();


            /*
            |--------------------------------------------------------------------------
            | سجل الطالب
            |--------------------------------------------------------------------------
            */

            $records =
                PreparationStu::with([
                    'driver',
                    'region',
                ])

                    ->where(
                        'student_id',
                        $student->id
                    )

                    ->whereBetween(
                        'Date',
                        [
                            $startDate,
                            $endDate,
                        ]
                    )

                    ->orderByDesc(
                        'Date'
                    )

                    ->orderBy(
                        'type'
                    )

                    ->get();


            /*
            |--------------------------------------------------------------------------
            | الإحصائيات
            |--------------------------------------------------------------------------
            */

            $stats['total'] =
                $records->count();


            $stats['present'] =
                $records
                    ->filter(
                        fn ($record) =>
                            (bool)
                            $record->Atend
                    )
                    ->count();


            $stats['absent'] =
                $records
                    ->filter(
                        fn ($record) =>
                            !(bool)
                            $record->Atend
                    )
                    ->count();


            $stats['morning'] =
                $records
                    ->where(
                        'type',
                        'morning'
                    )
                    ->count();


            $stats['leave'] =
                $records
                    ->where(
                        'type',
                        'leave'
                    )
                    ->count();
        }


        return view(
            'livewire.guardian.history',
            [
                'guardian' => $guardian,

                'students' => $students,

                'student' => $student,

                'records' => $records,

                'stats' => $stats,
            ]
        )
        ->layout(
            'layouts.guardian'
        );
    }
}