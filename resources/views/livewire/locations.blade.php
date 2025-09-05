<div>
    {{-- Toast --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <div class="container py-4">
        {{-- العنوان وزر إضافة --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            @if (!$showForm)
                <button wire:click="create" class="btn btn-primary add-btn">
                    <i class="fas fa-plus-circle me-1"></i> إضافة موقع جديد
                </button>
            @endif
            <h3 class="fw-bold text-primary d-none d-md-block">إدارة المواقع</h3>

        </div>

        {{-- Form --}}
        @if ($showForm)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">{{ $isEdit ? 'تعديل الموقع' : 'إضافة موقع جديد' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">اسم الموقع</label>
                                <input type="text" wire:model="Name"
                                    class="form-control @error('Name') is-invalid @enderror">
                                @error('Name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- <div class="col-md-3">
                                <label class="form-label">رقم الموقع</label>
                                <input type="number" wire:model="LocNo"
                                    class="form-control @error('LocNo') is-invalid @enderror"
                                    {{ $isEdit ? 'disabled' : '' }}>
                                @error('LocNo')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div> --}}

                            <div class="col-md-3">
                                <label class="form-label">المبلغ اليومي</label>
                                <input type="number" wire:model="DailyAmount"
                                    class="form-control @error('DailyAmount') is-invalid @enderror">
                                @error('DailyAmount')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">الرسوم</label>
                                <input type="number" wire:model="Fees"
                                    class="form-control @error('Fees') is-invalid @enderror">
                                @error('Fees')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6 mt-3">
                                <button type="submit"
                                    class="btn btn-{{ $isEdit ? 'primary' : 'success' }} w-100 rounded-pill">
                                    <i class="fas {{ $isEdit ? 'fa-save' : 'fa-plus-circle' }} me-1"></i>
                                    {{ $isEdit ? 'تحديث' : 'إضافة' }}
                                </button>
                            </div>
                            <div class="col-6 mt-3">
                                <button type="button" wire:click="cancel"
                                    class="btn btn-outline-secondary w-100 rounded-pill">
                                    إلغاء
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="col-md-12 mb-3">
            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                <span class="input-group-text bg-white border-0"><i class="fas fa-search text-primary"></i></span>
                <input type="text" wire:model.debounce.300ms.live="search" class="form-control border-0 py-2"
                    placeholder="ابحث باسم الموقع...">
            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">إدارة الطلاب</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0 text-center">
                        <thead class="table-success">
                            <tr>
                                <th>اسم المنطقة</th>
                                <th>المبلغ اليومي</th>
                                <th>الرسوم</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locations as $loc)
                                <tr>
                                    <td>{{ $loc->Name }}</td>
                                    <td>{{ $loc->DailyAmount }}</td>
                                    <td>{{ $loc->Fees }}</td>
                                    <td class="d-flex gap-2 justify-content-center">
                                        <button wire:click="edit({{ $loc->id }})"
                                            class="btn btn-outline-success btn-sm rounded-pill mr-2">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                        <button wire:click="confirmDelete({{ $loc->id }})"
                                            class="btn btn-outline-danger btn-sm rounded-pill">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        لا توجد مواقع حالياً
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- Delete Modal --}}
        @if ($deleteId)
            <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-sm">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> تأكيد الحذف</h5>
                            <button type="button" class="btn-close btn-light"
                                wire:click="$set('deleteId', null)"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد أنك تريد حذف الموقع التالي؟</p>
                            <p class="fw-bold text-danger">"منطقة :{{ $deleteTitle }}"</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill"
                                wire:click="$set('deleteId', null)">
                                إلغاء
                            </button>
                            <button type="button" class="btn btn-danger rounded-pill" wire:click="deleteLocation">
                                نعم، احذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
