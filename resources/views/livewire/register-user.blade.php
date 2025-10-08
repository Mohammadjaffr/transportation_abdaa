<div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- نموذج إضافة / تعديل --}}
    <div class="card shadow-lg border-0 rounded-3 mt-3 container ">
        <div class="card-header bg-success text-white mt-2">
            <h5><i class="fas fa-user-plus"></i> 
                {{ $editId ? 'تعديل مستخدم' : 'تسجيل مستخدم جديد' }}
            </h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="{{ $editId ? 'update' : 'store' }}">
                <div class="form-group mb-3">
                    <label>الاسم</label>
                    <input type="text" wire:model="name"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="أدخل الاسم الكامل">
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-3">
                    <label>كلمة المرور {{ $editId ? '(اتركها فارغة إذا لا تريد التغيير)' : '' }}</label>
                    <input type="password" wire:model="password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-3">
                    <label>تأكيد كلمة المرور</label>
                    <input type="password" wire:model="password_confirmation" class="form-control">
                </div>

                <button type="submit" class="btn btn-{{ $editId ? 'warning' : 'success' }} w-100">
                    <i class="fas fa-{{ $editId ? 'edit' : 'user-check' }}"></i>
                    {{ $editId ? 'تحديث' : 'إضافة' }}
                </button>
            </form>
        </div>
    </div>

    {{-- جدول المستخدمين --}}
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5><i class="fas fa-users"></i> قائمة المستخدمين</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td class="text-center">
                                    <button wire:click="edit({{ $user->id }})"
                                            class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    @if (Auth::user()->id != $user->id)
                                          <button wire:click="confirmDelete({{ $user->id }})"
                                            class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i> حذف
                                    </button>
                                    @endif
                                  
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">لا يوجد مستخدمين</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- نافذة تأكيد الحذف --}}
    @if($deleteId)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-sm">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> تأكيد الحذف</h5>
                        <button type="button" class="btn-close" wire:click="$set('deleteId', null)"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد أنك تريد حذف هذا المستخدم؟</p>
                        <p class="fw-bold text-danger">" الاسم: {{ $deleteName }} "</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('deleteId', null)">إلغاء</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">نعم، احذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
