<div class="container-fluid px-3 pb-4">


    {{-- العنوان --}}
    <div class="mb-4">

        <h4 class="fw-bold mb-1">

            <i class="fas fa-calendar-check text-success me-1"></i>

            سجل حضور الأبناء

        </h4>

        <small class="text-muted">

            متابعة حضور وغياب الأبناء في الذهاب والعودة

        </small>

    </div>



    {{-- الفلاتر --}}
    <div class="card mb-4">

        <div class="card-body">


            <div class="row g-3">


                {{-- اختيار الطالب --}}
                <div class="col-12 col-md-6">

                    <label class="form-label fw-bold">

                        <i class="fas fa-user-graduate text-success me-1"></i>

                        اختر الطالب

                    </label>


                    <select wire:model.live="selectedStudentId" class="form-select">

                        @if ($students->isEmpty())

                            <option value="">

                                لا يوجد أبناء مرتبطون بالحساب

                            </option>
                        @else
                            @foreach ($students as $child)
                                <option value="{{ $child->id }}">

                                    {{ $child->Name }}

                                    @if ($child->Grade)
                                        - {{ $child->Grade }}
                                    @endif

                                </option>
                            @endforeach

                        @endif

                    </select>

                </div>



                {{-- الشهر --}}
                <div class="col-12 col-md-6">

                    <label class="form-label fw-bold">

                        <i class="fas fa-calendar-alt text-success me-1"></i>

                        الشهر

                    </label>


                    <input type="month" wire:model.live="month" class="form-control">

                </div>


            </div>


        </div>

    </div>



    @if ($student)


        {{-- بيانات الطالب --}}
        <div class="card mb-4">

            <div class="card-body">

                <div
                    class="d-flex
                           align-items-center
                           justify-content-between">

                    <div>

                        <small class="text-muted">

                            الطالب

                        </small>


                        <h5 class="fw-bold mb-1">

                            {{ $student->Name }}

                        </h5>


                        <div class="text-muted small">

                            {{ $student->Grade }}

                            @if ($student->Division)
                                -
                                الشعبة:
                                {{ $student->Division }}
                            @endif

                        </div>

                    </div>


                    <div class="bg-success
                               bg-opacity-10
                               text-success
                               rounded-circle
                               d-flex
                               align-items-center
                               justify-content-center"
                        style="
                            width: 55px;
                            height: 55px;
                        ">

                        <i class="fas fa-user-graduate fs-4"></i>

                    </div>

                </div>

            </div>

        </div>



        {{-- الإحصائيات --}}
        <div class="row g-2 mb-4">


            {{-- إجمالي --}}
            <div class="col-6 col-md-3">

                <div class="card h-100">

                    <div class="card-body text-center">

                        <i class="fas fa-list text-primary fs-4 mb-2"></i>

                        <div class="text-muted small">

                            إجمالي السجلات

                        </div>

                        <div class="fw-bold fs-4">

                            {{ $stats['total'] }}

                        </div>

                    </div>

                </div>

            </div>



            {{-- حضور --}}
            <div class="col-6 col-md-3">

                <div class="card h-100">

                    <div class="card-body text-center">

                        <i class="fas fa-check-circle text-success fs-4 mb-2"></i>

                        <div class="text-muted small">

                            حاضر

                        </div>

                        <div class="fw-bold fs-4 text-success">

                            {{ $stats['present'] }}

                        </div>

                    </div>

                </div>

            </div>



            {{-- غياب --}}
            <div class="col-6 col-md-3">

                <div class="card h-100">

                    <div class="card-body text-center">

                        <i class="fas fa-times-circle text-danger fs-4 mb-2"></i>

                        <div class="text-muted small">

                            غائب

                        </div>

                        <div class="fw-bold fs-4 text-danger">

                            {{ $stats['absent'] }}

                        </div>

                    </div>

                </div>

            </div>



            {{-- الذهاب / العودة --}}
            <div class="col-6 col-md-3">

                <div class="card h-100">

                    <div class="card-body text-center">

                        <i class="fas fa-bus text-warning fs-4 mb-2"></i>

                        <div class="text-muted small">

                            ذهاب / عودة

                        </div>

                        <div class="fw-bold">

                            {{ $stats['morning'] }}

                            /

                            {{ $stats['leave'] }}

                        </div>

                    </div>

                </div>

            </div>


        </div>



        {{-- سجل الحضور --}}
        <div class="card">


            <div
                class="card-header
                       bg-white
                       border-0
                       pt-4
                       px-4">

                <h5 class="fw-bold mb-0">

                    <i class="fas fa-history text-success me-1"></i>

                    السجل التفصيلي

                </h5>

            </div>



            <div class="card-body">


                @forelse ($records as $record)
                    <div
                        class="
                            border
                            rounded-4
                            p-3
                            mb-3
                            bg-white
                        ">


                        <div
                            class="
                                d-flex
                                justify-content-between
                                align-items-start
                                mb-3
                            ">


                            {{-- التاريخ --}}
                            <div>

                                <div class="fw-bold">

                                    <i
                                        class="
                                            fas
                                            fa-calendar-day
                                            text-success
                                            me-1
                                        "></i>

                                    {{ \Carbon\Carbon::parse($record->Date)->format('Y-m-d') }}

                                </div>


                                <small class="text-muted">

                                    {{ $record->type === 'morning' ? 'رحلة الذهاب' : 'رحلة العودة' }}

                                </small>

                            </div>



                            {{-- الحالة --}}
                            @if ((bool) $record->Atend)
                                <span
                                    class="
                                        badge
                                        bg-success
                                        px-3
                                        py-2
                                    ">

                                    <i class="fas fa-check me-1"></i>

                                    حاضر

                                </span>
                            @else
                                <span
                                    class="
                                        badge
                                        bg-danger
                                        px-3
                                        py-2
                                    ">

                                    <i class="fas fa-times me-1"></i>

                                    غائب

                                </span>
                            @endif


                        </div>



                        {{-- معلومات إضافية --}}
                        <div class="row g-2 small">


                            <div class="col-12 col-md-6">

                                <div class="text-muted">

                                    <i class="fas fa-bus me-1"></i>

                                    السائق:

                                    <strong class="text-dark">

                                        {{ $record->driver?->Name ?? 'غير محدد' }}

                                    </strong>

                                </div>

                            </div>



                            <div class="col-12 col-md-6">

                                <div class="text-muted">

                                    <i class="fas fa-map-marker-alt me-1"></i>

                                    المنطقة:

                                    <strong class="text-dark">

                                        {{ $record->region?->Name ?? 'غير محددة' }}

                                    </strong>

                                </div>

                            </div>


                        </div>


                    </div>


                @empty


                    <div class="text-center py-5">


                        <i
                            class="
                                fas
                                fa-calendar-times
                                fa-3x
                                text-muted
                                opacity-25
                                mb-3
                            "></i>


                        <h6 class="text-muted">

                            لا توجد سجلات حضور لهذا الشهر

                        </h6>


                    </div>
                @endforelse


            </div>


        </div>
    @else
        <div class="card">

            <div class="card-body text-center py-5">

                <i
                    class="
                        fas
                        fa-user-graduate
                        fa-3x
                        text-muted
                        opacity-25
                        mb-3
                    "></i>

                <h5 class="text-muted">

                    لا يوجد أبناء مرتبطون بحسابك

                </h5>

                <p class="small text-muted mb-0">

                    يرجى مراجعة الإدارة لربط الطالب بولي الأمر.

                </p>

            </div>

        </div>


    @endif


</div>
