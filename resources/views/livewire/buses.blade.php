<div>

    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            @if (!$showForm)
                <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn w-sm-100  mb-3">
                    <i class="fas fa-plus-circle me-1"></i>
                    إضافة حافلة جديدة
                </button>
            @endif

            <h3 class="fw-bold text-primary d-none d-sm-block">إدارة الحافلات</h3>


        </div>

        @if ($showForm)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">{{ $editMode ? 'تعديل الحافلة' : 'إضافة حافلة جديدة' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editMode ? 'updateBus' : 'createBus' }}">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">نوع الحافلة</label>
                                <input type="text" wire:model="BusType"
                                    class="form-control @error('BusType') is-invalid @enderror">
                                @error('BusType')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الموديل</label>
                                <input type="text" wire:model="Model"
                                    class="form-control @error('Model') is-invalid @enderror">
                                @error('Model')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">عدد المقاعد</label>
                                <input type="number" wire:model="SeatsNo"
                                    class="form-control @error('SeatsNo') is-invalid @enderror">
                                @error('SeatsNo')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الجمارك</label>
                                <input type="text" wire:model="CustomsNo"
                                    class="form-control @error('CustomsNo') is-invalid @enderror">
                                @error('CustomsNo')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden mt-4 py-2">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="fas fa-filter text-primary"></i>
                                    </span>
                                    <select wire:model="location_id"
                                        class="form-control border-0  @error('location_id') is-invalid @enderror">
                                        <option value="">-- اختر الموقع --</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->id }}">{{ $region->Name }}</option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-md-6">
                                <label class="form-label">عدد الطلاب</label>
                                <input type="number" class="form-control" wire:model="StudentsNo">
                                @error('StudentsNo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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

        <div class="col-md-12 mb-3">
            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                <span class="input-group-text bg-white border-0">
                    <i class="fas fa-search text-primary"></i>
                </span>
                <input type="text" wire:model.debounce.300ms.live="search" class="form-control border-0  py-2"
                    placeholder=" ابحث برقم الحافلة او نوعه ">

            </div>
        </div>
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">إدارة الحافلات</h5>

            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-success">
                            <tr class="text-center">
                                <th>نوع الحافلة</th>
                                <th>الموديل</th>
                                <th>عدد المقاعد</th>
                                <th>الجمارك</th>
                                <th>الموقع</th>
                                <th>عدد الطلاب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buses as $bus)
                                <tr class="text-center">
                                    {{-- <td><span class=" px-3 py-2">{{ $bus->id }}</span></td> --}}
                                    <td>{{ $bus->BusType }}</td>
                                    <td>{{ $bus->Model }}</td>
                                    <td><span class="badge bg-success text-dark">{{ $bus->SeatsNo }}</span></td>
                                    <td>{{ $bus->CustomsNo ?? '-' }}</td>
                                    <td>{{ $bus->region?->Name ?? '-' }}</td>
                                    <td>{{ $bus->StudentsNo }}</td>

                                    <td class="d-flex justify-content-center gap-2">

                                        <button wire:click="edit({{ $bus->id }})"
                                            class="btn btn-outline-success btn-sm rounded-pill" title="تعديل">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                        <button wire:click="confirmDelete({{ $bus->id }})"
                                            class="btn btn-outline-danger btn-sm rounded-pill ml-2" title="حذف">
                                            <i class="fas fa-trash-alt"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-bus fa-2x mb-2 text-secondary"></i>
                                        <p class="mb-0">لا توجد حافلات مسجلة</p>
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
                            <p>هل أنت متأكد أنك تريد حذف الحافلة التالية؟</p>
                            <p class="fw-bold text-danger">" رقم:{{ $deleteBusName }} "</p>
                        </div>
                        <div class="modal-footer justify-content-start">
                            <button type="button" class="btn btn-secondary rounded-pill"
                                wire:click="$set('deleteId', null)">
                                إلغاء
                            </button>
                            <button type="button" class="btn btn-danger rounded-pill" wire:click="deleteBus">
                                نعم، احذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</div>
