<div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Switch Toggle Styles */
        .form-switch { position: relative; display: inline-block; width: 2.5rem; height: 1.4rem; margin-right: 0.5rem; }
        .form-switch input { opacity: 0; width: 0; height: 0; }
        .form-switch label::before { content: ""; position: absolute; top: 0; right: 0; width: 2.5rem; height: 1.4rem; background-color: #adb5bd; border-radius: 1.5rem; transition: background-color 0.3s; }
        .form-switch label::after { content: ""; position: absolute; top: 0.1rem; right: 0.1rem; width: 1.2rem; height: 1.2rem; background-color: white; border-radius: 50%; transition: transform 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.4); }
        .form-switch input:checked+label::after { transform: translateX(-1.1rem); }
        .switch-active label::before { background-color: #28a745; }
        .switch-banned label::before { background-color: #dc3545; }

        /* Badges */
        .badge-red { background-color: #dc3545; color: #fff; }
        .badge-yellow { background-color: #ffc107; color: #212529; }
        .badge-green { background-color: #28a745; color: #fff; }
        .badge-red,.badge-yellow,.badge-green { padding: 0.35em 0.65em; border-radius: 0.25rem; font-size: 0.875em; }

        /* Tables */
        .table th { background-color: #f8f9fa; font-weight: 600; }
        .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.03); }

        .pagination { justify-content: flex-start; }

        .action-link { color: #007bff; text-decoration: none; transition: color 0.2s; }
        .action-link:hover { color: #0056b3; text-decoration: underline; }

        .card-header-custom { background: linear-gradient(135deg, #1976D2 0%, #0D47A1 100%); }
        .filter-section { background-color: #f8f9fa; border-bottom: 1px solid rgba(0,0,0,0.05); }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-start d-none d-sm-block">تحضير السائقين</h3>
            <h3 class="fw-bold text-end d-none d-sm-block"> عام {{ date('Y') }} </h3>
        </div>

        <div class="d-flex justify-content-between  mb-4">
            @if (!$showForm)
                <div class="d-flex gap-2">
                    <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn">
                        <i class="fas fa-plus-circle me-1"></i> إضافة سجل جديد
                    </button>
                </div>
            @endif

            <div class="d-flex gap-2">
                <button wire:click="export" class="btn btn-outline-success">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </button>

                @if(!$showReport)
                    <button wire:click="report" class="btn btn-outline-secondary">
                        <i class="fas fa-chart-bar me-1"></i> التقرير
                    </button>
                @else
                    <button wire:click="closeReport" class="btn btn-outline-danger">
                        <i class="fas fa-times-circle me-1"></i> إغلاق التقرير
                    </button>
                @endif
            </div>
        </div>

        {{-- =================== نموذج الإضافة/التعديل =================== --}}
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
                                @error('driver_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المنطقة</label>
                                <select wire:model="region_id" class="form-control" disabled>
                                    <option value="">-- اختر المنطقة --</option>
                                    @foreach ($regions as $reg)
                                        <option value="{{ $reg->id }}">{{ $reg->Name }}</option>
                                    @endforeach
                                </select>
                                @error('region_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">التاريخ</label>
                                <input type="date" wire:model="Date" class="form-control">
                                @error('Date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم بديل</label>
                                <input type="text" wire:model="Alternative_name" class="form-control">
                                @error('Alternative_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-6 mt-3">
                                <button type="submit" class="btn btn-{{ $editMode ? 'primary' : 'success' }} w-100">
                                    {{ $editMode ? 'تحديث' : 'إضافة' }}
                                </button>
                            </div>
                            <div class="col-6 mt-3">
                                <button type="button" wire:click="cancel" class="btn btn-outline-secondary w-100">إلغاء</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- =================== بحث =================== --}}
        <div class="col-md-12 mb-3">
            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                <span class="input-group-text bg-white border-0"><i class="fas fa-search text-primary"></i></span>
                <input type="text" wire:model.debounce.300ms.live="search" class="form-control border-0 py-2" placeholder="ابحث باسم السائق...">
            </div>
        </div>

        {{-- =================== وضع التقرير (فترة من - إلى) =================== --}}
        @if($showReport)
            <div class="card shadow-sm mb-4 rounded-4">
                <div class="card-header bg-success text-white rounded-top-4 d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <h5 class="mb-0">تقرير غياب السائقين</h5>

                    <div class="d-flex align-items-center gap-2">
                        <label class="mx-1">من:</label>
                        <input type="date" class="form-control" style="width: 180px;" wire:model.live="reportFrom">

                        <label class="mx-1 ms-2">إلى:</label>
                        <input type="date" class="form-control" style="width: 180px;" wire:model.live="reportTo">
                    </div>
                </div>

                <div class="card-body">
                    @if(!$reportFrom || !$reportTo)
                        <div class="alert alert-warning mb-3">يرجى تحديد تاريخي <strong>من</strong> و<strong>إلى</strong> لعرض التقرير.</div>
                    @endif

                    @if($reportFrom && $reportTo)
                        <p class="text-muted mb-2">
                            الفترة: <strong>{{ $reportFrom }}</strong> إلى <strong>{{ $reportTo }}</strong>
                        </p>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>السائق</th>
                                        <th class="text-center">عدد أيام الغياب</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData as $row)
                                        <tr style="cursor:pointer"
                                            wire:click="showDriverDetails({{ $row->driver_id }})"
                                            title="عرض التفاصيل">
                                            <td class="fw-semibold">{{ $row->driver->Name ?? 'غير معروف' }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-red">{{ $row->absence_days }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">لا توجد بيانات داخل هذه الفترة</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- تفاصيل السائق --}}
                        @if($selectedDriverReport && $selectedDriverReport->count())
                            <div class="card mt-4">
                                <div class="card-header bg-success text-white">
                                    تفاصيل السائق: <strong>{{ $selectedDriverName }}</strong>
                                    <span class="ms-2">( {{ $reportFrom }} الى {{ $reportTo }} )</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-info">
                                                <tr>
                                                    <th>التاريخ</th>
                                                    <th>الاسم البديل</th>
                                                    <th>المنطقة</th>
                                                    <th>الحالة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($selectedDriverReport as $day)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($day->Date)->format('Y-m-d') }}</td>
                                                        <td>{{ $day->Alternative_name ?? '-' }}</td>
                                                        <td>{{ $day->region->Name ?? '-' }}</td>
                                                        <td>
                                                            @if($day->Atend)
                                                                <span class="text-success fw-semibold">حاضر</span>
                                                            @else
                                                                <span class="text-danger fw-semibold">غائب</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endif
        {{-- =================== /وضع التقرير =================== --}}

        {{-- =================== جدول الحضور (يظهر فقط عندما التقرير غير مفعّل) =================== --}}
        @if(!$showReport)
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
                                    <th>اسم بديل</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($preparations as $prep)
                                    <tr>
                                        <td>{{ $prep->driver?->Name ?? '-' }}</td>
                                        <td>{{ $prep->region?->Name ?? '-' }}</td>
                                        <td>{{ $prep->Date }}</td>

                                        <td>
                                            <div class="form-switch {{ $prep->Atend ? 'switch-active' : 'switch-banned' }}">
                                                <input type="checkbox" id="atendSwitch{{ $prep->id }}"
                                                       wire:click="toggleAtend({{ $prep->id }})"
                                                       @if ($prep->Atend) checked @endif>
                                                <label for="atendSwitch{{ $prep->id }}"></label>
                                            </div>
                                            <span class="ms-2 fw-semibold {{ $prep->Atend ? 'text-success' : 'text-danger' }}">
                                                {{ $prep->Atend ? 'حاضر' : 'غائب' }}
                                            </span>
                                        </td>

                                        <td>{{ $prep->Alternative_name ?? '-' }}</td>

                                        <td>
                                            <button wire:click="editPreparation({{ $prep->id }})" class="btn btn-outline-success btn-sm rounded-pill mr-2">
                                                <i class="fas fa-edit"></i> تعديل
                                            </button>
                                            <button wire:click="confirmDelete({{ $prep->id }})" class="btn btn-outline-danger btn-sm rounded-pill">
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
                            {{ $preparations->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- =================== /جدول الحضور =================== --}}

        {{-- Delete Modal --}}
        @if ($deleteId)
            <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-sm">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">تأكيد الحذف</h5>
                            <button type="button" class="btn-close btn-light" wire:click="$set('deleteId', null)"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد أنك تريد حذف هذا السجل؟</p>
                            <p class="fw-bold text-danger">" الاسم: {{ $deleteName }} "</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="$set('deleteId', null)" class="btn btn-secondary">إلغاء</button>
                            <button type="button" wire:click="deletePreparation" class="btn btn-danger">نعم، احذف</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
