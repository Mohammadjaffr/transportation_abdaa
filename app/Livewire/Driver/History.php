<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use App\Models\PreparationStu;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class History extends Component
{
    public $month;

    public function mount()
    {
        $this->month = Carbon::now()->format('Y-m');
    }

    public function render()
    {
        $driverId = Auth::user()->driver_id;
        
        $records = PreparationStu::select('Date', 'type', DB::raw('count(*) as total'), DB::raw('SUM(Atend) as present'))
            ->where('driver_id', $driverId)
            ->where('Date', 'like', $this->month . '%')
            ->groupBy('Date', 'type')
            ->orderBy('Date', 'desc')
            ->get();

        $history = [];
        foreach ($records as $record) {
            $history[$record->Date][] = [
                'type' => $record->type,
                'total' => $record->total,
                'present' => $record->present,
                'absent' => $record->total - $record->present
            ];
        }

        return view('livewire.driver.history', [
            'history' => $history
        ])->layout('layouts.driver');
    }
}
