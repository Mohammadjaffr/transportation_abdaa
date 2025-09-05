<div class="container py-4">

    {{-- زر الإضافة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        @if (!$showForm)
            <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn w-sm-100 mb-3">
                <i class="fas fa-plus-circle me-1"></i>
                إضافة أجر جديد
            </button>
        @endif
        <h3 class="fw-bold text-primary d-none d-sm-block">إدارة الأجور</h3>
    </div>

    {{-- النموذج --}}
    @if ($showForm)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">{{ $editMode ? 'تعديل الأجر' : 'إضافة أجر جديد' }}</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="{{ $editMode ? 'updateWage' : 'createWage' }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">الحافلة</label>
                            <select wire:model="bus_id"
                                class="form-control @error('bus_id') is-invalid @enderror">
                                <option value="">-- اختر الحافلة --</option>
                                @foreach ($buses as $bus)
                                    <option value="{{ $bus->id }}">{{ $bus->id }} - {{ $bus->BusType }}</option>
                                @endforeach
                            </select>
                            @error('bus_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">المبلغ</label>
                            <input type="number" wire:model="Fees"
                                class="form-control @error('Fees') is-invalid @enderror">
                            @error('Fees') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">التاريخ</label>
                            <input type="date" wire:model="Date"
                                class="form-control @error('Date') is-invalid @enderror">
                            @error('Date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">الموقع</label>
                            <select wire:model="location_id"
                                class="form-control @error('location_id') is-invalid @enderror">
                                <option value="">-- اختر الموقع --</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->Name }}</option>
                                @endforeach
                            </select>
                            @error('location_id') <span class="text-danger small">{{ $message }}</span> @enderror
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
                                class="btn btn-outline-secondary w-100 rounded-pill">إلغاء</button>
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
                placeholder="ابحث بالحافلة أو التاريخ أو المبلغ">
        </div>
    </div>

    {{-- الجدول --}}
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة الأجور</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-success">
                        <tr class="text-center">
                            <th>الحافلة</th>
                            <th>المبلغ</th>
                            <th>التاريخ</th>
                            <th>الموقع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wages as $wage)
                            <tr class="text-center">
                                <td>{{ $wage->bus?->BusType ?? '-' }}</td>
                                <td><span class="badge bg-info text-dark">{{ number_format($wage->Fees, 2) }}</span></td>
                                <td>{{ $wage->Date }}</td>
                                <td>{{ $wage->location?->Name ?? '-' }}</td>
                                <td class="d-flex justify-content-center gap-2">
                                    <button wire:click="edit({{ $wage->id }})"
                                        class="btn btn-outline-success btn-sm rounded-pill mr-2">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    <button wire:click="confirmDelete({{ $wage->id }})"
                                        class="btn btn-outline-danger btn-sm rounded-pill">
                                        <i class="fas fa-trash-alt"></i> حذف
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-money-bill fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">لا توجد أجور مسجلة</p>
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
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> تأكيد الحذف</h5>
                        <button type="button" class="btn-close btn-light" wire:click="$set('deleteId', null)"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد أنك تريد حذف السجل؟</p>
                        <p class="fw-bold text-danger">{{ $deleteWageName }}</p>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <button type="button" class="btn btn-secondary rounded-pill"
                            wire:click="$set('deleteId', null)">إلغاء</button>
                        <button type="button" class="btn btn-danger rounded-pill"
                            wire:click="deleteWage">نعم، احذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</div>
