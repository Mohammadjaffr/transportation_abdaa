<div class="container-fluid px-3 pt-4 text-center">
    <div class="mb-5 mt-4">
        <div class="d-inline-flex bg-primary bg-opacity-10 text-primary rounded-circle p-4 mb-3">
            <i class="fas fa-clipboard-list fa-4x"></i>
        </div>
        <h3 class="fw-bold">تحضير رحلات اليوم</h3>
        <p class="text-muted">اختر نوع الرحلة للبدء في تحضير الطلاب</p>
    </div>

    <div class="d-grid gap-4">
        <a href="{{ route('driver.attendance', ['type' => 'morning']) }}"
            class="card shadow-sm text-decoration-none border-0 rounded-4">
            <div
                class="card-body p-4 border-start border-5 border-primary text-start d-flex align-items-center justify-content-between rounded-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">رحلة الذهاب (الصباح)</h5>
                    <small class="text-muted">نقل الطلاب من المنزل إلى المدرسة</small>
                </div>
                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                    <i class="fas fa-chevron-left text-primary fa-lg"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('driver.attendance', ['type' => 'leave']) }}"
            class="card shadow-sm text-decoration-none border-0 rounded-4">
            <div
                class="card-body p-4 border-start border-5 border-info text-start d-flex align-items-center justify-content-between rounded-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">رحلة العودة (المساء)</h5>
                    <small class="text-muted">إعادة الطلاب إلى منازلهم</small>
                </div>
                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                    <i class="fas fa-chevron-left text-info fa-lg"></i>
                </div>
            </div>
        </a>
    </div>
</div>
