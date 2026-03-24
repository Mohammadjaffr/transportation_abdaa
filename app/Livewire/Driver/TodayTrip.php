<?php

namespace App\Livewire\Driver;

use Livewire\Component;

class TodayTrip extends Component
{
    public function render()
    {
        return view('livewire.driver.today-trip')->layout('layouts.driver');
    }
}
