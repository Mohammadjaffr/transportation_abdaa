<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use App\Models\Student;
use App\Services\Driver\DriverAttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Attendance extends Component
{
    public $type;
    public $search = '';
    public $date;

    /*
    |--------------------------------------------------------------------------
    | الحالات المؤقتة
    |--------------------------------------------------------------------------
    | true  = حاضر
    | false = غائب
    | null  = لم يتم تحديد الحالة
    |
    | هذه البيانات داخل Livewire فقط. لا تحفظ في قاعدة البيانات في هذه المرحلة.
    */
    public array $attendance = [];

    // Track unsaved changes
    public bool $hasUnsavedChanges = false;

    public function mount($type = 'morning')
    {
        $this->type = in_array($type, ['morning', 'leave']) ? $type : 'morning';
        $this->date = Carbon::today()->toDateString();

        // عند فتح الصفحة: إذا كان هناك تحضير محفوظ سابقًا نعرضه داخل الحالة المؤقتة.
        $this->loadAttendanceDraft();
    }

    /*
    |--------------------------------------------------------------------------
    | تحميل التحضير الحالي إلى Livewire
    |--------------------------------------------------------------------------
    */
    private function loadAttendanceDraft(): void
    {
        $driverId = Auth::user()->driver_id;
        $service = app(DriverAttendanceService::class);

        $students = $service->getStudents($driverId);
        $records = $service->getAttendanceRecords($driverId, $this->date, $this->type);

        $this->attendance = [];

        foreach ($students as $student) {
            if ($records->has($student->id)) {
                // تحضير محفوظ سابقًا
                $this->attendance[$student->id] = (bool) $records[$student->id]->Atend;
            } else {
                // لم يتم تحضير الطالب بعد
                $this->attendance[$student->id] = null;
            }
        }
        $this->hasUnsavedChanges = false;
    }

    /*
    |--------------------------------------------------------------------------
    | تحديد حاضر / غائب
    |--------------------------------------------------------------------------
    | لا يوجد حفظ في قاعدة البيانات هنا.
    */
    public function setAttendance(DriverAttendanceService $service, $studentId, $status)
    {
        // التحقق من فترة التحضير
        if ($service->isLocked($this->type, $this->date)) {
            $this->dispatch('show-toast', [
                'type'    => 'error',
                'message' => $service->getLockMessage($this->type),
            ]);
            return;
        }

        $driverId = Auth::user()->driver_id;

        // حماية: الطالب يجب أن يكون تابعًا للسائق الحالي
        $studentExists = Student::where('id', $studentId)
            ->where('driver_id', $driverId)
            ->exists();

        if (!$studentExists) {
            abort(403, 'لا تملك صلاحية تحضير هذا الطالب.');
        }

        // تحويل القيمة إلى Boolean
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($status === null) {
            return;
        }

        // فقط تغيير Livewire. (لا DB ولا SMS)
        $this->attendance[(int) $studentId] = $status;
        $this->hasUnsavedChanges = true;
    }

    /*
    |--------------------------------------------------------------------------
    | حاضر الكل
    |--------------------------------------------------------------------------
    | أصبح تحديدًا مؤقتًا فقط.
    */
    public function prepareAllPresent(DriverAttendanceService $service)
    {
        if ($service->isLocked($this->type, $this->date)) {
            $this->dispatch('show-toast', [
                'type'    => 'error',
                'message' => $service->getLockMessage($this->type),
            ]);
            return;
        }

        $driverId = Auth::user()->driver_id;

        // جميع طلاب السائق (لا نعتمد على البحث لأن "حاضر الكل" يشمل الجميع)
        $students = $service->getStudents($driverId);

        foreach ($students as $student) {
            $this->attendance[$student->id] = true;
        }
        $this->hasUnsavedChanges = true;

        $this->dispatch('show-toast', [
            'type'    => 'success',
            'message' => 'تم تحديد جميع الطلاب كحاضرين مؤقتًا. لم يتم حفظ التحضير بعد.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | اعتماد التحضير
    |--------------------------------------------------------------------------
    */
    public function submitAttendance(DriverAttendanceService $service)
    {
        if ($service->isLocked($this->type, $this->date)) {
            $this->dispatch('show-toast', [
                'type'    => 'error',
                'message' => $service->getLockMessage($this->type),
            ]);
            return;
        }

        $driverId = Auth::user()->driver_id;
        $students = $service->getStudents($driverId);

        // Check for pending students
        $counters = $this->getDraftCounters($students);
        if ($counters['pending'] > 0) {
            $this->dispatch('show-toast', [
                'type'    => 'error',
                'message' => "يوجد {$counters['pending']} طالب لم يتم تحديد حالته.",
            ]);
            return;
        }

        // Save batch
        $saved = $service->saveBatchAttendance(
            $driverId, 
            $students, 
            $this->attendance, 
            $this->date, 
            $this->type
        );

        if ($saved) {
            $this->hasUnsavedChanges = false;
            // Optionally count absences for the success message
            $absences = $counters['absent'];
            $msg = 'تم اعتماد التحضير وحفظه بنجاح.';
            if ($absences > 0 && config('attendance.sms_enabled', true)) {
                $msg = "تم اعتماد التحضير. الغياب: {$absences}، تم معالجة التنبيهات.";
            }

            $this->dispatch('show-toast', [
                'type'    => 'success',
                'message' => $msg,
            ]);
        } else {
            $this->dispatch('show-toast', [
                'type'    => 'error',
                'message' => 'حدث خطأ أثناء حفظ التحضير.',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | عدادات الحالة المؤقتة
    |--------------------------------------------------------------------------
    */
    private function getDraftCounters($students): array
    {
        $present = 0;
        $absent  = 0;
        $pending = 0;

        foreach ($students as $student) {
            $status = $this->attendance[$student->id] ?? null;

            if ($status === true) {
                $present++;
            } elseif ($status === false) {
                $absent++;
            } else {
                $pending++;
            }
        }

        return [
            'total'   => $students->count(),
            'present' => $present,
            'absent'  => $absent,
            'pending' => $pending,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    public function render(DriverAttendanceService $service)
    {
        $driverId = Auth::user()->driver_id;

        // جميع الطلاب للعدادات
        $allStudents = $service->getStudents($driverId);

        // الطلاب الظاهرون حسب البحث
        $students = $service->getStudents($driverId, $this->search);

        // العدادات من Livewire الآن، وليس من قاعدة البيانات
        $counters = $this->getDraftCounters($allStudents);

        $isLocked = $service->isLocked($this->type, $this->date);
        $lockMessage = $isLocked ? $service->getLockMessage($this->type) : null;

        $studentsList = [];

        foreach ($students as $student) {
            $studentsList[] = [
                'id'     => $student->id,
                'name'   => $student->Name,
                'region' => $student->region->Name ?? '',
                'status' => $this->attendance[$student->id] ?? null,
            ];
        }

        return view('livewire.driver.attendance', [
            'studentsList' => $studentsList,
            'counters'     => $counters,
            'isLocked'     => $isLocked,
            'lockMessage'  => $lockMessage,
        ])->layout('layouts.driver');
    }
}
