<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;

class ForceChangePassword extends Component
{
    #[Validate('required|min:6|confirmed', message: [
        'required' => 'حقل كلمة المرور الجديدة مطلوب.',
        'min' => 'يجب أن تتكون كلمة المرور من 6 أحرف على الأقل.',
        'confirmed' => 'حقل تأكيد كلمة المرور غير متطابق.'
    ])]
    public $password;

    public $password_confirmation;

    public function save()
    {
        $this->validate();

        $user = auth()->user();
        
        $user->password = Hash::make($this->password);
        $user->require_password_change = false;
        $user->save();

        $this->redirectRoute('driver.dashboard', navigate: true);
    }

    #[Layout('layouts.driver')]
    public function render()
    {
        return view('livewire.force-change-password');
    }
}
