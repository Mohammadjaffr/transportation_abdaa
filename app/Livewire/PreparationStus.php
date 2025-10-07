<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PreparationStu;
use App\Models\Driver;
use App\Models\Student;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DriverReportExport;

class PreparationStus extends Component
{
    public $type = null;
    public $selectedDriver = null;
    public $driverStudents = [];
    public $activeTab = 'morning'; // morning | leave | report

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->selectedDriver = null;
        $this->driverStudents = [];
    }

    public function updatedSelectedDriver($driverId)
    {
        // ندخل بيانات فقط لو التاب صباحي أو انصراف
        if ($driverId && in_array($this->activeTab, ['morning', 'leave'])) {
            $students = Student::where('driver_id', $driverId)->get();

            foreach ($students as $stu) {
                PreparationStu::updateOrCreate(
                    [
                        'student_id' => $stu->id,
                        'Date'       => Carbon::today()->toDateString(),
                        'type'       => $this->activeTab, // فقط morning أو leave
                    ],
                    [
                        'driver_id'  => $driverId,
                        'region_id'  => $stu->region_id,
                        'Atend'      => true, // افتراضي حاضر
                    ]
                );
            }

            $this->loadDriverStudents();
        }
    }

    public function loadDriverStudents()
    {
        if ($this->selectedDriver && in_array($this->activeTab, ['morning','leave'])) {
            $this->driverStudents = PreparationStu::with('student','region')
                ->where('driver_id', $this->selectedDriver)
                ->where('Date', Carbon::today()->toDateString())
                ->where('type', $this->activeTab)
                ->get();
        } else {
            $this->driverStudents = [];
        }
    }

    public function toggleAtend($prepId)
    {
        $prep = PreparationStu::find($prepId);
        if ($prep) {
            $prep->Atend = !$prep->Atend;
            $prep->save();
            $this->loadDriverStudents();
        }
    }

    public function exportDriverReport($driverId)
    {
        return Excel::download(
            new DriverReportExport($driverId, now()->toDateString()),
            "driver_report_{$driverId}.xlsx"
        );
    }

    public function render()
    {
        $today = Carbon::today()->toDateString();

        $morningPreps = PreparationStu::with(['student','driver','region'])
            ->whereDate('Date', $today)
            ->where('type', 'morning')
            ->get();

        $leavePreps = PreparationStu::with(['student','driver','region'])
            ->whereDate('Date', $today)
            ->where('type', 'leave')
            ->get();

        $drivers = Driver::with('students')->get();

        return view('livewire.preparation-stus', compact('morningPreps','leavePreps','drivers'));
    }
}