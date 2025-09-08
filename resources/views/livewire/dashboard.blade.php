<div class="container-fluid mt-4">

    {{-- العنوان --}}
    <div class="mb-4 text-center">
        <h2 class="fw-bold text-primary">
            <i class="fas fa-tachometer-alt"></i> لوحة التحكم - نظام الإبداع للمواصلات
        </h2>
        <p class="text-muted">نظرة عامة على الإحصائيات الرئيسية</p>
    </div>

    {{-- البطاقات الرئيسية --}}
    <div class="row g-4 mb-4">
        {{-- كوستر --}}
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 hover-card text-center">
                <div class="card-body">
                    <img src="{{ asset('image/costur.jpg') }}" 
                         class="rounded-circle mb-3 img-fluid mx-auto"
                         style="width:200px; height:120px; object-fit:cover;" 
                         alt="كوستر">
                    <h5 class="card-title">باصات كوستر</h5>
                    <h3 class="fw-bold text-primary">{{ $stats['coaster'] }}</h3>
                    <p class="text-muted small">إجمالي الباصات</p>
                </div>
            </div>
        </div>

        {{-- هايس --}}
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 hover-card text-center">
                <div class="card-body">
                    <img src="{{ asset('image/hise.jpg') }}" 
                         class="rounded-circle mb-3 img-fluid mx-auto"
                         style="width:200px; height:120px; object-fit:cover;" 
                         alt="هايس">
                    <h5 class="card-title">باصات هايس</h5>
                    <h3 class="fw-bold text-success">{{ $stats['hiace'] }}</h3>
                    <p class="text-muted small">إجمالي الباصات</p>
                </div>
            </div>
        </div>

        {{-- السائقين --}}
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 hover-card text-center">
                <div class="card-body">
                    <img src="{{ asset('image/school.jpg') }}" 
                         class="rounded-circle mb-3 img-fluid mx-auto"
                         style="width:200px; height:120px; object-fit:cover;" 
                         alt="سائقين">
                    <h5 class="card-title">السائقين</h5>
                    <h3 class="fw-bold text-warning">{{ $stats['drivers'] }}</h3>
                    <p class="text-muted small">عدد السائقين المسجلين</p>
                </div>
            </div>
        </div>

        {{-- الطلاب --}}
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 hover-card text-center">
                <div class="card-body">
                    <img src="{{ asset('image/hise.jpg') }}" 
                         class="rounded-circle mb-3 img-fluid mx-auto"
                         style="width:200px; height:120px; object-fit:cover;" 
                         alt="طلاب">
                    <h5 class="card-title">الطلاب</h5>
                    <h3 class="fw-bold text-danger">{{ $stats['students'] }}</h3>
                    <p class="text-muted small">عدد الطلاب المسجلين</p>
                </div>
            </div>
        </div>
    </div>

    {{-- البطاقات الثانوية --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 text-center bg-light hover-card">
                <h5 class="card-title mb-2">
                    <i class="fas fa-bus text-success"></i> الباصات النشطة حالياً
                </h5>
                <h2 class="fw-bold text-success">{{ $stats['active_buses'] }}</h2>
                <p class="text-muted">الباصات التي تعمل الآن</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 text-center bg-light hover-card">
                <h5 class="card-title mb-2">
                    <i class="fas fa-map-marker-alt text-secondary"></i> المواقع
                </h5>
                <h2 class="fw-bold text-secondary">{{ $stats['locations'] }}</h2>
                <p class="text-muted">إجمالي المواقع المسجلة</p>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-bar me-2"></i> إحصائيات النظام
                </div>
                <div class="card-body" style="height: 400px;">
                    <canvas id="statsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول سريع (آخر 5 سائقين) --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-users me-2"></i> آخر السائقين المسجلين
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped text-center mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                                <th>الباص</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\Driver::latest()->take(5)->get() as $driver)
                                <tr>
                                    <td>{{ $driver->id }}</td>
                                    <td>{{ $driver->name }}</td>
                                    <td>{{ $driver->phone }}</td>
                                    <td>{{ $driver->bus->BusNo ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@push('styles')
<style>
    .hover-card {
        transition: 0.3s;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .card-title {
        font-weight: bold;
    }
</style>
@endpush

</div>
