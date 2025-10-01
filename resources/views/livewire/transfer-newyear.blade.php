<div>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-start">إدارة الطلاب</h3>
            <h3 class="fw-bold text-end">عام {{ $currentYear->year ?? date('Y') }}</h3>
        </div>

        {{-- البحث --}}
        <div class="row mb-3 align-items-center">
            {{-- البحث --}}
            <div class="col-md-10">
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                    <span class="input-group-text bg-white border-0">
                        <i class="fas fa-search text-primary"></i>
                    </span>
                    <input type="text" wire:model.debounce.300ms.live="search" class="form-control border-0 py-2"
                        placeholder="ابحث باسم الطالب أو الرقم...">
                </div>
            </div>

            {{-- زر التصدير --}}
            <div class="col-md-2 text-start">
                <button wire:click="export" class="btn btn-success btn-lg">
                    <i class="fas fa-file-excel"></i>
                    {{ $this->selectedYear ? 'تصدير الطلاب - ' . $this->selectedYear->year : 'تصدير جميع الطلاب' }}
                </button>
            </div>
        </div>


        <div class="mb-3">
            <label for="yearSelect" class="form-label fw-bold">اختر السنة</label>
            <select id="yearSelect" class="form-control  form-select-lg shadow-sm" wire:model.live="yearFilter">
                @foreach ($allYears as $year)
                    <option value="{{ $year->id }}">
                        {{ $year->year }} {{ $year->is_current ? '(الحالية)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>


        {{-- Tabs الفلاتر --}}
        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link btn {{ $filter === 'all' ? 'active' : '' }}" wire:click="setFilter('all')">كل
                    الطلاب</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn {{ $filter === 'failed' ? 'active' : '' }}"
                    wire:click="setFilter('failed')">الراسبين</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn {{ $filter === 'graduated' ? 'active' : '' }}"
                    wire:click="setFilter('graduated')">المتخرجين</a>
            </li>
        </ul>

        {{-- جدول الطلاب --}}
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة الطلاب</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الصف</th>
                                <th>النوع</th>
                                <th>الهاتف</th>
                                <th>الجناح</th>
                                <th>الشعبة</th>
                                <th>المنطقة</th>
                                <th>الموقف</th>
                                <th>المعلم\ة</th>
                                <th>السائق</th>
                                <th>سنة الدراسة</th>
                                <th>ناجح/راسب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $record)
                                <tr>
                                    <td>{{ $record->student->id }}</td>
                                    <td>{{ $record->student->Name }}</td>
                                    <td>{{ $record->Grade }}</td>
                                    <td>{{ $record->student->Sex }}</td>
                                    <td>{{ $record->Phone }}</td>
                                    <td>{{ $record->wing?->Name }}</td>
                                    <td>{{ $record->student->Division }}</td>
                                    <td>{{ $record->region?->Name }}</td>
                                    <td>{{ $record->Stu_position }}</td>
                                    <td>{{ $record->teacher?->Name ?? 'غير موجود' }}</td>
                                    <td>{{ $record->driver?->Name ?? 'غير موجود' }}</td>
                                    <td>{{ $record->schoolYear->year }}</td>
                                    <td>
                                        @if ($record->schoolYear && $record->schoolYear->is_current)
                                            {{-- السنة الحالية --}}
                                            <div
                                                class="form-switch {{ $record->status === 'راسب' ? 'switch-banned' : 'switch-active' }}">
                                                <input type="checkbox" id="failSwitch{{ $record->id }}"
                                                    wire:click="toggleFailed({{ $record->id }})"
                                                    @if ($record->status !== 'راسب') checked @endif>
                                                <label for="failSwitch{{ $record->id }}"></label>
                                            </div>
                                            <span
                                                class="ms-2 fw-semibold {{ $record->status === 'راسب' ? 'text-danger' : 'text-success' }}">
                                                {{ $record->status }}
                                            </span>
                                        @else
                                            {{-- السنوات الماضية --}}
                                            <span
                                                class="badge {{ $record->status === 'راسب' ? 'bg-danger' : 'bg-success' }} text-white">
                                                {{ $record->status }}
                                            </span>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-4">
                                        <i class="fas fa-user-graduate fa-2x mb-2 text-secondary"></i>
                                        <p class="mb-0">لا يوجد طلاب</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="card-footer d-flex justify-content-center">
                        {{ $students->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            @if ($currentYear && $yearFilter == $currentYear->id)
                <div class="text-center my-4">
                    <button wire:click="confirmTransfer" class="btn btn-primary btn-lg">
                        نقل إلى السنة الجديدة
                    </button>
                </div>
            @else
                <span class="badge bg-success text-center my-4 p-2">
                    تم نقل السنة الحالية إلى السنة الجديدة
                </span>
            @endif
            {{-- SweetAlert --}}
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.on('show-confirm-transfer', () => {
                        Swal.fire({
                            title: '⚠️ تحذير هام',
                            text: "بمجرد تأكيد النقل سيتم ترحيل جميع الطلاب إلى السنة الجديدة ولن يمكنك التراجع عن العملية!",
                            icon: 'warning',
                            showCancelButton: true,
                            cancelButtonText: 'إلغاء',
                            confirmButtonText: 'نعم، أريد المتابعة',
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Livewire.dispatch('transferStudents');
                                Swal.fire({
                                    title: 'جارٍ النقل...',
                                    text: 'يتم الآن ترحيل جميع الطلاب، يرجى الانتظار.',
                                    icon: 'info',
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading()
                                    }
                                });
                            }
                        });
                    });
                });
            </script>

        </div>
