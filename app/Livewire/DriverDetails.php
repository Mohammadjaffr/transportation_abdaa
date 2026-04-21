<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Driver;
use App\Models\User;
use App\Models\PreparationDriver;
use Illuminate\Support\Facades\Hash;
use App\Services\AdminLoggerService;
use Carbon\Carbon;

class DriverDetails extends Component
{
    public $driverId;
    public $driver;
    public $newPassword;

    public function mount($driverId)
    {
        $this->driverId = $driverId;
        $this->loadDriver();
    }

    public function loadDriver()
    {
        // تم إضافة refresh لضمان تحديث البيانات بعد العمليات
        $this->driver = Driver::with(['user', 'bus', 'wing', 'students.region', 'regions'])->findOrFail($this->driverId);
    }

    public function createDriverAccount()
    {
        if ($this->driver->user) {
            $this->dispatch('show-toast', type: 'error', message: 'هذا السائق لديه حساب بالفعل');
            return;
        }

        $existingUser = User::where('name', '=', $this->driver->IDNo)->first();
        if ($existingUser) {
            $this->dispatch('show-toast', type: 'error', message: 'رقم البطاقة مسجل مسبقاً كاسم مستخدم لحساب آخر');
            return;
        }

        $password = '123456';

        User::create([
            'name' => $this->driver->Name,
            'email' => null,
            'password' => Hash::make($password),
            'role' => 'driver',
            'driver_id' => $this->driver->id,
            'is_banned' => false,
            'require_password_change' => true,
        ]);

        $this->logAction('إنشاء حساب', "تم إنشاء حساب للسائق: {$this->driver->Name}");

        $this->loadDriver();
        $this->dispatch('show-toast', type: 'success', message: 'تم إنشاء الحساب بنجاح! رقم الدخول: ' . $this->driver->Name . ' وكلمة المرور: 123456');
    }

    /**
     * وظيفة حظر أو إلغاء حظر الحساب (Toggle)
     */
    public function toggleAccountStatus()
    {
        if (!$this->driver->user) return;

        $user = $this->driver->user;
        $newStatus = !$user->is_banned;

        $user->update(['is_banned' => $newStatus]);

        $actionLabel = $newStatus ? 'حظر حساب' : 'تفعيل حساب';
        $message = $newStatus ? 'تم حظر حساب الكابتن بنجاح' : 'تم إلغاء الحظر وتفعيل الحساب';
        $type = $newStatus ? 'warning' : 'success';

        $this->logAction($actionLabel, "{$actionLabel} السائق: {$this->driver->Name}");

        $this->loadDriver();
        $this->dispatch('show-toast', type: $type, message: $message);
    }

    /**
     * وظيفة حذف (إلغاء) الحساب نهائياً
     */
    public function deleteAccount()
    {
        if (!$this->driver->user) return;

        $userName = $this->driver->user->name;
        $this->driver->user->delete();

        $this->logAction('إلغاء حساب', "تم حذف حساب المستخدم المرتبط بالسائق: {$this->driver->Name}");

        $this->loadDriver();
        $this->dispatch('show-toast', type: 'success', message: 'تم حذف حساب الدخول للسائق نهائياً');
    }

    public function resetPassword()
    {
        $this->validate([
            'newPassword' => 'required|min:6'
        ], [
            'newPassword.required' => 'يرجى إدخال كلمة المرور',
            'newPassword.min' => 'يجب ألا تقل عن 6 أحرف',
        ]);

        if (!$this->driver->user) return;

        $this->driver->user->update([
            'password' => Hash::make($this->newPassword),
            'require_password_change' => true,
        ]);

        $this->logAction('تحديث كلمة مرور', "تم تغيير كلمة مرور السائق: {$this->driver->Name}");

        $this->newPassword = '';
        $this->dispatch('close-password-modal');
        $this->dispatch('show-toast', type: 'success', message: 'تم إعادة تعيين كلمة المرور بنجاح');
    }

    // دالة مساعدة لتقليل تكرار كود الـ Log
    private function logAction($action, $description)
    {
        if (class_exists(AdminLoggerService::class)) {
            AdminLoggerService::log($action, 'User', $description);
        }
    }

    public function getTodayAttendanceStatusProperty()
    {
        $today = Carbon::today()->format('Y-m-d');
        return PreparationDriver::where('driver_id', $this->driverId)
            ->where('Date', '=', $today)
            ->first();
    }
    public function resetPasswordToDefault()
{
    if (!$this->driver->user) {
        $this->dispatch('show-toast', type: 'error', message: 'لا يوجد حساب مرتبط بهذا السائق');
        return;
    }

    $defaultPassword = '123456';

    $this->driver->user->update([
        'password' => Hash::make($defaultPassword),
        'require_password_change' => true,
    ]);

    $this->logAction('إعادة تعيين كلمة المرور', "تم إعادة تعيين كلمة مرور السائق: {$this->driver->Name} إلى الرقم الافتراضي");

    $this->dispatch('show-toast', type: 'success', message: 'تم إعادة تعيين كلمة المرور إلى 123456 بنجاح');
}

    public function render()
    {
        return view('livewire.driver-details', [
            'todayAttendance' => $this->todayAttendanceStatus,
        ]);
    }
}
