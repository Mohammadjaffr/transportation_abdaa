<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use App\Services\Driver\DriverAttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Attendance extends Component
{
    public $type;
    public $search = '';
    public $date;

    public function mount($type = 'morning')
    {
        $this->type = in_array($type, ['morning', 'leave']) ? $type : 'morning';
        $this->date = Carbon::today()->toDateString();
    }

    public function setAttendance(DriverAttendanceService $service, $studentId, $status)
    {
        if ($service->isLocked($this->type, $this->date)) {
            $this->dispatch('show-toast', ['type' => 'error', 'message' => $service->getLockMessage($this->type)]);
            return;
        }

        $driverId = Auth::user()->driver_id;
        $success = $service->markAttendance($driverId, $studentId, $this->date, $this->type, $status);

        if (!$success) {
            $this->dispatch('show-toast', ['type' => 'error', 'message' => 'حدث خطأ أثناء حفظ التحضير.']);
        }
    }

    public function prepareAllPresent(DriverAttendanceService $service)
    {
        if ($service->isLocked($this->type, $this->date)) {
            $this->dispatch('show-toast', ['type' => 'error', 'message' => $service->getLockMessage($this->type)]);
            return;
        }

        $driverId = Auth::user()->driver_id;
        $students = $service->getStudents($driverId, $this->search);
        
        if ($service->markAllPresent($driverId, $students, $this->date, $this->type)) {
            $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحضير الكل كحاضر بنجاح!']);
        }
    }

    public function render(DriverAttendanceService $service)
    {
        $driverId = Auth::user()->driver_id;
        
        $students = $service->getStudents($driverId, $this->search);
        $records = $service->getAttendanceRecords($driverId, $this->date, $this->type);
        
        $counters = $service->getCounters($students, $records);
        $isLocked = $service->isLocked($this->type, $this->date);
        $lockMessage = $isLocked ? $service->getLockMessage($this->type) : null;

        $studentsList = [];
        foreach ($students as $student) {
            $status = null;
            if ($records->has($student->id)) {
                $status = (bool) $records[$student->id]->Atend;
            }
            
            $studentsList[] = [
                'id' => $student->id,
                'name' => $student->Name,
                'region' => $student->region->Name ?? '',
                'status' => $status,
            ];
        }

        return view('livewire.driver.attendance', [
            'studentsList' => $studentsList,
            'counters' => $counters,
            'isLocked' => $isLocked,
            'lockMessage' => $lockMessage
        ])->layout('layouts.driver');
    }
}
