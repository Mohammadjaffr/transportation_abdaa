<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\SchoolYear;
use App\Models\StudentRecord;
use App\Services\AdminLoggerService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransferNewyearStuExport;

class TransferNewyear extends Component
{
    public $confirming = false;
    public $studentsCount = 0;
    public $currentYear;
    public $newYear;
    public $filter = 'all';
    public $search = '';
    public $yearFilter = null;
    public $allYears = [];

    protected $listeners = ['transferStudents'];

    public function mount()
    {
        $this->currentYear = SchoolYear::where('is_current', true)->first();
        $this->newYear = SchoolYear::where('is_current', false)->orderBy('year', 'desc')->first();
        $this->allYears = SchoolYear::orderBy('year', 'desc')->get();

        if ($this->currentYear) {
            $this->studentsCount = StudentRecord::where('school_year_id', $this->currentYear->id)->count();
            $this->yearFilter = $this->currentYear->id;

            // 👇 التهيئة: لو ما فيه طلاب في student_records ننسخ من students
            if ($this->studentsCount == 0) {
                $students = Student::where('school_year_id', $this->currentYear->id)->get();
                foreach ($students as $student) {
                    StudentRecord::create([
                        'student_id'     => $student->id,
                        'school_year_id' => $this->currentYear->id,
                        'Grade'          => $student->Grade,
                        'status'         => $student->status ?? 'ناجح',
                        'Phone'          => $student->Phone,
                        'Stu_position'   => $student->Stu_position,
                        'teacher_id'     => $student->teacher_id,
                        'driver_id'      => $student->driver_id,
                        'region_id'      => $student->region_id,
                        'wing_id'        => $student->wing_id,
                    ]);
                }
                $this->studentsCount = StudentRecord::where('school_year_id', $this->currentYear->id)->count();
            }
        }
    }
    public function export()
    {
        $year = SchoolYear::find($this->yearFilter);
        $fileName = 'سجلات الطلاب - ' . ($year?->year ?? 'غير محدد') . '.xlsx';

        return Excel::download(
            new TransferNewyearStuExport($this->yearFilter),
            $fileName
        );
    }


    public function setYearFilter($yearId)
    {
        $this->yearFilter = $yearId;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function toggleFailed($recordId)
    {
        $record = StudentRecord::find($recordId);
        if (!$record) return;

        $record->status = $record->status === 'راسب' ? 'ناجح' : 'راسب';
        $record->save();
        AdminLoggerService::log('تحديث حالة طالب', 'StudentRecord', "تحديث حالة الطالب: {$record->student->Name} إلى {$record->status}");

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => "تم تحديث حالة الطالب: {$record->student->Name} إلى {$record->status}"
        ]);
    }

    public function confirmTransfer()
    {
        $this->dispatch('show-confirm-transfer');
    }

    private function getNextGrade($currentGrade)
    {
        $grades = [
            'الأول',
            'الثاني',
            'الثالث',
            'الرابع',
            'الخامس',
            'السادس',
            'السابع',
            'الثامن',
            'التاسع',
            'اول ثانوي',
            'ثاني ثانوي',
            'ثالث ثانوي'
        ];

        $currentGrade = trim(mb_strtolower($currentGrade));
        $gradesNormalized = array_map(fn($g) => mb_strtolower($g), $grades);

        $index = array_search($currentGrade, $gradesNormalized);

        if ($index !== false && $index < count($grades) - 1) {
            return $grades[$index + 1];
        }

        return $currentGrade;
    }

    public function transferStudents()
    {
        if (!$this->currentYear) {
            session()->flash('error', 'لا توجد سنة حالية في قاعدة البيانات.');
            return;
        }

        $this->newYear = SchoolYear::create([
            'year' => $this->currentYear->year + 1,
            'is_current' => false,
        ]);

        $records = StudentRecord::where('school_year_id', $this->currentYear->id)->get();

        foreach ($records as $record) {
            if ($record->Grade === 'ثالث ثانوي') {
                continue;
            }

            StudentRecord::create([
                'student_id'     => $record->student_id,
                'school_year_id' => $this->newYear->id,
                'Grade'          => $this->getNextGrade($record->Grade),
                'status'         => 'ناجح',
                'Phone'          => $record->Phone,
                'Stu_position'   => $record->Stu_position,
                'teacher_id'     => $record->teacher_id,
                'driver_id'      => $record->driver_id,
                'region_id'      => $record->region_id,
                'wing_id'        => $record->wing_id,
            ]);
        }

        $this->currentYear->update(['is_current' => false]);
        $this->newYear->update(['is_current' => true]);

        $this->currentYear = $this->newYear;
        AdminLoggerService::log('تم نقل الطلاب إلى السنة الجديدة', 'StudentRecord', "تم نقل الطلاب الى سنة جديده");

        session()->flash('success', '✅ تم النقل وتخزين الطلاب في سجل السنة الجديدة.');
    }
public function getSelectedYearProperty()
{
    return SchoolYear::find($this->yearFilter);
}


    public function render()
    {
        $students = StudentRecord::with(['student', 'wing', 'region', 'teacher', 'driver', 'schoolYear'])
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->whereHas('student', function ($q) use ($searchTerm) {
                    $q->where('Name', 'like', $searchTerm)
                        ->orWhere('id', 'like', $searchTerm)
                        ->orWhere('Phone', 'like', $searchTerm)
                        ->orWhere('Stu_position', 'like', $searchTerm)
                        ->orWhere('Grade', 'like', $searchTerm)
                        ->orWhere('Division', 'like', $searchTerm);
                });
            })
            ->when($this->yearFilter, fn($q) => $q->where('school_year_id', $this->yearFilter))
            ->when($this->filter === 'failed', fn($q) => $q->where('status', 'راسب'))
            ->when($this->filter === 'graduated', fn($q) =>
            $q->where('Grade', 'ثالث ثانوي')->where('status', 'ناجح'))
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.transfer-newyear', [
            'students' => $students,
            'currentYear' => $this->currentYear,
            'newYear' => $this->newYear,
            'filter' => $this->filter,
        ]);
    }
}