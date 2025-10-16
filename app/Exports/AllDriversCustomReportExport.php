<?php

namespace App\Exports;

use App\Models\Driver;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\DriverCustomReportExport;
use Maatwebsite\Excel\Concerns\Exportable;

class AllDriversCustomReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $from;
    protected $to;
    protected $showNames;

    public function __construct($from, $to, $showNames = false)
    {
        $this->from = $from;
        $this->to = $to;
        $this->showNames = $showNames;
    }

    public function sheets(): array
    {
        $sheets = [];

        $drivers = Driver::all();

        foreach ($drivers as $driver) {
            // استخدم نفس الـ Export القديم للسائق الواحد
            $sheets[] = new DriverCustomReportExport($driver->id, $this->from, $this->to, $this->showNames);
        }

        return $sheets;
    }
}