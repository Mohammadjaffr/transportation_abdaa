<?php

namespace App\Livewire;

use App\Models\Region;
use Livewire\Component;

use function Ramsey\Uuid\v1;

class Regions extends Component
{

    public $regions;
    public $Name, $parent_id, $region_id;
    public $isEdit = false;
    public $deleteId = null;
    public $deleteName = null;
    public $search = '';
    public $showForm = false;

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
        $this->regions = Region::with('parent')
            ->when($this->search, function ($query) {
                $query->where('Name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'asc')
            ->get();
    }

    public function store()
    {
        $this->validate([
            'Name' => 'required|string|max:255|unique:regions,Name',
        ]);

        Region::create([
            'Name' => $this->Name,
            'parent_id' => $this->parent_id,
        ]);
        $this->resetForm();
        $this->loadRegions();
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم اضافة المنطقه بنجاح'
        ]);
    }

    public function edit($id)
    {

        $region = Region::findOrFail($id);
        $this->region_id = $region->id;
        $this->Name = $region->Name;
        $this->parent_id = $region->parent_id;
        $this->isEdit = true;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate([
            'Name' => 'required|string|max:255',
        ]);

        $region = Region::findOrFail($this->region_id);
        $region->update([
            'Name' => $this->Name,
            'parent_id' => $this->parent_id,
        ]);
        $this->resetForm();
        $this->loadRegions();
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم تعديل المنطقه بنجاح'
        ]);
    }
    public function confirmDelete($id)
    {
        $region = Region::findOrFail($id);
        $this->deleteId = $region->id;
        $this->deleteName = $region->Name;
    }
    public function deleteRegion()
    {
        Region::findOrFail($this->deleteId)->delete();
        $this->loadRegions();
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم حذف المنطقه بنجاح'
        ]);
        $this->deleteId = null;
        $this->deleteName = null;

        $this->dispatch('close-delete-modal');
    }
    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->Name = '';
        $this->parent_id = null;
        $this->region_id = null;
        $this->isEdit = false;
        $this->showForm = false;
    }

    public function render()
    {
        $parents = Region::with('parent')->get();
                $parent_regions = Region::whereNull('parent_id')->get();
        return view('livewire.regions', compact('parents','parent_regions'));
    }
}