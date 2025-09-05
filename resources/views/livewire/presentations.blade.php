<div>
    {{-- Toast --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="container py-4">

        {{-- العنوان وزر إضافة --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            @if (!$showForm)
                <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn">
                    <i class="fas fa-plus-circle me-1"></i> إضافة سجل جديد
                </button>
            @endif
            <h3 class="fw-bold text-primary d-none d-md-block">إدارة سجلات الحضور</h3>

        </div>

        {{-- Form --}}
        @if ($showForm)
            <div class="card shadow-sm mb-4 rounded-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="mb-0">{{ $editMode ? 'تعديل سجل الحضور' : 'إضافة سجل حضور جديد' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editMode ? 'updatePresentation' : 'createPresentation' }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">رقم الباص</label>
                                <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden  py-2">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="fas fa-filter text-primary"></i>
                                    </span>
                                    <select wire:model="bus_id" class="form-control border-0">
                                        <option value="">-- بدون --</option>
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

                            <div class="col-md-6">
                                <label class="form-label">التاريخ</label>
                                <input type="date" class="form-control" wire:model="date">
                                @error('date')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">وقت الحضور</label>
                                <input type="time" class="form-control" wire:model="atendTime">
                                @error('atendTime')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">عدد الحاضرين</label>
                                <input type="number" min="0" class="form-control" wire:model="atendStudents">
                                @error('atendStudents')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">وقت الانصراف</label>
                                <input type="time" class="form-control" wire:model="leaveTime">
                                @error('leaveTime')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">عدد المنصرفين</label>
                                <input type="number" min="0" class="form-control" wire:model="leaveStudents">
                                @error('leaveStudents')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <input type="text" class="form-control" wire:model="note">
                                @error('note')
                                    <span class="text-danger small">{{ $message }}</span>
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

        {{-- Search --}}
         <div class="col-md-12 mb-3">
            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                <span class="input-group-text bg-white border-0"><i class="fas fa-search text-primary"></i></span>
                <input type="text" wire:model.debounce.300ms.live="search" class="form-control border-0 py-2"
                    placeholder="ابحث باسم الباص أو التاريخ...">
            </div>
        </div>

        {{-- Table --}}
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">إدارة سجلات الحضور</h5>

            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-success">
                            <tr>
                                <th>الباص</th>
                                <th>التاريخ</th>
                                <th>وقت الحضور</th>
                                <th>وقت الانصراف</th>
                                <th>عدد الحاضرين</th>

                                <th>عدد المنصرفين</th>
                                <th>ملاحظات</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presentations as $pres)
                                <tr>
                                    <td>{{ $pres->bus?->BusType ?? '-' }}</td>
                                    <td>{{ $pres->date }}</td>
                                    <td>{{ $pres->atendTime }}</td>
                                    <td>{{ $pres->leaveTime }}</td>
                                    <td>{{ $pres->atendStudents }}</td>

                                    <td>{{ $pres->leaveStudents }}</td>
                                    <td>{{ $pres->note ?? '-' }}</td>
                                    <td class="d-flex justify-content-center gap-2">
                                        <button wire:click="editPresentation({{ $pres->id }})"
                                            class="btn btn-outline-success btn-sm rounded-pill mr-2 " title="تعديل">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                        <button wire:click="confirmDelete({{ $pres->id }})"
                                            class="btn btn-outline-danger btn-sm rounded-pill mr-2" title="حذف">
                                            <i class="fas fa-trash-alt"></i> حذف
                                        </button>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">لا توجد سجلات حضور</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Delete Modal --}}
            @if ($deleteId)
                <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-sm">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> تأكيد الحذف
                                </h5>
                                <button type="button" class="btn-close btn-light"
                                    wire:click="$set('deleteId', null)"></button>
                            </div>
                            <div class="modal-body">
                                <p>هل أنت متأكد أنك تريد حذف سجل الحضور هذا؟</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary rounded-pill"
                                    wire:click="$set('deleteId', null)">
                                    إلغاء
                                </button>
                                <button type="button" class="btn btn-danger rounded-pill"
                                    wire:click="deletePresentation">
                                    نعم، احذف
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
