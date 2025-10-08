<div class="container-fluid py-4">

    {{-- العنوان --}}
    <div class="mb-5 text-center">
        <h2 class="fw-bold text-gradient">
            <i class="fas fa-chart-line"></i> لوحة التحكم - نظام الإبداع
        </h2>
        <p class="text-muted">إحصائيات مباشرة ورؤية عامة للنظام</p>
    </div>

    {{-- KPIs --}}
    <div class="row g-4 mb-4">
           <div class="col-lg-3 col-md-6">
            <div class="card kpi-card kpi-gee text-center animate-fade">
                <div class="icon-box"><i class="fas fa-user"></i></div>
                <h6>اجمالي المستخدمين</h6>

                <h2>{{ $stats['users'] }}</h2>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card kpi-blue text-center animate-fade">
                <div class="icon-box"><i class="fas fa-user-tie"></i></div>
                <h6>إجمالي السائقين</h6>
                <h2>{{ $stats['drivers'] }}</h2>
                <small class="badge">موزعين على {{ $stats['regions'] }} مناطق</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card kpi-green  text-center animate-fade">
                <div class="icon-box"><i class="fas fa-user-tie"></i></div>
                <h6>إجمالي الطلاب المنسحبين</h6>
                <h2>{{ $retreats }}</h2>
                <small class="badge">من اجمالي  {{ $stats['students'] }} طالب</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card kpi-red text-center animate-fade">
                <div class="icon-box"><i class="fas fa-user-graduate"></i></div>
                <h6>الطلاب</h6>
                <h2>{{ $stats['students'] }}</h2>
                <small class="badge">موزعين على {{ $stats['regions'] }} مناطق</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card kpi-bulre text-center animate-fade">
                <div class="icon-box"><i class="fas fa-bus"></i></div>
                <h6>إجمالي الباصات</h6>
                <h2>{{ $stats['buses'] }}</h2>
                <div class="bus-types mt-2">
                    <span class="badge bg-info px-3  me-1">
                        عدد الباصات كوستر: {{ $stats['coaster'] ?? 0 }}
                    </span>
                    <span class="badge bg-info text-dark px-3 ">
                        عدد الباصات هايس : {{ $stats['hiace'] ?? 0 }}
                    </span>
                </div>
            </div>
        </div>


        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card kpi-purple text-center animate-fade">
                <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
                <h6>المناطق</h6>

                <h2>{{ $stats['regions'] }}</h2>
                <small class="badge px-3 py-1">عدد الأجنحة : {{ $wings }} اجنحة</small>
            </div>
        </div>
     
    </div>

    {{-- المخططات --}}


    {{-- آخر البيانات --}}
    <div class="row">
        {{-- آخر السائقين --}}
        <div class="col-md-6 mb-4">
            <div class="card glass-table animate-up">
                <div class="card-header bg-success text-black">
                    <i class="fas fa-users"></i> آخر السائقين
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0 text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الهاتف</th>
                                <th>نوع الباص</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestDrivers as $driver)
                                <tr>
                                    <td>{{ $driver->id }}</td>
                                    <td>{{ $driver->Name }}</td>
                                    <td>{{ $driver->Phone }}</td>
                                    <td>{{ $driver->Bus_type ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- أحدث الطلاب --}}
        <div class="col-md-6 mb-4">
            <div class="card glass-table animate-up">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-user-graduate"></i> أحدث الطلاب
                </div>
                <div class="card-body p-0">
                    <table class="table  mb-0 text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الصف</th>
                                <th>الجناح</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestStudents as $student)
                                <tr>
                                    <td>{{ $student->id }}</td>
                                    <td>{{ $student->Name }}</td>
                                    <td>{{ $student->Grade }}</td>
                                    <td>{{ $student->wing->Name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card modern-chart animate-up">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-white">
                        <i class="fas fa-chart-pie me-2"></i> توزيع الطلاب حسب المناطق
                    </h6>

                </div>
                <div class="card-body">
                    <canvas id="studentsByRegion"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card modern-chart animate-up">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-white">
                        <i class="fas fa-bus-alt me-2"></i> توزيع الباصات حسب النوع
                    </h6>

                </div>
                <div class="card-body">
                    <canvas id="busesByType"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // توزيع الطلاب حسب المناطق
        new Chart(document.getElementById('studentsByRegion'), {
            type: 'doughnut',
            data: {
                labels: @json($studentsByRegion->pluck('Name')),
                datasets: [{
                    label: 'عدد الطلاب',
                    data: @json($studentsByRegion->pluck('students_count')),
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14']
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1200,
                    easing: 'easeOutBounce'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        // توزيع الباصات حسب النوع
        new Chart(document.getElementById('busesByType'), {
            type: 'bar',
            data: {
                labels: @json($busesByType->pluck('Bus_type')),
                datasets: [{
                    label: 'عدد الباصات',
                    data: @json($busesByType->pluck('total')),
                    backgroundColor: ['#00c6ff', '#6610f2', '#28a745', '#ffc107']
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#555'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#555'
                        }
                    }
                }
            }
        });
    </script>

    {{-- CSS --}}
    <style>
        .text-gradient {
            background: linear-gradient(90deg, #007bff, #00c6ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .bus-types .badge {
            font-size: 0.8rem;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }


        /* KPI Cards */
        .kpi-card {
            border-radius: 20px;
            color: #fff;
            padding: 25px;
            backdrop-filter: blur(10px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            transition: transform .3s, box-shadow .3s;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .icon-box {
            font-size: 2.5rem;
            margin-bottom: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: inline-block;
            padding: 12px;
            border-radius: 50%;
        }

        .kpi-blue {
            background: linear-gradient(135deg, #007bff, #00c6ff);
        }
        .kpi-bulre {
            background: linear-gradient(135deg, #479733, #dce738);
        }

        .kpi-red {
            background: linear-gradient(135deg, #dc3545, #ff6f61);
        }

        .kpi-green {
            background: linear-gradient(135deg, #28a745, #00d27a);
        }

        .kpi-purple {
            background: linear-gradient(135deg, #6f42c1, #b76df0);
        }
        .kpi-gee {
            background: linear-gradient(135deg, #0a4ecc, #0a3b7c);
        }

        /* Modern Chart Cards */
        .modern-chart {
            border-radius: 20px;
            background: linear-gradient(135deg, #007bff, #00c6ff);
            overflow: hidden;
            transition: all .3s ease-in-out;
        }

        .modern-chart:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .modern-chart .card-header {
            background: transparent;
            border: none;
            padding: 1rem 1.5rem;
        }

        .modern-chart .card-body {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0 0 20px 20px;
            padding: 20px;
        }

        /* Tables */
        .glass-table {
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(8px);
        }

        .stylish-table thead {
            background: linear-gradient(90deg, #21a567, #06ca21);
            color: #fff;
        }

        .stylish-table tbody tr {
            transition: background .3s;
        }

        .stylish-table tbody tr:hover {
            background: rgba(0, 123, 255, 0.1);
        }

        /* Animations */
        .animate-fade {
            animation: fadeIn 1s ease-in-out;
        }

        .animate-up {
            animation: slideUp 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1e1e2f;
                color: #eaeaea;
            }

            .card {
                background: rgba(40, 40, 60, 0.85);
                color: #fff;
            }

            .stylish-table thead {
                background: #2e2e40;
            }
        }
    </style>

</div>

