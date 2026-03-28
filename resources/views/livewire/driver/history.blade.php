<div class="container-fluid px-3 pt-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0 text-dark">سجل التحضير</h4>
        <input type="month" wire:model.live="month"
            class="form-control rounded-pill shadow-sm border-0 bg-white text-center fw-bold text-primary"
            style="width: 160px; padding: 10px;">
    </div>

    @forelse($history as $date => $trips)
        <div class="mb-5">
            <h6 class="text-secondary fw-bold mb-3 px-2 d-flex align-items-center">
                <div class="bg-secondary rounded-circle d-inline-block me-2" style="width: 8px; height: 8px;"></div>
                {{ \Carbon\Carbon::parse($date)->locale('ar')->translatedFormat('l, j F Y') }}
            </h6>

            <div class="d-grid gap-3">
                @foreach ($trips as $trip)
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden relative">
                        <!-- Strip color decorator -->
                        <div class="position-absolute top-0 bottom-0 end-0 {{ $trip['type'] == 'morning' ? 'bg-primary' : 'bg-info' }}"
                            style="width: 6px;"></div>

                        <div class="card-body p-4 pe-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle {{ $trip['type'] == 'morning' ? 'bg-primary text-primary' : 'bg-info text-info' }} bg-opacity-10 me-2"
                                        style="width: 32px; height: 32px;">
                                        <i class="fas fa-{{ $trip['type'] == 'morning' ? 'sun' : 'moon' }}"></i>
                                    </div>
                                    رحلة {{ $trip['type'] == 'morning' ? 'الذهاب' : 'العودة' }}
                                </h6>
                                <span class="badge bg-light text-dark shadow-sm rounded-pill px-3 py-2 fw-bold"
                                    style="border: 1px solid #e5e7eb;">الركاب: {{ $trip['total'] }}</span>
                            </div>
                            <div class="d-flex gap-3">
                                <div
                                    class="flex-grow-1 bg-success bg-opacity-10 text-success rounded-4 p-3 text-center border border-success border-opacity-25">
                                    <small class="d-block fw-bold mb-1 opacity-75">حضور</small>
                                    <span class="fs-4 fw-bolder">{{ $trip['present'] }}</span>
                                </div>
                                <div
                                    class="flex-grow-1 bg-danger bg-opacity-10 text-danger rounded-4 p-3 text-center border border-danger border-opacity-25">
                                    <small class="d-block fw-bold mb-1 opacity-75">غياب</small>
                                    <span class="fs-4 fw-bolder">{{ $trip['absent'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="text-center py-5 mt-5">
            <div class="d-inline-flex bg-light text-muted rounded-circle p-4 mb-3 border">
                <i class="fas fa-calendar-times fa-3x opacity-50"></i>
            </div>
            <h5 class="fw-bold text-dark">لا توجد سجلات</h5>
            <p class="text-muted small">لم يتم العثور على أي عمليات تحضير في هذا الشهر.</p>
        </div>
    @endforelse
</div>
