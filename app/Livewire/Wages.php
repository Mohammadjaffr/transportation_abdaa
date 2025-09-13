<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wage;
use App\Models\Driver;
use App\Models\region;

class Wages extends Component
{
    public $wages, $buses, $drivers, $regions;
    public $wageId,  $Fees, $Date, $driver_id, $region_id;
    public $editMode = false, $selectedWage;
    public $showForm = false;
    public $deleteId = null;
    public $search = '';

    protected $rules = [
        'driver_id' => 'required|exists:drivers,id',
        'region_id' => 'required|exists:regions,id',
        'Fees' => 'required|numeric|min:0',
        'Date' => 'required|date',
    ];

    protected $messages = [
        'driver_id' => 'السائق مطلوب',
        'region_id' => 'المنطقة مطلوبة',
        'Fees' => 'الأجرة مطلوبة',
        'Date' => 'التاريخ مطلوب',
    ];

    public function mount()
    {
        $this->drivers = Driver::all();
        $this->regions = region::all();
    }

    public function createWage()
    {
        $this->validate();

        Wage::create([

            'driver_id' => $this->driver_id,
            'region_id' => $this->region_id,
            'Fees' => $this->Fees,
            'Date' => $this->Date,
        ]);

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم إضافة الأجرة بنجاح']);
    }

    public function edit($id)
    {
        $this->selectedWage = Wage::findOrFail($id);
        $this->wageId = $id;
        $this->driver_id = $this->selectedWage->driver_id;
        $this->region_id = $this->selectedWage->region_id;
        $this->Fees = $this->selectedWage->Fees;
        $this->Date = $this->selectedWage->Date;
        $this->editMode = true;
        $this->showForm = true;
    }

    public function updateWage()
    {
        $this->validate();

        $this->selectedWage->update([
            'driver_id' => $this->driver_id,
            'region_id' => $this->region_id,
            'Fees' => $this->Fees,
            'Date' => $this->Date,
        ]);

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث بيانات الأجرة بنجاح']);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function deleteWage()
    {
        Wage::destroy($this->deleteId);
        $this->reset('deleteId');
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حذف الأجرة بنجاح']);
    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['wageId', 'driver_id', 'region_id', 'Fees', 'Date', 'editMode', 'showForm', 'selectedWage']);
    }

    public function render()
    {
        $this->wages = Wage::with(['bus', 'driver', 'region'])
            ->when(
                $this->search,
                fn($q) => $q->whereHas('bus', fn($b) => $b->where('BusType', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('driver', fn($d) => $d->where('Name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('region', fn($r) => $r->where('Name', 'like', '%' . $this->search . '%'))
                    ->orWhere('Fees', 'like', '%' . $this->search . '%')
                    ->orWhere('Date', 'like', '%' . $this->search . '%')
            )
            ->orderBy('Date', 'desc')->get();

        return view('livewire.wages');
    }
}