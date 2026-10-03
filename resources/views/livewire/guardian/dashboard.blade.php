<div class="container-fluid px-3 pb-4">


    {{-- الترحيب --}}
    <div class="card text-white mb-4
               position-relative
               overflow-hidden"
        style="
            background:
            linear-gradient(
                135deg,
                #198754,
                #20c997
            );
        ">

        <div class="position-absolute
                   end-0 top-0
                   opacity-25"
            style="
                transform:
                translate(
                    20%,
                    -25%
                );
            ">

            <i class="fas fa-users" style="font-size: 9rem;"></i>

        </div>


        <div class="card-body
                   p-4
                   position-relative">

            <div class="small mb-1">
                مرحباً بك
            </div>


            <h4 class="fw-bold mb-2">

                {{ $guardian->name }}

            </h4>


            <div class="small opacity-75">

                <i class="fas fa-phone me-1"></i>

                {{ $guardian->phone }}

            </div>

        </div>

    </div>



    {{-- إحصائية الأبناء --}}
    <div class="row mb-4">

        <div class="col-12">

            <div class="card">

                <div class="card-body">

                    <div
                        class="d-flex
                               align-items-center
                               justify-content-between">

                        <div>

                            <div
                                class="text-muted
                                       small
                                       mb-1">
                                عدد الأبناء المسجلين
                            </div>


                            <div
                                class="fw-bold
                                       fs-3
                                       text-success">
                                {{ $totalStudents }}
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
                                width: 60px;
                                height: 60px;
                            ">

                            <i class="fas fa-user-graduate
                                       fs-3"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- العنوان --}}
    <div class="d-flex
               justify-content-between
               align-items-center
               mb-3">

        <h5 class="fw-bold mb-0">

            <i class="fas fa-child
                       text-success
                       me-1"></i>

            أبنائي

        </h5>

    </div>



    {{-- الطلاب --}}
    <div class="row g-3">


        @forelse ($students as $student)
            <div class="col-12 col-md-6">


                <div class="card h-100">


                    <div class="card-body p-4">


                        {{-- اسم الطالب --}}
                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-start
                                   mb-3">


                            <div>

                                <h5
                                    class="fw-bold
                                           text-dark
                                           mb-1">

                                    {{ $student->Name }}

                                </h5>


                                <small class="text-muted">

                                    الرقم:
                                    {{ $student->id }}

                                </small>

                            </div>



                            {{-- الأساسي --}}
                            @if ($student->pivot->is_primary)
                                <span class="badge
                                           bg-success">
                                    ولي أساسي
                                </span>
                            @endif


                        </div>



                        {{-- صلة القرابة --}}
                        <div class="mb-3">

                            <span
                                class="badge
                                       bg-light
                                       text-dark
                                       border">

                                <i
                                    class="fas fa-link
                                           text-success
                                           me-1"></i>

                                صلة القرابة:

                                {{ $student->pivot->relationship ?: 'غير محددة' }}

                            </span>

                        </div>



                        <hr>



                        {{-- الصف --}}
                        <div
                            class="d-flex
                                   justify-content-between
                                   py-2">

                            <span class="text-muted">

                                <i class="fas fa-school
                                           me-1"></i>

                                الصف

                            </span>


                            <strong>

                                {{ $student->Grade }}

                                @if ($student->Division)
                                    /
                                    {{ $student->Division }}
                                @endif

                            </strong>

                        </div>



                        {{-- المنطقة --}}
                        <div
                            class="d-flex
                                   justify-content-between
                                   py-2">

                            <span class="text-muted">

                                <i class="fas fa-map-marker-alt
                                           me-1"></i>

                                المنطقة

                            </span>


                            <strong>

                                {{ $student->region?->Name ?? 'غير محددة' }}

                            </strong>

                        </div>



                        {{-- الموقف --}}
                        <div
                            class="d-flex
                                   justify-content-between
                                   py-2">

                            <span class="text-muted">

                                <i class="fas fa-location-dot
                                           me-1"></i>

                                الموقف

                            </span>


                            <strong>

                                {{ $student->Stu_position ?: 'غير محدد' }}

                            </strong>

                        </div>



                        {{-- المعلم --}}
                        <div
                            class="d-flex
                                   justify-content-between
                                   py-2">

                            <span class="text-muted">

                                <i
                                    class="fas fa-chalkboard-teacher
                                           me-1"></i>

                                المعلم/ة

                            </span>


                            <strong>

                                {{ $student->teacher?->Name ?? 'غير محدد' }}

                            </strong>

                        </div>



                        {{-- السائق --}}
                        <div
                            class="d-flex
                                   justify-content-between
                                   py-2">

                            <span class="text-muted">

                                <i class="fas fa-bus
                                           me-1"></i>

                                السائق

                            </span>


                            <strong>

                                {{ $student->driver?->Name ?? 'غير محدد' }}

                            </strong>

                        </div>



                        {{-- السنة --}}
                        <div
                            class="d-flex
                                   justify-content-between
                                   py-2">

                            <span class="text-muted">

                                <i class="fas fa-calendar-alt
                                           me-1"></i>

                                السنة الدراسية

                            </span>


                            <strong>

                                {{ $student->schoolYear?->year ?? 'غير محددة' }}

                            </strong>

                        </div>



                        {{-- الإشعارات --}}
                        <div
                            class="mt-3
                                   alert
                                   {{ $student->pivot->receive_notifications ? 'alert-success' : 'alert-secondary' }}
                                   py-2
                                   mb-0">

                            <i
                                class="fas
                                {{ $student->pivot->receive_notifications ? 'fa-bell' : 'fa-bell-slash' }}
                                me-1"></i>


                            @if ($student->pivot->receive_notifications)
                                استقبال إشعارات هذا الطالب مفعل
                            @else
                                استقبال إشعارات هذا الطالب غير مفعل
                            @endif

                        </div>


                    </div>

                </div>

            </div>


        @empty


            <div class="col-12">


                <div class="card">


                    <div
                        class="card-body
                               text-center
                               py-5">


                        <i
                            class="fas fa-user-graduate
                                   fa-3x
                                   text-muted
                                   opacity-25
                                   mb-3"></i>


                        <h5 class="text-muted">

                            لا يوجد أبناء مرتبطون بحسابك

                        </h5>


                        <p
                            class="text-muted
                                   small
                                   mb-0">

                            يرجى مراجعة الإدارة لربط الطلاب
                            بحساب ولي الأمر.

                        </p>


                    </div>

                </div>

            </div>
        @endforelse


    </div>


</div>
