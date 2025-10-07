<div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="container py-2 py-md-4">

        <div class="d-flex justify-content-between align-items-start my-2" dir="rtl">
            @if (!$showForm && !$isEdit)
                <button wire:click="create" class="btn btn-primary add-btn ">
                    <span>إضافة منطقة</span>
                    <i class="fas fa-plus me-1 d-none d-md-inline"></i>

                </button>
            @endif
            <h3 class="fw-bold text-center d-none d-sm-block"> عام {{ date('Y') }} </h3>

            <h3 class="fw-bold text-primary mb-2 mb-sm-0 text-center text-sm-start d-none d-sm-block">
                إدارة المناطق
            </h3>
        </div>

        @if ($showForm || $isEdit)
            <div class="card mb-4 border-0 shadow-sm rounded-3">
                <div class="bg-success text-white py-2 py-md-3 rounded-top-3">
                    <h5 class="mb-0 ">
                        {{ $isEdit ? 'تعديل المنطقة' : 'إضافة منطقة جديدة' }}
                        <i class="fas fa-map-marked-alt ml-2"></i>
                    </h5>
                </div>

                <div class="card-body py-3 py-md-4">
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                        <div class="row g-3">
                            {{-- حقل المنطقة الرئيسية --}}
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label text-primary fw-bold d-block  mb-1 mb-md-2">
                                        المنطقة الرئيسية
                                    </label>

                                    <div class="input-group input-group-lg shadow-sm rounded-2 overflow-hidden">
                                        <span class="input-group-text bg-white border-0">
                                            <i class="fas fa-layer-group text-primary"></i>
                                        </span>
                                        <select wire:model.defer="parent_id" class="form-control  border-0 ">
                                            <option value="">بدون منطقة رئيسية</option>
                                            @foreach ($parent_regions as $parent)
                                                <option value="{{ $parent->id }}">{{ $parent->Name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- حقل اسم المنطقة --}}
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label text-primary fw-bold d-block  mb-1 mb-md-2">
                                        اسم المنطقة \موقف <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group input-group-lg shadow-sm rounded-2 overflow-hidden">
                                        <span class="input-group-text bg-white border-0">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                        </span>
                                        <input type="text" wire:model.defer="Name"
                                            class="form-control border-0  py-2 py-md-3 @error('Name') is-invalid @enderror"
                                            placeholder="أدخل اسم المنطقة \موقف">

                                    </div>

                                    @error('Name')
                                        <div class="invalid-feedback  d-block mt-1 mt-md-2">
                                            <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- زر الإرسال والإلغاء --}}
                            <div class="col-12 m-2 mt-md-4">
                                <div class="d-flex flex-column flex-md-row gap-2 gap-md-3 mx-1">
                                    <div class="flex-grow-1">
                                        <button type="submit"
                                            class="btn btn-{{ $isEdit ? 'warning' : 'primary' }} btn-lg w-100 rounded-pill shadow-sm ">
                                            <i class="fas {{ $isEdit ? 'fa-save' : 'fa-plus-circle' }} mr-2"></i>
                                            {{ $isEdit ? 'حفظ التعديلات' : 'إضافة منطقة' }}

                                        </button>
                                    </div>
                                    <div class="flex-grow-1">
                                        <button type="button" wire:click="cancel"
                                            class="btn btn-outline-secondary btn-lg w-100 rounded-pill shadow-sm ">
                                            <i class="fas fa-times mr-2"></i> إلغاء

                                        </button>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class=" bg-success py-2 py-md-3">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 d-none d-md-block">
                        <h5 class="mb-0 text-white ">
                            <i class="fas fa-map-marked-alt ml-2"></i> قائمة المناطق
                        </h5>
                    </div>
                    <div class="col-12 col-md-6 mb-2  mb-md-0">
                        <div class="input-group shadow-sm rounded-pill overflow-hidden border-0 ">
                            <span class="input-group-text bg-white border-0 rounded-0">
                                <i class="fas fa-search text-primary"></i>
                            </span>
                            <input type="text" class="form-control border-0  py-2 "
                                placeholder="ابحث باسم المنطقه..." wire:model.debounce.300ms.live="search">
                        </div>
                    </div>

                </div>
            </div>

            <!-- جدول البيانات -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-success">
                            <tr>
                                <th class="text-center py-2 py-md-3 fw-bold">#</th>
                                <th class="text-start py-2 py-md-3 fw-bold">المنطقة</th>
                                <th class="text-start py-2 py-md-3 fw-bold">منطقة رئيسية /موقف</th>

                                <th class="text-center py-2 py-md-3 fw-bold">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($regions as $region)
                                <tr class="border-bottom">
                                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                    <td class="text-start">
                                        @if ($region->parent)
                                            <span
                                                class="badge bg-success bg-opacity-10 text-white rounded-pill px-2 px-md-3 py-1">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $region->parent->Name }}
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-primary bg-opacity-10 text-white rounded-pill px-2 px-md-3 py-1">
                                                {{ $region->Name }} </span>
                                        @endif
                                    </td>


                                    <td class="text-start">
                                        @if ($region->parent)
                                            <span class="text-muted">
                                                {{ $region->Name }}
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-primary bg-opacity-10 text-white rounded-pill px-2 px-md-3 py-1">منطقة
                                                رئيسية</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 gap-md-2 ">
                                            <button
                                                class="btn btn-icon btn-sm btn-success rounded-circle shadow-sm mx-1"
                                                wire:click="edit({{ $region->id }})" data-bs-toggle="tooltip"
                                                title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-icon btn-sm btn-danger rounded-circle shadow-sm"
                                                wire:click="confirmDelete({{ $region->id }})" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal" data-bs-toggle="tooltip" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 py-md-5 text-muted">
                                        <i class="fas fa-map-marker-alt fa-2x mb-3"></i>
                                        <p class="mb-0 fs-5">لا توجد مناطق مسجلة</p>
                                        @if ($search)
                                            <p class="text-muted small mt-2">لا توجد نتائج مطابقة لبحثك</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="card-footer d-flex justify-content-center">
                        {{ $regions->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال الحذف Livewire فقط --}}
    @if ($deleteId)
        <div class="modal fade show d-block " tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">تأكيد الحذف</h5>
                        <button type="button" class="btn-close btn-light rounded-circle"
                            wire:click="$set('deleteId', false)" aria-label="Close">X</button>


                    </div>
                    <div class="modal-body">
                        <p>:هل أنت متأكد أنك تريد حذف المنطقة</p>
                        <div>
                            <p class="text-danger fw-bold">"{{ $deleteName }}"</p>
                            <p>لايمكن التراجع بعد الحذف</p>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-start">
                        <button type="button" class="btn btn-secondary btn-sm"
                            wire:click="$set('deleteId', false)">إلغاء</button>
                        <button type="button" class="btn btn-danger btn-sm" wire:click="deleteRegion">نعم،
                            حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
