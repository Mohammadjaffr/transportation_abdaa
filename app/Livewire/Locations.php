<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Location;

class Locations extends Component
{
    public $locations;
    public  $Name, $DailyAmount, $Fees;
    public $isEdit = false;
    public $deleteId = null;
    public $deleteTitle = null;
    public $search = '';
    public $showForm = false;
    public $editId = null;

    protected $queryString = ['search'];

    public function mount()
    {
        $this->loadLocations();
    }

    public function updatedSearch()
    {
        $this->loadLocations();
    }

    public function loadLocations()
    {
        $this->locations = Location::query()
            ->when($this->search, fn($q) => $q->where('Name', 'like', '%' . $this->search . '%'))
            ->orderBy('id', 'desc')
            ->get();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    protected function rules()
    {
        return [

            'Name' => 'required|string|max:255',
            'DailyAmount' => 'required|numeric|min:0',
            'Fees' => 'required|numeric|min:0',
        ];
    }
    protected $messages = [
        'Name.required' => 'اسم الموقع مطلوب',
        'Name.string'   => 'اسم الموقع يجب أن يكون نصًا',
        'Name.max'      => 'اسم الموقع لا يجب أن يتجاوز 255 حرفًا',
        'DailyAmount.required' => 'المبلغ اليومي مطلوب',
        'DailyAmount.numeric' => 'المبلغ اليومي يجب أن يكون رقمًا',
        'DailyAmount.min'     => 'المبلغ اليومي لا يمكن أن يكون أقل من صفر',

        'Fees.required' => 'الرسوم مطلوبة',
        'Fees.numeric' => 'الرسوم يجب أن تكون رقمًا',
        'Fees.min'     => 'الرسوم لا يمكن أن تكون أقل من صفر',
    ];


    public function store()
    {
        $this->validate();

        Location::create([

            'Name' => $this->Name,
            'DailyAmount' => $this->DailyAmount,
            'Fees' => $this->Fees,
        ]);

        $this->resetForm();
        $this->loadLocations();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم إضافة الموقع بنجاح',
        ]);
    }

    public function edit($id)
    {
        $loc = Location::findOrFail($id);
        $this->editId = $loc->id;
        $this->Name = $loc->Name;
        $this->DailyAmount = $loc->DailyAmount;
        $this->Fees = $loc->Fees;
        $this->isEdit = true;
        $this->showForm = true;
    }


    public function update()
    {
        $this->validate();

        $loc = Location::findOrFail($this->editId);
        $loc->update([
            'Name' => $this->Name,
            'DailyAmount' => $this->DailyAmount,
            'Fees' => $this->Fees,
        ]);

        $this->resetForm();
        $this->loadLocations();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم تعديل الموقع بنجاح',
        ]);
    }

    public function confirmDelete($id)
    {
        $loc = Location::findOrFail($id);
        $this->deleteId = $loc->id;
        $this->deleteTitle = $loc->Name;
    }

    public function deleteLocation()
    {
        $loc = Location::findOrFail($this->deleteId);
        $loc->delete();

        $this->deleteId = null;
        $this->deleteTitle = null;

        $this->loadLocations();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم حذف الموقع بنجاح',
        ]);
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->Name = '';
        $this->DailyAmount = '';
        $this->Fees = '';
        $this->isEdit = false;
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.locations');
    }
}
