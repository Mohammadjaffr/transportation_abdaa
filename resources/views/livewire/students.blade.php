<div>



    <div class="container py-4">

        {{-- العنوان وزر إضافة --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            @if (!$showForm)
                <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn w-sm-100 mb-3">
                    <i class="fas fa-plus-circle me-1"></i> إضافة طالب جديد
                </button>
            @endif
            <h3 class="fw-bold text-primary d-none d-sm-block">إدارة الطلاب</h3>
        </div>

        {{-- Form --}}
        @if ($showForm)
            <div class="card shadow-sm mb-4 rounded-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">{{ $editMode ? 'تعديل بيانات الطالب' : 'إضافة طالب جديد' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editMode ? 'updateStudent' : 'createStudent' }}">
                        <div class="row g-3">



                            <div class="col-md-6">
                                <label class="form-label">اسم الطالب</label>
                                <input type="text" wire:model="name"
                                    class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الصف / المرحلة</label>
                                <input type="text" wire:model="grade"
                                    class="form-control @error('grade') is-invalid @enderror">
                                @error('grade')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-md-12">
                                <label class="form-label fw-bold">رقم الباص</label>
                                <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden  py-2">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="fas fa-filter text-primary"></i>
                                    </span>
                                    <select wire:model="bus_id" class="form-control border-0">
                                        <option value="">-- اختر الباص --</option>
                                        @foreach ($buses as $bus)
                                            <option value="{{ $bus->id }}">حافلة رقم {{ $bus->id }} -
                                                {{ $bus->BusType }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                @error('bus_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6 mt-3">
                                <button type="submit"
                                    class="btn btn-{{ $editMode ? 'primary' : 'success' }} w-100 rounded-pill">
                                    <i class="fas {{ $editMode ? 'fa-save' : 'fa-plus-circle' }} me-1"></i>
                                    {{ $editMode ? 'تحديث الطالب' : 'إضافة الطالب' }}
                                </button>
                            </div>
                            <div class="col-6 mt-3">

                                <button type="button" wire:click="cancel"
                                    class="btn btn-outline-secondary w-100 rounded-pill">
                                    <i class="fas fa-undo me-1"></i> إلغاء
                                </button>

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
                <input type="text" wire:model.debounce.300ms.live="search" class="form-control border-0 py-2"
                    placeholder="ابحث باسم الطالب أو الرقم...">
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">إدارة الطلاب</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0 text-center">
                        <thead class="table-success">
                            <tr>
                                <th>اسم الطالب</th>
                                <th>المرحله الدرسية</th>
                                <th>نوع الباص</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>{{ $student->Name }}</td>
                                    <td>{{ $student->Grade ?? '-' }}</td>
                                    <td>{{ $student->bus?->BusType ? 'الباص نوع : ' . $student->bus->BusType : '-' }}
                                    </td>
                                    <td class="d-flex gap-2 justify-content-center">
                                        <button wire:click="editStudent({{ $student->id }})"
                                            class="btn btn-outline-success btn-sm rounded-pill mr-2">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                        <button wire:click="confirmDelete({{ $student->id }})"
                                            class="btn btn-outline-danger btn-sm rounded-pill">
                                            <i class="fas fa-trash-alt"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-user-graduate fa-2x mb-2 text-secondary"></i>
                                        <p class="mb-0">لا يوجد طلاب مسجلين</p>
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
                            <p>هل أنت متأكد أنك تريد حذف هذا الطالب؟</p>
                        </div>
                        <div class="modal-footer justify-content-start">
                            <button type="button" class="btn btn-secondary rounded-pill"
                                wire:click="$set('deleteId', null)">إلغاء</button>
                            <button type="button" class="btn btn-danger rounded-pill" wire:click="deleteStudent">نعم،
                                احذف</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </div>
</div>
