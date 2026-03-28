<div>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* تحسينات الخط والمساحات */
        .driver-container {
            font-family: 'Tajawal', sans-serif !important;
        }

        /* الهيدر المتجاوب */
        .premium-header {
            background: linear-gradient(135deg, #1f2422 0%, #29793d 100%) !important;
            border-radius: 15px;
            padding: 2rem;
            color: white;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* تعديلات الشاشات الصغيرة (الجوال) */
        @media (max-width: 768px) {
            .premium-header {
                padding: 1.5rem;
                text-align: center;
            }

            .premium-header .d-flex {
                flex-direction: column !important;
            }

            .premium-header .btn {
                margin-top: 15px;
                width: 100%;
            }

            .stat-card {
                margin-bottom: 10px;
                padding: 15px;
            }

            .stat-card i {
                font-size: 1.4rem;
            }

            .stat-value {
                font-size: 0.9rem !important;
            }

            .action-link-item {
                padding: 10px;
            }

            .card-body {
                padding: 1.25rem !important;
            }
        }

        /* كروت الحالة الملونة */
        .stat-card {
            border-radius: 12px;
            padding: 20px;
            color: white;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .stat-card i {
            font-size: 1.6rem;
            margin-bottom: 8px;
            display: block;
        }

        .stat-label {
            font-size: 0.75rem;
            opacity: 0.85;
            font-weight: 600;
            display: block;
        }

        .stat-value {
            font-size: 1rem;
            font-weight: 800;
            display: block;
            margin-top: 4px;
        }

        /* ألوان الكروت المتوافقة مع ملفك */
        .bg-custom-dark {
            background: linear-gradient(135deg, #1f2422 0%, #343a40 100%);
        }

        .bg-custom-green {
            background: linear-gradient(135deg, #18974d 0%, #0f5d30 100%);
        }

        .bg-custom-teal {
            background: linear-gradient(135deg, #20c997 0%, #158c6a 100%);
        }

        .bg-custom-olive {
            background: linear-gradient(135deg, #29793d 0%, #1c5229 100%);
        }

        /* الأزرار الجانبية */
        .btn-side-action {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #eee;
            margin-bottom: 10px;
            color: #1f2422 !important;
            transition: all 0.2s;
            text-decoration: none !important;
        }

        .btn-side-action:hover {
            background-color: #f0fdf4 !important;
            border-color: #18974d;
        }

        .btn-side-action .icon-box {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 12px;
            color: white;
            background-color: #18974d;
        }

        /* ستايل الجدول */
        .table-custom-style thead th {
            background-color: #f8f9fa !important;
            color: #1f2422 !important;
            font-size: 0.85rem;
            border-bottom: 2px solid #18974d !important;
        }

        .badge-active {
            background-color: rgba(24, 151, 77, 0.1);
            color: #18974d;
            border: 1px solid #18974d;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .btn-side-action {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            background: #ffffff;
            border-radius: 14px;
            /* حواف ناعمة */
            border: 1px solid #f1f5f9;
            margin-bottom: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none !important;
            position: relative;
            overflow: hidden;
        }

        /* تأثير المرور (Hover) */
        .btn-side-action:hover {
            background-color: #f8fafc !important;
            border-color: #18974d;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(24, 151, 77, 0.1) !important;
        }

        /* حاوية الأيقونة */
        .btn-side-action .icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            /* مسافة لـ RTL */
            transition: all 0.3s;
            background: linear-gradient(135deg, #1f2422 0%, #29793d 100%);
            /* تدرج ألوانك */
            color: white;
            font-size: 1.1rem;
        }

        /* تغيير لون الأيقونة عند الهوفر */
        .btn-side-action:hover .icon-box {
            transform: rotate(-10deg) scale(1.1);
            background: #18974d;
        }

        /* نصوص الأزرار */
        .btn-side-action .title-text {
            color: #1f2422;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 2px;
            display: block;
        }

        .btn-side-action .desc-text {
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 400;
            display: block;
        }

        /* سهم التنقل */
        .btn-side-action .chevron-icon {
            color: #cbd5e1;
            font-size: 0.8rem;
            transition: transform 0.3s;
        }

        .btn-side-action:hover .chevron-icon {
            color: #18974d;
            transform: translateX(-5px);
            /* حركة السهم لليمين في RTL */
        }
    </style>

    <div class="driver-container px-1 px-md-3">
        <!-- Main Header -->
        <div class="premium-header d-flex justify-content-between align-items-center my-2">
            <div>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb bg-transparent p-0 mb-2">
                        <li class="breadcrumb-item"><a href="#" class="text-white-50 small">نظام المواصلات</a></li>
                        <li class="breadcrumb-item active text-white fw-bold small">تفاصيل الكابتن</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">الكابتن: {{ $driver->Name }}</h3>
                <div class="mt-2 opacity-75 small">
                    <i class="fas fa-check-circle ml-1"></i> عضو منذ {{ $driver->created_at->format('Y-m-d') }}
                </div>
            </div>
            <a href="{{ route('drivers') }}" class="btn btn-light add-btn text-success shadow-sm rounded-pill px-4">
                <i class="fas fa-chevron-right ml-2"></i> العودة
            </a>
        </div>

        <div class="row">
            <!-- الـ Sidebar (يظهر في الأعلى في الجوال) -->
            <div class="col-xl-3 col-lg-4 order-2 order-lg-1">
                <!-- كارد الحساب -->
                <div class="card shadow-sm border-0 rounded-lg mb-3">
                    <div class="card-body text-center py-4">
                        @if ($driver->user)
                            <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm {{ $driver->user->is_banned ? 'bg-danger' : 'bg-success' }}"
                                style="width: 60px; height: 60px;">
                                <i
                                    class="fas {{ $driver->user->is_banned ? 'fa-user-slash' : 'fa-user-check' }} text-white fa-lg"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ $driver->user->is_banned ? 'الحساب محظور' : 'الحساب نشط' }}</h6>
                            <p class="text-muted small mb-3">@ {{ $driver->user->name }}</p>

                            <div class="account-actions px-2">
                                <button class="btn btn-outline-dark btn-sm btn-block rounded-pill mb-2 fw-bold"
                                    onclick="$('#passwordModal').modal('show')">
                                    <i class="fas fa-key ml-1"></i> كلمة المرور
                                </button>

                                <button wire:click="toggleAccountStatus"
                                    class="btn {{ $driver->user->is_banned ? 'btn-success' : 'btn-warning' }} btn-sm btn-block rounded-pill mb-2 fw-bold shadow-sm text-white">
                                    <i class="fas {{ $driver->user->is_banned ? 'fa-unlock' : 'fa-ban' }} ml-1"></i>
                                    {{ $driver->user->is_banned ? 'تفعيل الحساب' : 'حظر الحساب' }}
                                </button>

                                {{-- <button x-data x-on:click="
                                    Swal.fire({
                                        title: 'هل أنت متأكد؟',
                                        text: 'سيتم حذف بيانات دخول الكابتن نهائياً من النظام!',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#3085d6',
                                        confirmButtonText: 'نعم، احذف نهائياً',
                                        cancelButtonText: 'إلغاء',
                                        reverseButtons: true
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.deleteAccount();
                                        }
                                    })
                                " class="btn btn-link btn-sm text-danger btn-block fw-bold p-0 mt-2" style="font-size: 0.75rem;">
                                    <i class="fas fa-trash-alt ml-1"></i> حذف الحساب نهائياً
                                </button> --}}
                            </div>
                        @else
                            <div class="mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-user-plus text-muted fa-lg"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-muted">لا يوجد حساب دخول</h6>
                            <button wire:click="createDriverAccount"
                                class="btn btn-success btn-sm btn-block rounded-pill mt-3 shadow-sm fw-bold">
                                <i class="fas fa-plus ml-1"></i> إنشاء حساب
                            </button>
                        @endif
                    </div>
                </div>

                <!-- كارد حالة اليوم -->
                <div class="card shadow-sm border-0 rounded-lg mb-3 bg-light">
                    <div class="card-body p-3">
                        <h6 class="text-muted fw-bold mb-2 small"><i class="fas fa-clock ml-1"></i> حالة التحضير اليوم
                        </h6>
                        @if ($todayAttendance)
                            <div class="d-flex align-items-center">
                                <div class="badge badge-success p-2 rounded-circle ml-2">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div class="small fw-bold text-dark">تم التحضير</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        {{ \Carbon\Carbon::parse($todayAttendance->created_at)->format('H:i') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center text-muted opacity-75">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                <span class="small fw-bold">لم يتم التحضير اليوم بعد</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- أزرار العمليات -->
                <div class="mb-4">
                    <h6 class="text-muted fw-bold mb-3 small pr-2">
                        <i class="fas fa-sliders-h ml-2 text-success"></i> أدوات الإدارة والتشغيل
                    </h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <a href="{{ route('preparation-drivers', ['search' => $driver->Name]) }}"
                                class="btn-side-action shadow-sm">
                                <div class="icon-box shadow-sm">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="title-text">سجل التحضير</span>
                                    <span class="desc-text">متابعة غياب وحضور الكابتن</span>
                                </div>
                                <i class="fas fa-chevron-left chevron-icon"></i>
                            </a>
                        </div>

                        <div class="col-12">
                            <a href="{{ route('preparation-stus', ['driver_id' => $driver->id]) }}"
                                class="btn-side-action shadow-sm">
                                <div class="icon-box shadow-sm"
                                    style="background: linear-gradient(135deg, #18974d 0%, #0f5d30 100%);">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="title-text">تحضير الطلاب</span>
                                    <span class="desc-text">تسجيل صعود الطلاب للحافلة</span>
                                </div>
                                <i class="fas fa-chevron-left chevron-icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-xl-9 col-lg-8 order-1 order-lg-2">
                <!-- كارد المعلومات -->
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <i class="fas fa-info-circle text-success ml-2"></i>
                            <h6 class="fw-bold mb-0">بيانات السائق والمركبة</h6>
                        </div>

                        <div class="row text-center text-md-right mb-4">
                            <div class="col-6 col-md-4 mb-3">
                                <span class="text-muted small d-block mb-1 fw-bold">رقم الجوال</span>
                                <span class="fw-bold text-success">{{ $driver->Phone }}</span>
                            </div>
                            <div class="col-6 col-md-4 mb-3 border-md-right px-md-4">
                                <span class="text-muted small d-block mb-1 fw-bold">المركبة</span>
                                <span
                                    class="fw-bold text-dark text-truncate d-block">{{ $driver->Bus_type ?? '---' }}</span>
                            </div>
                            <div class="col-12 col-md-4 mb-3 border-md-right">
                                <span class="text-muted small d-block mb-1 fw-bold">رقم الرخصة</span>
                                <span class="fw-bold text-dark">{{ $driver->LicenseNo }}</span>
                            </div>
                        </div>

                        <!-- شبكة كروت الحالة (Responsive Grid) -->
                        <div class="row">
                            <div class="col-6 col-md-3 mb-2 px-1">
                                <div class="stat-card bg-custom-dark">
                                    <i class="fas fa-tools"></i>
                                    <span class="stat-label">الفحص</span>
                                    <span class="stat-value">{{ $driver->CheckUp ? 'صالح' : 'منتهي' }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2 px-1">
                                <div class="stat-card bg-custom-green">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="stat-label">الاستمارة</span>
                                    <span class="stat-value">{{ $driver->Form ? 'جاهزة' : 'ناقصة' }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2 px-1">
                                <div class="stat-card bg-custom-teal">
                                    <i class="fas fa-shield-alt"></i>
                                    <span class="stat-label">السلوك</span>
                                    <span class="stat-value">{{ $driver->Behavior ? 'جيد' : 'ملاحظة' }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2 px-1">
                                <div class="stat-card bg-custom-olive">
                                    <i class="fas fa-heartbeat"></i>
                                    <span class="stat-label">اللياقة</span>
                                    <span class="stat-value">{{ $driver->Fitnes ? 'لائق' : 'مراجعة' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- جدول الطلاب المتجاوب -->
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 small"><i class="fas fa-users ml-2 text-success"></i> الطلاب المخصصين
                        </h6>
                        <span class="badge badge-success rounded-pill px-3">{{ $driver->students->count() }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom-style mb-0">
                            <thead>
                                <tr>
                                    <th class="pr-3">الطالب</th>
                                    <th class="d-none d-md-table-cell">المنطقة</th>
                                    <th class="text-center">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($driver->students as $student)
                                    <tr>
                                        <td class="pr-3">
                                            <div class="fw-bold small text-dark">{{ $student->Name }}</div>
                                            <div class="text-muted d-md-none" style="font-size: 0.7rem;">
                                                {{ $student->region->Name ?? '' }}</div>
                                        </td>
                                        <td class="d-none d-md-table-cell small">{{ $student->region->Name ?? '---' }}
                                        </td>
                                        <td class="text-center">
                                            <i class="fas fa-check-circle text-success small"></i>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">لا يوجد طلاب</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content border-0 rounded-lg shadow">
                <div class="modal-header text-white border-0" style="background: #1f2422;">
                    <h6 class="modal-title fw-bold">كلمة المرور </h6>
                    <button type="button" class="close text-white"
                        data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form wire:submit.prevent="resetPassword">
                    <div class="modal-body p-3">
                        <input type="password" wire:model.defer="newPassword"
                            class="form-control form-control-sm rounded-pill border-light bg-light px-3"
                            placeholder="كلمة المرور الجديدة" required>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="submit"
                            class="btn btn-primary btn-sm btn-block rounded-pill fw-bold shadow">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @script
        <script>
            // الاستماع لإغلاق المودال
            $wire.on('close-password-modal', () => {
                $('#passwordModal').modal('hide');
            });

            // الاستماع لفتح المودال (إذا لزم الأمر برمجياً)
            $wire.on('open-password-modal', () => {
                $('#passwordModal').modal('show');
            });
        </script>
    @endscript
</div>
