<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\Driver;
use App\Models\Region;

class DistributionStu extends Component
{
    public $students;
    public $drivers;
    public $regions;
    public $stu_postion;

    public $search = '';
    public $regionFilter = '';
    public $driverFilter = '';
    public $positionFilter = '';

    public $selectedStudents = [];
    public $selectedRegion = '';
    public $selectedDriver = '';
    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedStudents = $this->students->pluck('id')->toArray();
        } else {
            $this->selectedStudents = [];
        }
    }

    public function updateDistribution($studentId, $driverId, $regionId,$positionId)
    {
        $student = Student::find($studentId);
        if ($student) {
            if ($driverId !== '') {
                $student->driver_id = $driverId;
            }
            if ($regionId !== '') {
                $student->region_id = $regionId;
            }
            if($positionId !== ''){
                $student->Stu_position = $positionId;
            }
            $student->save();
        }
    }

    public function bulkAssign()
    {
        if (count($this->selectedStudents) > 0) {
            $data = [];

            if ($this->selectedDriver !== '') {
                $data['driver_id'] = $this->selectedDriver;
            }

            if ($this->selectedRegion !== '') {
                $data['region_id'] = $this->selectedRegion;
            }

            if (!empty($data)) {
                Student::whereIn('id', $this->selectedStudents)->update($data);
            }

            $this->selectedStudents = [];
            $this->selectAll = false;

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'تم توزيع الطلاب المحددين بنجاح'
            ]);
        }
    }

    public function render()
    {
        $query = Student::with(['region', 'driver']);

        if ($this->search) {
            $query->where('Name', 'like', "%{$this->search}%");
        }

      if ($this->regionFilter) {
        $childRegions = Region::where('parent_id', $this->regionFilter)->pluck('id');
        
        $query->whereIn('region_id', $childRegions);
    }

        if ($this->driverFilter) {
            $query->where('driver_id', $this->driverFilter);
        }

        if ($this->positionFilter) {
            $query->where('Stu_position', 'like', "%{$this->positionFilter}%");
        }

        $this->students = $query->get();
        $this->drivers = Driver::all();
       $this->regions =  Region::whereNull('parent_id')->get();
        $this->stu_postion = Student::distinct()->pluck('Stu_position')->toArray();

        return view('livewire.distribution-stu');
    }
}