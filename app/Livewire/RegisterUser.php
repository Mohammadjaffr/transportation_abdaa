<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\User;
use App\Models\Driver;
use App\Models\Guardian;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Services\AdminLoggerService;

class RegisterUser extends Component
{
    public $name;
    public $password;
    public $password_confirmation;
    public $deleteName;

    public $role = '';

    public $driver_id = null;

    public $guardian_id = null;

    public $users;

    public $editId = null;

    public $deleteId = null;


    /*
    |--------------------------------------------------------------------------
    | عند تغيير الدور
    |--------------------------------------------------------------------------
    */

    public function updatedRole($value)
    {
        $this->resetValidation();

        /*
         * إذا أصبح سائقًا
         * نلغي guardian_id
         */
        if ($value === 'driver') {

            $this->guardian_id = null;

            return;
        }


        /*
         * إذا أصبح ولي أمر
         * نلغي driver_id
         */
        if ($value === 'guardian') {

            $this->driver_id = null;

            return;
        }


        /*
         * إذا كان Admin أو فارغ
         */
        $this->driver_id = null;

        $this->guardian_id = null;
    }


    /*
    |--------------------------------------------------------------------------
    | عند اختيار السائق
    |--------------------------------------------------------------------------
    */

    public function updatedDriverId($value)
    {
        if (!empty($value)) {

            $driver = Driver::find($value);

            if ($driver) {

                /*
                 * نحافظ على السلوك الحالي للنظام:
                 * اسم السائق يصبح اسم المستخدم.
                 */
                $this->name = $driver->Name;

                $this->password = '';

                $this->password_confirmation = '';
            }
        } else {

            if (!$this->editId) {

                $this->name = '';
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | عند اختيار ولي الأمر
    |--------------------------------------------------------------------------
    */

    public function updatedGuardianId($value)
    {
        if (empty($value)) {

            if (
                !$this->editId
                && $this->role === 'guardian'
            ) {

                $this->name = '';
            }

            return;
        }


        $guardian = Guardian::find($value);


        /*
         * عند إنشاء حساب جديد فقط:
         * نضع اسم ولي الأمر كبداية لاسم المستخدم.
         *
         * لكن حقل اسم المستخدم سيبقى قابلًا للتعديل،
         * لأنه يجب أن يكون Unique.
         */
        if (
            $guardian
            && !$this->editId
        ) {

            $this->name = $guardian->name;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | إنشاء مستخدم
    |--------------------------------------------------------------------------
    */

    public function store()
    {
        $this->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:users,name',
                ],

                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'confirmed',
                ],

                'role' => [
                    'required',
                    'string',
                    'in:admin,driver,guardian',
                ],


                /*
                 * السائق مطلوب فقط إذا كان الدور driver
                 */
                'driver_id' => [
                    'required_if:role,driver',
                    'nullable',
                    'exists:drivers,id',

                    Rule::unique(
                        'users',
                        'driver_id'
                    ),
                ],


                /*
                 * ولي الأمر مطلوب فقط إذا كان الدور guardian
                 */
                'guardian_id' => [
                    'required_if:role,guardian',
                    'nullable',
                    'exists:guardians,id',

                    Rule::unique(
                        'users',
                        'guardian_id'
                    ),
                ],
            ],

            [
                'name.required' =>
                'يرجى إدخال اسم المستخدم',

                'name.unique' =>
                'اسم المستخدم موجود بالفعل',

                'password.required' =>
                'يرجى إدخال كلمة المرور',

                'password.min' =>
                'يجب ألا تقل كلمة المرور عن 6 أحرف',

                'password.confirmed' =>
                'كلمتا المرور غير متطابقتين',

                'role.required' =>
                'يرجى اختيار الدور',

                'role.in' =>
                'الدور غير صحيح',

                'driver_id.required_if' =>
                'يجب اختيار السائق عند تحديد دور السائق',

                'driver_id.exists' =>
                'السائق المحدد غير موجود',

                'driver_id.unique' =>
                'هذا السائق لديه حساب مستخدم بالفعل',

                'guardian_id.required_if' =>
                'يجب اختيار ولي الأمر عند تحديد دور ولي الأمر',

                'guardian_id.exists' =>
                'ولي الأمر المحدد غير موجود',

                'guardian_id.unique' =>
                'ولي الأمر هذا لديه حساب مستخدم بالفعل',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | إنشاء المستخدم
        |--------------------------------------------------------------------------
        */

        User::create([

            'name' =>
            $this->name,

            'password' =>
            Hash::make(
                $this->password
            ),

            'role' =>
            $this->role,


            /*
             * driver_id فقط للسائق
             */
            'driver_id' =>
            $this->role === 'driver'
                ? $this->driver_id
                : null,


            /*
             * guardian_id فقط لولي الأمر
             */
            'guardian_id' =>
            $this->role === 'guardian'
                ? $this->guardian_id
                : null,
        ]);


        AdminLoggerService::log(
            'إضافة مستخدم جديد',
            'User',
            "تم إضافة المستخدم: {$this->name}"
        );


        $this->resetForm();


        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تمت إضافة المستخدم بنجاح'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | تحميل المستخدم للتعديل
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $user = User::findOrFail($id);


        $this->editId =
            $user->id;

        $this->name =
            $user->name;

        $this->role =
            $user->role;

        $this->driver_id =
            $user->driver_id;

        $this->guardian_id =
            $user->guardian_id;


        /*
         * لا نعرض كلمة المرور القديمة
         */
        $this->password = '';

        $this->password_confirmation = '';


        $this->resetValidation();
    }


    /*
    |--------------------------------------------------------------------------
    | تحديث مستخدم
    |--------------------------------------------------------------------------
    */

    public function update()
    {
        $this->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',

                    Rule::unique(
                        'users',
                        'name'
                    )
                        ->ignore(
                            $this->editId
                        ),
                ],


                /*
                 * كلمة المرور اختيارية عند التعديل
                 */
                'password' => [
                    'nullable',
                    'string',
                    'min:6',
                    'confirmed',
                ],


                'role' => [
                    'required',
                    'string',
                    'in:admin,driver,guardian',
                ],


                'driver_id' => [
                    'required_if:role,driver',
                    'nullable',
                    'exists:drivers,id',

                    Rule::unique(
                        'users',
                        'driver_id'
                    )
                        ->ignore(
                            $this->editId
                        ),
                ],


                'guardian_id' => [
                    'required_if:role,guardian',
                    'nullable',
                    'exists:guardians,id',

                    Rule::unique(
                        'users',
                        'guardian_id'
                    )
                        ->ignore(
                            $this->editId
                        ),
                ],
            ],

            [
                'name.required' =>
                'يرجى إدخال اسم المستخدم',

                'name.unique' =>
                'اسم المستخدم موجود بالفعل',

                'password.min' =>
                'يجب ألا تقل كلمة المرور عن 6 أحرف',

                'password.confirmed' =>
                'كلمتا المرور غير متطابقتين',

                'role.required' =>
                'يرجى اختيار الدور',

                'role.in' =>
                'الدور غير صحيح',

                'driver_id.required_if' =>
                'يجب اختيار السائق عند تحديد دور السائق',

                'driver_id.exists' =>
                'السائق المحدد غير موجود',

                'driver_id.unique' =>
                'هذا السائق مرتبط بحساب آخر',

                'guardian_id.required_if' =>
                'يجب اختيار ولي الأمر عند تحديد دور ولي الأمر',

                'guardian_id.exists' =>
                'ولي الأمر المحدد غير موجود',

                'guardian_id.unique' =>
                'ولي الأمر مرتبط بحساب آخر',
            ]
        );


        $user = User::findOrFail(
            $this->editId
        );


        /*
         * اسم المستخدم
         */
        $user->name =
            $this->name;


        /*
         * تحديث كلمة المرور فقط
         * إذا أدخل المدير كلمة جديدة
         */
        if (
            !empty($this->password)
        ) {

            $user->password =
                Hash::make(
                    $this->password
                );
        }


        /*
         * الدور
         */
        $user->role =
            $this->role;


        /*
         * إذا كان Driver
         */
        $user->driver_id =
            $this->role === 'driver'
            ? $this->driver_id
            : null;


        /*
         * إذا كان Guardian
         */
        $user->guardian_id =
            $this->role === 'guardian'
            ? $this->guardian_id
            : null;


        $user->save();


        AdminLoggerService::log(
            'تعديل مستخدم',
            'User',
            "تم تعديل المستخدم: {$this->name}"
        );


        $this->resetForm();


        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تم تعديل المستخدم بنجاح'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | تأكيد الحذف
    |--------------------------------------------------------------------------
    */

    public function confirmDelete($id)
    {
        $user =
            User::findOrFail($id);


        $this->deleteId =
            $user->id;

        $this->deleteName =
            $user->name;
    }


    /*
    |--------------------------------------------------------------------------
    | حذف المستخدم
    |--------------------------------------------------------------------------
    */

    public function delete()
    {
        if (!$this->deleteId) {
            return;
        }


        $user =
            User::findOrFail(
                $this->deleteId
            );


        $userName =
            $user->name;


        $user->delete();


        AdminLoggerService::log(
            'حذف مستخدم',
            'User',
            "تم حذف المستخدم: {$userName}"
        );


        $this->deleteId = null;

        $this->deleteName = null;


        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تم حذف المستخدم بنجاح'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Form
    |--------------------------------------------------------------------------
    */

    public function resetForm()
    {
        $this->reset([
            'name',
            'password',
            'password_confirmation',
            'role',
            'driver_id',
            'guardian_id',
            'editId',
        ]);


        $this->role = '';

        $this->driver_id = null;

        $this->guardian_id = null;


        $this->resetValidation();
    }


    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        /*
        |--------------------------------------------------------------------------
        | المستخدمون
        |--------------------------------------------------------------------------
        */

        $this->users = User::with([
            'driver',
            'guardian',
        ])
            ->orderBy(
                'id',
                'desc'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | السائقون المرتبطون بحسابات
        |--------------------------------------------------------------------------
        */

        $assignedDriverIds =
            User::whereNotNull(
                'driver_id'
            )

            ->when(
                $this->editId,
                function ($query) {

                    $query->where(
                        'id',
                        '!=',
                        $this->editId
                    );
                }
            )

            ->pluck(
                'driver_id'
            )

            ->toArray();


        /*
         * السائقون المتاحون
         */
        $drivers =
            Driver::whereNotIn(
                'id',
                $assignedDriverIds
            )

            ->orderBy(
                'Name'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | أولياء الأمور المرتبطون بحسابات
        |--------------------------------------------------------------------------
        */

        $assignedGuardianIds =
            User::whereNotNull(
                'guardian_id'
            )

            ->when(
                $this->editId,
                function ($query) {

                    $query->where(
                        'id',
                        '!=',
                        $this->editId
                    );
                }
            )

            ->pluck(
                'guardian_id'
            )

            ->toArray();


        /*
         * أولياء الأمور المتاحون
         */
        $guardians =
            Guardian::whereNotIn(
                'id',
                $assignedGuardianIds
            )

            /*
             * المفعلون يظهرون أولًا
             */
            ->orderByDesc(
                'is_active'
            )

            ->orderBy(
                'name'
            )

            ->get();


        return view(
            'livewire.register-user',
            compact(
                'drivers',
                'guardians'
            )
        );
    }
}