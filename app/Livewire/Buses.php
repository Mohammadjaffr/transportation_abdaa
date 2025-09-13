<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Bus;
use App\Models\region;
use App\Models\Driver;
class Buses extends Component
{
    public $buses;
    public $regions;
    public $drivers;

    public $StudentsNo, $BusType, $Model, $SeatsNo, $CustomsNo, $region_id,$driver_id;

    public $editMode = false;
    public $selectedBus;
    public $deleteId = null;
    public $deleteBusName = null;

    public $search = '';
    public $showForm = false;
    public $editId = null;
    protected $queryString = ['search'];

    public function mount()
    {
        $this->loadBuses();
        $this->regions = region::all();
        $this->drivers = Driver::all();
    }

    public function updatedSearch()
    {
        $this->loadBuses();
    }

    public function loadBuses()
    {
        $this->buses = Bus::with('region','driver')
            ->when($this->search, function ($query) {
                $query->where('BusType', 'like', "%{$this->search}%")->orWhere('id', 'like', "%{$this->search}%");
            })
          
            ->when($this->driver_id, function ($query) {
                $query->where('driver_id', $this->driver_id);
            })
            ->orderBy('id', 'desc')
            ->get();
    }


    protected function rules()
    {
        return $this->editMode
            ? [
                'BusType' => 'required|string|max:50',
                'Model' => 'required|string|max:30',
                'SeatsNo' => 'required|integer|min:1',
                'CustomsNo' => 'required|string|max:30',
                'StudentsNo' => 'required|integer|min:0',
                'region_id' => 'required|exists:regions,id',
                'driver_id' => 'required|exists:drivers,id',
            ]
            : [
                'BusType' => 'required|string|max:50',
                'Model' => 'required|string|max:30',
                'SeatsNo' => 'required|integer|min:1',
                'StudentsNo' => 'required|integer|min:0',
                'CustomsNo' => 'required|string|max:30',
                'region_id' => 'required|exists:regions,id',
                'driver_id' => 'required|exists:drivers,id',
            ];
    }

    protected $messages = [

        'BusType.required' => 'نوع الحافلة مطلوب',
        'BusType.string'   => 'نوع الحافلة يجب أن يكون نصًا',
        'BusType.max'      => 'نوع الحافلة لا يجب أن يتجاوز 50 حرفًا',
        'StudentsNo.required' => 'عدد الطلاب مطلوب',
        'StudentsNo.integer'  => 'عدد الطلاب يجب أن يكون رقمًا',
        'StudentsNo.min'      => 'عدد الطلاب لا يمكن أن يقل عن 0',

        'Model.required' => 'الموديل مطلوب',
        'Model.string'   => 'الموديل يجب أن يكون نصًا',
        'Model.max'      => 'الموديل لا يجب أن يتجاوز 30 حرفًا',

        'SeatsNo.required' => 'عدد المقاعد مطلوب',
        'SeatsNo.integer'  => 'عدد المقاعد يجب أن يكون رقمًا',
        'SeatsNo.min'      => 'عدد المقاعد لا يمكن أن يقل عن 1',

        'CustomsNo.string' => 'رقم الجمارك يجب أن يكون نصًا',
        'CustomsNo.required' => 'رقم الجمارك مطلوب',
        'CustomsNo.max'    => 'رقم الجمارك لا يجب أن يتجاوز 30 حرفًا',
        'CustomsNo.unique' => 'رقم الجمارك موجود بالفعل',

        'region_id.required' => 'الموقع مطلوب',
        'region_id.exists'   => 'الموقع غير صحيح',
        'driver_id.required' => 'السائق مطلوب',
        'driver_id.exists'   => 'السائق غير صحيح',
    ];


    public function createBus()
    {
        $this->validate();

        Bus::create([
            'BusType' => $this->BusType,
            'Model' => $this->Model,
            'SeatsNo' => $this->SeatsNo,
            'CustomsNo' => $this->CustomsNo,
            'StudentsNo' => $this->StudentsNo,
            'region_id' => $this->region_id,
            'driver_id' => $this->driver_id,
        ]);

        $this->resetForm();
        $this->loadBuses();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم إضافة الحافلة بنجاح'
        ]);
    }

    public function edit($id)
    {
        $bus = Bus::findOrFail($id);
        $this->editId = $bus->id;
        $this->selectedBus = $bus;
        $this->editMode = true;
        $this->showForm = true;

        $this->BusType = $bus->BusType;
        $this->Model = $bus->Model;
        $this->SeatsNo = $bus->SeatsNo;
        $this->CustomsNo = $bus->CustomsNo;
        $this->StudentsNo = $bus->StudentsNo;
        $this->region_id = $bus->region_id;
        $this->driver_id = $bus->driver_id;
    }

    public function updateBus()
    {
        $this->validate();

        $bus = Bus::findOrFail($this->editId);
        $bus->update([
            'BusType' => $this->BusType,
            'Model' => $this->Model,
            'SeatsNo' => $this->SeatsNo,
            'CustomsNo' => $this->CustomsNo,
            'StudentsNo' => $this->StudentsNo,
            'region_id' => $this->region_id,
            'driver_id' => $this->driver_id,
        ]);

        $this->resetForm();
        $this->loadBuses();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم تحديث الحافلة بنجاح'
        ]);
    }

    public function confirmDelete($id)
    {
        $bus = Bus::findOrFail($id);
        $this->deleteId = $bus->id;
        $this->deleteBusName = $bus->BusType . ' (' . $bus->id . ')';
    }

    public function deleteBus()
    {
        $bus = Bus::findOrFail($this->deleteId);
        $bus->delete();

        $this->deleteId = null;
        $this->deleteBusName = null;

        $this->loadBuses();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم حذف الحافلة بنجاح'
        ]);
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->BusType = '';
        $this->StudentsNo = '';
        $this->Model = '';
        $this->SeatsNo = '';
        $this->CustomsNo = '';
        $this->region_id = '';
        $this->driver_id = '';
        $this->editMode = false;
        $this->selectedBus = null;
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.buses');
    }
}