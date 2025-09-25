<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Retreat;
use App\Models\Student;
use App\Models\Driver;
use App\Models\Region;

class Retreats extends Component
{
    public $retreats, $students, $drivers, $regions;

    public $retreatId;
    public $student_id, $Grade, $Division, $Date_of_interruption, $Reason, $region_id;

    public $editMode = false, $selectedRetreat;
    public $showForm = false;
    public $deleteId = null;
    public $deleteRetreatName = null;
    public $search = '';

    protected $rules = [
        'student_id' => 'required|exists:students,id',
        'Grade' => 'required|string|max:20',
        'Division' => 'nullable|string|max:20',
        'Date_of_interruption' => 'required|date',
        'Reason' => 'required|string|max:200',
        'region_id' => 'required|exists:regions,id',
    ];

    protected $messages = [
        'student_id.required' => 'الطالب مطلوب',
        'student_id.exists'   => 'الطالب غير موجود في السجلات',

        'Grade.required' => 'الصف مطلوب',
        'Grade.string'   => 'الصف يجب أن يكون نصاً',
        'Grade.max'      => 'الصف يجب ألا يتجاوز 20 حرفاً',

        'Division.string' => 'الشعبة يجب أن تكون نصاً',
        'Division.max'    => 'الشعبة يجب ألا تتجاوز 20 حرفاً',

        'Date_of_interruption.required' => 'تاريخ الانسحاب مطلوب',
        'Date_of_interruption.date'     => 'تاريخ الانسحاب يجب أن يكون تاريخاً صحيحاً',

        'Reason.required' => 'سبب الانسحاب مطلوب',
        'Reason.string'   => 'سبب الانسحاب يجب أن يكون نصاً',
        'Reason.max'      => 'سبب الانسحاب يجب ألا يتجاوز 200 حرفاً',

        'region_id.required' => 'المنطقة مطلوبة',
        'region_id.exists'   => 'المنطقة غير موجودة في السجلات',
    ];


  
    public function updatedStudentId($value)
    {
        if ($value) {
            $student = Student::with(['region'])->find($value);

            if ($student) {
                $this->Grade     = $student->Grade;
                $this->region_id = $student->region_id;
                $this->Division  = $student->Division;
            }
        }
    }

    public function createRetreat()
    {
        $this->validate();

        Retreat::create([
            'student_id' => $this->student_id,
            'Grade' => $this->Grade,
            'Division' => $this->Division,
            'Date_of_interruption' => $this->Date_of_interruption,
            'Reason' => $this->Reason,
            'region_id' => $this->region_id,
        ]);

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تسجيل المنسحب بنجاح']);
    }

    public function edit($id)
    {
        $this->selectedRetreat = Retreat::findOrFail($id);
        $this->retreatId = $id;

        $this->student_id = $this->selectedRetreat->student_id;
        $this->Grade = $this->selectedRetreat->Grade;
        $this->Division = $this->selectedRetreat->Division;
        $this->Date_of_interruption = $this->selectedRetreat->Date_of_interruption;
        $this->Reason = $this->selectedRetreat->Reason;
        $this->region_id = $this->selectedRetreat->region_id;

        $this->editMode = true;
        $this->showForm = true;
    }

    public function updateRetreat()
    {
        $this->validate();

        $this->selectedRetreat->update([
            'student_id' => $this->student_id,
            'Grade' => $this->Grade,
            'Division' => $this->Division,
            'Date_of_interruption' => $this->Date_of_interruption,
            'Reason' => $this->Reason,
            'region_id' => $this->region_id,

        ]);

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث بيانات المنسحب بنجاح']);
    }

    public function confirmDelete($id)
    {
        $retreat = Retreat::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteRetreatName = $retreat->student?->Name ?? 'لا يوجد اسم';
    }

    public function deleteRetreat()
    {
        Retreat::destroy($this->deleteId);
        $this->reset(['deleteId', 'deleteRetreatName']);
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حذف المنسحب بنجاح']);
    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'retreatId',
            'student_id',
            'Grade',
            'Division',
            'Date_of_interruption',
            'Reason',
            'region_id',
            'editMode',
            'showForm'
        ]);
    }

    public function render()
    {
        // $this->students = Student::all();
        $this->students = Student::whereNotNull('region_id')
            ->whereNotNull('Grade')
            ->whereDoesntHave('retreats')
            ->get();

        $this->drivers = Driver::all();
        $this->regions = Region::all();

        $this->retreats = Retreat::with(['student', 'region'])
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('Name', 'like', "%{$this->search}%");
                })
                    ->orWhere('Grade', 'like', "%{$this->search}%")
                    ->orWhereHas('driver', function ($q) {
                        $q->where('Name', 'like', "%{$this->search}%");
                    });
            })
            ->orderBy('Date_of_interruption', 'desc')
            ->get();

        return view('livewire.retreats');
    }
}