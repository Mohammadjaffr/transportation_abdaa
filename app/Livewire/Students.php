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

class Students extends Component
{
    use WithFileUploads;

    public $students, $wings, $regions, $teachers;
    public $name, $grade, $sex, $phone,
        $stu_position, $wing_id, $division, $region_id, $teacher_id;
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

    public function mount()
    {
        $this->students = Student::with(['wing', 'region'])->get();
        $this->wings = Wing::all();
        $this->regions = Region::all();
        $this->teachers = Teacher::all();
    }

    // public function openImportModal()
    // {
    //     $this->resetErrorBag();
    //     $this->resetValidation();
    //     $this->excelFile = null;
    //     $this->showImportModal = true;
    // }

    public function closeImportModal()
    {
        $this->showImportModal = false;
    }

    public function importExcel()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new StudentsImport, $this->excelFile);

        // $this->excelFile = null;
        // $this->students = Student::with(['wing', 'region'])->get();
        $this->showImportModal = false;
        $this->reset('excelFile', 'showImportForm');

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم استيراد الطلاب بنجاح'
        ]);
    }

    public function resetImportForm()
    {
        $this->reset('excelFile', 'showImportForm');
    }




    public function createStudent()
    {
        $this->validate();

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
        ]);

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
        $this->validate();

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

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث الطالب بنجاح']);
    }

    public function confirmDelete($studentId)
    {
        $this->deleteId = $studentId;
    }

    public function deleteStudent()
    {
        if ($this->deleteId) {
            Student::find($this->deleteId)->delete();
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
            'selectedStudent',
            'showForm'
        ]);
        $this->primary_image = null;
    }

    public function render()
    {
        $this->students = Student::with(['wing', 'region'])
            ->where('Name', 'like', '%' . $this->search . '%')
            ->orWhere('id', 'like', '%' . $this->search . '%')
            ->get();

        $children_regions = $this->region_id
            ? Region::where('parent_id', $this->region_id)->get()
            : null;

        $parent_regions = Region::whereNull('parent_id')->get();

        return view('livewire.students', compact('children_regions', 'parent_regions'));
    }
}