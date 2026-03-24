<div class="container-fluid px-3 pt-2">
    <!-- Greeting Card -->
    <div class="card bg-primary text-white mb-4 position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0d6efd, #0dcaf0);">
        <div class="position-absolute end-0 top-0 opacity-25" style="transform: translate(20%, -30%);">
            <i class="fas fa-bus" style="font-size: 8rem;"></i>
        </div>
        <div class="card-body p-4 position-relative z-1">
            <h4 class="fw-bold mb-1">مرحباً كابتن: {{ $driver->Name ?? auth()->user()->name }}</h4>
            <p class="mb-0 opacity-75">{{ \Carbon\Carbon::now()->locale('ar')->translatedFormat('l, j F Y') }}</p>
            @if($driver && $driver->bus)
            <div class="mt-3 bg-white bg-opacity-25 rounded p-2 d-inline-block shadow-sm">
                <i class="fas fa-bus-alt me-1"></i> الحافلة: {{ $driver->bus->BusType ?? 'غير محدد' }}
            </div>
            @endif
        </div>
    </div>

    <h6 class="fw-bold mb-3 text-secondary px-1">نظرة عامة على اليوم</h6>
    
    <!-- Quick Summary Cards Grid -->
    <div class="row g-3 mb-5">
        <div class="col-12">
            <div class="card shadow-sm h-100 p-4 border-0 rounded-4 animate-scale" style="background-color: #ffffff;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted fw-bold fs-6">الطلاب المستهدفين</small>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2"><i class="fas fa-users fa-lg"></i></div>
                </div>
                <h1 class="fw-bolder mb-0 text-dark display-4">{{ $totalStudents }}</h1>
            </div>
        </div>
        <div class="col-6">
            <div class="card shadow-sm h-100 p-4 border-0 rounded-4 text-center animate-scale" style="background-color: #f0fdf4;">
                <div class="text-success mb-2"><i class="fas fa-check-circle fa-2x opacity-75"></i></div>
                <h2 class="fw-bolder mb-1 text-success">{{ $preparedStudents }}</h2>
                <small class="text-success fw-bold d-block">سجلات الحضور اليوم</small>
            </div>
        </div>
        <div class="col-6">
            <div class="card shadow-sm h-100 p-4 border-0 rounded-4 text-center animate-scale" style="background-color: #fef2f2;">
                <div class="text-danger mb-2"><i class="fas fa-times-circle fa-2x opacity-75"></i></div>
                <h2 class="fw-bolder mb-1 text-danger">{{ $absentStudents }}</h2>
                <small class="text-danger fw-bold d-block">سجلات الغياب اليوم</small>
            </div>
        </div>
    </div>

    <h6 class="fw-bold mb-3 text-secondary px-1">إجراءات سريعة</h6>
    
    <div class="d-grid gap-3">
        <a href="{{ route('driver.attendance', ['type' => 'morning']) }}" class="btn btn-primary btn-lg rounded-4 shadow-sm py-3 fw-bold d-flex align-items-center justify-content-between px-4">
            <span>بدء تحضير الذهاب</span>
            <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                <i class="fas fa-sun"></i>
            </div>
        </a>
        <a href="{{ route('driver.attendance', ['type' => 'leave']) }}" class="btn btn-info btn-lg text-white rounded-4 shadow-sm py-3 fw-bold d-flex align-items-center justify-content-between px-4" style="background-color: #0dcaf0;">
            <span>بدء تحضير العودة</span>
            <div class="bg-white text-info rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                <i class="fas fa-moon"></i>
            </div>
        </a>
        <a href="{{ route('driver.history') }}" class="btn btn-light btn-lg rounded-4 shadow-sm py-3 fw-bold d-flex align-items-center justify-content-between px-4 text-dark border">
            <span>سجل التحضير</span>
            <div class="bg-light border rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                <i class="fas fa-history text-secondary"></i>
            </div>
        </a>
    </div>
</div>
