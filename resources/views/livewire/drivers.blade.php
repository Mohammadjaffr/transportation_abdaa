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
            <h3 class="fw-bold text-center d-none d-sm-block"> عام {{ date('Y') }} </h3>
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

                            {{-- الحقول الأساسية --}}
                            @foreach ([
        'Name' => 'الاسم',
        'IDNo' => 'رقم البطاقة',
        'Phone' => 'الهاتف',
        'LicenseNo' => 'رقم الرخصة',
        'Ownership' => 'الملكية',
    ] as $field => $label)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ $label }}</label>

                                    @php
                                        $type = $field === 'Phone' ? 'number' : 'text';
                                    @endphp

                                    <input type="{{ $type }}" wire:model="fields.{{ $field }}"
                                        class="form-control @error('fields.' . $field) is-invalid @enderror"
                                        {{ $field == 'IDNo' && $editMode }}>

                                    @error('fields.' . $field)
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach

                            {{-- الجناح --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">الجناح</label>
                                <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden py-2">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="fas fa-filter text-primary"></i>
                                    </span>
                                    <select wire:model="fields.wing_id" class="form-control border-0">
                                        <option value="">-- بدون --</option>
                                        @foreach ($wings as $Wing)
                                            <option value="{{ $Wing->id }}">{{ $Wing->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('fields.wing_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- نوع الباص --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">نوع الباص</label>
                                <select wire:model="fields.Bus_type"
                                    class="form-control border-  @error('fields.Bus_type') is-invalid @enderror">
                                    <option value="">-- اختر نوع الباص --</option>
                                    <option value="هايس">هايس</option>
                                    <option value="كوستر">كوستر</option>
                                </select>
                                @error('fields.Bus_type')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- عدد الركاب --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">عدد الركاب</label>
                                <input type="text" wire:model="fields.No_Passengers"
                                    class="form-control @error('fields.No_Passengers') is-invalid @enderror">
                                @error('fields.No_Passengers')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- المنطقة --}}
                          

                            {{-- الحقول Boolean --}}
                            @foreach (['CheckUp' => 'الفحص للمركبة', 'Behavior' => 'السلوك', 'Form' => 'الاستمارة', 'Fitnes' => 'لياقة السائق'] as $field => $label)
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

                              <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold text-primary">المناطق</label>

                                <div class="border rounded-4 shadow-sm bg-white p-3"
                                    style="max-height: 300px; overflow-y: auto;">
                                    @forelse ($regions as $region)
                                        <div class="form-check form-check-inline w-75 mb-2">
                                            <input class="form-check-input" type="checkbox" value="{{ $region->id }}"
                                                wire:model="fields.region_ids" id="region_{{ $region->id }}">
                                            <label class="form-check-label fw-semibold ms-2"
                                                for="region_{{ $region->id }}">
                                                {{ $region->Name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-muted small">لا توجد مناطق متاحة حاليًا.</p>
                                    @endforelse
                                </div>

                                <!-- عرض المناطق المحددة -->
                                @if (!empty($fields['region_ids']))
                                    <div class="mt-2">
                                        <label class="form-label fw-bold text-primary">المناطق المحددة:</label>

                                        {{-- <small class="text-success fw-bold">المناطق المحددة:</small> --}}
                                        <div class="d-flex flex-wrap mt-1">
                                            @foreach ($regions->whereIn('id', $fields['region_ids']) as $selected)
                                                <span
                                                    class="badge bg-success text-white border border-success mr-1 mb-1">    
                                                    {{ $selected->Name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>



                            {{-- أزرار --}}
                            <div class="col-md-6 mt-3">
                                <button type="submit"
                                    class="btn btn-{{ $editMode ? 'primary' : 'success' }} w-100 rounded-pill">
                                    <i class="fas {{ $editMode ? 'fa-save' : 'fa-plus-circle' }} me-1"></i>
                                    {{ $editMode ? 'تحديث' : 'إضافة' }}
                                </button>
                            </div>
                            <div class="col-md-6 mt-3">
                                <button type="button" wire:click="resetForm"
                                    class="btn btn-outline-secondary w-100 rounded-pill">
                                    إلغاء
                                </button>
                            </div>
                        </div>
                </div>
                </form>
            </div>
    </div>
    @endif

    {{-- البحث --}}
    <div class="col-md-12 mb-3">
        <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0">
                <i class="fas fa-search text-primary"></i>
            </span>
            <input type="text" wire:model.debounce.300ms.live="search" class="form-control border-0 py-2"
                placeholder="ابحث باسم السائق او رقم البطاقة">
        </div>
    </div>

    {{-- جدول السائقين --}}
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
                            <th>نوع الباص</th>
                            <th>عدد الركاب</th>
                            <th>الجناح</th>
                            <th>المنطقة</th>
                            <th>الاستمارة</th>
                            <th>الفحص للمركبة</th>
                            <th>السلوك</th>
                            <th>لياقة السائق</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr class="text-center">
                                <td><span class="badge px-3 py-2">{{ $driver->IDNo }}</span></td>
                                <td>{{ $driver->Name }}</td>
                                <td>{{ $driver->Phone }}</td>
                                <td>{{ $driver->LicenseNo }}</td>
                                <td>{{ $driver->Ownership ?? '-' }}</td>
                                <td>{{ $driver->Bus_type ?? '-' }}</td>
                                <td>{{ $driver->No_Passengers ?? '-' }}</td>
                                <td>{{ $driver->Wing->Name ?? '-' }}</td>
                                <td>
                                    @foreach ($driver->regions as $region)
                                        <span class="badge bg-success">{{ $region->Name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($driver->Form === 1)
                                        <span class="badge bg-success">يوجد</span>
                                    @elseif($driver->Form === 0)
                                        <span class="badge bg-danger">لايوجد</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($driver->CheckUp === 1)
                                        <span class="badge bg-success">يوجد</span>
                                    @elseif($driver->CheckUp === 0)
                                        <span class="badge bg-danger">لايوجد</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($driver->Behavior === 1)
                                        <span class="badge bg-success">جيد</span>
                                    @elseif($driver->Behavior === 0)
                                        <span class="badge bg-danger">سيء</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($driver->Fitnes === 1)
                                        <span class="badge bg-success">لائق</span>
                                    @elseif($driver->Fitnes === 0)
                                        <span class="badge bg-danger">غير لائق</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td class="d-flex gap-2 justify-content-center">
                                    <button wire:click="editDriver({{ $driver->id }})"
                                        class="btn btn-outline-success btn-sm rounded-pill mr-2" title="تعديل">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    <button wire:click="confirmDelete({{ $driver->id }})"
                                        class="btn btn-outline-danger btn-sm rounded-pill" title="حذف">
                                        <i class="fas fa-trash-alt"></i> حذف
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center text-muted py-4">
                                    <i class="fas fa-user-times fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">لا يوجد سائقين مسجلين</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="card-footer d-flex justify-content-center">
                    {{ $students->links() }}
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
                            <p>هل أنت متأكد أنك تريد حذف السائق التالي؟</p>
                            <p class="fw-bold text-danger">" الاسم: {{ $deleteDriverName }} "</p>
                        </div>
                        <div class="modal-footer justify-content-start">
                            <button type="button" class="btn btn-secondary rounded-pill"
                                wire:click="$set('deleteId', null)">إلغاء</button>
                            <button type="button" class="btn btn-danger rounded-pill"
                                wire:click="deleteDriver({{ $deleteId }})">نعم، احذف</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <script>
        document.addEventListener('livewire:load', function() {
            const selectElement = document.getElementById('regionSelect');

            const choices = new Choices(selectElement, {
                removeItemButton: true,
                placeholderValue: 'اختر المناطق',
                searchPlaceholderValue: 'بحث...',
            });

            selectElement.addEventListener('change', function() {
                @this.set('fields.region_ids', choices.getValue(true));
            });

            Livewire.hook('message.processed', () => {
                choices.setChoiceByValue(@this.get('fields.region_ids'));
            });
        });
        document.addEventListener('DOMContentLoaded', () => {
            try {
                new Choices('#regionSelect');
                console.log("Choices.js يعمل ✅");
            } catch (e) {
                console.log("Choices.js لا يعمل ❌", e);
            }
        });
    </script>

</div>
