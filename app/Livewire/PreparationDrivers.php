<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PreparationDriver;
use App\Models\Driver;
use App\Models\Region;

class PreparationDrivers extends Component
{
    public  $drivers, $regions;
    public $Atend = false, $Month, $driver_id, $region_id;
    public $editMode = false, $selectedId;
    public $search = '';
    public $showForm = false;
    public $deleteId = null;

    protected $rules = [
        'Atend' => 'required|boolean',
        'Month' => 'required|date',
        'driver_id' => 'required|exists:drivers,id',
        'region_id' => 'required|exists:regions,id',
    ];

    protected $messages = [
        'Atend.required' => 'حالة الحضور مطلوبة',
        'Month.required' => 'التاريخ مطلوب',
        'Month.date' => 'التاريخ غير صالح',
        'driver_id.required' => 'السائق مطلوب',
        'driver_id.exists' => 'السائق غير موجود',
        'region_id.required' => 'المنطقة مطلوبة',
        'region_id.exists' => 'المنطقة غير موجودة',
    ];

    public function mount()
    {
        $this->regions = Region::all();
        $this->Month = now()->format('Y-m-d');
        $this->refreshDrivers();   // تحميل السائقين بدون المكررين
        $this->loadPreparations();
    }

    /** فلترة السائقين بحيث لا يظهروا إذا تم تحضيرهم في نفس اليوم */
    private function refreshDrivers()
    {
        $this->drivers = Driver::whereNotNull('Name')
            ->whereDoesntHave('preparations', function ($q) {
                $q->whereDate('Month', $this->Month);
            })
            ->get();
    }

    /** عند اختيار السائق يجيب منطقته تلقائي */
    public function updatedDriverId($value)
    {
        if ($value) {
            $driver = Driver::with('region')->find($value);
            if ($driver) {
                $this->region_id = $driver->region_id;
            }
        }
    }

    public function loadPreparations()
    {
     
    }

    public function createPreparation()
    {
        $this->validate();

        PreparationDriver::create([
            'Atend' => $this->Atend ? 1 : 0,
            'Month' => $this->Month,
            'driver_id' => $this->driver_id,
            'region_id' => $this->region_id,
        ]);

        $this->resetForm();
        $this->refreshDrivers();   // يحدث السائقين بعد الإضافة
        $this->loadPreparations(); // يحدث الجدول
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تسجيل حضور السائق']);
    }

    public function editPreparation($id)
    {
        $prep = PreparationDriver::findOrFail($id);
        $this->selectedId = $id;
        $this->editMode = true;
        $this->showForm = true;

        $this->Atend     = $prep->Atend;
        $this->Month     = $prep->Month;
        $this->driver_id = $prep->driver_id;
        $this->region_id = $prep->region_id;
    }

    public function updatePreparation()
    {
        $this->validate();
        $prep = PreparationDriver::findOrFail($this->selectedId);

        $prep->update([
            'Atend'     => $this->Atend ? 1 : 0,
            'Month'     => $this->Month,
            'driver_id' => $this->driver_id,
            'region_id' => $this->region_id,
        ]);

        $this->resetForm();
        $this->refreshDrivers();
        $this->loadPreparations();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث سجل حضور السائق']);
    }

    public function toggleAtend($prepId)
    {
        $prep = PreparationDriver::find($prepId);
        if ($prep) {
            $prep->Atend = !$prep->Atend;
            $prep->save();
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'تم تحديث حالة الحضور للسائق'
            ]);
            $this->loadPreparations();
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function deletePreparation()
    {
        if ($this->deleteId) {
            PreparationDriver::find($this->deleteId)->delete();
            $this->deleteId = null;
            $this->refreshDrivers();
            $this->loadPreparations();
            $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حذف سجل حضور السائق']);
        }
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['Atend', 'Month', 'driver_id', 'region_id', 'editMode', 'selectedId', 'showForm', 'deleteId']);
        $this->Month = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->loadPreparations();
    }

    public function render()
    {
           $preparations = PreparationDriver::with(['driver', 'region'])
            ->when($this->search, function ($q) {
                $q->whereHas('driver', fn($dq) => $dq->where('Name', 'like', '%' . $this->search . '%'))
                  ->orWhere('Month', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
        return view('livewire.preparation-drivers',compact('preparations'));
    }
}