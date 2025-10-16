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
        <li class="nav-item">
            <a class="nav-link btn @if ($activeTab === 'custom') active @endif" wire:click="setTab('custom')">
                <i class="fas fa-filter"></i> تقرير مخصص
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn @if ($activeTab === 'missing') active @endif" wire:click="setTab('missing')">
                <i class="fas fa-user-clock"></i> تحضير مفقود للسائقين
            </a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- 🟢 تحضير صباحي --}}
        @if ($activeTab === 'morning')
            <div class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-sun"></i> تحضير صباحي  (تاريخ {{ date('Y-m-d') }})</h5>
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
                                    <th>السائق</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($driverStudents as $prep)
                                    <tr>
                                        <td>{{ $prep->student?->Name }}</td>
                                        <td>{{ $prep->region?->Name }}</td>
                                        <td>{{ $prep->driver?->Name }}</td>
                                        <td>{{ $prep->created_at->format('y-m-d') }}</td>
                                        <td>
                                            <div
                                                class="form-switch {{ $prep->Atend ? 'switch-active' : 'switch-banned' }}">
                                                <input type="checkbox" id="atendSwitchMorning{{ $prep->id }}"
                                                    wire:click="toggleAtend({{ $prep->id }})"
                                                    @if ($prep->Atend) checked @endif>
                                                <label for="atendSwitchMorning{{ $prep->id }}"></label>
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
                    <h5 class="mb-0"><i class="fas fa-moon"></i> تحضير انصراف (تاريخ {{ date('Y-m-d') }})</h5>
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
                                    <th>السائق</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($driverStudents as $prep)
                                    <tr>
                                        <td>{{ $prep->student?->Name }}</td>
                                        <td>{{ $prep->region?->Name }}</td>
                                        <td>{{ $prep->driver?->Name }}</td>
                                        <td>{{ $prep->created_at->format('y-m-d') }}</td>
                                        <td>
                                            <div
                                                class="form-switch {{ $prep->Atend ? 'switch-active' : 'switch-banned' }}">
                                                <input type="checkbox" id="atendSwitchLeave{{ $prep->id }}"
                                                    wire:click="toggleAtend({{ $prep->id }})"
                                                    @if ($prep->Atend) checked @endif>
                                                <label for="atendSwitchLeave{{ $prep->id }}"></label>
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

        {{-- 🟣 تقرير مخصص --}}
        @if ($activeTab === 'custom')
            <div class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> تقرير مخصص (إجمالي الفترة)</h5>
                </div>

                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" wire:model.live="from_date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" wire:model.live="to_date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">السائق</label>
                            <select class="form-control" wire:model.live="selectedDriver">
                                <option value="">كل السائقين</option>
                                @foreach ($drivers as $drv)
                                    <option value="{{ $drv->id }}">{{ $drv->Name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">عرض التقرير</label>
                            <select class="form-control" wire:model.live="showNames">
                                <option value="0">إحصائي فقط</option>
                                <option value="1">مع الأسماء (غياب فقط)</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn btn-dark" wire:click="generateCustomReport">
                                <i class="fas fa-eye"></i> عرض
                            </button>
                        </div>
                    </div>

                    @if (!empty($customReport))
                        <hr>

                        {{-- سائق واحد --}}
                        @if ($selectedDriver)
                            @php $r = $customReport[0] ?? null; @endphp
                            @if ($r)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div
                                        class="d-flex align-items-center justify-content-between p-2 bg-light rounded border mb-2">
                                        <div>
                                            <span class="fw-bold text-primary mx-1">
                                                <i class="fas fa-user-tie"></i> السائق:
                                            </span>
                                            <span class="text-dark mr-3">{{ $r['driver_name'] }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-success mx-1">
                                                <i class="fas fa-calendar-alt"></i> الفترة:
                                            </span>
                                            <span class="text-dark">من {{ $r['from'] }}</span>
                                            <i class="fas fa-arrow-left text-muted mx-2"></i>
                                            <span class="text-dark">الى {{ $r['to'] }}</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-success" wire:click="exportCustomReport">
                                        <i class="fas fa-file-excel"></i> تصدير Excel
                                    </button>
                                </div>

                                <table class="table table-bordered text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>السائق</th>
                                            <th>إجمالي الحضور</th>
                                            <th>غياب جزئي</th>
                                            <th>غياب كلي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold">{{ $r['driver_name'] }}</td>
                                            <td class="text-success fw-bold">{{ $r['present'] }}</td>
                                            <td class="text-danger fw-bold">{{ $r['absent_part'] }}</td>
                                            <td class="text-danger fw-bold">{{ $r['total_absent'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                @if ($showNames && count($r['absentees']))
                                    <table class="table table-striped">
                                        <thead class="table-danger">
                                            <tr>
                                                <th>الطالب الغائب</th>
                                                <th>نوع الغياب</th>
                                                <th>التاريخ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($r['absentees'] as $row)
                                                <tr>
                                                    <td>{{ $row['student'] }}</td>
                                                    <td>{{ $row['type'] }}</td>
                                                    <td>{{ $row['date'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            @endif

                            {{-- كل السائقين --}}
                        @else
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div
                                    class="d-flex align-items-center justify-content-between p-2 bg-light rounded border mb-2">
                                    <div>
                                        <span class="fw-bold text-primary mr-3">
                                            <i class="fas fa-users"></i> كل السائقين
                                        </span>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-success mx-1">
                                            <i class="fas fa-calendar-alt"></i> الفترة:
                                        </span>
                                        <span class="text-dark">من {{ $customReport[0]['from'] }}</span>
                                        <i class="fas fa-arrow-left text-muted mx-2"></i>
                                        <span class="text-dark">الى {{ $customReport[0]['to'] }}</span>
                                    </div>
                                </div>

                                <button class="btn btn-sm btn-success" wire:click="exportCustomReport">
                                    <i class="fas fa-file-archive"></i> تصدير ZIP
                                </button>
                            </div>

                            <table class="table table-bordered text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>السائق</th>
                                        <th>إجمالي الحضور</th>
                                        <th>غياب جزئي</th>
                                        <th>غياب كلي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customReport as $r)
                                        <tr>
                                            <td class="fw-bold">{{ $r['driver_name'] }}</td>
                                            <td class="text-success fw-bold">{{ $r['present'] }}</td>
                                            <td class="text-danger fw-bold">{{ $r['absent_part'] }}</td>
                                            <td class="text-danger fw-bold">{{ $r['total_absent'] }}</td>
                                        </tr>

                                        @if ($showNames && count($r['absentees']))
                                            <tr>
                                                <td colspan="4">
                                                    <table class="table mb-0">
                                                        <thead class="table-danger">
                                                            <tr>
                                                                <th>الطالب الغائب</th>
                                                                <th>نوع الغياب</th>
                                                                <th>التاريخ</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($r['absentees'] as $row)
                                                                <tr>
                                                                    <td>{{ $row['student'] }}</td>
                                                                    <td>{{ $row['type'] }}</td>
                                                                    <td>{{ $row['date'] }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    @endif
                </div>
            </div>
        @endif

        {{-- 🟤 تحضير مفقود للسائقين (النسخة النهائية) --}}
        @if ($activeTab === 'missing')
            <div class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-clock"></i> تحضير مفقود للسائقين (صباح + انصراف)
                    </h5>
                    <small class="opacity-75">يساعدك على تصحيح في حال نسي أحد السائقين التحضير</small>
                </div>

                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">التاريخ</label>
                            <input type="date" class="form-control" wire:model.live="missingDate">
                        </div>
                        <div class="col-md-4 d-grid">
                            <button class="btn btn-dark" wire:click="findMissingDrivers">
                                <i class="fas fa-search"></i> بحث
                            </button>
                        </div>

                        @if (!empty($missingDrivers))
                            <div class="col-md-4 ms-auto d-grid">
                                <button class="btn btn-success" wire:click="autoPrepareAllMissing">
                                    <i class="fas fa-bolt"></i> تحضير تلقائي لكل السائقين
                                </button>
                            </div>
                        @endif
                    </div>

                    <hr>

                    @if (is_array($missingDrivers) && count($missingDrivers))
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>السائق</th>
                                        <th class="text-center">عدد طلابه</th>
                                        <th class="text-center">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($missingDrivers as $drv)
                                        <tr>
                                            <td class="fw-bold">{{ $drv['name'] }}</td>
                                            <td class="text-center">{{ $drv['students_count'] }}</td>
                                            <td class="text-center">
                                                {{-- فتح التحضير اليدوي داخل نفس التبويب --}}
                                                <button class="btn btn-sm btn-warning"
                                                    wire:click="startManualPrepare({{ $drv['id'] }})">
                                                    <i class="fas fa-user-edit"></i> تحضير يدوي
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- لوحة التحضير اليدوي تظهر أسفل الجدول --}}
                        @if ($selectedMissingDriverId)
                            @php
                                $driverName =
                                    collect($missingDrivers)->firstWhere('id', $selectedMissingDriverId)['name'] ?? '';
                            @endphp

                            <div class="card mt-4 border-0 shadow-sm">
                                <div
                                    class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <i class="fas fa-user-tie me-2"></i>
                                            تحضير يدوي — <span class="fw-bold ms-1">{{ $driverName }}</span>
                                        </h6>
                                        <small class="d-block">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            التاريخ: {{ \Carbon\Carbon::parse($missingDate)->format('Y-m-d') }}
                                        </small>
                                    </div>

                                    <button class="btn btn-sm btn-light text-dark ml-auto"
                                        wire:click="$set('selectedMissingDriverId', null)">
                                        <i class="fas fa-times"></i> إغلاق
                                    </button>
                                </div>


                                <div class="card-body p-0">
                                    @if (count($manualStudents))
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover mb-0 align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>الطالب</th>
                                                        <th class="text-center">صباح</th>
                                                        <th class="text-center">انصراف</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($manualStudents as $row)
                                                        @php
                                                            $mId = 'm_' . $row['student_id'];
                                                            $lId = 'l_' . $row['student_id'];
                                                        @endphp
                                                        <tr>
                                                            <td class="fw-semibold">{{ $row['student_name'] }}</td>

                                                            {{-- Toggle صباح بنفس ستايل التبويبات الأخرى (بدون كلمات) --}}
                                                            <td class="text-center">
                                                                <div
                                                                    class="form-switch d-inline-block {{ $row['morning'] ? 'switch-active' : 'switch-banned' }}">
                                                                    <input id="{{ $mId }}" type="checkbox"
                                                                        @checked($row['morning'])
                                                                        wire:change="setAttendance({{ $row['student_id'] }}, 'morning', $event.target.checked)">
                                                                    <label for="{{ $mId }}"></label>
                                                                </div>
                                                            </td>

                                                            {{-- Toggle انصراف بنفس ستايل التبويبات الأخرى (بدون كلمات) --}}
                                                            <td class="text-center">
                                                                <div
                                                                    class="form-switch d-inline-block {{ $row['leave'] ? 'switch-active' : 'switch-banned' }}">
                                                                    <input id="{{ $lId }}" type="checkbox"
                                                                        @checked($row['leave'])
                                                                        wire:change="setAttendance({{ $row['student_id'] }}, 'leave', $event.target.checked)">
                                                                    <label for="{{ $lId }}"></label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="p-3 text-center text-muted">لا توجد بيانات طلاب لعرضها.</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mb-0">
                            لا توجد بيانات حالياً. اختر التاريخ ثم اضغط <strong>بحث</strong>.
                            <br>سيتم عرض السائقين الذين لم يسجلوا أي تحضير في التاريخ المحدد.
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- 🟡 تقرير الطلاب (اليومي) --}}
        @if ($activeTab === 'report')
            <div class="card shadow border-0 rounded-3 mb-4">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-chart-bar"></i> تقرير الطلاب  (تاريخ {{ date('Y-m-d') }}) (صباحي/انصراف)</h5>

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
                                    <th>التاريخ</th>
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
                                                   
                                                        <span class="badge badge-green {{ $morning->Atend ? 'bg-success' : 'bg-danger' }}">{{  $morning->Atend ? 'حاضر' : 'غائب' }}</span>

                                            @else
                                                <span class="text-muted">لم يسجل</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($leave)
                                               
                                 <span class="badge badge-green {{ $leave->Atend ? 'bg-success' : 'bg-danger' }}">{{  $leave->Atend ? 'حاضر' : 'غائب' }}</span>
                                            @else
                                                <span class="text-muted">لم يسجل</span>
                                            @endif
                                        </td>
                                        <td>{{ $today }}</td>
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
                                    <td>{{ $today }}</td>
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
