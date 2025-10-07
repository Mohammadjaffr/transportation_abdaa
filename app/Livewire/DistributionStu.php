<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\Driver;
use App\Models\Region;
use Livewire\WithPagination;

class DistributionStu extends Component
{
    use WithPagination;
    public $paginationTheme = 'bootstrap-5';
    // public $students;
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


    public function updateDistribution($studentId, $driverId = null, $regionId = null, $positionId = null)
    {
        $student = Student::find($studentId);
        if ($student) {
            if (!empty($driverId)) {
                $student->driver_id = $driverId;
            }
            if (!empty($regionId)) {
                $student->region_id = $regionId;
            }
            if (!empty($positionId)) {
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
            $searchTerm = "%{$this->search}%";

            $query->where(function ($q) use ($searchTerm) {
                $q->where('Name', 'like', $searchTerm)
                    ->orWhere('Stu_position', 'like', $searchTerm)
                    ->orWhereHas('region', function ($qr) use ($searchTerm) {
                        $qr->where('Name', 'like', $searchTerm);
                    })
                    ->orWhereHas('driver', function ($qd) use ($searchTerm) {
                        $qd->where('Name', 'like', $searchTerm);
                    });
            });
        }
        if ($this->regionFilter) {
            $childRegions = Region::where('parent_id', $this->regionFilter)->pluck('id')->toArray();

            $regionIds = array_merge([$this->regionFilter], $childRegions);

            $query->whereIn('region_id', $regionIds);
        }


        if ($this->driverFilter) {
            $query->where('driver_id', $this->driverFilter);
        }

        if ($this->positionFilter) {
            $query->where('Stu_position', 'like', "%{$this->positionFilter}%");
        }

        $students = $query->paginate(10);

        $regionIds = $students->pluck('region_id')->filter()->unique();
        $this->drivers = Driver::whereIn('region_id', $regionIds)->get();

        $this->regions = Region::whereNull('parent_id')->get();
        $this->stu_postion = Student::distinct()->pluck('Stu_position')->toArray();

        return view('livewire.distribution-stu', [
            'students' => $students,
            'drivers' => $this->drivers,
        ]);
    }
}