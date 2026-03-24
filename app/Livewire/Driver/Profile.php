<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public function render()
    {
        $user = Auth::user();
        $driver = $user->driver;
        
        return view('livewire.driver.profile', [
            'user' => $user,
            'driver' => $driver
        ])->layout('layouts.driver');
    }
}
