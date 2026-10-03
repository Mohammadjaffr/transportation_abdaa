<?php

namespace App\Livewire\Guardian;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        /*
        |--------------------------------------------------------------------------
        | المستخدم الحالي
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | ولي الأمر المرتبط بالحساب
        |--------------------------------------------------------------------------
        */

        $guardian = $user->guardian;


        /*
        |--------------------------------------------------------------------------
        | أبناء ولي الأمر فقط
        |--------------------------------------------------------------------------
        |
        | مهم:
        | لا نستخدم Student::all()
        | ولا Student::get()
        |
        | نبدأ دائمًا من علاقة ولي الأمر.
        |
        */

        $students = $guardian
            ->students()
            ->with([
                'wing',
                'region',
                'teacher',
                'driver',
                'schoolYear',
            ])
            ->orderBy('Name')
            ->get();


        return view(
            'livewire.guardian.dashboard',
            [
                'guardian' => $guardian,
                'students' => $students,
                'totalStudents' => $students->count(),
            ]
        )
            ->layout('layouts.guardian');
    }
}