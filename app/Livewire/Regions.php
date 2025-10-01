<?php

namespace App\Livewire;

use App\Models\Region;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\AdminLoggerService;

use function Ramsey\Uuid\v1;

class Regions extends Component
{
    use WithPagination;

    // public $regions;
    public $parent_regions;
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
        $this->parent_regions = Region::whereNull('parent_id')->get();
    }

    public function store()
    {
        $this->validate([
            'Name' => 'required|string|max:255|unique:regions,Name',
        ], [
            'Name.required' => 'يرجى إدخال اسم المنطقة',
        ]);

        Region::create([
            'Name' => $this->Name,
            'parent_id' => $this->parent_id,
        ]);
        AdminLoggerService::log('اضافة منطقه', 'Region', "اضافة منطقه: {$this->Name}");

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
        AdminLoggerService::log('تعديل منطقه', 'Region', "تعديل منطقه: {$this->Name}");

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
        AdminLoggerService::log('حذف منطقه', 'Region', "حذف منطقه: {$this->deleteName}");

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
        $regions = Region::with('parent')
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';

                $query->where('Name', 'like', $searchTerm)
                    ->orWhereHas('parent', function ($q) use ($searchTerm) {
                        $q->where('Name', 'like', $searchTerm);
                    });
            })
            ->orderBy('id', 'asc')
            ->paginate(10);

        $parents = Region::with('parent')->get();
        $parent_regions = Region::whereNull('parent_id')->get();
        return view('livewire.regions', compact('parents', 'parent_regions', 'regions'));
    }
}
