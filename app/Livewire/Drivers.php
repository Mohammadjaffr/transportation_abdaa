<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Driver;
use App\Models\Bus;

class Drivers extends Component
{
    public $drivers;
    public $buses;

    public $fields = [
        'Name' => '',
        'Phone' => '',
        'LicenseNo' => '',
        'Ownership' => '',
        'Wing' => '',
        'bus_id' => '',
        'CheckUp' => '',
        'Behavior' => '',
        'Form' => '',
        'Fitnes' => '',
    ];

    public $editMode = false;
    public $selectedDriver;
    public $deleteId = null;
    public $deleteDriverName = null;

    public $search = '';
    public $showForm = false;

    protected $queryString = ['search'];

    protected function rules()
    {
        return $this->editMode
            ? [
                'fields.Name' => 'required|string|max:100',
                'fields.LicenseNo' => 'required|string|max:50|unique:drivers,LicenseNo,' . $this->selectedDriver->id,
                'fields.Phone' => 'required|string|max:20|unique:drivers,Phone,' . $this->selectedDriver->id,

                'fields.Ownership' => 'required|string|max:50',
                'fields.Wing' => 'required|string|max:50',
                'fields.bus_id' => 'nullable|exists:buses,id',
                'fields.CheckUp' => 'nullable|boolean',
                'fields.Behavior' => 'nullable|boolean',
                'fields.Form' => 'nullable|boolean',
                'fields.Fitnes' => 'nullable|boolean',
            ]
            : [
                'fields.Name' => 'required|string|max:100',
                'fields.LicenseNo' => 'required|string|max:50|unique:drivers,LicenseNo',
                'fields.Phone' => 'required|string|max:20|unique:drivers,Phone',
                'fields.Ownership' => 'required|string|max:50',
                'fields.Wing' => 'required|string|max:50',
                'fields.bus_id' => 'nullable|exists:buses,id',
                'fields.CheckUp' => 'nullable|boolean',
                'fields.Behavior' => 'nullable|boolean',
                'fields.Form' => 'nullable|boolean',
                'fields.Fitnes' => 'nullable|boolean',
            ];
    }

    protected $messages = [


        'fields.Name.required' => 'اسم السائق مطلوب',
        'fields.Name.string'   => 'الاسم يجب أن يكون نصًا',
        'fields.Name.max'      => 'الاسم لا يجب أن يتجاوز 100 حرف',

        'fields.CardNo.required' => 'رقم الهوية مطلوب',
        'fields.CardNo.integer'  => 'رقم الهوية يجب أن يكون رقمًا',
        'fields.CardNo.unique'   => 'رقم الهوية مستخدم من قبل',

        'fields.LicenseNo.required' => 'رقم الرخصة مطلوب',
        'fields.LicenseNo.unique'   => 'رقم الرخصة مستخدم من قبل',
        'fields.LicenseNo.max'      => 'رقم الرخصة لا يجب أن يتجاوز 50 حرف',

        'fields.Phone.required' => 'رقم الهاتف مطلوب',
        'fields.Phone.unique'   => 'رقم الهاتف مستخدم من قبل',
        'fields.Phone.max'      => 'رقم الهاتف لا يجب أن يتجاوز 20 رقم',

        'fields.Ownership.required' => 'حقل الملكية مطلوب',
        'fields.Ownership.string'   => 'حقل الملكية يجب أن يكون نصًا',
        'fields.Ownership.max'      => 'حقل الملكية لا يجب أن يتجاوز 50 حرف',

        'fields.Wing.required' => 'الجناح مطلوب',
        'fields.Wing.string'   => 'الجناح يجب أن يكون نصًا',
        'fields.Wing.max'      => 'الجناح لا يجب أن يتجاوز 50 حرف',

        'fields.bus_id.exists' => 'رقم الحافلة غير صحيح',
        'fields.CheckUp.boolean' => 'حقل الفحص الطبي يجب أن يكون صحيحًا أو خطأ',
        'fields.Behavior.boolean' => 'حقل السلوك يجب أن يكون صحيحًا أو خطأ',
        'fields.Form.boolean' => 'حقل النموذج يجب أن يكون صحيحًا أو خطأ',
        'fields.Fitnes.boolean' => 'حقل اللياقة يجب أن يكون صحيحًا أو خطأ',

    ];



    public function mount()
    {
        $this->loadDrivers();
        $this->buses = Bus::all();
    }

    public function updatedSearch()
    {
        $this->loadDrivers();
    }

    public function loadDrivers()
    {
        $this->drivers = Driver::with('bus')
            ->when($this->search, function ($query) {
                $query->where('Name', 'like', "%{$this->search}%")->orWhere('id', 'like', "%{$this->search}%");
            })
            ->orderBy('Name', 'desc')
            ->get();
    }

    public function createDriver()
    {
        $this->validate();

        Driver::create($this->fields);

        $this->resetForm();
        $this->loadDrivers();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم إضافة السائق بنجاح'
        ]);
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
        // $this->validate();

        $this->selectedDriver->update($this->fields);

        $this->resetForm();
        $this->loadDrivers();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم تحديث السائق بنجاح'
        ]);
    }

    public function confirmDelete($id)
    {
        $driver = Driver::findOrFail($id);
        $this->deleteId = $driver->id;
        $this->deleteDriverName = $driver->Name;

        $this->dispatch('show-delete-driver', [
            'deleteDriverName' => $this->deleteDriverName
        ]);
    }


    public function deleteDriver()
    {
        Driver::findOrFail($this->deleteId)->delete();
        $this->loadDrivers();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم حذف السائق بنجاح'
        ]);

        $this->deleteId = null;
        $this->deleteDriverName = null;
    }

    public function resetForm()
    {
        $this->fields = [

            'Name' => '',
            'Phone' => '',
            'LicenseNo' => '',
            'Ownership' => '',
            'Wing' => '',
            'bus_id' => '',
            'CheckUp' => '',
            'Behavior' => '',
            'Form' => '',
            'Fitnes' => '',
        ];
        $this->editMode = false;
        $this->selectedDriver = null;
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.drivers');
    }
}
