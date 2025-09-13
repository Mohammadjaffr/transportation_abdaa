<div>
    {{-- Toast --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="container py-4">

        {{-- العنوان وزر إضافة --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            @if(!$showForm)
                <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn">
                    <i class="fas fa-plus-circle me-1"></i> إضافة أجرة جديدة
                </button>
            @endif
            <h3 class="fw-bold text-primary d-none d-md-block">إدارة رواتب السائقين</h3>
        </div>

        {{-- Form --}}
        @if($showForm)
            <div class="card shadow-sm mb-4 rounded-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="mb-0">{{ $editMode ? 'تعديل الأجرة' : 'إضافة أجرة جديدة' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editMode ? 'updateWage' : 'createWage' }}">
                        <div class="row g-3">

                            {{-- <div class="col-md-4">
                                <label class="form-label fw-bold">الباص</label>
                                <select wire:model="bus_id" class="form-control">
                                    <option value="">-- اختر الباص --</option>
                                    @foreach($buses as $bus)
                                        <option value="{{ $bus->id }}">حافلة رقم {{ $bus->id }} - {{ $bus->BusType }}</option>
                                    @endforeach
                                </select>
                                @error('bus_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div> --}}

                            <div class="col-md-6">
                                <label class="form-label fw-bold">السائق</label>
                                <select wire:model="driver_id" class="form-control">
                                    <option value="">-- اختر السائق --</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->Name }}</option>
                                    @endforeach
                                </select>
                                @error('driver_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">المنطقة</label>
                                <select wire:model="region_id" class="form-control">
                                    <option value="">-- اختر المنطقة --</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->Name }}</option>
                                    @endforeach
                                </select>
                                @error('region_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">الأجرة (ريال)</label>
                                <input type="number" min="0" class="form-control" wire:model="Fees">
                                @error('Fees') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">التاريخ</label>
                                <input type="date" class="form-control" wire:model="Date">
                                @error('Date') <span class="text-danger small">{{ $message }}</span> @enderror
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

        {{-- Search --}}
        <div class="col-md-12 mb-3">
            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                <span class="input-group-text bg-white border-0"><i class="fas fa-search text-primary"></i></span>
                <input type="text" wire:model.debounce.300ms="search" class="form-control border-0 py-2"
                    placeholder="ابحث باسم السائق، الباص، المنطقة أو التاريخ...">
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">رواتب السائقين</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-success">
                            <tr>
                                <th>السائق</th>
                                <th>المنطقة</th>
                                <th>الأجرة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wages as $wage)
                                <tr>
                                    
                                    <td>{{ $wage->driver?->Name ?? '-' }}</td>
                                    <td>{{ $wage->region?->Name ?? '-' }}</td>
                                    <td>{{ $wage->Fees }}</td>
                                    <td>{{ $wage->Date }}</td>
                                    <td class="d-flex gap-2">
                                        <button wire:click="edit({{ $wage->id }})" class="btn btn-outline-success btn-sm rounded-pill mr-2">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                        <button wire:click="confirmDelete({{ $wage->id }})" class="btn btn-outline-danger btn-sm rounded-pill">
                                            <i class="fas fa-trash-alt"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Delete Modal --}}
            @if($deleteId)
                <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-sm">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> تأكيد الحذف</h5>
                                <button type="button" class="btn-close btn-light" wire:click="$set('deleteId', null)"></button>
                            </div>
                            <div class="modal-body">
                                هل أنت متأكد من حذف هذا السجل؟
                            </div>
                            <div class="modal-footer">
                                <button type="button" wire:click="$set('deleteId', null)" class="btn btn-secondary rounded-pill">إلغاء</button>
                                <button type="button" wire:click="deleteWage" class="btn btn-danger rounded-pill">نعم، احذف</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
