<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PreparationDriver;
use App\Models\Driver;
use App\Models\Region;
use App\Exports\PreparationDriverStuExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\AdminLoggerService;
use Carbon\Carbon;

class PreparationDrivers extends Component
{
    use WithPagination;

    public $drivers, $regions;

    // الحقول الأساسية
    public $Atend = true;
    public $Date;
    public $driver_id, $region_id, $Alternative_name, $deleteName;

    public $editMode = false, $selectedId;
    public $search = '';
    public $showForm = false;
    public $deleteId = null;

    // التقرير: فترة (من - إلى)
    public $showReport = false;
    public $reportFrom; // Y-m-d
    public $reportTo;   // Y-m-d
    public $selectedDriverReport = null; // Collection تفاصيل السائق داخل الفترة
    public $selectedDriverName = null;
    public $selectedDriverId = null;

    public function mount()
    {
        $this->regions = Region::all();
        $this->Date = now()->format('Y-m-d');

        // افتراضيًا: الشهر الحالي كفترة
        $this->reportFrom = now()->startOfMonth()->format('Y-m-d');
        $this->reportTo   = now()->endOfMonth()->format('Y-m-d');

        $this->refreshDrivers();
        $this->loadPreparations();
    }

    /** تحميل السائقين الذين لم يُسجَّل لهم حضور في يوم Date الحالي */
    private function refreshDrivers()
    {
        $this->drivers = Driver::whereNotNull('Name')
            ->whereDoesntHave('preparations', function ($q) {
                $q->whereDate('Date', $this->Date);
            })
            ->get();
    }

    /** عند اختيار السائق اجلب منطقته تلقائيًا */
    public function updatedDriverId($value)
    {
        if ($value) {
            $driver = Driver::with('region')->find($value);
            if ($driver) {
                $this->region_id = $driver->region_id;
            }
        }
    }

    public function loadPreparations() { /* محجوز للتوسّع مستقبلاً */ }

    public function export()
    {
        return Excel::download(new PreparationDriverStuExport, 'drivers_preparations.xlsx');
    }

    protected $rules = [
        'Atend' => 'required|boolean',
        'Date' => 'required|date',
        'Alternative_name' => 'nullable|string|max:255',
        'driver_id' => 'required|exists:drivers,id',
        'region_id' => 'required|exists:regions,id',
    ];

    protected $messages = [
        'Atend.required' => 'حالة الحضور مطلوبة',
        'Date.required' => 'التاريخ مطلوب',
        'Date.date' => 'التاريخ غير صالح',
        'Alternative_name.string' => 'اسم بديل غير صالح',
        'Alternative_name.max' => 'اسم بديل يجب أن يكون أقل من 255 حرف',
        'driver_id.required' => 'السائق مطلوب',
        'driver_id.exists' => 'السائق غير موجود',
        'region_id.required' => 'المنطقة مطلوبة',
        'region_id.exists' => 'المنطقة غير موجودة',
    ];

    public function createPreparation()
    {
        $this->validate();

        $prep = PreparationDriver::create([
            'Atend' => $this->Atend ? 1 : 0,
            'Date' => $this->Date,
            'Alternative_name' => $this->Alternative_name,
            'driver_id' => $this->driver_id,
            'region_id' => $this->region_id,
        ]);

        AdminLoggerService::log('اضافة حضور السائق', 'PreparationDriver', "اضافة حضور السائق: {$prep->driver->Name}");

        $this->resetForm();
        $this->refreshDrivers();
        $this->loadPreparations();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تسجيل حضور السائق']);
    }

    public function editPreparation($id)
    {
        $prep = PreparationDriver::findOrFail($id);
        $this->selectedId = $id;
        $this->editMode = true;
        $this->showForm = true;
        $this->Alternative_name = $prep->Alternative_name;
        $this->Atend     = $prep->Atend;
        $this->Date      = $prep->Date;
        $this->driver_id = $prep->driver_id;
        $this->region_id = $prep->region_id;
    }

    public function updatePreparation()
    {
        $this->validate();

        $prep = PreparationDriver::findOrFail($this->selectedId);

        $prep->update([
            'Atend'     => $this->Atend ? 1 : 0,
            'Date'      => $this->Date,
            'Alternative_name' => $this->Alternative_name,
            'driver_id' => $this->driver_id,
            'region_id' => $this->region_id,
        ]);

        AdminLoggerService::log('تحديث حضور السائق', 'PreparationDriver', "تحديث حضور السائق: {$prep->driver->Name}");

        $this->resetForm();
        $this->refreshDrivers();
        $this->loadPreparations();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث سجل حضور السائق']);
    }

    public function toggleAtend($prepId)
    {
        $prep = PreparationDriver::find($prepId);
        if ($prep) {
            $prep->Atend = !$prep->Atend;
            $prep->save();

            AdminLoggerService::log('تحديث حالة حضور السائق', 'PreparationDriver', "تحديث حالة حضور السائق: {$prep->driver->Name}");

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'تم تحديث حالة الحضور للسائق'
            ]);

