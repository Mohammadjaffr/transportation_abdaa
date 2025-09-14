<?php

namespace App\Livewire;
    
use Livewire\Component;
use App\Models\PreparationStu;
use App\Models\Driver;
use App\Models\Region;
use App\Models\Student;

class PreparationStus extends Component
{
    public $preparations, $drivers, $regions, $students;
    public $Atend = false, $Year, $driver_id, $region_id, $student_id;
    public $editMode = false, $selectedId;
    public $search = '';
    public $showForm = false;
    public $deleteId = null;

    protected $rules = [
        'Atend' => 'required|boolean',
        'Year' => 'required|date',
        'driver_id' => 'required|exists:drivers,id',
        'region_id' => 'required|exists:regions,id',
        'student_id' => 'required|exists:students,id',
    ];

    protected $messages = [
        'Atend.required' => 'حالة الحضور مطلوبة',
        'Year.required' => 'التاريخ مطلوب',
        'Year.date' => 'التاريخ غير صالح',
        'driver_id.required' => 'السائق مطلوب',
        'driver_id.exists' => 'السائق غير موجود',
        'region_id.required' => 'المنطقة مطلوبة',
        'region_id.exists' => 'المنطقة غير موجودة',
        'student_id.required' => 'الطالب مطلوب',
        'student_id.exists' => 'الطالب غير موجود',
    ];

    public function mount()
    {
        $this->drivers = Driver::all();
        $this->regions = Region::all();
        $this->students = Student::all();
        $this->Year = now()->format('Y-m-d');
        $this->loadPreparations();
    }

    public function toggleAtend($prepId)
{
    $prep = PreparationStu::find($prepId);
    if ($prep) {
        $prep->Atend = !$prep->Atend;
        $prep->save();
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم تحديث حالة الحضور لطالب'
        ]);
        $this->loadPreparations(); 
    }
}
    public function loadPreparations()
    {
        $this->preparations = PreparationStu::with(['driver','region','student'])
            ->when($this->search, function($q) {
                $q->whereHas('student', fn($sq) => $sq->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhere('Year', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function createPreparation()
    {
        $this->validate();
        PreparationStu::create([
            'Atend' => $this->Atend,
            'Year' => $this->Year,
            'driver_id' => $this->driver_id,
            'region_id' => $this->region_id,
            'student_id' => $this->student_id,
        ]);
        $this->resetForm();
        $this->dispatch('show-toast', ['type'=>'success','message'=>'تم تسجيل الحضور بنجاح']);
    }

    public function editPreparation($id)
    {
        $prep = PreparationStu::findOrFail($id);
        $this->selectedId = $id;
        $this->editMode = true;
        $this->showForm = true;

        $this->Atend = $prep->Atend;
        $this->Year = $prep->Year;
        $this->driver_id = $prep->driver_id;
        $this->region_id = $prep->region_id;
        $this->student_id = $prep->student_id;
    }

    public function updatePreparation()
    {
        $this->validate();
        $prep = PreparationStu::findOrFail($this->selectedId);
        $prep->update([
            'Atend' => $this->Atend,
            'Year' => $this->Year,
            'driver_id' => $this->driver_id,
            'region_id' => $this->region_id,
            'student_id' => $this->student_id,
        ]);
        $this->resetForm();
        $this->dispatch('show-toast', ['type'=>'success','message'=>'تم تحديث سجل الحضور']);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function deletePreparation()
    {
        if($this->deleteId){
            PreparationStu::find($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('show-toast', ['type'=>'success','message'=>'تم حذف سجل الحضور']);
        }
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['Atend','Year','driver_id','region_id','student_id','editMode','selectedId','showForm','deleteId']);
        $this->Year = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->loadPreparations();
    }

    public function render()
    {
           $this->loadPreparations();
        return view('livewire.preparation-stus');
    }
}