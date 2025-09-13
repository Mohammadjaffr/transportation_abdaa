<?php

namespace App\Livewire;

use App\Models\region;
use Livewire\Component;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\Student;
use App\Models\Location;
class Dashboard extends Component
{
    public function render()
    {
           $stats = [
            'buses' => Bus::count(),
            'drivers' => Driver::count(),
            'students' => Student::count(),
            'regions' => region::count(),
            'active_buses' => Bus::where('StudentsNo', '>', 0)->count(),
            'coaster'  => Bus::where('BusType', 'كوستر')->count(),
            'hiace'        => Bus::where('BusType', 'هايس')->count(),
        ];

        return view('livewire.dashboard', compact('stats'));
    
    }
}