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
        {{-- باصات كوستر --}}
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 hover-card text-center p-3">
                <div class="card-body">
                    <img src="{{ asset('image/costur.jpg') }}" class="rounded-circle mb-3 img-fluid mx-auto"
                        style="width: 300px; height: 180px; object-fit: cover;" alt="كوستر">
                    <h5 class="card-title">باصات كوستر</h5>
                    <h3 class="fw-bold text-primary">{{ $stats['coaster'] }}</h3>
                    <p class="text-muted small">إجمالي الباصات من هذا النوع</p>
                </div>
            </div>
        </div>

        {{-- باصات هايس --}}
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 hover-card text-center p-3">
                <div class="card-body">
                    <img src="{{ asset('image/hise.jpg') }}" class="rounded-circle mb-3 img-fluid mx-auto"
                        style="width: 300px; height: 180px; object-fit: cover;" alt="هايس">
                    <h5 class="card-title">باصات هايس</h5>
                    <h3 class="fw-bold text-success">{{ $stats['hiace'] }}</h3>
                    <p class="text-muted small">إجمالي الباصات من هذا النوع</p>
                </div>
            </div>
        </div>

        {{-- السائقين --}}
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 hover-card text-center p-3">
                <div class="card-body">
                    <img src="{{ asset('image/school.jpg') }}" class="rounded-circle mb-3 img-fluid mx-auto"
                        style="width: 300px; height: 180px; object-fit: cover;" alt="سائقين">
                    <h5 class="card-title">السائقين</h5>
                    <h3 class="fw-bold text-warning">{{ $stats['drivers'] }}</h3>
                    <p class="text-muted small">عدد السائقين المسجلين</p>
                </div>
            </div>
        </div>

        {{-- الطلاب --}}
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 hover-card text-center p-3">
                <div class="card-body">
                    <img src="{{ asset('image/hise.jpg') }}" class="rounded-circle mb-3 img-fluid mx-auto"
                        style="width: 300px; height: 180px; object-fit: cover;" alt="طلاب">
                    <h5 class="card-title">الطلاب</h5>
                    <h3 class="fw-bold text-danger">{{ $stats['students'] }}</h3>
                    <p class="text-muted small">عدد الطلاب المسجلين</p>
                </div>
            </div>
        </div>
    </div>

    {{-- بطاقة الباصات النشطة --}}
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 p-4 text-center bg-light hover-card">
                <h5 class="card-title mb-2">
                    <i class="fas fa-bus text-success"></i> الباصات النشطة حالياً
                </h5>
                <h2 class="fw-bold text-success">{{ $stats['active_buses'] }}</h2>
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

</div>

{{-- CSS إضافي --}}
@push('styles')
    <style>
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: 0.3s;
        }

        @media (max-width: 576px) {
            .card-title {
                font-size: 1rem;
            }

            .fw-bold {
                font-size: 1.5rem;
            }

            img.rounded-circle {
                width: 100px;
                height: 100px;
            }
        }
    </style>
@endpush

{{-- Chart.js --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('statsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['كوستر', 'هايس', 'سائقين', 'طلاب', 'المواقع', 'باصات نشطة'],
                datasets: [{
                    label: 'إحصائيات النظام',
                    data: [
                        {{ $stats['coaster'] }},
                        {{ $stats['hiace'] }},
                        {{ $stats['drivers'] }},
                        {{ $stats['students'] }},
                        {{ $stats['locations'] }},
                        {{ $stats['active_buses'] }}
                    ],
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#dc3545',
                        '#6c757d',
                        '#20c997'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
