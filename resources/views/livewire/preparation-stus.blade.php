<div>
    <div class="container py-4">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-start d-none d-sm-block">تحضير الطلاب</h3>
            <h3 class="fw-bold text-end d-none d-sm-block"> عام {{ date('Y') }} </h3>

        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            @if (!$showForm)
                <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn">
                    <i class="fas fa-plus-circle me-1"></i> إضافة سجل جديد
                </button>
                <a wire:click.prevent="export" href="#" class="btn btn-outline-success">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
            @endif
        </div>

        {{-- Form --}}
        @if ($showForm)
            <div class="card shadow-sm mb-4 rounded-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="mb-0">{{ $editMode ? 'تعديل سجل الحضور' : 'إضافة سجل حضور جديد' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editMode ? 'updatePreparation' : 'createPreparation' }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الطالب</label>
                                <select wire:model.live="student_id" class="form-control">
                                    <option value="">-- اختر الطالب --</option>
                                    @foreach ($students as $stu)
                                        <option value="{{ $stu->id }}">{{ $stu->Name }}</option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">السائق</label>
                                <select wire:model="driver_id" class="form-control" disabled>
                                    <option value="">-- اختر السائق --</option>
                                    @foreach ($drivers as $drv)
                                        <option value="{{ $drv->id }}" @selected($driver_id == $drv->id)>
                                            {{ $drv->Name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('driver_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المنطقة</label>
                                <select wire:model="region_id" class="form-control" disabled>
                                    <option value="">-- اختر المنطقة --</option>
                                    @foreach ($regions as $reg)
                                        <option value="{{ $reg->id }}">{{ $reg->Name }}</option>
                                    @endforeach
                                </select>
                                @error('region_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">التاريخ</label>
                                <input type="date" wire:model="Year" class="form-control">
                                @error('Year')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- <div class="col-md-12">
                                <label class="form-label">حاضر</label>
                                <input type="checkbox" wire:model="Atend" class="form-check-input ms-2">
                                @error('Atend')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div> --}}

                            <div class="col-6 mt-3">
                                <button type="submit" class="btn btn-{{ $editMode ? 'primary' : 'success' }} w-100">
                                    {{ $editMode ? 'تحديث' : 'إضافة' }}
                                </button>
                            </div>
                            <div class="col-6 mt-3">
                                <button type="button" wire:click="cancel"
                                    class="btn btn-outline-secondary w-100">إلغاء</button>
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
                    placeholder="ابحث باسم الطالب...">
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">سجلات الحضور</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-success">
                            <tr>
                                <th>الطالب</th>
                                <th>السائق</th>
                                <th>المنطقة</th>
                                <th>التاريخ</th>
                                <th>حاضر</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($preparations as $prep)
                                <tr>
                                    <td>{{ $prep->student?->Name ?? '-' }}</td>
                                    <td>{{ $prep->driver?->Name ?? '-' }}</td>
                                    <td>{{ $prep->region?->Name ?? '-' }}</td>
                                    <td>{{ $prep->Year }}</td>
                                    <td>
                                        <div
                                            class="form-switch {{ $prep->Atend ? 'switch-active' : 'switch-banned' }}">
                                            <input type="checkbox" id="atendSwitch{{ $prep->id }}"
                                                wire:click="toggleAtend({{ $prep->id }})"
                                                @if ($prep->Atend) checked @endif>
                                            <label for="atendSwitch{{ $prep->id }}"></label>
                                        </div>
                                        <span
                                            class="ms-2 fw-semibold {{ $prep->Atend ? 'text-success' : 'text-danger' }}">
                                            {{ $prep->Atend ? 'حاضر' : 'غائب' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="editPreparation({{ $prep->id }})"
                                            class="btn btn-outline-success btn-sm rounded-pill mr-2">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                        <button wire:click="confirmDelete({{ $prep->id }})"
                                            class="btn btn-outline-danger btn-sm rounded-pill">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد سجلات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="card-footer d-flex justify-content-center">
                        {{ $preparations->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

            {{-- Delete Modal --}}
            @if ($deleteId)
                <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-sm">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">تأكيد الحذف</h5>
                                <button type="button" class="btn-close btn-light"
                                    wire:click="$set('deleteId', null)"></button>
                            </div>
                            <div class="modal-body">
                                <p>هل أنت متأكد أنك تريد حذف هذا الطالب؟</p>
                                <p class="fw-bold text-danger">" الاسم: {{ $deleteName }} "</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" wire:click="$set('deleteId', null)"
                                    class="btn btn-secondary">إلغاء</button>
                                <button type="button" wire:click="deletePreparation" class="btn btn-danger">نعم،
                                    احذف</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
