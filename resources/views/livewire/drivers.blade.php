<div>
    {{-- Toast --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <div class="container py-4">

        {{-- العنوان وزر إضافة --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            @if (!$showForm)
                <button wire:click="$set('showForm', true)" class="btn btn-primary add-btn">
                    <i class="fas fa-plus-circle me-1"></i> إضافة سائق جديد
                </button>
            @endif
            <h3 class="fw-bold text-primary d-none d-sm-block">إدارة السائقين</h3>
        </div>

        {{-- Form --}}
        @if ($showForm)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">{{ $editMode ? 'تعديل السائق' : 'إضافة سائق جديد' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editMode ? 'updateDriver' : 'createDriver' }}">
                        <div class="row g-3">
                            @foreach ([
        'Name' => 'الاسم',
        'CardNo' => 'رقم البطاقة',
        'Phone' => 'الهاتف',
        'LicenseNo' => 'رقم الرخصة',
        'Ownership' => 'الملكية',
        'Wing' => 'الجناح',
    ] as $field => $label)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <input
                                        type="{{ in_array($field, ['CardNo', 'Phone', 'LicenseNo']) ? 'number' : 'text' }}"
                                        wire:model="fields.{{ $field }}"
                                        class="form-control @error('fields.' . $field) is-invalid @enderror"
                                        {{ $field == 'CardNo' && $editMode ? 'disabled' : '' }}>
                                    @error('fields.' . $field)
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach


                            {{-- الحقول Boolean --}}
                            @foreach (['CheckUp' => 'الفحص الطبي', 'Behavior' => 'السلوك', 'Form' => 'نموذج التدريب', 'Fitnes' => 'اللياقة البدنية'] as $field => $label)
                                <div class="col-md-6 my-1">
                                    <label class="form-label fw-bold">{{ $label }}</label>
                                    <div
                                        class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden my-1 py-2">
                                        <span class="input-group-text bg-white border-0">
                                            <i class="fas fa-filter text-primary"></i>
                                        </span>
                                        <select wire:model="fields.{{ $field }}" class="form-control border-0">
                                            <option value="">-- اختر --</option>
                                            <option value="1">نعم</option>
                                            <option value="0">لا</option>
                                        </select>
                                    </div>
                                    @error('fields.' . $field)
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach


                            {{-- الحافلة المرتبطة --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold">رقم الحافلة</label>
                                <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden  py-2">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="fas fa-filter text-primary"></i>
                                    </span>
                                    <select wire:model="fields.bus_id" class="form-control border-0">
                                        <option value="">-- بدون --</option>
                                        @foreach ($buses as $bus)
                                            <option value="{{ $bus->id }}">حافلة رقم {{ $bus->id }} -
                                                {{ $bus->BusType }}</option>
                                        @endforeach
                                    </select>
                                    @error('fields.bus_id')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <button type="submit"
                                    class="btn btn-{{ $editMode ? 'primary' : 'success' }} w-100 rounded-pill">
                                    <i class="fas {{ $editMode ? 'fa-save' : 'fa-plus-circle' }} me-1"></i>
                                    {{ $editMode ? 'تحديث' : 'إضافة' }}
                                </button>
                            </div>
                            <div class="col-6 mt-3">
                                <button type="button" wire:click="resetForm"
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
                    placeholder=" ابحث باسم السائق او رقم البطاقة ">

            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">إدارة السائقين</h5>

            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-success text-center">
                            <tr>
                                <th>رقم البطاقة</th>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                                <th>رقم الرخصة</th>
                                <th>الملكية</th>
                                <th>الجناح</th>
                                <th>الفحص الطبي</th>
                                <th>السلوك</th>
                                <th>نموذج التدريب</th>
                                <th>اللياقة البدنية</th>
                                <th>الحافلة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($drivers as $driver)
                                <tr class="text-center">
                                    <td><span class="badge  px-3 py-2">{{ $driver->CardNo }}</span></td>
                                    <td>{{ $driver->Name }}</td>
                                    <td>{{ $driver->Phone }}</td>
                                    <td>{{ $driver->LicenseNo }}</td>
                                    <td>{{ $driver->Ownership ?? '-' }}</td>
                                    <td>{{ $driver->Wing ?? '-' }}</td>

                                    <!-- الفحص الطبي -->
                                    <td>
                                        @if ($driver->CheckUp === 1)
                                            <span class="badge bg-success">يوجد</span>
                                        @elseif($driver->CheckUp === 0)
                                            <span class="badge bg-danger">لايوجد</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>

                                    <!-- السلوك -->
                                    <td>
                                        @if ($driver->Behavior === 1)
                                            <span class="badge bg-success">جيد</span>
                                        @elseif($driver->Behavior === 0)
                                            <span class="badge bg-danger">سيء</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>

                                    <!-- نموذج التدريب -->
                                    <td>
                                        @if ($driver->Form === 1)
                                            <span class="badge bg-success">مكتمل</span>
                                        @elseif($driver->Form === 0)
                                            <span class="badge bg-warning text-dark">غير مكتمل</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>

                                    <!-- اللياقة -->
                                    <td>
                                        @if ($driver->Fitnes === 1)
                                            <span class="badge bg-success">لائق</span>
                                        @elseif($driver->Fitnes === 0)
                                            <span class="badge bg-danger">غير لائق</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>

                                    <!-- الحافلة -->
                                    <td>
                                        @if ($driver->bus)
                                            <span class="badge bg-info text-dark">حافلة رقم :
                                                {{ $driver->bus->id }}</span>
                                        @else
                                            <span class="badge bg-secondary">بدون رقم </span>
                                        @endif

                                    </td>

                                    <!-- الإجراءات -->
                                    <td class="d-flex justify-content-center gap-2 px-5">
                                        <button wire:click="editDriver({{ $driver->id }})"
                                            class="btn btn-outline-success btn-sm rounded-pill px-4 mr-2 "
                                            title="تعديل">
                                            <i class="fas fa-edit"></i> تعديل
                                        </button>
                                        <button wire:click="confirmDelete({{ $driver->id }})"
                                            class="btn btn-outline-danger btn-sm rounded-pill px-4 " title="حذف">
                                            <i class="fas fa-trash-alt"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                        <i class="fas fa-user-times fa-2x mb-2 text-secondary"></i>
                                        <p class="mb-0">لا يوجد سائقين مسجلين</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
                            <p class="fw-bold text-danger">" الاسم:{{ $deleteDriverName }} "</p>
                        </div>
                        <div class="modal-footer justify-content-start">
                            <button type="button" class="btn btn-secondary rounded-pill"
                                wire:click="$set('deleteId', null)">
                                إلغاء
                            </button>
                            <button type="button" class="btn btn-danger rounded-pill"
                                wire:click="deleteDriver({{ $deleteId }})">
                                نعم، احذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif


    </div>
</div>
