<div class="pt-4">
    <div class="card shadow-sm border-0 rounded-3 ">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> سجلات الأنشطة</h5>
            <div class="col-12 col-md-6 mb-2  mb-md-0">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border-0 ">
                    <span class="input-group-text bg-white border-0 rounded-0">
                        <i class="fas fa-search text-success"></i>
                    </span>
                    <input type="text" class="form-control border-0  py-2 " placeholder="بحث عن النشاط..."
                        wire:model.debounce.300ms.live="search">

                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>المسؤول</th>
                            <th>الإجراء</th>
                            <th>الوصف</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $index => $log)
                            <tr>
                                <td>{{ $logs->firstItem() + $index }}</td>
                                <td>
                                    <i class="fas fa-user-circle text-success me-1"></i>
                                    {{ $log->admin?->name ?? 'غير معروف' }}
                                </td>
                                <td><span class="badge bg-success">{{ $log->action }}</span></td>
                                <td>{{ $log->description }}</td>
                                <td>
                                    <i class="fas fa-clock text-success me-1"></i>
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-3">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <div>لا توجد سجلات حتى الآن</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-center">
            {{ $logs->links('pagination::bootstrap-5') }}

        </div>
    </div>
</div>
