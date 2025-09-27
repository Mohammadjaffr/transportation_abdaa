<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PreparationStu;
use App\Models\Driver;
use App\Models\Region;
use App\Models\Student;
use App\Exports\PreparationStuExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\AdminLoggerService;

class PreparationStus extends Component
{
    public  $drivers, $regions, $students;
    public $Atend = true, $Year, $driver_id, $region_id, $student_id;
    public $editMode = false, $selectedId, $Grade, $Division;
    public $search = '';
    public $showForm = false;
    public $deleteId = null;

    public function export()
    {
        return Excel::download(new PreparationStuExport, 'preparations.xlsx');
    }
    protected $rules = [
        'Atend' => 'required|boolean',
        'Year' => 'required|date',
        'driver_id' => 'required|exists:drivers,id',
        'region_id' => 'required|exists:regions,id',
        'student_id' => 'required|exists:students,id',
    ];

    protected $messages = [
        'Atend.required' => 'حالة الحضور مطلوبة',
        'Year.required' => 'التاريخ مطلوب',
        'Year.date' => 'التاريخ غير صالح',
        'driver_id.required' => 'السائق مطلوب',
        'driver_id.exists' => 'السائق غير موجود',
        'region_id.required' => 'المنطقة مطلوبة',
        'region_id.exists' => 'المنطقة غير موجودة',
        'student_id.required' => 'الطالب مطلوب',
        'student_id.exists' => 'الطالب غير موجود',
    ];

    public function mount()
    {
        $this->drivers = Driver::all();
        $this->regions = Region::all();
        $this->Year = now()->format('Y-m-d');

        $this->students = Student::whereNotNull('driver_id')
            ->whereDoesntHave('preparations', function ($q) {
                $q->whereDate('Year', $this->Year);
            })
            ->get();

        $this->loadPreparations();
    }


    public function toggleAtend($prepId)
    {
        $prep = PreparationStu::find($prepId);
        if ($prep) {
            $prep->Atend = !$prep->Atend;
            $prep->save();
            AdminLoggerService::log('تحديث حالة حضور طالب', 'PreparationStu', "تحديث حالة حضور طالب: {$prep->student->Name}");

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'تم تحديث حالة الحضور لطالب'
            ]);
            $this->loadPreparations();
        }
    }
    public function loadPreparations() {}
    public function updatedStudentId($value)
    {
        if ($value) {
            $student = Student::with(['region', 'driver'])->find($value);

            if ($student) {
                $this->Grade     = $student->Grade;
                $this->region_id = $student->region_id;
                $this->Division  = $student->Division;
                $this->driver_id  = $student->driver_id;
            }
        }
    }
    public function createPreparation()
    {
        $this->validate();
        PreparationStu::create([
            'Atend' => $this->Atend,
            'Year' => $this->Year,
            'driver_id' => $this->driver_id,
            'region_id' => $this->region_id,
            'student_id' => $this->student_id,
        ]);
        $this->loadPreparations();
        $preparedIds = PreparationStu::pluck('student_id')->toArray();
        $this->students = Student::whereNotNull('driver_id')
            ->whereNotIn('id', $preparedIds)
            ->get();
        AdminLoggerService::log('اضافة حضور طالب', 'PreparationStu', "اضافة حضور طالب: {$this->student->Name}");

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تسجيل الحضور بنجاح']);
    }

    public function editPreparation($id)
    {
        $prep = PreparationStu::findOrFail($id);
        $this->selectedId = $id;
        $this->editMode = true;
        $this->showForm = true;

        $this->Atend = $prep->Atend;
        $this->Year = $prep->Year;
        $this->driver_id = $prep->driver_id;
        $this->region_id = $prep->region_id;
        $this->student_id = $prep->student_id;
    }

    public function updatePreparation()
    {
        $this->validate();
        $prep = PreparationStu::findOrFail($this->selectedId);
        $prep->update([
            'Atend' => $this->Atend,
            'Year' => $this->Year,
            'driver_id' => $this->driver_id,
            'region_id' => $this->region_id,
            'student_id' => $this->student_id,
        ]);
        AdminLoggerService::log('تحديث حضور طالب', 'PreparationStu', "تحديث حضور طالب: {$prep->student->Name}");

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث سجل الحضور']);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function deletePreparation()
    {
        if ($this->deleteId) {
            PreparationStu::find($this->deleteId)->delete();
            AdminLoggerService::log('حذف حضور طالب', 'PreparationStu', "حذف حضور طالب: {$this->student->Name}");

            $this->deleteId = null;
            $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حذف سجل الحضور']);
        }
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['Atend', 'Year', 'driver_id', 'region_id', 'student_id', 'editMode', 'selectedId', 'showForm', 'deleteId']);
        $this->Year = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->loadPreparations();
    }

    public function render()
    {
        $this->loadPreparations();

        $students = Student::whereNotNull('driver_id')
            ->whereDoesntHave('preparations', function ($q) {
                $q->whereDate('Year', $this->Year);
            })
            ->get();
        $preparations = PreparationStu::with(['driver', 'region', 'student'])
            ->when($this->search, function ($q) {
                $q->whereHas('student', fn($sq) => $sq->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhere('Year', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.preparation-stus', compact('preparations'));
    }
}
