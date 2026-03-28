<div class="container-fluid px-2 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-3 px-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark">تحضير {{ $type == 'morning' ? 'الذهاب' : 'العودة' }}</h5>
            <small class="text-muted"><i class="fas fa-calendar-day me-1"></i>
                {{ \Carbon\Carbon::parse($date)->locale('ar')->translatedFormat('j F Y') }}</small>
        </div>
        <div class="bg-light px-3 py-2 rounded-4 shadow-sm border text-center">
            <small class="d-block text-muted fw-bold" style="font-size: 0.75rem;">الإجمالي</small>
            <span class="fw-bold fs-5 text-dark">{{ $counters['total'] }}</span>
        </div>
    </div>

    <!-- Active Counters -->
    <div class="row g-2 px-2 mb-3">
        <div class="col-4">
            <div
                class="bg-success bg-opacity-10 text-success rounded-4 p-2 text-center border border-success border-opacity-25 shadow-sm">
                <small class="d-block fw-bold opacity-75">الحضور</small>
                <span class="fs-5 fw-bolder">{{ $counters['present'] }}</span>
            </div>
        </div>
        <div class="col-4">
            <div
                class="bg-danger bg-opacity-10 text-danger rounded-4 p-2 text-center border border-danger border-opacity-25 shadow-sm">
                <small class="d-block fw-bold opacity-75">الغياب</small>
                <span class="fs-5 fw-bolder">{{ $counters['absent'] }}</span>
            </div>
        </div>
        <div class="col-4">
            <div
                class="bg-warning bg-opacity-10 text-warning rounded-4 p-2 text-center border border-warning border-opacity-25 shadow-sm">
                <small class="d-block fw-bold opacity-75">المتبقي</small>
                <span class="fs-5 fw-bolder">{{ $counters['pending'] }}</span>
            </div>
        </div>
    </div>

    @if ($isLocked)
        <div
            class="alert alert-danger rounded-4 shadow-sm d-flex flex-column align-items-center text-center p-5 mb-4 mx-2 border-0 bg-danger bg-opacity-10 text-danger">
            <i class="fas fa-user-lock fa-4x mb-3 text-danger opacity-75"></i>
            <h4 class="fw-bolder mb-2">انتهى وقت التحضير</h4>
            <h6 class="fw-bold opacity-75 mb-3">{{ $lockMessage }}</h6>
            <p class="mb-0 fw-bold fs-6">في حال فاتك الوقت، يرجى التواصل مع الإدارة</p>
        </div>
    @else
        <!-- Search & Bulk action -->
        <div class="d-flex gap-2 mb-3 px-2">
            <div class="input-group shadow-sm rounded-4 overflow-hidden">
                <span class="input-group-text bg-white border-end-0 border-0"><i
                        class="fas fa-search text-muted"></i></span>
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="form-control border-start-0 border-0 ps-0 fw-bold" placeholder="بحث عن طالب...">
            </div>
            <button wire:click="prepareAllPresent"
                class="btn btn-outline-success text-nowrap rounded-4 shadow-sm fw-bold" style="border-width:2px;"
                {{ $isLocked ? 'disabled' : '' }}>
                <i class="fas fa-check-double me-1"></i> الكل حاضر
            </button>
        </div>

        <!-- Students List -->
        <div class="d-grid gap-3 mb-5 pb-5 px-2">
            @forelse($studentsList as $student)
                <div class="card shadow-sm rounded-4 border-0 {{ isset($student['status']) ? 'animate-scale' : '' }}"
                    style="background-color: {{ isset($student['status']) ? ($student['status'] ? '#f0fdf4' : '#fef2f2') : '#ffffff' }}; border-right: 6px solid {{ isset($student['status']) ? ($student['status'] ? '#16a34a' : '#ef4444') : '#e5e7eb' }} !important; transition: 0.2s all;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">{{ $student['name'] }}</h5>
                                <small class="text-muted"><i
                                        class="fas fa-map-marker-alt text-primary me-1 opacity-75"></i>
                                    {{ $student['region'] ?: 'بدون منطقة' }}</small>
                            </div>
                            @if (isset($student['status']))
                                <div
                                    class="{{ $student['status'] ? 'text-success bg-white border border-success' : 'text-danger bg-white border border-danger' }} px-3 py-1 rounded-pill shadow-sm">
                                    <small class="fw-bold"><i
                                            class="fas {{ $student['status'] ? 'fa-check' : 'fa-times' }} me-1"></i>
                                        {{ $student['status'] ? 'حاضر' : 'غائب' }}</small>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2 w-100">
                            <button wire:click="setAttendance({{ $student['id'] }}, true)"
                                class="btn flex-grow-1 rounded-4 shadow-sm {{ isset($student['status']) && $student['status'] === true ? 'btn-success fw-bold border-success' : 'btn-light border-success text-success bg-white border-2' }}"
                                style="padding: 14px 10px; font-size: 1.15rem; transition: 0.1s transform;"
                                {{ $isLocked ? 'disabled' : '' }}>
                                حاضر
                            </button>

                            <button wire:click="setAttendance({{ $student['id'] }}, false)"
                                class="btn flex-grow-1 rounded-4 shadow-sm {{ isset($student['status']) && $student['status'] === false ? 'btn-danger fw-bold border-danger' : 'btn-light border-danger text-danger bg-white border-2' }}"
                                style="padding: 14px 10px; font-size: 1.15rem; transition: 0.1s transform;"
                                {{ $isLocked ? 'disabled' : '' }}>
                                غائب
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3 opacity-25"></i>
                    <p class="text-muted fw-bold">لا يوجد طلاب مطابقين للبحث</p>
                </div>
            @endforelse
        </div>
    @endif
</div>
