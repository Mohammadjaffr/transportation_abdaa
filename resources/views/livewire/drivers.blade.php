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
                            <th>الاسم / رقم البطاقة</th>
                            <th>معلومات التواصل</th>
                            <th>الباص / الجناح / المناطق</th>
                            <th class="text-center">الطلاب المخصصين</th>
                            <th class="text-center">حالة الحساب</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr class="align-middle">
                                <td>
                                    <div class="fw-bold">{{ $driver->Name }}</div>
                                    <div class="text-muted small"><i class="fas fa-id-card me-1"></i> {{ $driver->IDNo }}</div>
                                </td>
                                <td>
                                    <div class="text-primary"><i class="fas fa-phone-alt me-1"></i> {{ $driver->Phone }}</div>
                                </td>
                                <td>
                                    @if($driver->Bus_type) <span class="badge bg-secondary mb-1"><i class="fas fa-bus-alt"></i> {{ $driver->Bus_type }}</span> @endif
                                    @if($driver->wing) <span class="badge bg-info text-dark mb-1"><i class="fas fa-building"></i> {{ $driver->wing->Name }}</span> @endif
                                    <div class="mt-1">
                                    @foreach ($driver->regions as $region)
                                        <span class="badge bg-success me-1">{{ $region->Name }}</span>
                                    @endforeach
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark shadow-sm px-3 py-2 fs-6">
                                        <i class="fas fa-users text-info me-1"></i> {{ $driver->students ? $driver->students->count() : 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($driver->user)
                                        <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i> مرتبط</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-times-circle me-1"></i> غير مرتبط</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('driver.details', $driver->id) }}" class="btn btn-primary btn-sm" title="التفاصيل"><i class="fas fa-eye"></i></a>
                                        <button wire:click="editDriver({{ $driver->id }})" class="btn btn-success btn-sm" title="تعديل"><i class="fas fa-edit"></i></button>
                                        <button wire:click="confirmDelete({{ $driver->id }})" class="btn btn-danger btn-sm" title="حذف"><i class="fas fa-trash-alt"></i></button>
                                    </div>
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
                    {{ $drivers->links() }}
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
