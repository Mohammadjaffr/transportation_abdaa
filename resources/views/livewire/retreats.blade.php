<div class="container py-4">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- زر الإضافة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        @if (!$showForm)
            <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn w-sm-100 mb-3">
                <i class="fas fa-plus-circle me-1"></i>
                إضافة طالب منسحب
            </button>
        @endif
        <h3 class="fw-bold text-primary d-none d-sm-block">إدارة المنسحبين</h3>
    </div>

    {{-- النموذج --}}
    @if ($showForm)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">{{ $editMode ? 'تعديل بيانات' : 'إضافة منسحب' }}</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="{{ $editMode ? 'updateRetreat' : 'createRetreat' }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">الاسم</label>
                            <input type="text" wire:model="Name"
                                class="form-control @error('Name') is-invalid @enderror">
                            @error('Name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الصف</label>
                            <input type="text" wire:model="Grade"
                                class="form-control @error('Grade') is-invalid @enderror">
                            @error('Grade') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">رقم الحافلة</label>
                            <select wire:model="bus_id"
                                class="form-control @error('bus_id') is-invalid @enderror">
                                <option value="">-- اختر الحافلة --</option>
                                @foreach ($buses as $bus)
                                    <option value="{{ $bus->id }}">{{ $bus->id }} - {{ $bus->BusType }}</option>
                                @endforeach
                            </select>
                            @error('bus_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-6 mt-3">
                            <button type="submit"
                                class="btn btn-{{ $editMode ? 'primary' : 'success' }} w-100 rounded-pill">
                                <i class="fas {{ $editMode ? 'fa-save' : 'fa-plus-circle' }} me-1"></i>
                                {{ $editMode ? 'تحديث' : 'إضافة' }}
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

    {{-- مربع البحث --}}
    <div class="col-md-12 mb-3">
        <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0">
                <i class="fas fa-search text-primary"></i>
            </span>
            <input type="text" wire:model.debounce.300ms.live="search" class="form-control border-0 py-2"
                placeholder="ابحث بالاسم أو الصف أو رقم الحافلة">
        </div>
    </div>

    {{-- الجدول --}}
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة المنسحبين</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-success">
                        <tr class="text-center">
                            <th>الاسم</th>
                            <th>الصف</th>
                            <th>نوع الحافلة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($retreats as $retreat)
                            <tr class="text-center">
                                <td>{{ $retreat->Name }}</td>
                                <td>{{ $retreat->Grade }}</td>
                                <td>{{ $retreat->bus?->BusType ?? '-' }}</td>
                                <td class="d-flex justify-content-center gap-2">
                                    <button wire:click="edit({{ $retreat->id }})"
                                        class="btn btn-outline-success btn-sm rounded-pill">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    <button wire:click="confirmDelete({{ $retreat->id }})"
                                        class="btn btn-outline-danger btn-sm rounded-pill ml-2">
                                        <i class="fas fa-trash-alt"></i> حذف
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-user-slash fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">لا توجد بيانات مسجلة</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- نافذة الحذف --}}
    @if ($deleteId)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-sm">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i> تأكيد الحذف
                        </h5>
                        <button type="button" class="btn-close btn-light"
                            wire:click="$set('deleteId', null)"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد أنك تريد حذف السجل التالي؟</p>
                        <p class="fw-bold text-danger">"{{ $deleteRetreatName }}"</p>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <button type="button" class="btn btn-secondary rounded-pill"
                            wire:click="$set('deleteId', null)">إلغاء</button>
                        <button type="button" class="btn btn-danger rounded-pill"
                            wire:click="deleteRetreat">نعم، احذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
