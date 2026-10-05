<div>
    <style>
        .wa-card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .wa-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
        }

        .wa-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-size: 1.75rem;
            transition: all 0.3s ease;
        }

        .wa-action-btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 18px;
            transition: all 0.2s ease;
            letter-spacing: 0.3px;
        }

        .wa-action-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .wa-code {
            font-size: 2.2rem;
            letter-spacing: 12px;
            font-weight: 900;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.05);
        }

        .nav-pills .nav-link {
            color: #6c757d;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #f8f9fa;
            border-color: #e9ecef;
        }

        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            color: #fff;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }

        .info-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            transition: all 0.3s ease;
        }

        .info-box:hover {
            background: #fff;
            border-color: #dee2e6;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
    </style>

    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="fab fa-whatsapp text-success me-2"></i> إدارة الواتساب
                </h3>
                <p class="text-muted mb-0">
                    إدارة اتصال الواتساب وإعداد جلسة النظام
                </p>
            </div>

            <button wire:click="refreshStatus" wire:loading.attr="disabled" wire:target="refreshStatus" class="btn btn-light border rounded-pill px-4">
                <i class="fas fa-sync-alt me-1" wire:loading.class="fa-spin" wire:target="refreshStatus"></i> تحديث الحالة
            </button>
        </div>

        <div class="row g-4">

            {{-- 1. حالة الاتصال --}}
            <div class="col-lg-5">
                <div class="card wa-card h-100">
                    <div class="card-body p-4">

                        <div class="d-flex align-items-center mb-4">
                            <div class="wa-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">حالة الاتصال</h5>
                                <small class="text-muted">حالة جلسة واتساب الحالية</small>
                            </div>
                        </div>

                        @php
                            $connState = $this->getConnectionStateProperty();
                            $isConn = $connState['key'] === 'connected';
                            $isDisconn = $connState['key'] === 'disconnected' || $connState['key'] === 'logged_out';
                        @endphp

                        <div class="alert alert-{{ $connState['color'] }} rounded-4 d-flex align-items-center">
                            <i class="{{ $connState['icon'] }} fs-4 me-3 ms-2"></i>
                            <div>
                                <strong>{{ $connState['label'] }}</strong>
                                <div class="small mt-1">{{ $connState['message'] }}</div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            @if(!$isConn)
                            <button wire:click="connect" wire:loading.attr="disabled" wire:target="connect" class="btn btn-success wa-action-btn">
                                <span wire:loading.remove wire:target="connect"><i class="fas fa-link me-1"></i> بدء الاتصال</span>
                                <span wire:loading wire:target="connect"><i class="fas fa-spinner fa-spin me-1"></i> جاري الاتصال...</span>
                            </button>
                            @endif

                            @if(!$isDisconn && !$isConn)
                             <button wire:click="reconnect" wire:loading.attr="disabled" wire:target="reconnect" class="btn btn-outline-primary wa-action-btn">
                                <span wire:loading.remove wire:target="reconnect"><i class="fas fa-sync me-1"></i> إعادة الاتصال</span>
                                <span wire:loading wire:target="reconnect"><i class="fas fa-spinner fa-spin me-1"></i> جاري إعادة الاتصال...</span>
                            </button>
                            <button type="button" 
                                x-data
                                x-on:click="
                                    Swal.fire({
                                        title: 'هل أنت متأكد؟',
                                        text: 'سيتم إيقاف الجلسة مؤقتًا ويمكن إعادة تشغيلها لاحقًا.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#ffc107',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'نعم، افصل الاتصال!',
                                        cancelButtonText: 'إلغاء'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.disconnect();
                                        }
                                    });
                                "
                                wire:loading.attr="disabled" wire:target="disconnect" 
                                class="btn btn-outline-warning wa-action-btn">
                                <span wire:loading.remove wire:target="disconnect"><i class="fas fa-unlink me-1"></i> فصل الاتصال</span>
                                <span wire:loading wire:target="disconnect"><i class="fas fa-spinner fa-spin me-1"></i> جاري الفصل...</span>
                            </button>
                            @endif

                            @if(!$isDisconn)
                             <button wire:click="logoutWhatsApp" 
                                wire:confirm="سيتم تسجيل خروج جلسة الواتساب الحالية وستحتاج إلى إعادة ربط الحساب باستخدام QR Code أو كود الاقتران. هل تريد المتابعة؟" 
                                wire:loading.attr="disabled" wire:target="logoutWhatsApp" 
                                class="btn btn-outline-danger wa-action-btn">
                                <span wire:loading.remove wire:target="logoutWhatsApp"><i class="fas fa-sign-out-alt me-1"></i> تسجيل خروج الواتساب</span>
                                <span wire:loading wire:target="logoutWhatsApp"><i class="fas fa-spinner fa-spin me-1"></i> جاري تسجيل الخروج...</span>
                            </button>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="row g-4">
                    
                    {{-- 2. ربط حساب واتساب --}}
                    @if(!$isConn)
                    <div class="col-12">
                        <div class="card wa-card">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3"><i class="fas fa-qrcode text-primary me-2"></i> ربط حساب واتساب</h5>
                                
                                <ul class="nav nav-pills justify-content-center mb-4 bg-light rounded-pill p-1 d-inline-flex" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-pill px-4 py-2 {{ $linkMode === 'qr' ? 'active' : '' }}" wire:click="showQrTab" type="button" role="tab"><i class="fas fa-qrcode me-1"></i> QR Code</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-pill px-4 py-2 {{ $linkMode === 'pairing' ? 'active' : '' }}" wire:click="showPairingTab" type="button" role="tab"><i class="fas fa-keyboard me-1"></i> كود الاقتران</button>
                                    </li>
                                </ul>
                                
                                <div class="tab-content">
                                    {{-- QR Tab --}}
                                    @if($linkMode === 'qr')
                                    <div class="tab-pane fade show active" role="tabpanel">
                                        <div class="text-center">
                                            <button wire:click="loadQrCode" wire:loading.attr="disabled" wire:target="loadQrCode" class="btn btn-dark wa-action-btn mb-3">
                                                <span wire:loading.remove wire:target="loadQrCode"><i class="fas fa-qrcode me-1"></i> إظهار QR Code</span>
                                                <span wire:loading wire:target="loadQrCode"><i class="fas fa-spinner fa-spin me-1"></i> جاري التحميل...</span>
                                            </button>
                                        </div>

                                        @if($qrCode)
                                            <div class="text-center border rounded-4 p-4 bg-light mx-auto" style="max-width: 320px;">
                                                <p class="text-muted small mb-3">قم بمسح الرمز باستخدام تطبيق الواتساب الخاص بك</p>
                                                @if(str_starts_with($qrCode, 'data:image'))
                                                    <img src="{{ $qrCode }}" alt="QR Code" class="img-fluid rounded">
                                                @else
                                                    <span class="text-danger small">صيغة الـ QR غير مدعومة لعرضها كصورة</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                    
                                    {{-- Pairing Code Tab --}}
                                    @if($linkMode === 'pairing')
                                    <div class="tab-pane fade show active" role="tabpanel">
                                        <div class="mb-3" style="max-width: 400px;">
                                            <label class="form-label">رقم الهاتف (مثال: 967777123456)</label>
                                            <div class="input-group">
                                                <input type="text" wire:model.defer="pairingPhone" class="form-control" placeholder="9677XXXXXXXX">
                                                <button wire:click="getPairingCode" wire:loading.attr="disabled" wire:target="getPairingCode" class="btn btn-primary px-4">
                                                    <span wire:loading.remove wire:target="getPairingCode">إنشاء</span>
                                                    <span wire:loading wire:target="getPairingCode"><i class="fas fa-spinner fa-spin"></i></span>
                                                </button>
                                            </div>
                                            @error('pairingPhone') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        @if($pairingCode)
                                            <div class="mt-3 p-3 bg-light rounded-4 text-center" style="max-width: 400px;">
                                                <div class="text-muted small mb-1">كود الاقتران الخاص بك</div>
                                                <div class="wa-code text-primary">{{ $pairingCode }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                 
                    {{-- 4. إرسال رسالة تجريبية --}}
                    {{-- <div class="col-12">
                        <div class="card wa-card border-top border-3 border-success">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3"><i class="fas fa-paper-plane text-success me-2"></i> إرسال رسالة تجريبية</h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label small">رقم الهاتف</label>
                                        <input type="text" wire:model.defer="testPhone" class="form-control" placeholder="9677XXXXXXXX">
                                        @error('testPhone') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label small">نص الرسالة</label>
                                        <div class="input-group">
                                            <input type="text" wire:model.defer="testMessage" class="form-control" placeholder="اكتب رسالة تجريبية هنا...">
                                            <button wire:click="sendTestMessage" wire:loading.attr="disabled" wire:target="sendTestMessage" class="btn btn-success px-4">
                                                <span wire:loading.remove wire:target="sendTestMessage">إرسال</span>
                                                <span wire:loading wire:target="sendTestMessage"><i class="fas fa-spinner fa-spin"></i></span>
                                            </button>
                                        </div>
                                        @error('testMessage') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                </div>
            </div>

        </div>
    </div>
</div>