<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\region;

class Regions extends Component
{
    public $regions;
    public $Name;
    public $isEdit = false;
    public $deleteId = null;
    public $deleteTitle = null;
    public $search = '';
    public $showForm = false;
    public $editId = null;

    protected $queryString = ['search'];

    public function mount()
    {
        $this->loadRegions();
    }

    public function updatedSearch()
    {
        $this->loadRegions();
    }

    public function loadRegions()
    {
        $this->regions = region::query()
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
        ];
    }

    protected $messages = [
        'Name.required' => 'اسم الموقع مطلوب',
        'Name.string'   => 'اسم الموقع يجب أن يكون نصًا',
        'Name.max'      => 'اسم الموقع لا يجب أن يتجاوز 255 حرفًا',
    ];

    public function store()
    {
        $this->validate();

        region::create([
            'Name' => $this->Name,
        ]);

        $this->resetForm();
        $this->loadRegions();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم إضافة المنطقة بنجاح',
        ]);
    }

    public function edit($id)
    {
        $loc = region::findOrFail($id);
        $this->editId = $loc->id;
        $this->Name = $loc->Name;
        $this->isEdit = true;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        $loc = region::findOrFail($this->editId);
        $loc->update([
            'Name' => $this->Name,
        ]);

        $this->resetForm();
        $this->loadRegions();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم تعديل المنطقة بنجاح',
        ]);
    }

    public function confirmDelete($id)
    {
        $loc = region::findOrFail($id);
        $this->deleteId = $loc->id;
        $this->deleteTitle = $loc->Name;
    }

    public function deleteRegion()
    {
        $loc = region::findOrFail($this->deleteId);
        $loc->delete();

        $this->deleteId = null;
        $this->deleteTitle = null;

        $this->loadRegions();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم حذف المنطقة بنجاح',
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
        $this->isEdit = false;
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.regions');
    }
}