<div class="container py-4">
   <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-start d-none d-sm-block">تحضير االطلاب</h3>
            <h3 class="fw-bold text-end d-none d-sm-block"> عام {{ date('Y') }} </h3>

        </div>
    {{-- التبويبات --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link btn @if ($activeTab === 'morning') active @endif" wire:click="setTab('morning')">
                <i class="fas fa-sun"></i> تحضير صباحي
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn @if ($activeTab === 'leave') active @endif" wire:click="setTab('leave')">
                <i class="fas fa-moon"></i> تحضير انصراف
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn @if ($activeTab === 'report') active @endif" wire:click="setTab('report')">
                <i class="fas fa-chart-bar"></i> تقرير الطلاب
            </a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- 🟢 تحضير صباحي --}}
        @if ($activeTab === 'morning')
            <div class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-sun"></i> تحضير صباحي</h5>
                    <div class="col-md-4">
                        <select wire:model.live="selectedDriver" class="form-control">
                            <option value="">-- اختر السائق --</option>
                            @foreach ($drivers as $drv)
                                <option value="{{ $drv->id }}">{{ $drv->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if ($driverStudents && count($driverStudents))
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-success">
                                <tr>
                                    <th>الطالب</th>
                                    <th>المنطقة</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($driverStudents as $prep)
                                    <tr>
                                        <td>{{ $prep->student?->Name }}</td>
                                        <td>{{ $prep->region?->Name }}</td>
                                        <td>
                                            <div
                                                class="form-switch {{ $prep->Atend ? 'switch-active' : 'switch-banned' }}">
                                                <input type="checkbox" id="atendSwitch{{ $prep->id }}"
                                                    wire:click="toggleAtend({{ $prep->id }})"
                                                    @if ($prep->Atend) checked @endif>
                                                <label for="atendSwitch{{ $prep->id }}"></label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-3 text-center text-muted">اختر سائق لعرض طلابه</div>
                    @endif
                </div>
            </div>
        @endif

        {{-- 🔵 تحضير انصراف --}}
        @if ($activeTab === 'leave')
            <div class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-moon"></i> تحضير انصراف</h5>
                    <div class="col-md-4">
                        <select wire:model.live="selectedDriver" class="form-control">
                            <option value="">-- اختر السائق --</option>
                            @foreach ($drivers as $drv)
                                <option value="{{ $drv->id }}">{{ $drv->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if ($driverStudents && count($driverStudents))
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>الطالب</th>
                                    <th>المنطقة</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($driverStudents as $prep)
                                    <tr>
                                        <td>{{ $prep->student?->Name }}</td>
                                        <td>{{ $prep->region?->Name }}</td>
                                        <td>
                                            <div
                                                class="form-switch {{ $prep->Atend ? 'switch-active' : 'switch-banned' }}">
                                                <input type="checkbox" id="atendSwitch{{ $prep->id }}"
                                                    wire:click="toggleAtend({{ $prep->id }})"
                                                    @if ($prep->Atend) checked @endif>
                                                <label for="atendSwitch{{ $prep->id }}"></label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-3 text-center text-muted">اختر سائق لعرض طلابه</div>
                    @endif
                </div>
            </div>
        @endif

        @if ($activeTab === 'report')
            <div class="card shadow border-0 rounded-3 mb-4">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-chart-bar"></i> تقرير الطلاب (صباحي/انصراف)</h5>

                    {{-- اختيار السائق --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <select wire:model.live="selectedDriver" class="form-control form-control-sm">
                            <option value="">-- اختر السائق --</option>
                            @foreach ($drivers as $drv)
                                <option value="{{ $drv->id }}">{{ $drv->Name }}</option>
                            @endforeach
                        </select>

                        @if ($selectedDriver)
                            <button wire:click="exportDriverReport({{ $selectedDriver }})"
                                class="btn btn-sm btn-light ms-2">
                                <i class="fas fa-file-excel text-success"></i> تصدير Excel
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if ($selectedDriver)
                        @php
                            $today = \Carbon\Carbon::today()->toDateString();
                            $driver = $drivers->where('id', $selectedDriver)->first();
                            $morningPresent = $morningAbsent = $leavePresent = $leaveAbsent = 0;
                        @endphp

                        <h6 class="fw-bold mb-3 text-primary">السائق: {{ $driver->Name }}</h6>

                        <table class="table table-bordered text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>الطالب</th>
                                    <th>صباحي</th>
                                    <th>انصراف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($driver->students as $stu)
                                    @php
                                        $morning = \App\Models\PreparationStu::where('student_id', $stu->id)
                                            ->where('Date', $today)
                                            ->where('type', 'morning')
                                            ->first();
                                        $leave = \App\Models\PreparationStu::where('student_id', $stu->id)
                                            ->where('Date', $today)
                                            ->where('type', 'leave')
                                            ->first();

                                        if ($morning) {
                                            $morning->Atend ? $morningPresent++ : $morningAbsent++;
                                        }
                                        if ($leave) {
                                            $leave->Atend ? $leavePresent++ : $leaveAbsent++;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $stu->Name }}</td>
                                        <td>
                                            @if ($morning)
                                                <span class="{{ $morning->Atend ? 'text-success' : 'text-danger' }}">
                                                    {{ $morning->Atend ? 'حاضر' : 'غائب' }}
                                                </span>
                                            @else
                                                <span class="text-muted">لم يسجل</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($leave)
                                                <span class="{{ $leave->Atend ? 'text-success' : 'text-danger' }}">
                                                    {{ $leave->Atend ? 'حاضر' : 'غائب' }}
                                                </span>
                                            @else
                                                <span class="text-muted">لم يسجل</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- صف الإحصائيات --}}
                                <tr class="table-info fw-bold">
                                    <td>الإحصائيات</td>
                                    <td>
                                        حضور: <span class="text-success">{{ $morningPresent }}</span> /
                                        غياب: <span class="text-danger">{{ $morningAbsent }}</span>
                                    </td>
                                    <td>
                                        حضور: <span class="text-success">{{ $leavePresent }}</span> /
                                        غياب: <span class="text-danger">{{ $leaveAbsent }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">الرجاء اختيار سائق لعرض التقرير.</p>
                    @endif
                </div>
            </div>
        @endif




    </div>
</div>
