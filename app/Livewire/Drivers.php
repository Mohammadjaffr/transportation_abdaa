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
                'fields.Name' => 'required|string|max:50',
                'fields.IDNo' => 'required|string|max:11|unique:drivers,IDNo,' . $this->selectedDriver->id,
                'fields.Phone' => 'required|digits:9|unique:drivers,Phone,' . ($this->editMode ? $this->selectedDriver->id : 'NULL'),

                'fields.LicenseNo' => 'required|string|max:10|unique:drivers,LicenseNo,' . $this->selectedDriver->id,
                'fields.Ownership' => 'required|string|max:50',
                'fields.wing_id' => 'required|exists:wings,id',
                'fields.CheckUp' => 'required|boolean',
                'fields.Behavior' => 'required|boolean',
                'fields.Form' => 'required|boolean',
                'fields.Fitnes' => 'required|boolean',
                'fields.region_id' => 'required|exists:regions,id',

            ]
            : [
                'fields.Name' => 'required|string|max:50',
                'fields.IDNo' => 'required|string|max:11|unique:drivers,IDNo',  
                'fields.Phone' => 'required|digits:9|unique:drivers,Phone',
                'fields.LicenseNo' => 'required|string|max:8|unique:drivers,LicenseNo',
                'fields.Ownership' => 'required|string|max:50',
                'fields.wing_id' => 'required|exists:wings,id',
                'fields.CheckUp' => 'required|boolean',
                'fields.Behavior' => 'required|boolean',
                'fields.Form' => 'required|boolean',
                'fields.Fitnes' => 'required|boolean',
                'fields.region_id' => 'required|exists:regions,id',
            ];
    }

    protected $messages = [
    'fields.Name.required'      => 'اسم السائق مطلوب',
    'fields.Name.max'           => 'اسم السائق يجب ألا يتجاوز 50 حرف',
    
    'fields.IDNo.required'      => 'رقم الهوية مطلوب',
    'fields.IDNo.unique'        => 'رقم الهوية مستخدم من قبل',
    'fields.IDNo.max'           => 'رقم الهوية يجب ألا يتجاوز 11 ارقام',
    
    'fields.Phone.required'     => 'رقم الهاتف مطلوب',
    'fields.Phone.digits'       => 'رقم الهاتف يجب أن يكون 9 أرقام',
    'fields.Phone.unique'       => 'رقم الهاتف مستخدم من قبل',
    
    'fields.LicenseNo.required' => 'رقم الرخصة مطلوب',
    'fields.LicenseNo.unique'   => 'رقم الرخصة مستخدم من قبل',
    'fields.LicenseNo.max'      => 'رقم الرخصة يجب ألا يتجاوز 10 ارقام',
    
    'fields.Ownership.required' => 'حقل الملكية مطلوب',
    'fields.Ownership.max'      => 'الملكية يجب ألا تتجاوز 20 ارقام',

    'fields.wing_id.required'   => 'الجناح مطلوب',
    'fields.wing_id.exists'     => 'الجناح المحدد غير صحيح',

    'fields.region_id.required' => 'المنطقة مطلوبة',
    'fields.region_id.exists'   => 'رقم المنطقة غير صحيح',

    'fields.CheckUp.required'   => 'حقل الفحص الطبي مطلوب',
    'fields.CheckUp.boolean'    => 'قيمة الفحص الطبي يجب أن تكون نعم أو لا',

    'fields.Behavior.required'  => 'حقل السلوك مطلوب',
    'fields.Behavior.boolean'   => 'قيمة السلوك يجب أن تكون نعم أو لا',

    'fields.Form.required'      => 'حقل الاستمارة مطلوب',
    'fields.Form.boolean'       => 'قيمة الاستمارة يجب أن تكون نعم أو لا',

    'fields.Fitnes.required'    => 'حقل اللياقة مطلوب',
    'fields.Fitnes.boolean'     => 'قيمة اللياقة يجب أن تكون نعم أو لا',

    'primary_image.required'    => 'صورة السائق مطلوبة',
    'primary_image.image'       => 'الملف المرفوع يجب أن يكون صورة',
    'primary_image.mimes'       => 'الصورة يجب أن تكون من نوع jpeg أو png أو jpg أو gif أو svg',
    'primary_image.max'         => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
];


    public function mount()
    {
        $this->loadDrivers();
        $this->buses = Bus::all();
        $this->wings = Wing::all();
        // $this->regions = Region::all();
       $this->regions =  Region::whereNull('parent_id')->get();
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

        // $imageService = new ImageService();
        // $primary_image = null;

        // if ($this->primary_image) {
        //     $primary_image = $imageService->saveImage($this->primary_image, 'images/drivers');
        //     $data['Picture'] = $primary_image;
        // }

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

        // $imageService = new ImageService();
        // $primary_image = null;

        // if ($this->primary_image) {
        //     $primary_image = $imageService->saveImage($this->primary_image, 'images/drivers');
        //     $data['Picture'] = $primary_image;
        // } else {
        //     $data['Picture'] = $this->selectedDriver->Picture;
        // }

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