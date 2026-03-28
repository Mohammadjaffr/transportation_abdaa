<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AdminSettings extends Component
{
    // متغيرات الفترة الصباحية
    public $morning_start;
    public $morning_end;

    // متغيرات الفترة المسائية
    public $leave_start;
    public $leave_end;

    public function mount()
    {
        // جلب البيانات من قاعدة البيانات (أو وضع قيم افتراضية)
        $m_start = Setting::where('key', 'morning_start')->value('value') ?? '07:00';
        $m_end   = Setting::where('key', 'morning_end')->value('value') ?? '09:00';
        
        $l_start = Setting::where('key', 'leave_start')->value('value') ?? '13:00';
        $l_end   = Setting::where('key', 'leave_end')->value('value') ?? '16:00';

        // استخدام Carbon لضمان أن صيغة الوقت هي H:i ليتمكن حقل time من قراءتها
        $this->morning_start = $this->formatTime($m_start, '07:00');
        $this->morning_end   = $this->formatTime($m_end, '09:00');
        $this->leave_start   = $this->formatTime($l_start, '13:00');
        $this->leave_end     = $this->formatTime($l_end, '16:00');
    }

    // دالة مساعدة لتنسيق الوقت وتجنب الأخطاء
    private function formatTime($time, $default)
    {
        try {
            return Carbon::parse($time)->format('H:i');
        } catch (\Exception $e) {
            return $default;
        }
    }

    public function saveLocks()
    {
        // التحقق من أن جميع الحقول ممتلئة
        $this->validate([
            'morning_start' => 'required',
            'morning_end'   => 'required',
            'leave_start'   => 'required',
            'leave_end'     => 'required',
        ], [
            'required' => 'هذا الوقت مطلوب',
        ]);

        // حفظ الإعدادات
        Setting::updateOrCreate(['key' => 'morning_start'], ['value' => $this->morning_start]);
        Setting::updateOrCreate(['key' => 'morning_end'],   ['value' => $this->morning_end]);
        Setting::updateOrCreate(['key' => 'leave_start'],   ['value' => $this->leave_start]);
        Setting::updateOrCreate(['key' => 'leave_end'],     ['value' => $this->leave_end]);

        // مسح الكاش
        Cache::forget('morning_start');
        Cache::forget('morning_end');
        Cache::forget('leave_start');
        Cache::forget('leave_end');

        // إرسال الإشعار للواجهة (Livewire 3 syntax)
        $this->dispatch('show-toast', [
            'type' => 'success', 
            'message' => 'تم حفظ فترات التحضير بنجاح'
        ]);
    }

    public function render()
    {
        return view('livewire.admin-settings');
    }
}