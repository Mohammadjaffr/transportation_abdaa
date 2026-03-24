<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use App\Models\Student;
use App\Models\PreparationStu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $driverId = Auth::user()->driver_id;
        $today = Carbon::today()->toDateString();

        $totalStudents = Student::where('driver_id', $driverId)->count();
        
        $preparedStudents = PreparationStu::where('driver_id', $driverId)
            ->where('Date', $today)
            ->where('Atend', true)
            ->count();
            
        $absentStudents = PreparationStu::where('driver_id', $driverId)
            ->where('Date', $today)
            ->where('Atend', false)
            ->count();

        return view('livewire.driver.dashboard', [
            'totalStudents' => $totalStudents,
            'preparedStudents' => $preparedStudents,
            'absentStudents' => $absentStudents,
            'driver' => Auth::user()->driver,
        ])->layout('layouts.driver');
    }
}
