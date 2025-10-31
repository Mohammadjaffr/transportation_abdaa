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
    public $regionDrivers = [];
    public $showUnassignedOnly = false;


    public function toggleUnassigned()
{
    $this->showUnassignedOnly = !$this->showUnassignedOnly;
}


public function updateDistribution($studentId, $driverId = null, $regionId = null, $positionId = null)
{
    $student = Student::find($studentId);
    if (!$student) return;

    // ✅ عند تغيير المنطقة فقط (بدون اختيار سائق)
    if (!empty($regionId) && empty($driverId)) {
        // جلب السائقين المرتبطين بالمنطقة عبر العلاقة many-to-many
        $driversForRegion = Driver::whereHas('regions', function ($q) use ($regionId) {
            $q->where('regions.id', $regionId);
        })->get();

        // 🔴 لا يوجد سائق → تعطيل القائمة
        if ($driversForRegion->count() === 0) {
            $student->region_id = $regionId;
            $student->driver_id = null;
            $student->save();
            $this->regionDrivers[$studentId] = collect(); // فارغة = تعطيل
            return;
        }

        // 🟢 يوجد سائق واحد → تعيينه تلقائياً
        if ($driversForRegion->count() === 1) {
            $driverId = $driversForRegion->first()->id;
        }

        // 🟡 أكثر من سائق → عرضهم للاختيار
        if ($driversForRegion->count() > 1) {
            $this->regionDrivers[$studentId] = $driversForRegion;
            $student->region_id = $regionId;
            $student->save();
            return;
        }
    }

    // ⚙️ تحديث القيم النهائية
    if (!empty($driverId)) $student->driver_id = $driverId;
    if (!empty($regionId)) $student->region_id = $regionId;
    if (!empty($positionId)) $student->Stu_position = $positionId;
    $student->save();

    // إزالة القائمة المؤقتة بعد الحفظ
    unset($this->regionDrivers[$studentId]);
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
    public function updatedRegionFilter($value)
    {
        // عند اختيار منطقة معينة
        if ($value) {
            $this->drivers = Driver::whereHas('regions', function ($q) use ($value) {
                $q->where('regions.id', $value);
            })->get();
        } else {
            // إذا لم يتم اختيار منطقة، نظهر جميع السائقين
            $this->drivers = Driver::with('regions')->get();
        }
    }


   public function render()
{
    $query = Student::with(['region', 'driver']);

    // ✅ فلتر عرض غير الموزعين فقط
    if ($this->showUnassignedOnly) {
        $query->where(function ($q) {
            $q->whereNull('region_id')
              ->orWhereNull('driver_id')
              ->orWhereNull('Stu_position');
        });
    }

    // 🔍 البحث والفلاتر الأخرى كما هي
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

    // تحميل البيانات العامة
    $this->regions = Region::whereNull('parent_id')->get();
    $this->stu_postion = Student::distinct()->pluck('Stu_position')->toArray();

    if (!$this->drivers) {
        $this->drivers = Driver::with('regions')->get();
    }

    // ✅ تحقق إذا ما في أي طالب غير موزع
    $unassignedCount = Student::where(function ($q) {
        $q->whereNull('region_id')
          ->orWhereNull('driver_id')
          ->orWhereNull('Stu_position');
    })->count();

    return view('livewire.distribution-stu', [
        'students' => $students,
        'drivers' => $this->drivers,
        'unassignedCount' => $unassignedCount,
    ]);
}

}