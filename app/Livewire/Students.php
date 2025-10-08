<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Student;
use App\Models\Wing;
use App\Models\Region;
use App\Models\Teacher;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Exports\StudentsExport;
use Livewire\WithPagination;
use App\Services\AdminLoggerService;
use App\Models\SchoolYear;

class Students extends Component
{

    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public  $wings, $regions, $teachers;
    public $name, $grade, $sex, $phone,
        $stu_position, $wing_id, $division, $region_id, $teacher_id, $deleteName;
    public $editMode = false, $selectedStudentId, $editId = null;

    public $primary_image;
    public $showForm = false;
    public $deleteId = null;
    public $search = '';
    public $child_region_id;
    public $showImportForm = false;
    public $excelFile;

    public $showImportModal = false;

    protected $rules = [
        'name' => 'required|string|max:200',
        'grade' => 'required|string|max:30',
        'sex' => 'required|string|max:20',
        'phone' => 'required|string|max:20',
        'child_region_id' => 'required|string|max:200',
        'wing_id' => 'required|exists:wings,id',
        'division' => 'required|string|max:20',
        'region_id' => 'required|exists:regions,id',
        'teacher_id' => 'nullable|exists:teachers,id',
    ];
    protected $messages = [
        'name.required' => 'يرجى إدخال اسم الطالب',
        'grade.required' => 'يرجى إدخال الصف',
        'sex.required' => 'يرجى إدخال النوع',
        'phone.required' => 'يرجى إدخال رقم الهاتف',
        'child_region_id.required' => 'يرجى إدخال المنطقة',
        'wing_id.required' => 'يرجى إدخال الجناح',
        'division.required' => 'يرجى إدخال الشعبة',
        'region_id.required' => 'يرجى إدخال المنطقة',
    ];

    public function mount()
    {
        $this->wings = Wing::all();
        $this->regions = Region::all();
        $this->teachers = Teacher::all();
    }



    public function closeImportModal()
    {
        $this->showImportModal = false;
    }

    public function importExcel()
    {
        $this->validate(
            [
                'excelFile' => 'required|mimes:xlsx,csv',
            ],
            [
                'excelFile.required' => 'يرجى اختيار ملف Excel',
                'excelFile.mimes'    => 'يجب أن يكون الملف بصيغة Excel (xlsx) أو CSV فقط',
            ]
        );

        $import = new StudentsImport();
        Excel::import($import, $this->excelFile->getRealPath());

        if ($import->failures()->isNotEmpty()) {
            $labels = [
                'name' => 'عمود الاسم ',
                'grad' => 'عمود الصف',
                'sex' => 'عمود النوع',
                'phone' => 'عمود الهاتف',
                'stu_position' => 'عمود الموقف',
                'wing' => 'عمود الجناح',
                'region' => 'عمود المنطقة',
                'teacher' => 'عمود المعلم',
                'division' => 'عمود الشعبة',
            ];

            foreach ($import->failures() as $failure) {
                $row    = $failure->row();
                $attr   = $failure->attribute();
                $value  = $failure->values()[$attr] ?? '';
                $label  = $labels[$attr] ?? $attr;

                foreach ($failure->errors() as $msg) {
                    $pretty = "الصف {$row} – {$label}: {$msg}" . ($value !== '' ? " (القيمة: {$value})" : '');
                    $this->addError('excelFile', $pretty);
                }
            }

            return;
        }

        $fileName = $this->excelFile->getClientOriginalName();
        AdminLoggerService::log('استيراد ملف Excel لطلاب', 'Student', "تم استيراد الطلاب من الملف: {$fileName}");

        $this->reset('excelFile', 'showImportForm');
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم استيراد الطلاب بنجاح']);
    }


    public function exportExcel()
    {
        return Excel::download(new StudentsExport, 'كشف الطلاب.xlsx');
    }

    public function resetImportForm()
    {
        $this->reset('excelFile', 'showImportForm');
    }