            $this->loadPreparations();
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->deleteName = optional(PreparationDriver::with('driver')->find($id))->driver->Name;
    }

    public function deletePreparation()
    {
        if ($this->deleteId) {
            $prep = PreparationDriver::with('driver')->find($this->deleteId);

            if ($prep) {
                $driverName = $prep->driver?->Name;
                $prep->delete();

                AdminLoggerService::log('حذف حضور السائق','PreparationDriver',"حذف حضور السائق: {$driverName}");

                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'message' => 'تم حذف سجل حضور السائق'
                ]);
            } else {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'السجل غير موجود أو تم حذفه مسبقاً'
                ]);
            }

            $this->deleteId = null;
            $this->refreshDrivers();
            $this->loadPreparations();
        }
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'Atend', 'Date', 'driver_id', 'region_id',
            'Alternative_name', 'editMode', 'selectedId', 'showForm', 'deleteId'
        ]);

        $this->Atend = true;
        $this->Date = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->loadPreparations();
    }

    /* =====================[ التقــــريــــر بالفترة ]===================== */

    // تفعيل وضع التقرير
    public function report()
    {
        $this->showReport = true;

        // تأكد من أن التاريخين مضبوطان
        if (!$this->reportFrom) $this->reportFrom = now()->startOfMonth()->format('Y-m-d');
        if (!$this->reportTo)   $this->reportTo   = now()->endOfMonth()->format('Y-m-d');

        if ($this->selectedDriverId) {
            $this->showDriverDetails($this->selectedDriverId);
        }
    }

    // إغلاق وضع التقرير
    public function closeReport()
    {
        $this->showReport = false;
        $this->selectedDriverReport = null;
        $this->selectedDriverName = null;
        $this->selectedDriverId = null;
    }

    // عند تغيير تاريخ من/إلى، حدّث التفاصيل لو كانت مفتوحة
    public function updatedReportFrom()
    {
        $this->normalizeReportRange();
        if ($this->showReport && $this->selectedDriverId) {
            $this->showDriverDetails($this->selectedDriverId);
        }
    }

    public function updatedReportTo()
    {
        $this->normalizeReportRange();
        if ($this->showReport && $this->selectedDriverId) {
            $this->showDriverDetails($this->selectedDriverId);
        }
    }

    // ضمان أن reportFrom <= reportTo
    private function normalizeReportRange()
    {
        if ($this->reportFrom && $this->reportTo) {
            $from = Carbon::parse($this->reportFrom);
            $to   = Carbon::parse($this->reportTo);
            if ($from->greaterThan($to)) {
                // بدّل بينهما لو المستخدم عكس التواريخ
                [$this->reportFrom, $this->reportTo] = [$this->reportTo, $this->reportFrom];
            }
        }
    }

    // تفاصيل سائق محدد داخل الفترة
    public function showDriverDetails($driverId)
    {
        $this->selectedDriverId = $driverId;

        $from = Carbon::parse($this->reportFrom)->startOfDay()->toDateString();
        $to   = Carbon::parse($this->reportTo)->endOfDay()->toDateString();

        $this->selectedDriverReport = PreparationDriver::with(['region','driver'])
            ->where('driver_id', $driverId)
            ->whereBetween('Date', [$from, $to])
            ->orderBy('Date','asc')
            ->get();

        $this->selectedDriverName = optional(Driver::find($driverId))->Name
            ?? optional($this->selectedDriverReport->first())->driver->Name
            ?? 'غير معروف';
    }

    public function render()
    {
        // الجدول الرئيسي (غير التقرير)
        $preparations = PreparationDriver::with(['driver', 'region'])
            ->when($this->search, function ($q) {
                $searchTerm = '%' . $this->search . '%';
                $q->where(function ($sub) use ($searchTerm) {
                        $sub->where('Atend', 'like', $searchTerm)
                            ->orWhere('Date', 'like', $searchTerm);
                    })
                  ->orWhereHas('driver', function ($dq) use ($searchTerm) {
                        $dq->where('Name', 'like', $searchTerm)
                           ->orWhere('IDNo', 'like', $searchTerm)
                           ->orWhere('Phone', 'like', $searchTerm);
                    })
                  ->orWhereHas('region', function ($rq) use ($searchTerm) {
                        $rq->where('Name', 'like', $searchTerm);
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        // بيانات التقرير: عدد أيام الغياب لكل سائق في الفترة
        $reportData = collect();
        if ($this->showReport && $this->reportFrom && $this->reportTo) {
            $from = Carbon::parse($this->reportFrom)->startOfDay()->toDateString();
            $to   = Carbon::parse($this->reportTo)->endOfDay()->toDateString();

            $reportData = PreparationDriver::select('driver_id')
                ->with('driver')
                ->selectRaw('SUM(CASE WHEN Atend = 0 THEN 1 ELSE 0 END) as absence_days')
                ->whereBetween('Date', [$from, $to])
                ->groupBy('driver_id')
                ->orderByDesc('absence_days')
                ->get();
        }

        return view('livewire.preparation-drivers', compact('preparations', 'reportData'));
    }
}