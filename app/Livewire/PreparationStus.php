<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PreparationStu;
use App\Models\Driver;
use App\Models\Student;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DriverReportExport;
use App\Exports\DriverCustomReportExport;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\DB;

class PreparationStus extends Component
{
    public $type = null;
    public $selectedDriver = null;
    public $driverStudents = [];
    public $activeTab = 'morning'; // morning | leave | report | custom | missing

    // فلاتر التقرير المخصص
    public $from_date;
    public $to_date;
    public $showNames = false; 
    public $customReport = [];

    // تحضير مفقود
    public $missingDate;            
    public $missingDrivers = [];        
    public $selectedMissingDriverId = null; 
    public $manualStudents = [];         

    /* ==================== تبويب التحكم ==================== */

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->selectedDriver = null;
        $this->driverStudents = [];

        if ($tab === 'custom') {
            $today = Carbon::today()->toDateString();
            $this->from_date = $today;
            $this->to_date = $today;
            $this->showNames = false;
            $this->customReport = [];
        }

        if ($tab === 'missing') {
            $this->missingDate = Carbon::today()->toDateString();
            $this->missingDrivers = [];
            $this->selectedMissingDriverId = null;
            $this->manualStudents = [];
        }
    }

    /* ==================== تبويب تحضير مفقود ==================== */

    // جلب السائقين الذين لديهم طلاب ولم يسجلوا أي تحضير (صباح/انصراف) في التاريخ المحدد
    public function findMissingDrivers()
    {
        $this->validate([
            'missingDate' => 'required|date',
        ]);

        $date = Carbon::parse($this->missingDate)->toDateString();

        $drivers = Driver::has('students')
            ->whereNotExists(function ($query) use ($date) {
                $query->select(DB::raw(1))
                    ->from('preparation_stus')
                    ->whereColumn('preparation_stus.driver_id', 'drivers.id')
                    ->whereDate('preparation_stus.Date', $date)
                    ->whereIn('preparation_stus.type', ['morning', 'leave']);
            })
            ->withCount('students')
            ->get(['id', 'Name']);

        $this->missingDrivers = $drivers->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->Name,
            'students_count' => $d->students_count,
        ])->toArray();

        // إغلاق لوحة التحضير اليدوي (إن وُجدت) بعد تغيير التاريخ
        $this->selectedMissingDriverId = null;
        $this->manualStudents = [];
    }

    // فتح لوحة التحضير اليدوي لسائق معيّن داخل نفس التبويب
    public function startManualPrepare($driverId)
    {
        $this->selectedMissingDriverId = $driverId;
        $this->loadManualStudents();
    }

    // تحميل الطلاب مع حالات صباح/انصراف في التاريخ المحدد
    public function loadManualStudents($driverId = null)
    {
        if ($driverId) {
            $this->selectedMissingDriverId = $driverId;
        }

        if (!$this->selectedMissingDriverId || !$this->missingDate) {
            $this->manualStudents = [];
            return;
        }

        $date = Carbon::parse($this->missingDate)->toDateString();
        $driverId = $this->selectedMissingDriverId;

        // جميع طلاب السائق
        $students = Student::where('driver_id', $driverId)
            ->orderBy('Name')
            ->get(['id', 'Name', 'region_id']);

        // سجلات التحضير الحالية (إن وُجدت) لهذا التاريخ
        $records = PreparationStu::where('driver_id', $driverId)
            ->whereDate('Date', $date)
            ->whereIn('type', ['morning', 'leave'])
            ->get(['student_id', 'type', 'Atend']);

        // فهرسة الحالات: [student_id][type] => bool
        $idx = [];
        foreach ($records as $r) {
            $idx[$r->student_id][$r->type] = (bool) $r->Atend;
        }

        $rows = [];
        foreach ($students as $s) {
            $rows[] = [
                'student_id'   => $s->id,
                'student_name' => $s->Name,
                'morning'      => $idx[$s->id]['morning'] ?? false,
                'leave'        => $idx[$s->id]['leave'] ?? false,
            ];
        }

        $this->manualStudents = $rows;
    }

    // تحديث مباشر عند تغيير Toggle (صباح/انصراف)
    public function setAttendance($studentId, $period, $checked)
    {
        if (!in_array($period, ['morning', 'leave'])) return;

        $checked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
        $date = Carbon::parse($this->missingDate ?: Carbon::today())->toDateString();

        $student = Student::findOrFail($studentId);
        $driverId = $student->driver_id;

        PreparationStu::updateOrCreate(
            [
                'student_id' => $studentId,
                'driver_id'  => $driverId,
                'Date'       => $date,
                'type'       => $period,
            ],
            [
                'Atend'     => $checked,
                'region_id' => $student->region_id,
            ]
        );

        // حدّث المصفوفة المحلية لواجهة المستخدم
        foreach ($this->manualStudents as &$row) {
            if ($row['student_id'] == $studentId) {
                $row[$period] = $checked;
                break;
            }
        }

        // إشعار بسيط (كما طلبت - لا تغيير)
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم تحديث الحالة مباشرة'
        ]);
    }

    // تحضير تلقائي لكل السائقين الظاهرين في القائمة (حسب رغبتك في الإبقاء عليه)
    public function autoPrepareAllMissing()
    {
        if (!$this->missingDate) {
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'تم اضافة التحضير التلقائي لكل الطلاب الظاهرين'
            ]);
            return;
        }

        $date = Carbon::parse($this->missingDate)->toDateString();

        $ids = collect($this->missingDrivers)->pluck('id')->all();

        foreach ($ids as $driverId) {
            $students = Student::where('driver_id', $driverId)->get();
            foreach ($students as $stu) {
                // صباح
                PreparationStu::firstOrCreate([
                    'student_id' => $stu->id,
                    'driver_id'  => $driverId,
                    'Date'       => $date,
                    'type'       => 'morning',
                ], [
                    'Atend'     => true,
                    'region_id' => $stu->region_id,
                ]);

                // انصراف
                PreparationStu::firstOrCreate([
                    'student_id' => $stu->id,
                    'driver_id'  => $driverId,
                    'Date'       => $date,
                    'type'       => 'leave',
                ], [
                    'Atend'     => true,
                    'region_id' => $stu->region_id,
                ]);
            }
        }

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'تم اضافة التحضير التلقائي لكل الطلاب الظاهرين'
        ]);

        // حدّث القائمة بعد التحضير
        $this->findMissingDrivers();
        $this->selectedMissingDriverId = null;
        $this->manualStudents = [];
    }

    /* ==================== تبويبا الصباح/الانصراف (كما هي) ==================== */

    public function updatedSelectedDriver($driverId)
    {
        if ($driverId && in_array($this->activeTab, ['morning', 'leave'])) {
            $students = Student::where('driver_id', $driverId)->get();

            foreach ($students as $stu) {
                PreparationStu::updateOrCreate(
                    [
                        'student_id' => $stu->id,
                        'Date' => Carbon::today()->toDateString(),
                        'type' => $this->activeTab,
                    ],
                    [
                        'driver_id' => $driverId,
                        'region_id' => $stu->region_id,
                        'Atend' => true,
                    ]
                );
            }

            $this->loadDriverStudents();
        }
    }

    public function loadDriverStudents()
    {
        if ($this->selectedDriver && in_array($this->activeTab, ['morning', 'leave'])) {
            $this->driverStudents = PreparationStu::with('student', 'region', 'driver')
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

    /* ==================== تقرير السائق/تقرير مخصص (كما لديك) ==================== */

    public function exportDriverReport($driverId)
    {
        $driver = Driver::findOrFail($driverId);
        $driverName = str_replace(' ', '_', $driver->Name);

        return Excel::download(
            new DriverReportExport($driverId, now()->toDateString()),
            "تقرير_السائق_{$driverName}.xlsx"
        );
    }

    public function generateCustomReport()
    {
        $this->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ]);

        $from = Carbon::parse($this->from_date)->toDateString();
        $to   = Carbon::parse($this->to_date)->toDateString();

        $drivers = $this->selectedDriver
            ? Driver::where('id', $this->selectedDriver)->get()
            : Driver::all();

        $results = [];

        foreach ($drivers as $drv) {

            $records = PreparationStu::with('student:id,Name')
                ->where('driver_id', $drv->id)
                ->whereBetween('Date', [$from, $to])
                ->whereIn('type', ['morning', 'leave'])
                ->get();

            // الحضور الكلي: يحسب للطالب الذي حضر الفترتين في نفس اليوم
            $studentsByDay = $records->groupBy(function ($r) {
                return $r->student_id . '|' . $r->Date;
            });

            $presentCount = 0;
            foreach ($studentsByDay as $rows) {
                $morning = $rows->firstWhere('type', 'morning');
                $leave   = $rows->firstWhere('type', 'leave');
                if ($morning?->Atend && $leave?->Atend) {
                    $presentCount++;
                }
            }

            // الغياب: تجميع جزئي/كلي باليوم
            $groupedAbsences = $records->where('Atend', false)
                ->groupBy(function ($item) {
                    return $item->student_id . '-' . $item->Date;
                });

            $absentPartCount = 0;
            $totalAbsentCount = 0;
            $absentees = [];

            foreach ($groupedAbsences as $dayRecords) {
                $type = $dayRecords->count() == 1
                    ? ($dayRecords->first()->type == 'morning' ? 'جزئي (صباح)' : 'جزئي (انصراف)')
                    : 'كلي';

                if ($dayRecords->count() == 1) $absentPartCount++;
                else $totalAbsentCount++;

                if ($this->showNames) {
                    $row = $dayRecords->first();
                    $absentees[] = [
                        'student' => optional($row->student)->Name,
                        'date'    => $row->Date,
                        'type'    => $type,
                    ];
                }
            }

            $results[] = [
                'driver_id'     => $drv->id,
                'driver_name'   => $drv->Name,
                'present'       => $presentCount,
                'absent_part'   => $absentPartCount,
                'total_absent'  => $totalAbsentCount,
                'absentees'     => $absentees,
                'from'          => $from,
                'to'            => $to,
            ];
        }

        $this->customReport = $results;
    }

    public function exportCustomReport()
    {
        $this->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ]);

        $from = Carbon::parse($this->from_date)->toDateString();
        $to   = Carbon::parse($this->to_date)->toDateString();

        if ($this->selectedDriver) {
            $driver = Driver::findOrFail($this->selectedDriver);
            $driverName = str_replace(' ', '_', $driver->Name);

            return Excel::download(
                new DriverCustomReportExport($driver->id, $from, $to, $this->showNames),
                "تقرير_مخصص_{$driverName}_{$from}_{$to}.xlsx"
            );
        }

        return $this->exportAllDriversAsZip($from, $to, $this->showNames);
    }

    protected function exportAllDriversAsZip($from, $to, $showNames)
    {
        $drivers = Driver::all();

        $folder = 'reports/tmp_' . now()->format('Ymd_His');
        Storage::makeDirectory($folder);

        $paths = [];
        foreach ($drivers as $drv) {
            $driverName = str_replace(' ', '_', $drv->Name);
            $fileName = "تقرير_مخصص_{$driverName}_{$from}_{$to}.xlsx";
            $relativePath = "{$folder}/{$fileName}";

            Excel::store(
                new DriverCustomReportExport($drv->id, $from, $to, $showNames),
                $relativePath
            );

            $paths[] = [
                'absolute' => storage_path('app/' . $relativePath),
                'relative' => $relativePath,
                'name' => $fileName,
            ];
        }

        $zipName = "تقارير_كل_السائقين_{$from}_{$to}.zip";
        $zipRelative = "{$folder}/{$zipName}";
        $zipAbsolute = storage_path('app/' . $zipRelative);

        $zip = new ZipArchive;
        if ($zip->open($zipAbsolute, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($paths as $p) {
                $zip->addFile($p['absolute'], $p['name']);
            }
            $zip->close();
        }

        return response()->download($zipAbsolute);
    }

    public function render()
    {
        $today = Carbon::today()->toDateString();

        $morningPreps = PreparationStu::with(['student', 'driver', 'region'])
            ->whereDate('Date', $today)
            ->where('type', 'morning')
            ->get();

        $leavePreps = PreparationStu::with(['student', 'driver', 'region'])
            ->whereDate('Date', $today)
            ->where('type', 'leave')
            ->get();

        $drivers = Driver::with('students')->get();

        return view('livewire.preparation-stus', compact('morningPreps', 'leavePreps', 'drivers'));
    }
}