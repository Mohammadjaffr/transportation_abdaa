<?php

namespace App\Livewire;

use App\Models\Region;
use App\Models\Driver;
use App\Models\Student;
use Livewire\Component;
use App\Models\Wing;
use App\Models\Retreat;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'drivers'      => Driver::count(),
            'students'     => Student::count(),
            'regions'      => Region::count(),
            'coaster'      => Driver::where('Bus_type', 'كوستر')->count(),
            'hiace'        => Driver::where('Bus_type', 'هايس')->count(),
            'buses'        => Driver::whereNotNull('Bus_type')->count(),
            'active_buses' => Driver::where('CheckUp', 'نشط')->count(),
        ];
        $retreats = Retreat::count();
        $wings =Wing::count() ;
        $studentsByRegion = Region::withCount('students')->get(['Name', 'students_count']);
        $driversByRegion  = Region::withCount('drivers')->get(['Name', 'drivers_count']);
        $busesByType      = Driver::selectRaw('Bus_type, COUNT(*) as total')
            ->groupBy('Bus_type')
            ->get();

        // آخر السائقين والطلاب
        $latestDrivers = Driver::latest()->take(5)->get();
        $latestStudents = Student::latest()->take(5)->get();

        return view('livewire.dashboard', compact(
            'stats',
            'studentsByRegion',
            'driversByRegion',
            'busesByType',
            'latestDrivers',
            'latestStudents',
            'wings',
            'retreats'
        ));
    }
}