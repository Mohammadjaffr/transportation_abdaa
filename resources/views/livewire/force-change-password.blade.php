<div class="container mt-5 section">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0" style="border-radius: 15px;">
                <div class="card-header bg-primary text-white text-center py-3" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0 fw-bold">إعداد كلمة مرور جديدة</h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert" style="border-radius: 10px;">
                        <div class="me-2 text-warning fs-3 px-2">⚠️</div>
                        <div>
                            <strong>تنبيه أمني:</strong> يجب عليك تغيير كلمة المرور الافتراضية الخاصة بك قبل المتابعة واستخدام النظام.
                        </div>
                    </div>

                    <form wire:submit="save">
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">كلمة المرور الجديدة</label>
                            <input type="password" id="password" wire:model="password" 
                                class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                placeholder="أدخل كلمة المرور الجديدة">
                            @error('password') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">تأكيد كلمة المرور</label>
                            <input type="password" id="password_confirmation" wire:model="password_confirmation" 
                                class="form-control form-control-lg" 
                                placeholder="أعد إدخال كلمة المرور">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 10px;">
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            تحديث ومتابعة للوحة التحكم
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
