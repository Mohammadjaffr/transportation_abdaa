<div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <div class="container py-4">


        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-start d-none d-sm-block">إدارة الطلاب</h3>
            <h3 class="fw-bold text-end d-none d-sm-block"> عام {{ date('Y') }} </h3>

        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            @if (!$showForm && !$showImportForm)
                <button wire:click="$set('showForm', true)"
                    class="btn btn-primary add-btn w-sm-100 mb-3 rounded-pill shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i> إضافة طالب جديد
                </button>
                <div>
                    <button wire:click="$set('showImportForm', true)"
                        class="btn btn-success w-sm-100 mb-3 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-1"></i> استيراد Excel
                    </button>

                    <button wire:click="exportExcel"
                        class="btn btn-outline-success w-sm-100 mb-3 rounded-pill shadow-sm">
                        <i class="fas fa-download me-1"></i> تصدير Excel
                    </button>
                </div>
            @endif




            @if ($showImportForm)
                <div class="card shadow-sm mb-4 rounded-3 w-100 ">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">استيراد ملف Excel</h5>
                    </div>
                    <div class="card-body ">
                        <form wire:submit.prevent="importExcel" enctype="multipart/form-data">

                            <div class="mb-4">
                                <label class="form-label fw-bold text-primary">
                                    <i class="fas fa-file-excel me-2 text-success"></i> اختر ملف Excel
                                </label>

                                <div class="card shadow-sm border-0 rounded-3 p-3 d-flex align-items-center justify-content-center bg-light"
                                    style="border: 2px dashed #28a745; cursor: pointer;">
                                    <label class="w-100 text-center" style="cursor: pointer;">
                                        <input type="file" wire:model="excelFile" class="d-none" accept=".xlsx,.csv">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-success mb-2"></i>
                                        <p class="fw-bold mb-0 text-muted">
                                            {{ $excelFile ? $excelFile->getClientOriginalName() : 'اسحب الملف هنا أو اضغط للرفع' }}
                                        </p>
                                    </label>
                                </div>

                                @error('excelFile')
                                    <div class="text-danger small mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror

                                @if ($excelFile)
                                    <div class="mt-3 text-success fw-bold">
                                        <i class="fas fa-check-circle me-1"></i> تم رفع الملف بنجاح:
                                        <span class="text-dark">{{ $excelFile->getClientOriginalName() }}</span>
                                    </div>
                                @endif
                            </div>




                            <div x-data="{ progress: 0 }" x-on:livewire-upload-start="progress = 0"
                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                                x-on:livewire-upload-finish="progress = 100; setTimeout(() => progress = 0, 1200)"
                                x-on:livewire-upload-error="progress = 0" class="mt-3">
                                <div x-show="progress > 0" class="progress" style="height: 22px;">
                                    <div class="progress-bar bg-success fw-bold" role="progressbar"
                                        :style="`width: ${progress}%`" x-text="progress + '%'">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success w-100 rounded-pill"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="importExcel">
                                            <i class="fas fa-file-import me-1"></i> استيراد
                                        </span>
                                        <span wire:loading wire:target="importExcel">
                                            <i class="fas fa-spinner fa-spin me-1"></i> جاري الاستيراد...
                                        </span>
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" wire:click="resetImportForm"
                                        class="btn btn-outline-secondary w-100 rounded-pill">
                                        إلغاء
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            @endif


        </div>





        {{-- Form --}}
        @if ($showForm)
            <div class="card shadow-sm mb-4 rounded-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">{{ $editMode ? 'تعديل بيانات الطالب' : 'إضافة طالب جديد' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editMode ? 'updateStudent' : 'createStudent' }}">

                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label>الاسم</label>
                                <input type="text" wire:model="name"
                                    class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">

                                <label>الصف</label>
                                <select wire:model="grade" class="form-control @error('grade') is-invalid @enderror">
                                    <option value="">اختر الصف</option>
                                    <option value="الاول">الاول</option>
                                    <option value="الثاني">الثاني</option>
                                    <option value="الثالث">الثالث</option>
                                    <option value="الرابع">الرابع</option>
                                    <option value="الخامس">الخامس</option>
                                    <option value="السادس">السادس</option>
                                    <option value="السابع">السابع</option>
                                    <option value="الثامن">الثامن</option>
                                    <option value="التاسع">التاسع</option>
                                    <option value="اول ثانوي">اول ثانوي</option>
                                    <option value="ثاني ثانوي">ثاني ثانوي</option>
                                    <option value="ثالث ثانوي">ثالث ثانوي</option>
                                </select>
                                @error('grade')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>النوع</label>
                                <select wire:model="sex" class="form-control @error('sex') is-invalid @enderror">
                                    <option value="">اختر النوع</option>
                                    <option value="ذكر">ذكر</option>
                                    <option value="أنثى">أنثى</option>
                                </select>
                                @error('sex')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>الهاتف</label>
                                <input type="number" wire:model="phone"
                                    class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>المنطقة</label>
                                <select wire:model.live="region_id"
                                    class="form-control @error('region_id') is-invalid @enderror">
                                    <option value="">اختر المنطقة</option>
                                    @foreach ($parent_regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->Name }}</option>
                                    @endforeach
                                </select>
                                @error('region_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($children_regions)

                                <div class="col-md-4 mt-4">
                                    <select wire:model="child_region_id"
                                        class="form-control @error('child_region_id') is-invalid @enderror">
                                        <option value="">اختر الموقف</option>
                                        @foreach ($children_regions as $region)
                                            <option value="{{ $region->Name }}">{{ $region->Name }}</option>
                                        @endforeach
                                    </select>
                                    @error('child_region_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>
                            @endif

                            <div class="col-md-4 mb-3">
                                <label>الجناح</label>
                                <select wire:model="wing_id"
                                    class="form-control @error('wing_id') is-invalid @enderror">
                                    <option value="">اختر الجناح</option>
                                    @foreach ($wings as $wing)
                                        <option value="{{ $wing->id }}">{{ $wing->Name }}</option>
                                    @endforeach
                                </select>
                                @error('wing_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>الشعبه</label>
                                <select wire:model="division"
                                    class="form-control @error('division') is-invalid @enderror">
                                    <option value="">اختر الشعبه</option>
                                    <option value="أ">أ</option>
                                    <option value="ب">ب</option>
                                    <option value="ج">ج</option>
                                    <option value="د">د</option>
                                    <option value="ه">ه</option>
                                    <option value="و">و</option>
                                    <option value="ز">ز</option>
                                </select>
                                @error('division')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>المعلم\ة</label>
                                <select wire:model="teacher_id"
                                    class="form-control @error('teacher_id') is-invalid @enderror">
                                    <option value="">اختر المعلم\ة</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->Name }}</option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>




                            {{-- <div class="col-md-12 mb-3">
                                <label class="form-label text-primary fw-bold d-block mb-2">
                                    <span class="text-danger">*</span>
                                    <i class="fas fa-image me-2"></i> صورة الطالب
                                </label>

                                <div class="input-group input-group-lg shadow-sm rounded-2 overflow-hidden">
                                    <label class="input-group-text btn btn-primary text-white border-0"
                                        style="cursor: pointer;">
                                        <i class="fas fa-cloud-upload-alt me-2"></i> رفع
                                        <input type="file" wire:model="primary_image" class="d-none"
                                            accept="image/*">
                                    </label>
                                </div>

                                @error('picture')
                                    <div class="text-danger small mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                @if ($primary_image)
                                    <img src="{{ $primary_image->temporaryUrl() }}" alt="معاينة"
                                        class="img-thumbnail" width="120">
                                @elseif ($picture)
                                    <img src="{{ url($picture) }}" alt="الصورة الحالية" class="img-thumbnail"
                                        width="120">
                                @endif

                            </div> --}}


                            <div class="col-md-6 mt-3">
                                <button type="submit"
                                    class="btn btn-{{ $editMode ? 'primary' : 'success' }} w-100 rounded-pill">
                                    <i class="fas {{ $editMode ? 'fa-save' : 'fa-plus-circle' }} me-1"></i>
                                    {{ $editMode ? 'تحديث' : 'إضافة' }}
                                </button>
                            </div>
                            <div class="col-md-6 mt-3">
                                <button type="button" wire:click="resetForm"
                                    class="btn btn-outline-secondary w-100 rounded-pill">
                                    إلغاء
                                </button>
                            </div>

                    </form>
                </div>
            </div>
    </div>
    @endif

    {{-- Search --}}
    <div class="col-md-12 mb-3">
        <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-primary"></i></span>
            <input type="text" wire:model.debounce.300ms.live="search" class="form-control border-0 py-2"
                placeholder="ابحث باسم الطالب أو الرقم...">
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">إدارة الطلاب</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            {{-- <th>الصورة</th> --}}
                            <th>الاسم</th>
                            <th>الصف</th>
                            <th>النوع</th>
                            <th>الهاتف</th>
                            <th>الجناح</th>
                            <th>الشعبة</th>
                            <th>المنطقة</th>
                            <th>الموقف</th>
                            <th>المعلم\ة</th>
                            <th>السائق</th>
                            <th>سنة الدراسة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->id }}</td>


                                <td> {{ $student->Name }}
                                </td>
                                <td>{{ $student->Grade }}</td>
                                <td>{{ $student->Sex }}</td>
                                <td>{{ $student->Phone }}</td>
                                <td>{{ $student->wing?->Name }}</td>
                                <td>{{ $student->Division }}</td>
                                <td>{{ $student->region?->Name }}</td>
                                <td>{{ $student->Stu_position }}</td>
                                <td>{{ $student->teacher?->Name ?? 'غير موجود' }}</td>
                                <td>{{ $student->driver?->Name ?? 'غير موجود' }}</td>
                                <td>{{ $student->schoolYear->year ?? 'غير محددة' }}</td>

                                <td class="d-flex gap-2 justify-content-center">
                                    <button wire:click="editStudent({{ $student->id }})"
                                        class="btn btn-outline-success btn-sm rounded-pill mr-2">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    <button wire:click="confirmDelete({{ $student->id }})"
                                        class="btn btn-outline-danger btn-sm rounded-pill">
                                        <i class="fas fa-trash-alt"></i> حذف
                                    </button>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-user-graduate fa-2x mb-2 text-secondary"></i>
                                    <p class="mb-0">لا يوجد طلاب مسجلين</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                
                </table>
        
            </div>
                    <div class="card-footer d-flex justify-content-center">
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
        </div>

    </div>

    @if ($deleteId)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-sm">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> تأكيد الحذف</h5>
                        <button type="button" class="btn-close btn-light"
                            wire:click="$set('deleteId', null)"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد أنك تريد حذف هذا الطالب؟</p>
                        <p class="fw-bold text-danger">" الاسم: {{ $deleteName }} "</p>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <button type="button" class="btn btn-secondary rounded-pill"
                            wire:click="$set('deleteId', null)">إلغاء</button>
                        <button type="button" class="btn btn-danger rounded-pill" wire:click="deleteStudent">نعم،
                            احذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</div>
</div>
