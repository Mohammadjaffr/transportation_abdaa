<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wage;
use App\Models\Bus;
use App\Models\Location;

class Wages extends Component
{

    public $wages, $buses, $locations;
    public $wageId, $bus_id, $Fees, $Date, $location_id;
    public $editMode = false, $selectedWage;
    public $showForm = false;
    public $deleteId = null, $deleteWageName = null;
    public $search = '';

    protected $rules = [
        'bus_id' => 'required|exists:buses,id',
        'Fees'  => 'required|numeric|min:0',
        'Date'  => 'required|date',
        'location_id' => 'required|exists:locations,id',
    ];
    public function createWage()
    {
        $this->validate();

        Wage::create([
            'bus_id' => $this->bus_id,
            'Fees'  => $this->Fees,
            'Date'  => $this->Date,
            'location_id' => $this->location_id,
        ]);

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم إضافة الأجرة بنجاح']);
    }

    public function edit($id)
    {
        $this->selectedWage = Wage::findOrFail($id);
        $this->wageId = $id;
        $this->bus_id = $this->selectedWage->bus_id;
        $this->Fees = $this->selectedWage->Fees;
        $this->Date = $this->selectedWage->Date;
        $this->location_id = $this->selectedWage->location_id;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function updateWage()
    {
        $this->validate();

        $this->selectedWage->update([
            'bus_id' => $this->bus_id,
            'Fees'  => $this->Fees,
            'Date'  => $this->Date,
            'location_id' => $this->location_id,
        ]);

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث بيانات الأجرة بنجاح']);
    }

    public function confirmDelete($id)
    {
        $wage = Wage::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteWageName = $wage->bus?->bus_id . ' - ' . $wage->Date;
        
    }

    public function deleteWage()
    {
        Wage::destroy($this->deleteId);
        $this->reset(['deleteId', 'deleteWageName']);
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حذف الأجرة بنجاح']);
    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['wageId', 'bus_id', 'Fees', 'Date', 'location_id', 'editMode', 'showForm']);
        
    }

    public function render()
    {
        $this->buses = Bus::all();
        $this->locations = Location::all();

        $this->wages = Wage::with(['bus', 'location'])
            ->when($this->search, function ($query) {
                $query->where('Fees', 'like', "%{$this->search}%")
                    ->orWhere('Date', 'like', "%{$this->search}%")
                    ->orWhere('bus.BusType', 'like', "%{$this->search}%")
                    ->orWhere('location.Name', 'like', "%{$this->search}%");
            })
            ->orderBy('Date', 'desc')
            ->get();
        return view('livewire.wages');
    }
}