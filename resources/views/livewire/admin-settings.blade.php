<div>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    .settings-card {
        border: none;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }
    
    .time-input-wrapper {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 2px solid #f0f2f5;
    }

    .time-input-wrapper:focus-within {
        border-color: #4e73df;
        box-shadow: 0 0 15px rgba(78, 115, 223, 0.15);
    }

    .time-input-wrapper .input-group-text {
        background: transparent;
        border: none;
        padding-left: 20px;
    }

    .time-input-wrapper input {
        border: none !important;
        padding: 15px 10px;
        font-weight: 600;
        color: #495057;
    }

    .btn-save {
        border-radius: 15px;
        padding: 14px;
        letter-spacing: 0.5px;
        background: linear-gradient(45deg, #1cc88a, #13855c);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        filter: brightness(1.1);
        box-shadow: 0 8px 20px rgba(28, 200, 138, 0.3);
    }

    .instruction-alert {
        border-radius: 15px;
        background-color: #f8f9fc;
        border-right: 5px solid #4e73df;
    }

    .icon-badge {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-left: 15px;
    }
    
    input[type="time"]::-webkit-calendar-picker-indicator {
        background: transparent;
        bottom: 0;
        color: transparent;
        cursor: pointer;
        height: auto;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        width: auto;
    }
</style>

<div class="container-fluid py-5" style="font-family: 'Tajawal', sans-serif;">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-6">
            
            <div class="card settings-card shadow-lg">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 text-center">
                    <div class="d-inline-block bg-light-primary p-3 rounded-circle mb-3">
                        <i class="fas fa-user-clock fa-3x text-primary"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">إعدادات أوقات التحضير</h4>
                    <p class="text-muted small">إدارة الفترات الزمنية المسموحة لتحضير الطلاب</p>
                </div>

                <div class="card-body px-4">
                    <div class="instruction-alert d-flex align-items-center p-3 mb-4">
                        <div class="icon-badge bg-white shadow-sm">
                            <i class="fas fa-info text-primary"></i>
                        </div>
                        <div>
                            <p class="mb-0 small text-dark fw-medium">
                                سيتم فتح وإغلاق إمكانية التحضير تلقائياً بناءً على الفترات المحددة أدناه.
                            </p>
                        </div>
                    </div>

                    <form wire:submit="saveLocks">
                    
                        <!-- رحلة الذهاب (الصباحية) -->
                        <div class="mb-4 p-3 border rounded bg-light">
                            <label class="form-label fw-bold text-secondary mb-3 d-block border-bottom pb-2">
                                <i class="fas fa-sun text-warning me-1"></i> رحلة الذهاب (الصباحية)
                            </label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="small text-muted mb-1">من الساعة:</label>
                                    <div class="input-group time-input-wrapper shadow-sm bg-white">
                                        <span class="input-group-text"><i class="fas fa-play text-success"></i></span>
                                        <input type="time" wire:model="morning_start" class="form-control @error('morning_start') is-invalid @enderror">
                                    </div>
                                    @error('morning_start') <div class="text-danger small mt-1 ps-2">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted mb-1">إلى الساعة:</label>
                                    <div class="input-group time-input-wrapper shadow-sm bg-white">
                                        <span class="input-group-text"><i class="fas fa-stop text-danger"></i></span>
                                        <input type="time" wire:model="morning_end" class="form-control @error('morning_end') is-invalid @enderror">
                                    </div>
                                    @error('morning_end') <div class="text-danger small mt-1 ps-2">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- رحلة العودة (المسائية) -->
                        <div class="mb-4 p-3 border rounded bg-light">
                            <label class="form-label fw-bold text-secondary mb-3 d-block border-bottom pb-2">
                                <i class="fas fa-moon text-indigo me-1"></i> رحلة العودة (المسائية)
                            </label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="small text-muted mb-1">من الساعة:</label>
                                    <div class="input-group time-input-wrapper shadow-sm bg-white">
                                        <span class="input-group-text"><i class="fas fa-play text-success"></i></span>
                                        <input type="time" wire:model="leave_start" class="form-control @error('leave_start') is-invalid @enderror">
                                    </div>
                                    @error('leave_start') <div class="text-danger small mt-1 ps-2">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted mb-1">إلى الساعة:</label>
                                    <div class="input-group time-input-wrapper shadow-sm bg-white">
                                        <span class="input-group-text"><i class="fas fa-stop text-danger"></i></span>
                                        <input type="time" wire:model="leave_end" class="form-control @error('leave_end') is-invalid @enderror">
                                    </div>
                                    @error('leave_end') <div class="text-danger small mt-1 ps-2">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-save btn-lg w-100 text-white fw-bold shadow-sm mt-2">
                            <i class="fas fa-save me-2"></i> حفظ الإعدادات الحالية
                        </button>
                    </form>
                </div>
                
                <div class="card-footer bg-light border-0 py-3 text-center" style="border-radius: 0 0 20px 20px;">
                    <span class="text-muted small">
                        <i class="fas fa-sync-alt fa-spin me-1 text-success"></i> 
                        يتم تحديث التطبيقات فور الحفظ
                    </span>
                </div>
            </div>

            <p class="text-center mt-4 text-muted small px-4">
                <i class="fas fa-lock me-1"></i> النظام يقوم بتشفير وحماية كافة الإعدادات لضمان دقة البيانات.
            </p>
        </div>
    </div>
</div>

<script>
    window.addEventListener('show-toast', event => {
        // نتحقق من شكل البيانات المستلمة لضمان التوافق مع Livewire 3
        let data = Array.isArray(event.detail) ? event.detail[0] : event.detail;
        
        // استخراج النوع والرسالة، مع وضع قيم افتراضية
        let type = data?.type || 'success';
        let message = data?.message || 'تم الحفظ بنجاح';
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: 'عملية ناجحة',
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });
        } else {
            alert(message);
        }
    });
</script>
</div>