<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\AdminLoggerService;

class RegisterUser extends Component
{
    public $name, $password, $password_confirmation, $deleteName;
    public $role = '';
    public $driver_id = null;
    public $users;
    public $editId = null;   
    public $deleteId = null; 



    // إنشاء مستخدم جديد
    public function store()
    {
        $this->validate([
              'name'     => 'required|string|unique:users,name|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:admin,driver',
            'driver_id' => 'required_if:role,driver|nullable|exists:drivers,id',
        ],[
            'name.required'     => 'يرجى إدخال اسم المستخدم',
            'password.required' => 'يرجى إدخال كلمة المرور',
            'password.confirmed'=> 'يرجى إدخال نفس كلمة المرور مرة أخرى',
            'name.unique' => 'اسم المستخدم موجود بالفعل',
            'role.required' => 'يرجى اختيار الدور',
            'role.in' => 'الدور غير صحيح',
            'driver_id.required_if' => 'يجب اختيار السائق عند تحديد دور السائق',
        ]);

        User::create([
            'name'     => $this->name,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'driver_id' => $this->role === 'driver' ? $this->driver_id : null,
        ]);

        AdminLoggerService::log('إضافة مستخدم جديد', 'User', "تم إضافة المستخدم: {$this->name}");

        $this->reset(['name','password','password_confirmation', 'role', 'driver_id']);
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تمت إضافة المستخدم ✅']);
    }

    // تحميل بيانات المستخدم للتعديل
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->editId = $id;
        $this->name = $user->name;
        $this->role = $user->role;
        $this->driver_id = $user->driver_id;
    }

    // حفظ التعديل
    public function update()
    {
        $this->validate([
            'name'     => 'required|string|max:255|unique:users,name,' . $this->editId,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|in:admin,driver',
            'driver_id' => 'required_if:role,driver|nullable|exists:drivers,id',
        ],[
            'name.required'     => 'يرجى إدخال اسم المستخدم',
            'password.required' => 'يرجى إدخال كلمة المرور',
            'password.confirmed'=> 'يرجى إدخال نفس كلمة المرور مرة أخرى',
            'name.unique' => 'اسم المستخدم موجود بالفعل',
            'role.required' => 'يرجى اختيار الدور',
            'role.in' => 'الدور غير صحيح',
            'driver_id.required_if' => 'يجب اختيار السائق عند تحديد دور السائق',
        ]);



        $user = User::findOrFail($this->editId);
        $user->name = $this->name;
        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }
        $user->role = $this->role;
        $user->driver_id = $this->role === 'driver' ? $this->driver_id : null;
        $user->save();

        AdminLoggerService::log('تعديل مستخدم', 'User', "تم تعديل المستخدم: {$this->name}");

        $this->reset(['name','password','password_confirmation','editId', 'role', 'driver_id']);
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تعديل المستخدم ✅']);
    }

    // تأكيد الحذف
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $user = User::findOrFail($id);
        $this->deleteName = $user->name;
    }

    // حذف المستخدم
    public function delete()
    {
        $user = User::findOrFail($this->deleteId);
        $userName = $user->name;
        $this->deleteName = null;
        
        $user->delete();

        AdminLoggerService::log('حذف مستخدم', 'User', "تم حذف المستخدم: {$userName}");

        $this->deleteId = null;
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حذف المستخدم ✅']);
    }
        public function render()
    {
        $this->users = User::with('driver')->get();
        $drivers = \App\Models\Driver::all();
        return view('livewire.register-user', compact('drivers'));
    }
}