<div class="container py-4">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">

    <div class="d-flex justify-content-between align-items-center mb-4">
        @if (!$showForm)
            <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn">
                <i class="fas fa-plus-circle me-1"></i> إضافة منسحب جديدة
            </button>
        @endif
        <h3 class="fw-bold text-primary d-none d-md-block"> إدارة الطلاب المنسحبين</h3>
    </div>

    {{-- النموذج --}}
    @if ($showForm)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5>{{ $editMode ? 'تعديل بيانات' : 'إضافة منسحب' }}</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="{{ $editMode ? 'updateRetreat' : 'createRetreat' }}">
                    <div class="row g-3">

                        {{-- اختيار الطالب --}}
                        <div class="col-md-6">
                            <label class="fw-bold">الطالب</label>
                            <select id="student-select" wire:model.live="student_id" class="form-control">
                                <option value="">-- اختر الطالب --</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->Name }}</option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- الصف --}}
                        <div class="col-md-6">
                            <label class="fw-bold">الصف</label>
                            <input type="text" class="form-control" wire:model="Grade" readonly>
                            @error('Grade')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>



                        {{-- المنطقة --}}
                        <div class="col-md-6">
                            <label class="fw-bold">المنطقة</label>
                            <select wire:model="region_id" class="form-control" disabled>
                                <option value="">-- اختر المنطقة --</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->Name }}</option>
                                @endforeach
                            </select>
                            @error('region_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- تاريخ الانسحاب --}}
                        <div class="col-md-6">
                            <label class="fw-bold">تاريخ الانسحاب</label>
                            <input type="date" wire:model="Date_of_interruption" class="form-control">
                            @error('Date_of_interruption')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- الشعبة --}}
                        <div class="col-md-6">
                            <label class="fw-bold">الشعبة</label>
                            <input type="text" class="form-control" wire:model="Division" readonly>
                            @error('Division')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>


                        {{-- السبب --}}
                        <div class="col-md-12">
                            <label class="fw-bold">السبب</label>
                            <input type="text" wire:model="Reason" class="form-control">
                            @error('Reason')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- أزرار --}}
                        <div class="col-6 mt-3">
                            <button type="submit" class="btn btn-{{ $editMode ? 'primary' : 'success' }} w-100">
                                {{ $editMode ? 'تحديث' : 'إضافة' }}
                            </button>
                        </div>
                        <div class="col-6 mt-3">
                            <button type="button" wire:click="cancel" class="btn btn-outline-secondary w-100">
                                إلغاء
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- البحث --}}
    <div class="col-md-12 mb-3">
        <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-primary"></i></span>
            <input type="text" wire:model.debounce.300ms="search" class="form-control border-0 py-2"
                placeholder="ابحث باسم الطالب، السائق، المنطقة أو التاريخ...">
        </div>
    </div>

    {{-- جدول --}}
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">قائمة الطلاب المنسحبين</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle text-center mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>الطالب</th>
                            <th>الصف</th>
                            <th>المنطقة</th>
                            <th>تاريخ الانسحاب</th>
                            <th>السبب</th>
                            <th>الشعبة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($retreats as $retreat)
                            <tr>
                                <td>{{ $retreat->student?->Name ?? '-' }}</td>
                                <td>{{ $retreat->Grade }}</td>
                                <td>{{ $retreat->region?->Name ?? '-' }}</td>
                                <td>{{ $retreat->Date_of_interruption }}</td>
                                <td>{{ $retreat->Reason }}</td>
                                <td>{{ $retreat->Division ?? '-' }}</td>
                                <td>
                                    <button wire:click="edit({{ $retreat->id }})"
                                        class="btn btn-outline-success btn-sm rounded-pill mr-2">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    <button wire:click="confirmDelete({{ $retreat->id }})"
                                        class="btn btn-outline-danger btn-sm rounded-pill">
                                        <i class="fas fa-trash-alt"></i> حذف
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">لا توجد بيانات مسجلة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                 <div class="card-footer d-flex justify-content-center">
                    {{ $retreats->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- نافذة الحذف --}}
    @if ($deleteId)
        <div class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5>تأكيد الحذف</h5>
                        <button type="button" class="btn-close btn-light" wire:click="$set('deleteId', null)"></button>
                    </div>
                    <div class="modal-body">
                        هل أنت متأكد من حذف "{{ $deleteRetreatName }}"؟
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('deleteId', null)">إلغاء</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteRetreat">نعم، احذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('livewire:navigated', function() {
            new TomSelect("#student-select", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "ابحث عن الطالب..."
            });
        });
    </script>

</div>
