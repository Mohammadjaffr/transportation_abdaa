@extends('adminlte::page')

@section('title', 'الابداع | الطلاب الموزعون')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@stop

@section('content')
<div class="container py-4">

    {{-- العنوان العلوي --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-start d-none d-sm-block">
            <i class="fas fa-users text-success me-2"></i> الطلاب الموزعون
        </h3>
        <h3 class="fw-bold text-end d-none d-sm-block">عام {{ date('Y') }}</h3>
    </div>

    {{-- الأزرار العلوية --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ url('distribution-stu') }}" class="btn btn-outline-secondary rounded-pill shadow-sm">
            <i class="fas fa-arrow-right me-1"></i> الرجوع لإدارة الطلاب
        </a>

        <a href="{{ route('distributed.export') }}" class="btn btn-success rounded-pill shadow-sm">
            <i class="fas fa-file-excel me-1"></i> تصدير Excel
        </a>
    </div>

    {{-- مربع البحث --}}
    <form method="GET" class="mb-4">
        <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0">
                <i class="fas fa-search text-primary"></i>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" 
                class="form-control border-0 py-2" placeholder="ابحث باسم الطالب أو السائق...">
            <button class="btn btn-success px-4">بحث</button>
        </div>
    </form>

    {{-- جدول عرض الطلاب --}}
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-gradient text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> قائمة الطلاب الموزعين</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-header sticky-top">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الصف</th>
                            <th>النوع</th>
                            <th>الهاتف</th>
                            <th>المنطقة</th>
                            <th>الموقف</th>
                            <th>السائق</th>
                            <th>المعلم/ة</th>
                            <th>سنة الدراسة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->id }}</td>
                                <td class="fw-bold">{{ $student->Name }}</td>
                                <td>{{ $student->Grade }}</td>
                                <td>{{ $student->Sex }}</td>
                                <td>{{ $student->Phone }}</td>
                                <td><span class="badge bg-info text-dark">{{ $student->region?->Name ?? 'غير محدد' }}</span></td>
                                <td><span class="badge bg-secondary">{{ $student->Stu_position ?? 'غير محدد' }}</span></td>
                                <td><span class="badge bg-primary">{{ $student->driver?->Name ?? 'غير محدد' }}</span></td>
                                <td>{{ $student->teacher?->Name ?? 'غير موجود' }}</td>
                                <td>{{ $student->schoolYear->year ?? 'غير محددة' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="fas fa-user-slash fa-3x mb-3 text-secondary"></i>
                                    <p class="mb-0 fs-5">لا يوجد طلاب موزعون بعد</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-center">
                {{ $students->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- تنسيقات إضافية --}}
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }

        .bg-gradient {
            background: linear-gradient(90deg, #198754, #28a745);
        }

        .table-header {
            background: linear-gradient(90deg, #d4edda, #c3e6cb);
        }

        table tbody tr:hover {
            background-color: #f1fdf6 !important;
            transition: 0.2s;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }

        .btn-success, .btn-outline-secondary {
            font-weight: 500;
        }
    </style>

</div>
@stop
