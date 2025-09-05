<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\Bus;

class Students extends Component
{
    public $students, $buses;
    public $name, $grade, $bus_id;
    public $editMode = false, $selectedStudent;
    public $showForm = false;
    public $deleteId = null;
    public $search = '';
    public $selectedStudentId;
     public $editId=null;
    protected $rules = [
        'name' => 'required|string|max:200',
        'grade' => 'required|string|max:30',
        'bus_id' => 'required|exists:buses,id'
    ];
    protected $messages = [
    

        'name.required' => 'اسم الطالب مطلوب',
        'name.string'   => 'اسم الطالب يجب أن يكون نصًا',
        'name.max'      => 'اسم الطالب لا يجب أن يتجاوز 200 حرف',
        'grade.required' => 'الصف مطلوب',
        'grade.string'  => 'الصف يجب أن يكون نصًا',
        'grade.max'     => 'الصف لا يجب أن يتجاوز 30 حرف',

        'bus_id.required' => 'الباص مطلوب',
        'bus_id.exists'   => 'رقم الباص غير صحيح',
    ];

    public function mount()
    {
        $this->students = Student::with('bus')->get();
        $this->buses = Bus::all();
    }

    public function createStudent()
    {
        $this->validate();

        Student::create([
            'Name' => $this->name,
            'Grade' => $this->grade,
            'bus_id' => $this->bus_id
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
        $this->bus_id = $student->bus_id;
    }
    public function updateStudent()
    {
        $this->validate([
            'name' => 'required|string|max:200',
            'grade' => 'required|string|max:30',
            'bus_id' => 'required|exists:buses,id'
        ]);

        $student = Student::findOrFail($this->selectedStudentId);
        $student->update([
            'Name' => $this->name,
            'Grade' => $this->grade,
            'bus_id' => $this->bus_id,
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

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['editId','name', 'grade', 'bus_id', 'editMode', 'selectedStudent', 'showForm']);
        $this->editId = null;
    }

    public function render()
    {
        $this->students = Student::with('bus')
            ->where('Name', 'like', '%' . $this->search . '%')
            ->orWhere('id', 'like', '%' . $this->search . '%')
            ->get();

        return view('livewire.students');
    }
}