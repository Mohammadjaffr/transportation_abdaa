<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Retreat;
use App\Models\Student;
use App\Models\Driver;
use App\Models\region;
class Retreats extends Component
{
  public $retreats, $students, $drivers, $regions;

    public $retreatId;
    public $student_id, $Grade, $Division, $Date_of_interruption, $Reason, $region_id, $driver_id;

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
        'driver_id' => 'required|exists:drivers,id',
    ];

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
            'driver_id' => $this->driver_id,
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
        $this->driver_id = $this->selectedRetreat->driver_id;

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
            'driver_id' => $this->driver_id,
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
        $this->dispatch('show-toast', ['type' => 'info', 'message' => 'تم إلغاء العملية بنجاح']);
    }

    private function resetForm()
    {
        $this->reset(['retreatId', 'student_id', 'Grade', 'Division', 'Date_of_interruption', 'Reason', 'region_id', 'driver_id', 'editMode', 'showForm']);
    }

    public function render()
    {
        $this->students = Student::all();
        $this->drivers = Driver::all();
        $this->regions = region::all();

        $this->retreats = Retreat::with(['student', 'driver', 'region'])
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