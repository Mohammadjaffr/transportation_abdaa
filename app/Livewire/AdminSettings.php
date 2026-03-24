<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class AdminSettings extends Component
{
    public $morning_lock = '09:00';
    public $leave_lock = '16:00';

    public function mount()
    {
        $this->morning_lock = Cache::rememberForever('attendance_lock_morning', function () {
            return Setting::where('key', 'attendance_lock_morning')->value('value') ?? config('attendance.locks.morning', '09:00');
        });
        
        $this->leave_lock = Cache::rememberForever('attendance_lock_leave', function () {
            return Setting::where('key', 'attendance_lock_leave')->value('value') ?? config('attendance.locks.leave', '16:00');
        });
    }

    public function saveLocks()
    {
        $this->validate([
            'morning_lock' => 'required',
            'leave_lock' => 'required',
        ], [
            'morning_lock.required' => 'وقت إغلاق رحلة الذهاب مطلوب',
            'leave_lock.required' => 'وقت إغلاق رحلة العودة مطلوب',
        ]);

        Setting::updateOrCreate(['key' => 'attendance_lock_morning'], ['value' => $this->morning_lock]);
        Setting::updateOrCreate(['key' => 'attendance_lock_leave'], ['value' => $this->leave_lock]);

        // Flush cache so the driver service picks up the new values immediately
        Cache::forget('attendance_lock_morning');
        Cache::forget('attendance_lock_leave');

        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حفظ أوقات التحضير بنجاح ✅']);
    }

    public function render()
    {
        return view('livewire.admin-settings');
    }
}
