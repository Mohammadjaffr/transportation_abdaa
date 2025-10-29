<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center">

        <h3 class="fw-bold text-dark mb-4">
            <i class="fas fa-users me-2 text-success"></i> توزيع الطلاب
        </h3>
        <h3 class="fw-bold text-center d-none d-sm-block"> عام {{ date('Y') }} </h3>


    </div>


    <div class="col-12 col-md-12 mb-2  mb-md-0">
        <div class="input-group shadow-sm rounded-pill overflow-hidden border-0 ">
            <span class="input-group-text bg-white border-0 rounded-0">
                <i class="fas fa-search text-primary"></i>
            </span>
            <input type="text" class="form-control border-0  py-2 " placeholder="ابحث باسم الطالب ..."
                wire:model.debounce.300ms.live="search">
        </div>
    </div>
    <div class="row m-3">
        <div class="col-md-4">
            <select wire:model.live="regionFilter" class="custom-select">
                <option value=""> كل المناطق</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->Name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select wire:model.live="driverFilter" class="custom-select">
                <option value=""> كل السائقين</option>
                @foreach ($drivers as $driver)
                    <option value="{{ $driver->id }}">{{ $driver->Name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select wire:model.live="positionFilter" class="custom-select">
                <option value=""> كل المواقف</option>
                @foreach ($stu_postion as $position)
                    <option value="{{ $position }}">{{ $position }}</option>
                @endforeach
            </select>
        </div>

    </div>


    <div class="card custom-card shadow-lg border-0 rounded-4">
        <div class="card-header bg-gradient text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> قائمة الطلاب</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="sticky-top table-header">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الموقف</th>
                            <th>المنطقة</th>
                            <th>السائق</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                </td>
                                <td>{{ $student->id }}</td>
                                <td class="fw-bold">{{ $student->Name }}</td>
                                <td><span class="badge bg-secondary">{{ $student->Stu_position ?? 'غير محدد' }}</span>
                                </td>
                                <td><span
                                        class="badge bg-info text-dark">{{ $student->region?->Name ?? 'غير محدد' }}</span>
                                </td>
                                <td><span class="badge bg-primary">{{ $student->driver?->Name ?? 'غير محدد' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-around">
                                        <div class="m-1">
                                            السائق
                                        </div>
                                        <div class="m-1">
                                            المنطقة
                                        </div>
                                        <div class="m-1">
                                            الموقف
                                        </div>

                                    </div>


                                    <div class="d-flex gap-2">
                                        @php
                                            $driversForStudent = $drivers->where('region_id', $student->region_id);
                                        @endphp

                                        {{-- اختيار السائق --}}
                                        <select class="custom-select" @if (isset($regionDrivers[$student->id]) && $regionDrivers[$student->id]->isEmpty()) disabled @endif
                                            wire:change="updateDistribution({{ $student->id }}, $event.target.value, {{ $student->region_id ?? 'null' }}, '{{ $student->Stu_position ?? '' }}')">

                                            <option value="">اختر سائق</option>

                                            {{-- 🔴 لا يوجد سائق في المنطقة --}}
                                            @if (isset($regionDrivers[$student->id]) && $regionDrivers[$student->id]->isEmpty())
                                                <option disabled selected>لا يوجد سائق في هذه المنطقة</option>

                                                {{-- 🟡 أكثر من سائق --}}
                                            @elseif (isset($regionDrivers[$student->id]))
                                                @foreach ($regionDrivers[$student->id] as $driver)
                                                    <option value="{{ $driver->id }}" @selected($student->driver_id == $driver->id)>
                                                        {{ $driver->Name }}
                                                    </option>
                                                @endforeach

                                                {{-- 🟢 الحالة العادية --}}
                                            @else
                                                @php
                                                    // جلب السائقين المرتبطين بنفس المنطقة
                                                    $driversForStudent = \App\Models\Driver::whereHas(
                                                        'regions',
                                                        function ($q) use ($student) {
                                                            $q->where('regions.id', $student->region_id);
                                                        },
                                                    )->get();
                                                @endphp
                                                @forelse ($driversForStudent as $driver)
                                                    <option value="{{ $driver->id }}" @selected($student->driver_id == $driver->id)>
                                                        {{ $driver->Name }}
                                                    </option>
                                                @empty
                                                    <option disabled>لا يوجد سائق في هذه المنطقة</option>
                                                @endforelse
                                            @endif
                                        </select>
                                        {{-- اختيار المنطقة --}}
                                        <select class="custom-select"
                                            wire:change="updateDistribution({{ $student->id }}, {{ $student->driver_id ?? 'null' }}, $event.target.value, '{{ $student->Stu_position ?? '' }}')">
                                            <option value="">اختر منطقة</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->id }}" @selected($student->region_id == $region->id)>
                                                    {{ $region->Name }}
                                                </option>
                                            @endforeach
                                        </select>


                                        <select class="custom-select"
                                            wire:change="updateDistribution({{ $student->id }}, {{ $student->driver_id ?? 'null' }}, {{ $student->region_id ?? 'null' }}, $event.target.value)">
                                            <option value="">اختر الموقف</option>
                                            @foreach ($stu_postion as $position)
                                                <option value="{{ $position }}" @selected($student->Stu_position == $position)>
                                                    {{ $position }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-user-slash fa-3x mb-3 text-secondary"></i>
                                    <p class="mb-0 fs-5">لا يوجد طلاب</p>
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
    {{-- CSS مخصص --}}
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }

        .text-gradient {
            background: linear-gradient(90deg, #0d6efd, #198754);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-gradient {
            background: linear-gradient(90deg, #198754, #20c997);
            color: #fff;
            border: none;
        }

        .btn-gradient:hover {
            opacity: 0.9;
        }

        .custom-card {
            border-radius: 15px;
            overflow: hidden;
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

        /* الحقول الكبيرة */
        .custom-select {
            border-radius: 12px;
            padding: 10px 10px 0px 15px;
            border: 1px solid #ced4da;
            background: #fff url("data:image/svg+xml;charset=UTF8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='gray' class='bi bi-chevron-down' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E") no-repeat right 12px center/16px 16px;
            transition: 0.3s;
            font-size: 15px;
            appearance: none;
            margin: 0px 5px;
        }

        /* الحقول الصغيرة داخل الجدول */
        .custom-select-sm {
            border-radius: 8px;
            padding: 6px 30px 6px 15px;
            border: 1px solid #dee2e6;
            background: #f8f9fa url("data:image/svg+xml;charset=UTF8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='gray' class='bi bi-caret-down-fill' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592c.86 0 1.319 1.013.753 1.658l-4.796 5.482a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") no-repeat right 10px center/14px 14px;
            font-size: 14px;
            transition: 0.2s;
            appearance: none;
        }

        /* تأثير عند التفاعل */
        .custom-select:focus,
        .custom-select-sm:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, .25);
            outline: none;
            background-color: #ffffff;
        }
    </style>
</div>