    public function createStudent()
    {
        $this->validate();
        $year = SchoolYear::where('is_current', true)->first();
        Student::create([
            'Name' => $this->name,
            'Grade' => $this->grade,
            'Sex' => $this->sex,
            'Phone' => $this->phone,
            'Stu_position' => $this->child_region_id,
            'wing_id' => $this->wing_id,
            'Division' => $this->division,
            'region_id' => $this->region_id,
            'teacher_id' => $this->teacher_id,
            'school_year_id' => $year->id,
        ]);
        AdminLoggerService::log('اضافة طالب', 'Student', "إضافة طالب جديد: {$this->name}");

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم إضافة الطالب بنجاح']);
    }

    public function editStudent($id)
    {
        $student = Student::findOrFail($id);
        $this->selectedStudentId = $student->id;
        $this->editMode = true;
        $this->showForm = true;

        $this->editId = $student->id;
        $this->name = $student->Name;
        $this->grade = $student->Grade;
        $this->sex = $student->Sex;
        $this->phone = $student->Phone;
        $this->stu_position = $student->Stu_position;
        $this->wing_id = $student->wing_id;
        $this->division = $student->Division;
        $this->region_id = $student->region_id;
        $this->child_region_id = $student->Stu_position;
        $this->teacher_id = $student->teacher_id;
    }

    public function updateStudent()
    {
        // $this->validate();

        $student = Student::findOrFail($this->selectedStudentId);
        $student->update([
            'Name' => $this->name,
            'Grade' => $this->grade,
            'Sex' => $this->sex,
            'Phone' => $this->phone,
            'Stu_position' => $this->child_region_id,
            'wing_id' => $this->wing_id,
            'Division' => $this->division,
            'region_id' => $this->region_id,
            'teacher_id' => $this->teacher_id ?: null,
        ]);
        AdminLoggerService::log('تحديث طالب', 'Student', "تحديث طالب: {$this->name}");

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث الطالب بنجاح']);
    }

    public function confirmDelete($studentId)
    {
        $student = Student::find($studentId);
        $this->deleteId = $studentId;
        $this->deleteName = $student->Name;
    }

    public function deleteStudent()
    {
        if ($this->deleteId) {
            Student::find($this->deleteId)->delete();
            AdminLoggerService::log('حذف طالب', 'Student', "حذف طالب: {$this->deleteName}");

            $this->deleteId = null;
            $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حذف الطالب بنجاح']);
        }
    }

    public function resetForm()
    {
        $this->reset([
            'editId',
            'name',
            'grade',
            'sex',
            'phone',
            'stu_position',
            'wing_id',
            'division',
            'region_id',
            'child_region_id',
            'teacher_id',
            'editMode',
            'showForm'
        ]);
        $this->primary_image = null;
    }

    public function render()
    {
        $students = Student::with(['wing', 'region', 'teacher', 'driver']) // ✅ أضفت driver هنا
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';

                $query->where(function ($q) use ($searchTerm) {
                    $q->where('Name', 'like', $searchTerm)
                        ->orWhere('id', 'like', $searchTerm)
                        ->orWhere('Phone', 'like', $searchTerm)
                        ->orWhere('Stu_position', 'like', $searchTerm)
                        ->orWhere('Grade', 'like', $searchTerm)
                        ->orWhere('Division', 'like', $searchTerm);
                })
                    ->orWhereHas('region', function ($q) use ($searchTerm) {
                        $q->where('Name', 'like', $searchTerm);
                    })
                    ->orWhereHas('driver', function ($q) use ($searchTerm) {
                        $q->where('Name', 'like', $searchTerm);
                    })
                    ->orWhereHas('teacher', function ($q) use ($searchTerm) {
                        $q->where('Name', 'like', $searchTerm);
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);




        $children_regions = $this->region_id
            ? Region::where('parent_id', $this->region_id)->get()
            : null;

        $parent_regions = Region::whereNull('parent_id')->get();

        return view('livewire.students', compact('students', 'children_regions', 'parent_regions'));
    }
}