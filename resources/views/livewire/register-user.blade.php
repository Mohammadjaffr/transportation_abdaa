<div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    {{-- نموذج إضافة / تعديل --}}
    <div class="card shadow-lg border-0 rounded-3 mt-3 container">

        <div class="card-header bg-success text-white mt-2">

            <h5 class="mb-0">

                <i class="fas fa-user-plus"></i>

                {{ $editId ? 'تعديل مستخدم' : 'تسجيل مستخدم جديد' }}

            </h5>

        </div>


        <div class="card-body">

            <form
                wire:submit.prevent="{{ $editId ? 'update' : 'store' }}">


                {{-- الدور --}}
                <div class="form-group mb-3">

                    <label class="fw-bold">
                        الدور
                    </label>


                    <select wire:model.live="role"
                        class="
                            form-control
                            @error('role')
                                is-invalid
                            @enderror
                        ">

                        <option value="">
                            اختر الدور
                        </option>

                        <option value="admin">
                            مدير
                        </option>

                        <option value="driver">
                            سائق
                        </option>

                        <option value="guardian">
                            ولي أمر
                        </option>

                    </select>


                    @error('role')
                        <span class="invalid-feedback">

                            {{ $message }}

                        </span>
                    @enderror

                </div>



                {{-- اختيار السائق --}}
                @if ($role === 'driver')

                    <div class="form-group mb-3">

                        <label class="fw-bold">

                            السائق المرتبط

                        </label>


                        <select wire:model.live="driver_id"
                            class="
                                form-control
                                @error('driver_id')
                                    is-invalid
                                @enderror
                            ">

                            <option value="">

                                اختر السائق من القائمة

                            </option>


                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}">

                                    {{ $driver->Name }}

                                    @if ($driver->IDNo)
                                        -
                                        الهوية:
                                        {{ $driver->IDNo }}
                                    @endif

                                </option>
                            @endforeach

                        </select>


                        @error('driver_id')
                            <span class="invalid-feedback">

                                {{ $message }}

                            </span>
                        @enderror

                    </div>

                @endif



                {{-- اختيار ولي الأمر --}}
                @if ($role === 'guardian')

                    <div class="form-group mb-3">

                        <label class="fw-bold">

                            ولي الأمر المرتبط

                        </label>


                        <select wire:model.live="guardian_id"
                            class="
                                form-control
                                @error('guardian_id')
                                    is-invalid
                                @enderror
                            ">

                            <option value="">

                                اختر ولي الأمر من القائمة

                            </option>


                            @foreach ($guardians as $guardian)
                                <option value="{{ $guardian->id }}">

                                    {{ $guardian->name }}

                                    @if ($guardian->phone)
                                        -
                                        {{ $guardian->phone }}
                                    @endif


                                    @if (!$guardian->is_active)
                                        (موقوف)
                                    @endif

                                </option>
                            @endforeach

                        </select>


                        @error('guardian_id')
                            <span class="invalid-feedback">

                                {{ $message }}

                            </span>
                        @enderror


                        <small class="text-muted d-block mt-2">

                            اختر ولي الأمر الذي سيتم ربط
                            حساب الدخول به.

                        </small>

                    </div>

                @endif



                {{-- اسم المستخدم --}}
                <div class="form-group mb-3">

                    <label class="fw-bold">

                        اسم المستخدم

                    </label>


                    <input type="text" wire:model="name"
                        class="
                            form-control
                            @error('name')
                                is-invalid
                            @enderror
                        "
                        placeholder="أدخل اسم المستخدم"
                        {{ $role === 'driver' ? 'readonly' : '' }}>


                    @error('name')
                        <span class="invalid-feedback">

                            {{ $message }}

                        </span>
                    @enderror


                    @if ($role === 'guardian')
                        <small class="text-muted">

                            هذا هو الاسم الذي سيستخدمه
                            ولي الأمر في شاشة تسجيل الدخول.
                            ويمكنك تغييره إذا كان الاسم
                            مستخدمًا مسبقًا.

                        </small>
                    @endif

                </div>



                {{-- كلمة المرور --}}
                <div class="form-group mb-3">

                    <label class="fw-bold">

                        كلمة المرور

                        @if ($editId)
                            <small class="text-muted">

                                (اتركها فارغة إذا لا تريد التغيير)

                            </small>
                        @endif

                    </label>


                    <input type="password" wire:model="password"
                        class="
                            form-control
                            @error('password')
                                is-invalid
                            @enderror
                        ">


                    @error('password')
                        <span class="invalid-feedback">

                            {{ $message }}

                        </span>
                    @enderror

                </div>



                {{-- تأكيد كلمة المرور --}}
                <div class="form-group mb-3">

                    <label class="fw-bold">

                        تأكيد كلمة المرور

                    </label>


                    <input type="password" wire:model="password_confirmation"
                        class="
                            form-control
                            @error('password_confirmation')
                                is-invalid
                            @enderror
                        ">


                    @error('password_confirmation')
                        <span class="invalid-feedback">

                            {{ $message }}

                        </span>
                    @enderror

                </div>



                {{-- حفظ --}}
                <button type="submit"
                    class="
                        btn
                        btn-{{ $editId ? 'warning' : 'success' }}
                        w-100
                    ">

                    <i
                        class="
                            fas
                            fa-{{ $editId ? 'edit' : 'user-check' }}
                        "></i>


                    {{ $editId ? 'تحديث' : 'إضافة' }}

                </button>


                {{-- إلغاء التعديل --}}
                @if ($editId)
                    <button type="button" wire:click="resetForm"
                        class="
                            btn
                            btn-outline-secondary
                            w-100
                            mt-2
                        ">

                        إلغاء التعديل

                    </button>
                @endif


            </form>

        </div>

    </div>



    {{-- جدول المستخدمين --}}
    <div class="card mt-4">

        <div class="card-header bg-success text-white">

            <h5 class="mb-0">

                <i class="fas fa-users"></i>

                قائمة المستخدمين

            </h5>

        </div>


        <div class="card-body p-0">


            <div class="table-responsive">


                <table
                    class="
                        table
                        table-bordered
                        table-hover
                        text-center
                        align-middle
                        mb-0
                    ">


                    <thead class="table-success">

                        <tr>

                            <th>#</th>

                            <th>
                                اسم المستخدم
                            </th>

                            <th>
                                الدور
                            </th>

                            <th>
                                الحساب المرتبط
                            </th>

                            <th>
                                إجراءات
                            </th>

                        </tr>

                    </thead>



                    <tbody>


                        @forelse ($users as $user)


                            <tr>


                                <td>
                                    {{ $user->id }}
                                </td>


                                <td>
                                    {{ $user->name }}
                                </td>



                                {{-- الدور --}}
                                <td>


                                    @if ($user->role === 'admin')
                                        <span class="badge bg-success">

                                            مدير

                                        </span>
                                    @elseif ($user->role === 'driver')
                                        <span class="badge bg-primary">

                                            سائق

                                        </span>
                                    @elseif ($user->role === 'guardian')
                                        <span class="badge bg-warning text-dark">

                                            ولي أمر

                                        </span>
                                    @else
                                        <span class="badge bg-secondary">

                                            {{ $user->role }}

                                        </span>
                                    @endif


                                </td>



                                {{-- الحساب المرتبط --}}
                                <td>


                                    @if ($user->role === 'driver')
                                        @if ($user->driver)
                                            <span class="badge bg-primary">

                                                <i class="fas fa-bus me-1"></i>

                                                {{ $user->driver->Name }}

                                            </span>
                                        @else
                                            <span class="text-danger">

                                                غير مرتبط بسائق

                                            </span>
                                        @endif
                                    @elseif ($user->role === 'guardian')
                                        @if ($user->guardian)
                                            <span
                                                class="
                                                    badge
                                                    bg-warning
                                                    text-dark
                                                ">

                                                <i
                                                    class="
                                                        fas
                                                        fa-user-friends
                                                        me-1
                                                    "></i>

                                                {{ $user->guardian->name }}

                                            </span>


                                            @if ($user->guardian->phone)
                                                <br>

                                                <small class="text-muted">

                                                    {{ $user->guardian->phone }}

                                                </small>
                                            @endif
                                        @else
                                            <span class="text-danger">

                                                غير مرتبط بولي أمر

                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">

                                            -

                                        </span>
                                    @endif


                                </td>



                                {{-- الإجراءات --}}
                                <td class="text-center">


                                    <button
                                        wire:click="
                                            edit(
                                                {{ $user->id }}
                                            )
                                        "
                                        class="
                                            btn
                                            btn-outline-success
                                            btn-sm
                                        ">

                                        <i class="fas fa-edit"></i>

                                        تعديل

                                    </button>


                                    @if (Auth::user()->id != $user->id)
                                        <button
                                            wire:click="
                                                confirmDelete(
                                                    {{ $user->id }}
                                                )
                                            "
                                            class="
                                                btn
                                                btn-outline-danger
                                                btn-sm
                                            ">

                                            <i
                                                class="
                                                    fas
                                                    fa-trash-alt
                                                "></i>

                                            حذف

                                        </button>
                                    @endif


                                </td>


                            </tr>


                        @empty


                            <tr>

                                <td colspan="5"
                                    class="
                                        text-center
                                        text-muted
                                        py-4
                                    ">

                                    لا يوجد مستخدمون

                                </td>

                            </tr>


                        @endforelse


                    </tbody>


                </table>


            </div>


        </div>

    </div>



    {{-- نافذة تأكيد الحذف --}}
    @if ($deleteId)
        <div class="modal fade show d-block" tabindex="-1"
            style="
                background-color:
                rgba(0,0,0,0.5);
            ">


            <div class="
                    modal-dialog
                    modal-dialog-centered
                ">


                <div
                    class="
                        modal-content
                        shadow-sm
                    ">


                    <div
                        class="
                            modal-header
                            bg-danger
                            text-white
                        ">

                        <h5 class="modal-title">

                            <i
                                class="
                                    fas
                                    fa-exclamation-triangle
                                "></i>

                            تأكيد الحذف

                        </h5>


                        <button type="button" class="btn-close"
                            wire:click="
                                $set(
                                    'deleteId',
                                    null
                                )
                            "></button>

                    </div>



                    <div class="modal-body">


                        <p>

                            هل أنت متأكد أنك تريد
                            حذف هذا المستخدم؟

                        </p>


                        <p
                            class="
                                fw-bold
                                text-danger
                            ">

                            " الاسم:
                            {{ $deleteName }} "

                        </p>


                    </div>



                    <div class="modal-footer">


                        <button type="button" class="btn btn-secondary"
                            wire:click="
                                $set(
                                    'deleteId',
                                    null
                                )
                            ">

                            إلغاء

                        </button>


                        <button type="button" class="btn btn-danger" wire:click="delete">

                            نعم، احذف

                        </button>


                    </div>


                </div>


            </div>


        </div>
    @endif


</div>
