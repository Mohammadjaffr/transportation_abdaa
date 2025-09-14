<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\Wing;
use App\Models\Region;
use App\Services\ImageService;
use Livewire\WithFileUploads;

class Students extends Component
{
    use WithFileUploads;
    public $students, $buses, $wings, $regions;
    public $name, $grade, $sex, $phone, $picture, $stu_position, $wing_id, $division, $region_id, $bus_id;
    public $editMode = false, $selectedStudent;
    public $primary_image;
    public $showForm = false;
    public $deleteId = null;
    public $search = '';
    public $selectedStudentId;
    public $editId = null;

    protected $rules = [
        'name' => 'required|string|max:200',
        'grade' => 'required|string|max:30',
        'sex' => 'required|string|max:20',
        'phone' => 'required|string|max:20',
        'stu_position' => 'required|string|max:200',
        'wing_id' => 'required|exists:wings,id',
        'division' => 'required|string|max:20',
        'region_id' => 'required|exists:regions,id',
        'picture' => 'required|image|max:2048',
    ];

    protected $messages = [
        'name.required' => 'اسم الطالب مطلوب',
        'name.string'   => 'اسم الطالب يجب أن يكون نصًا',
        'name.max'      => 'اسم الطالب لا يجب أن يتجاوز 200 حرف',
        'grade.required' => 'الصف مطلوب',
        'grade.string'  => 'الصف يجب أن يكون نصًا',
        'grade.max'     => 'الصف لا يجب أن يتجاوز 30 حرف',
        'sex.required' => 'النوع مطلوب',
        'phone.max' => 'رقم الهاتف لا يجب أن يتجاوز 20 حرف',
        'phone.required' => 'رقم الهاتف مطلوب',
        'stu_position.required' => '  موقف الطالب مطلوب',
        'wing_id.required' => 'الجناح مطلوب',
        'wing_id.exists' => 'الجناح غير موجود',
        'region_id.required' => 'المنطقة مطلوبة',
        'region_id.exists' => 'المنطقة غير موجودة',
        'division.required' => 'الشعبه مطلوبة',
        'picture.image' => 'الصورة يجب أن تكون صورة',
       
        'picture.max' => 'الصورة لا يجب أن تتجاوز 2048 كيلوبايت',
    ];

    public function mount()
    {
        $this->students = Student::with('bus')->get();
        $this->wings = Wing::all();
        $this->regions = region::all();
    }

    public function createStudent()
    {
        $this->validate();
        $imageService = new ImageService();
        $picturePath = null;

        if ($this->picture) {
            $picturePath = $imageService->saveImage($this->picture, 'images/students');
        }
        Student::create([
            'Name' => $this->name,
            'Grade' => $this->grade,
            'Sex' => $this->sex,
            'Phone' => $this->phone,
            'Picture' => $picturePath,
            'Stu_position' => $this->stu_position,
            'wing_id' => $this->wing_id,
            'Division' => $this->division,
            'region_id' => $this->region_id,
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
        $this->picture = $student->Picture;
        $this->stu_position = $student->Stu_position;
        $this->wing_id = $student->wing_id;
        $this->division = $student->Division;
        $this->region_id = $student->region_id;
    }

    public function updateStudent()
    {
        $this->validate();
        $imageService = new ImageService();
        $picturePath = null;

        if ($this->picture) {
            $picturePath = $imageService->saveImage($this->picture, 'images/students');
        }
        $student = Student::findOrFail($this->selectedStudentId);
        $student->update([
            'Name' => $this->name,
            'Grade' => $this->grade,
            'Sex' => $this->sex,
            'Phone' => $this->phone,
            'Picture' => $picturePath ?? $student->Picture,
            'Stu_position' => $this->stu_position,
            'wing_id' => $this->wing_id,
            'Division' => $this->division,
            'region_id' => $this->region_id,
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

    public  function resetForm()
    {
        $this->reset([
            'editId',
            'name',
            'grade',
            'sex',
            'phone',
            'picture',
            'stu_position',
            'wing_id',
            'division',
            'region_id',
    
            'editMode',
            'selectedStudent',
            'showForm'
        ]);
        $this->primary_image = null;
    }

    public function render()
    {
        $this->students = Student::with(['bus', 'wing', 'region'])
            ->where('Name', 'like', '%' . $this->search . '%')
            ->orWhere('id', 'like', '%' . $this->search . '%')
            ->get();

        return view('livewire.students');
    }
}