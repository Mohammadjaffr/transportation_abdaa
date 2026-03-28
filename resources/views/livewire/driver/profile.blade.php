<div class="container-fluid px-3 pt-4">
    <div class="text-center mb-5">
        <div class="position-relative d-inline-block">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm border-4 border-white"
                style="width: 110px; height: 110px; font-size: 3rem; margin: 0 auto; background: linear-gradient(135deg, #0d6efd, #0dcaf0);">
                @if ($driver && $driver->Picture)
                    <img src="{{ asset('storage/' . $driver->Picture) }}"
                        class="rounded-circle w-100 h-100 object-fit-cover shadow-sm">
                @else
                    <i class="fas fa-user-tie"></i>
                @endif
            </div>
        </div>
        <h4 class="fw-bold mt-3 mb-1 text-dark">{{ $driver->Name ?? $user->name }}</h4>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mt-1">حساب سائق
            نشط</span>
    </div>

    @if ($driver)
        <h6 class="fw-bold mb-3 text-secondary px-2 d-flex align-items-center"><i
                class="fas fa-address-card ms-2 opacity-50"></i> المعلومات الشخصية</h6>
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-0">
                <ul class="list-group list-group-flush rounded-4">
                    <li class="list-group-item d-flex justify-content-between align-items-center p-4">
                        <div>
                            <small class="text-muted d-block fw-bold mb-1">رقم الهوية</small>
                            <span class="fw-bold fs-6 text-dark">{{ $driver->IDNo ?? '-' }}</span>
                        </div>
                        <div class="bg-light rounded-circle p-3 text-muted">
                            <i class="fas fa-id-card fa-lg"></i>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center p-4">
                        <div>
                            <small class="text-muted d-block fw-bold mb-1">رقم الهاتف</small>
                            <span class="fw-bold fs-6 text-dark" dir="ltr">{{ $driver->Phone ?? '-' }}</span>
                        </div>
                        <div class="bg-light rounded-circle p-3 text-muted">
                            <i class="fas fa-phone fa-lg"></i>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center p-4 border-0">
                        <div>
                            <small class="text-muted d-block fw-bold mb-1">رقم الرخصة</small>
                            <span class="fw-bold fs-6 text-dark">{{ $driver->LicenseNo ?? '-' }}</span>
                        </div>
                        <div class="bg-light rounded-circle p-3 text-muted">
                            <i class="fas fa-id-badge fa-lg"></i>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        @if ($driver->bus)
            <h6 class="fw-bold mb-3 mt-4 text-secondary px-2 d-flex align-items-center"><i
                    class="fas fa-bus ms-2 opacity-50"></i> معلومات الحافلة</h6>
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush rounded-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center p-4">
                            <div>
                                <small class="text-muted d-block fw-bold mb-1">نوع الحافلة</small>
                                <span class="fw-bold fs-6 text-dark">{{ $driver->bus->BusType ?? '-' }}</span>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 text-primary">
                                <i class="fas fa-bus fa-lg"></i>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-4 border-0">
                            <div>
                                <small class="text-muted d-block fw-bold mb-1">عدد المقاعد الإجمالي</small>
                                <span class="fw-bold fs-6 text-dark">{{ $driver->bus->SeatsNo ?? '-' }} مقعد</span>
                            </div>
                            <div class="bg-light rounded-circle p-3 text-muted">
                                <i class="fas fa-chair fa-lg"></i>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
    @endif

    <div class="d-grid mt-5 mb-5 pb-4">
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="btn btn-danger btn-lg rounded-pill shadow-sm fw-bold border-0 py-3 d-flex align-items-center justify-content-center"
            style="background: linear-gradient(135deg, #ef4444, #dc2626);">
            <i class="fas fa-sign-out-alt ms-2"></i> تسجيل الخروج بشكل آمن
        </a>
    </div>
</div>
