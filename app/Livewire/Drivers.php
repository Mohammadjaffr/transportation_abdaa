<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Driver;
use App\Models\Bus;
use App\Models\Wing;
use App\Models\Region;
use Livewire\WithFileUploads;
use App\Services\ImageService;

class Drivers extends Component
{
    use WithFileUploads;
    public $drivers;
    public $buses;
    public $wings;
    public $regions;
    public $fields = [
        'Name' => '',
        'IDNo' => '',
        'Phone' => '',
        'LicenseNo' => '',
        'Ownership' => '',
        'wing_id' => '',
        // 'bus_id' => '',
        'CheckUp' => '',
        'Behavior' => '',
        'Form' => '',
        'Fitnes' => '',
        'region_id' => '',
        'Picture' => '',

    ];

    public $editMode = false;
    public $selectedDriver;
    public $deleteId = null;
    public $deleteDriverName = null;
    public $primary_image;
    public $search = '';
    public $showForm = false;

    protected $queryString = ['search'];

    protected function rules()
    {
        return $this->editMode
            ? [
                'fields.Name' => 'required|string|max:100',
                'fields.IDNo' => 'required|string|max:50|unique:drivers,IDNo,' . $this->selectedDriver->id,
                'fields.Phone' => 'required|digits:9|unique:drivers,Phone,' . ($this->editMode ? $this->selectedDriver->id : 'NULL'),

                'fields.LicenseNo' => 'required|string|max:10|unique:drivers,LicenseNo,' . $this->selectedDriver->id,
                'fields.Ownership' => 'required|string|max:50',
                'fields.wing_id' => 'required|exists:wings,id',
                // 'fields.bus_id' => 'nullable|exists:buses,id',
                'fields.CheckUp' => 'required|boolean',
                'fields.Behavior' => 'required|boolean',
                'fields.Form' => 'required|boolean',
                'fields.Fitnes' => 'required|boolean',
                'fields.region_id' => 'required|exists:regions,id',
                'primary_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            ]
            : [
                'fields.Name' => 'required|string|max:100',
                'fields.IDNo' => 'required|string|max:50|unique:drivers,IDNo',
                'fields.Phone' => 'required|digits:9|unique:drivers,Phone',
                'fields.LicenseNo' => 'required|string|max:10|unique:drivers,LicenseNo',
                'fields.Ownership' => 'required|string|max:50',
                'fields.wing_id' => 'required|exists:wings,id',
                // 'fields.bus_id' => 'required|exists:buses,id',
                'fields.CheckUp' => 'required|boolean',
                'fields.Behavior' => 'required|boolean',
                'fields.Form' => 'required|boolean',
                'fields.Fitnes' => 'required|boolean',
                'fields.region_id' => 'required|exists:regions,id',
                'primary_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
    }

    protected $messages = [
        'fields.Name.required' => 'اسم السائق مطلوب',
        'fields.IDNo.required' => 'رقم الهوية مطلوب',
        'fields.IDNo.unique'   => 'رقم الهوية مستخدم من قبل',
        'fields.Phone.required' => 'رقم الهاتف مطلوب',
        'fields.Phone.digits'   => 'رقم الهاتف يجب أن يكون 9 أرقام',
        'fields.Phone.unique'   => 'رقم الهاتف مستخدم من قبل',
        'fields.LicenseNo.required' => 'رقم الرخصة مطلوب',
        'fields.LicenseNo.unique'   => 'رقم الرخصة مستخدم من قبل',
        'fields.Ownership.required' => 'حقل الملكية مطلوب',
        'fields.wing_id.required' => 'الجناح مطلوب',
        'fields.wing_id.exists' => 'الجناح غير صحيح',
        // 'fields.bus_id.exists' => 'رقم الحافلة غير صحيح',

        'fields.region_id.exists' => 'رقم المنطقة غير صحيح',
        'fields.region_id.required' => '  مطلوب  حقل  المنطقة ',
        'fields.CheckUp.required' => 'حقل الفحص الطبي مطلوب',
        'fields.CheckUp.boolean' => 'حقل الفحص الطبي يجب أن يكون صحيحًا أو خطأ',
        'fields.Behavior.required' => 'حقل السلوك مطلوب',
        'fields.Behavior.boolean' => 'حقل السلوك يجب أن يكون صحيحًا أو خطأ',

        'fields.Form.required' => 'حقل النموذج مطلوب',

        'fields.Form.boolean' => 'حقل النموذج يجب أن يكون صحيحًا أو خطأ',
        'fields.Fitnes.required' => 'حقل اللياقة مطلوب',
        'fields.Fitnes.boolean' => 'حقل اللياقة يجب أن يكون صحيحًا أو خطأ',
        'fields.Picture.required' => 'صورة السائق مطلوبة',
        'primary_image.image' => 'صورة يجب أن تكون صورة',
        'primary_image.mimes' => 'صورة يجب أن تكون من نوع jpeg, png, jpg, gif, svg',
        'primary_image.max' => 'صورة يجب أن تقل عن 2 ميجابايت',

    ];

    public function mount()
    {
        $this->loadDrivers();
        $this->buses = Bus::all();
        $this->wings = Wing::all();
        $this->regions = Region::all();
    }

    public function updatedSearch()
    {
        $this->loadDrivers();
    }

    public function loadDrivers()
    {
        $this->drivers = Driver::with(['bus', 'wing'])
            ->when($this->search, function ($query) {
                $query->where('Name', 'like', "%{$this->search}%")
                    ->orWhere('IDNo', 'like', "%{$this->search}%");
            })
            ->orderBy('Name', 'desc')
            ->get();
    }

    public function createDriver()
    {
        $this->validate();

        $data = $this->fields;

        $imageService = new ImageService();
        $primary_image = null;

        if ($this->primary_image) {
            $primary_image = $imageService->saveImage($this->primary_image, 'images/drivers');
            $data['Picture'] = $primary_image;
        }

        Driver::create($data);

        $this->resetForm();
        $this->loadDrivers();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم إضافة السائق بنجاح']);
    }


    public function editDriver($id)
    {
        $driver = Driver::findOrFail($id);
        $this->selectedDriver = $driver;
        $this->fields = $driver->toArray();
        $this->editMode = true;
        $this->showForm = true;
    }

    public function updateDriver()
    {
        $this->validate();

        $data = $this->fields;

        $imageService = new ImageService();
        $primary_image = null;

        if ($this->primary_image) {
            $primary_image = $imageService->saveImage($this->primary_image, 'images/drivers');
            $data['Picture'] = $primary_image;
        } else {
            $data['Picture'] = $this->selectedDriver->Picture;
        }

        $this->selectedDriver->update($data);

        $this->resetForm();
        $this->loadDrivers();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث السائق بنجاح']);
    }


    public function confirmDelete($id)
    {
        $driver = Driver::findOrFail($id);
        $this->deleteId = $driver->id;
        $this->deleteDriverName = $driver->Name;
    }

    public function deleteDriver()
    {
        Driver::findOrFail($this->deleteId)->delete();
        $this->loadDrivers();
        $this->deleteId = null;
        $this->deleteDriverName = null;
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حذف السائق بنجاح']);
    }

    public function resetForm()
    {
        $this->fields = [
            'Name' => '',
            'IDNo' => '',
            'Phone' => '',
            'LicenseNo' => '',
            'Ownership' => '',
            'wing_id' => '',
            // 'bus_id' => '',
            'CheckUp' => '',
            'Behavior' => '',
            'Form' => '',
            'Fitnes' => '',
            'region_id' => '',

        ];
        $this->primary_image = null;
        $this->editMode = false;
        $this->selectedDriver = null;
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.drivers');
    }
}