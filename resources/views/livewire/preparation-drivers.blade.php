<div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Switch Toggle Styles */
        .form-switch {
            position: relative;
            display: inline-block;
            width: 2.5rem;
            height: 1.4rem;
            margin-right: 0.5rem;
        }

        .form-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .form-switch label::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 2.5rem;
            height: 1.4rem;
            background-color: #adb5bd;
            border-radius: 1.5rem;
            transition: background-color 0.3s;
        }

        .form-switch label::after {
            content: "";
            position: absolute;
            top: 0.1rem;
            right: 0.1rem;
            width: 1.2rem;
            height: 1.2rem;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        .form-switch input:checked+label::after {
            transform: translateX(-1.1rem);
        }

        .switch-active label::before {
            background-color: #28a745;
        }

        .switch-banned label::before {
            background-color: #dc3545;
        }

        /* Badge Styles */
        .badge-red {
            background-color: #dc3545;
            color: white;
        }

        .badge-yellow {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-green {
            background-color: #28a745;
            color: white;
        }

        .badge-red,
        .badge-yellow,
        .badge-green {
            padding: 0.35em 0.65em;
            border-radius: 0.25rem;
            font-size: 0.875em;
        }

        /* Table Styles */
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        /* Pagination */
        .pagination {
            justify-content: flex-start;
        }

        /* Action Links */
        .action-link {
            color: #007bff;
            text-decoration: none;
            transition: color 0.2s;
        }

        .action-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        /* Custom Card Styles */
        .card-header-custom {
            background: linear-gradient(135deg, #1976D2 0%, #0D47A1 100%);
        }

        .filter-section {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
    </style>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            @if (!$showForm)
                <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn">
                    <i class="fas fa-plus-circle me-1"></i> إضافة سجل جديد
                </button>
            @endif
            <h3 class="fw-bold text-primary">تحضير السائقين</h3>
        </div>

        {{-- Form --}}
        @if ($showForm)
            <div class="card shadow-sm mb-4 rounded-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="mb-0">{{ $editMode ? 'تعديل سجل' : 'إضافة سجل جديد' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editMode ? 'updatePreparation' : 'createPreparation' }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">السائق</label>
                                <select wire:model.live="driver_id" class="form-control">
                                    <option value="">-- اختر السائق --</option>
                                    @foreach ($drivers as $drv)
                                        <option value="{{ $drv->id }}">{{ $drv->Name }}</option>
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

                            <div class="col-md-12">
                                <label class="form-label">التاريخ</label>
                                <input type="date" wire:model="Month" class="form-control">
                                @error('Month')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>





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
                    placeholder="ابحث باسم السائق...">
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
                                    <td>{{ $prep->driver?->Name ?? '-' }}</td>
                                    <td>{{ $prep->region?->Name ?? '-' }}</td>
                                    <td>{{ $prep->Month }}</td>
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
                                    <td colspan="5" class="text-center">لا توجد سجلات</td>
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
                                <h5 class="modal-title">تأكيد الحذف</h5>
                                <button type="button" class="btn-close btn-light"
                                    wire:click="$set('deleteId', null)"></button>
                            </div>
                            <div class="modal-body">
                                هل أنت متأكد من حذف هذا السجل؟
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
