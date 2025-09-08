<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Retreat;
use App\Models\Bus;
use App\Models\Student;
class Retreats extends Component
{

    public $retreats, $buses;
    public $retreatId, $Name, $Grade, $bus_id;
    public $editMode = false, $selectedRetreat;
    public $showForm = false;
    public $deleteId = null;
    public $deleteRetreatName = null;
    public $search = '';

    protected $rules = [
        'Name' => 'required|string|max:255',
        'Grade' => 'required|string|max:50',
        'bus_id' => 'required|exists:buses,id',
    ];
    public function createRetreat()
    {
        $this->validate();

        Retreat::create([
            'Name' => $this->Name,
            'Grade' => $this->Grade,
            'bus_id' => $this->bus_id,
        ]);

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تسجيل الحضور بنجاح']);
    }

    public function edit($id)
    {
        $this->selectedRetreat = Retreat::findOrFail($id);
        $this->retreatId = $id;
        $this->Name = $this->selectedRetreat->Name;
        $this->Grade = $this->selectedRetreat->Grade;
        $this->bus_id = $this->selectedRetreat->bus_id;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function updateRetreat()
    {
        $this->validate();

        $this->selectedRetreat->update([
            'Name' => $this->Name,
            'Grade' => $this->Grade,
            'bus_id' => $this->bus_id,
        ]);

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث بيانات الحضور بنجاح']);
    }

    public function confirmDelete($id)
    {
        $retreat = Retreat::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteRetreatName = $retreat->Name;
    }

    public function deleteRetreat()
    {
        Retreat::destroy($this->deleteId);
        $this->reset(['deleteId', 'deleteRetreatName']);
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حذف الحضور بنجاح']);
    }

    public function cancel()
    {
        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'info', 'message' => 'تم إلغاء العملية بنجاح']);
    }

    private function resetForm()
    {
        $this->reset(['retreatId', 'Name', 'Grade', 'bus_id', 'editMode', 'showForm']);
    }

    public function render()
    {
        $this->buses = Bus::all();
        $students = Student::all();
        $this->retreats = Retreat::with('bus')
            ->when($this->search, function ($query) {
                $query->where('Name', 'like', "%{$this->search}%")
                    ->orWhere('Grade', 'like', "%{$this->search}%")
                    ->orWhere('bus_id', 'like', "%{$this->search}%");
            })
            ->get();
        return view('livewire.retreats', [
            'students' => $students,
        ]);
    }
}