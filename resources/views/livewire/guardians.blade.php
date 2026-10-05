<div>
    <div class="container py-4">

        {{-- العنوان --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-user-friends me-2"></i>
                إدارة أولياء الأمور
            </h3>
            <span class="text-muted">
                عام {{ date('Y') }}
            </span>
        </div>

        {{-- إضافة ولي أمر --}}
        @if (!$showForm && !$showImportForm)
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button type="button" wire:click="openCreateForm" class="btn btn-primary add-btn w-sm-100 mb-3 rounded-pill shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i>
                    إضافة ولي أمر جديد
                </button>
                <div>
                    <button wire:click="$set('showImportForm', true)" class="btn btn-success w-sm-100 mb-3 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-1"></i> استيراد Excel
                    </button>
                </div>
            </div>
        @endif

        @if ($showImportForm)
            <div class="card shadow-sm mb-4 rounded-3 w-100 ">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">استيراد ملف Excel</h5>
                </div>
                <div class="card-body ">
                    <form wire:submit.prevent="importExcel" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary">
                                <i class="fas fa-file-excel me-2 text-success"></i> اختر ملف Excel
                            </label>

                            <div class="card shadow-sm border-0 rounded-3 p-3 d-flex align-items-center justify-content-center bg-light"
                                style="border: 2px dashed #28a745; cursor: pointer;">
                                <label class="w-100 text-center" style="cursor: pointer;">
                                    <input type="file" wire:model="excelFile" class="d-none" accept=".xlsx,.csv">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-success mb-2"></i>
                                    <p class="fw-bold mb-0 text-muted">
                                        {{ $excelFile ? $excelFile->getClientOriginalName() : 'اسحب الملف هنا أو اضغط للرفع' }}
                                    </p>
                                </label>
                            </div>

                            @error('excelFile')
                                <div class="text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror

                            @if ($excelFile)
                                <div class="mt-3 text-success fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> تم رفع الملف بنجاح:
                                    <span class="text-dark">{{ $excelFile->getClientOriginalName() }}</span>
                                </div>
                            @endif
                        </div>

                        <div x-data="{ progress: 0 }" x-on:livewire-upload-start="progress = 0"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                            x-on:livewire-upload-finish="progress = 100; setTimeout(() => progress = 0, 1200)"
                            x-on:livewire-upload-error="progress = 0" class="mt-3">
                            <div x-show="progress > 0" class="progress" style="height: 22px;">
                                <div class="progress-bar bg-success fw-bold" role="progressbar"
                                    :style="`width: ${progress}%`" x-text="progress + '%'">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success w-100 rounded-pill"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="importExcel">
                                        <i class="fas fa-file-import me-1"></i> استيراد
                                    </span>
                                    <span wire:loading wire:target="importExcel">
                                        <i class="fas fa-spinner fa-spin me-1"></i> جاري الاستيراد...
                                    </span>
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" wire:click="resetImportForm"
                                    class="btn btn-outline-secondary w-100 rounded-pill">
                                    إلغاء
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- نموذج الإضافة والتعديل --}}
        @if ($showForm)
            <div class="card shadow-sm mb-4">
                <div class="card-header {{ $editId ? 'bg-warning' : 'bg-primary' }} text-white">
                    <h5 class="mb-0">
                        @if ($editId)
                            <i class="fas fa-user-edit me-1"></i>
                            تعديل بيانات ولي الأمر
                        @else
                            <i class="fas fa-user-plus me-1"></i>
                            إضافة ولي أمر جديد
                        @endif
                    </h5>
                </div>

                <div class="card-body">
                    <form wire:submit.prevent="{{ $editId ? 'updateGuardian' : 'createGuardian' }}">
                        <div class="row">

                            {{-- الاسم --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    اسم ولي الأمر <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="أدخل اسم ولي الأمر">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- الهاتف --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    رقم الهاتف <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="مثال: 777123456" dir="ltr">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- الهوية --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">رقم الهوية</label>
                                <input type="text" wire:model="national_id"
                                    class="form-control @error('national_id') is-invalid @enderror"
                                    placeholder="اختياري">
                                @error('national_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- العنوان --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العنوان</label>
                                <input type="text" wire:model="address"
                                    class="form-control @error('address') is-invalid @enderror" 
                                    placeholder="اختياري">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- التفعيل --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">الحالة</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="guardianActive" wire:model="is_active">
                                    <label class="form-check-label" for="guardianActive">
                                        ولي الأمر مفعل
                                    </label>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn {{ $editId ? 'btn-warning' : 'btn-success' }}">
                                @if ($editId)
                                    <i class="fas fa-save me-1"></i> حفظ التعديلات
                                @else
                                    <i class="fas fa-plus me-1"></i> إضافة ولي الأمر
                                @endif
                            </button>

                            <button type="button" wire:click="cancelForm" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- البحث --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="بحث بالاسم أو الهاتف أو الهوية أو العنوان...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- الجدول --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-1"></i> قائمة أولياء الأمور
                </h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                                <th>رقم الهوية</th>
                                <th>العنوان</th>
                                <th>الأبناء</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($guardians as $guardian)
                                <tr wire:key="guardian-{{ $guardian->id }}">
                                    <td>{{ $guardian->id }}</td>
                                    <td class="fw-bold">{{ $guardian->name }}</td>
                                    <td dir="ltr">{{ $guardian->phone }}</td>
                                    <td>{{ $guardian->national_id ?: '-' }}</td>
                                    <td>{{ $guardian->address ?: '-' }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $guardian->students_count }} طالب
                                        </span>
                                    </td>
                                    
                                    {{-- الحالة --}}
                                    <td>
                                        @if ($guardian->is_active)
                                            <span class="badge badge-success">مفعل</span>
                                        @else
                                            <span class="badge badge-danger">موقوف</span>
                                        @endif
                                    </td>

                                    {{-- الإجراءات --}}
                                    <td>
                                        <div class="d-flex justify-content-center flex-wrap gap-1">
                                            {{-- تعديل --}}
                                            <button type="button" wire:click="editGuardian({{ $guardian->id }})"
                                                class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- تفعيل / إيقاف --}}
                                            <button type="button" wire:click="toggleActive({{ $guardian->id }})"
                                                class="btn btn-sm {{ $guardian->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                title="{{ $guardian->is_active ? 'إيقاف' : 'تفعيل' }}">
                                                @if ($guardian->is_active)
                                                    <i class="fas fa-ban"></i>
                                                @else
                                                    <i class="fas fa-check"></i>
                                                @endif
                                            </button>

                                            {{-- حذف --}}
                                            <button type="button" wire:click="confirmDelete({{ $guardian->id }})"
                                                class="btn btn-sm btn-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                        لا يوجد أولياء أمور
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($guardians->hasPages())
                <div class="card-footer d-flex justify-content-center">
                    {{ $guardians->links() }}
                </div>
            @endif
        </div>

        {{-- نافذة تأكيد الحذف --}}
        @if ($deleteId)
            <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                تأكيد حذف ولي الأمر
                            </h5>
                        </div>

                        <div class="modal-body">
                            <p>هل أنت متأكد أنك تريد حذف ولي الأمر؟</p>
                            <p class="fw-bold text-danger">{{ $deleteName }}</p>
                            <div class="alert alert-warning mb-0">
                                حذف ولي الأمر سيزيل روابطه بالطلاب، لكنه لن يحذف الطلاب أنفسهم.
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" wire:click="cancelDelete" class="btn btn-secondary">
                                إلغاء
                            </button>
                            <button type="button" wire:click="deleteGuardian" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> نعم، حذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>