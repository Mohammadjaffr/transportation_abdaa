<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

use App\Models\Student;
use App\Models\Wing;
use App\Models\Region;
use App\Models\Teacher;
use App\Models\SchoolYear;
use App\Models\Guardian;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Exports\StudentsExport;

use App\Services\AdminLoggerService;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Students extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $wings;
    public $regions;
    public $teachers;

    public $name;
    public $grade;
    public $sex;
    public $phone;

    public $stu_position;
    public $wing_id;
    public $division;
    public $region_id;
    public $teacher_id;

    public $deleteName;

    public $editMode = false;
    public $selectedStudentId;
    public $editId = null;

    public $primary_image;

    public $showForm = false;

    public $deleteId = null;

    public $search = '';

    public $child_region_id;

    public $showImportForm = false;

    public $excelFile;

    public $showImportModal = false;

    /*
    |--------------------------------------------------------------------------
    | أولياء أمور الطالب
    |--------------------------------------------------------------------------
    |
    | كل عنصر يمثل ولي أمر مرتبط بالطالب مع بيانات جدول guardian_student
    |
    */
    public $guardianLinks = [
        [
            'guardian_id' => '',
            'relationship' => '',
            'is_primary' => false,
            'receive_notifications' => true,
        ],
    ];


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected $rules = [
        'name' => 'required|string|max:200',
        'grade' => 'required|string|max:30',
        'sex' => 'required|string|max:20',

        'phone' => 'nullable|string|max:20',

        'child_region_id' => 'required|string|max:200',

        'wing_id' => 'required|exists:wings,id',

        'division' => 'required|string|max:20',

        'region_id' => 'required|exists:regions,id',

        'teacher_id' => 'nullable|exists:teachers,id',

        /*
         * أولياء الأمور
         */
        'guardianLinks' => 'array',

        'guardianLinks.*.guardian_id' =>
        'nullable|exists:guardians,id',

        'guardianLinks.*.relationship' =>
        'nullable|string|max:30',

        'guardianLinks.*.is_primary' =>
        'boolean',

        'guardianLinks.*.receive_notifications' =>
        'boolean',
    ];


    protected $messages = [
        'name.required' =>
        'يرجى إدخال اسم الطالب',

        'grade.required' =>
        'يرجى إدخال الصف',

        'sex.required' =>
        'يرجى إدخال النوع',

        'child_region_id.required' =>
        'يرجى إدخال الموقف',

        'wing_id.required' =>
        'يرجى إدخال الجناح',

        'division.required' =>
        'يرجى إدخال الشعبة',

        'region_id.required' =>
        'يرجى إدخال المنطقة',

        'guardianLinks.*.guardian_id.exists' =>
        'ولي الأمر المحدد غير موجود.',

        'guardianLinks.*.relationship.max' =>
        'صلة القرابة طويلة أكثر من المسموح.',
    ];


    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount()
    {
        $this->wings = Wing::all();

        $this->regions = Region::all();

        $this->teachers = Teacher::all();
    }


    /*
    |--------------------------------------------------------------------------
    | Pagination Search
    |--------------------------------------------------------------------------
    */

    public function updatedSearch()
    {
        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Guardian Rows
    |--------------------------------------------------------------------------
    */

    public function addGuardianRow()
    {
        $this->guardianLinks[] = [
            'guardian_id' => '',
            'relationship' => '',
            'is_primary' => false,
            'receive_notifications' => true,
        ];
    }


    public function removeGuardianRow($index)
    {
        if (!isset($this->guardianLinks[$index])) {
            return;
        }

        unset($this->guardianLinks[$index]);

        /*
         * إعادة ترتيب indexes حتى لا تحدث مشكلة في Livewire
         */
        $this->guardianLinks =
            array_values($this->guardianLinks);

        /*
         * نترك دائمًا صفًا فارغًا واحدًا على الأقل
         */
        if (empty($this->guardianLinks)) {
            $this->guardianLinks = [
                [
                    'guardian_id' => '',
                    'relationship' => '',
                    'is_primary' => false,
                    'receive_notifications' => true,
                ],
            ];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | تحديد ولي الأمر الأساسي
    |--------------------------------------------------------------------------
    */

    public function setPrimaryGuardian($index)
    {
        /*
         * إزالة primary من الجميع
         */
        foreach (
            $this->guardianLinks as $key => $row
        ) {
            $this->guardianLinks[$key]['is_primary'] = false;
        }

        /*
         * جعل المحدد هو الأساسي
         */
        if (isset($this->guardianLinks[$index])) {
            $this->guardianLinks[$index]['is_primary'] = true;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | تجهيز بيانات Pivot
    |--------------------------------------------------------------------------
    */

    private function guardianSyncData(): array
    {
        $syncData = [];

        $usedGuardianIds = [];

        $primaryCount = 0;


        foreach (
            $this->guardianLinks as $index => $row
        ) {

            $guardianId =
                $row['guardian_id'] ?? null;


            /*
             * إذا كان الصف فارغًا نتجاهله
             */
            if (empty($guardianId)) {
                continue;
            }


            $guardianId = (int) $guardianId;


            /*
             * منع اختيار نفس ولي الأمر مرتين
             */
            if (
                isset(
                    $usedGuardianIds[$guardianId]
                )
            ) {

                throw ValidationException::withMessages([
                    "guardianLinks.$index.guardian_id" =>
                    'تم اختيار ولي الأمر نفسه أكثر من مرة.',
                ]);
            }


            $usedGuardianIds[$guardianId] = true;


            /*
             * صلة القرابة مطلوبة
             * إذا تم اختيار ولي أمر
             */
            $relationship = trim(
                $row['relationship'] ?? ''
            );


            if ($relationship === '') {

                throw ValidationException::withMessages([
                    "guardianLinks.$index.relationship" =>
                    'يرجى تحديد صلة القرابة.',
                ]);
            }


            $isPrimary = (bool) (
                $row['is_primary'] ?? false
            );


            if ($isPrimary) {
                $primaryCount++;
            }


            $syncData[$guardianId] = [

                'relationship' =>
                $relationship,

                'is_primary' =>
                $isPrimary,

                'receive_notifications' =>
                (bool) (
                    $row['receive_notifications']
                    ?? false
                ),
            ];
        }


        /*
         * إذا تم اختيار أي ولي أمر
         * فيجب تحديد ولي أساسي واحد
         */
        if (
            !empty($syncData)
            && $primaryCount === 0
        ) {

            throw ValidationException::withMessages([
                'guardianLinks' =>
                'يجب تحديد ولي أمر أساسي واحد.',
            ]);
        }


        /*
         * حماية إضافية:
         * لا نسمح بأكثر من ولي أساسي
         */
        if ($primaryCount > 1) {

            throw ValidationException::withMessages([
                'guardianLinks' =>
                'يمكن تحديد ولي أمر أساسي واحد فقط.',
            ]);
        }


        return $syncData;
    }


    /*
    |--------------------------------------------------------------------------
    | Import Modal
    |--------------------------------------------------------------------------
    */

    public function closeImportModal()
    {
        $this->showImportModal = false;
    }


    /*
    |--------------------------------------------------------------------------
    | Import Excel
    |--------------------------------------------------------------------------
    */

    public function importExcel()
    {
        $this->validate(
            [
                'excelFile' =>
                'required|mimes:xlsx,csv',
            ],
            [
                'excelFile.required' =>
                'يرجى اختيار ملف Excel',

                'excelFile.mimes' =>
                'يجب أن يكون الملف بصيغة Excel (xlsx) أو CSV فقط',
            ]
        );


        $import = new StudentsImport();


        Excel::import(
            $import,
            $this->excelFile->getRealPath()
        );


        if (
            $import->failures()->isNotEmpty()
        ) {

            $labels = [

                'name' =>
                'عمود الاسم',

                'grad' =>
                'عمود الصف',

                'sex' =>
                'عمود النوع',

                'phone' =>
                'عمود الهاتف',

                'stu_position' =>
                'عمود الموقف',

                'wing' =>
                'عمود الجناح',

                'region' =>
                'عمود المنطقة',

                'teacher' =>
                'عمود المعلم',

                'division' =>
                'عمود الشعبة',
            ];


            foreach (
                $import->failures() as $failure
            ) {

                $row =
                    $failure->row();

                $attr =
                    $failure->attribute();

                $value =
                    $failure
                        ->values()[$attr]
                    ?? '';

                $label =
                    $labels[$attr]
                    ?? $attr;


                foreach (
                    $failure->errors() as $msg
                ) {

                    $pretty =
                        "الصف {$row} – {$label}: {$msg}"
                        . (
                            $value !== ''
                            ? " (القيمة: {$value})"
                            : ''
                        );


                    $this->addError(
                        'excelFile',
                        $pretty
                    );
                }
            }


            return;
        }


        $fileName =
            $this->excelFile
            ->getClientOriginalName();


        AdminLoggerService::log(
            'استيراد ملف Excel لطلاب',
            'Student',
            "تم استيراد الطلاب من الملف: {$fileName}"
        );


        $this->reset(
            'excelFile',
            'showImportForm'
        );


        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تم استيراد الطلاب بنجاح'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Export Excel
    |--------------------------------------------------------------------------
    */

    public function exportExcel()
    {
        return Excel::download(
            new StudentsExport,
            'كشف الطلاب.xlsx'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Import
    |--------------------------------------------------------------------------
    */

    public function resetImportForm()
    {
        $this->reset(
            'excelFile',
            'showImportForm'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Student
    |--------------------------------------------------------------------------
    */

    public function createStudent()
    {
        /*
         * التحقق من بيانات الطالب
         */
        $this->validate();


        /*
         * تجهيز بيانات أولياء الأمور
         */
        $guardianSyncData =
            $this->guardianSyncData();


        /*
         * البحث عن السنة الحالية
         */
        $year = SchoolYear::where(
            'is_current',
            true
        )->first();


        /*
         * إنشاء سنة حالية إذا لم توجد
         */
        if (!$year) {

            $year = SchoolYear::create([

                'year' =>
                now()->year,

                'name' =>
                now()->year
                    . '-'
                    . (
                        now()->year + 1
                    ),

                'start_date' =>
                now()->startOfYear(),

                'end_date' =>
                now()->endOfYear(),

                'is_current' =>
                true,
            ]);
        }


        /*
         * إنشاء الطالب وعلاقاته
         * داخل Transaction واحدة
         */
        DB::transaction(
            function () use (
                $year,
                $guardianSyncData
            ) {

                $student =
                    Student::create([

                        'Name' =>
                        $this->name,

                        'Grade' =>
                        $this->grade,

                        'Sex' =>
                        $this->sex,

                        'Phone' =>
                        $this->phone,

                        'Stu_position' =>
                        $this->child_region_id,

                        'wing_id' =>
                        $this->wing_id,

                        'Division' =>
                        $this->division,

                        'region_id' =>
                        $this->region_id,

                        'teacher_id' =>
                        $this->teacher_id
                            ?: null,

                        'school_year_id' =>
                        $year->id,
                    ]);


                /*
                 * ربط أولياء الأمور
                 */
                $student
                    ->guardians()
                    ->sync(
                        $guardianSyncData
                    );
            }
        );


        AdminLoggerService::log(
            'اضافة طالب',
            'Student',
            "إضافة طالب جديد: {$this->name}"
        );


        $this->resetForm();


        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تم إضافة الطالب والسنة الدراسية بنجاح'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Student
    |--------------------------------------------------------------------------
    */

    public function editStudent($id)
    {
        /*
         * تحميل ولي الأمر والـ Pivot
         */
        $student =
            Student::with('guardians')
            ->findOrFail($id);


        $this->selectedStudentId =
            $student->id;

        $this->editMode = true;

        $this->showForm = true;

        $this->editId =
            $student->id;


        /*
         * بيانات الطالب
         */
        $this->name =
            $student->Name;

        $this->grade =
            $student->Grade;

        $this->sex =
            $student->Sex;

        $this->phone =
            $student->Phone;

        $this->stu_position =
            $student->Stu_position;

        $this->wing_id =
            $student->wing_id;

        $this->division =
            $student->Division;

        $this->region_id =
            $student->region_id;

        $this->child_region_id =
            $student->Stu_position;

        $this->teacher_id =
            $student->teacher_id;


        /*
         * تحميل أولياء أمر الطالب
         */
        $this->guardianLinks =
            $student
            ->guardians
            ->map(
                function ($guardian) {

                    return [

                        'guardian_id' =>
                        (string)
                        $guardian->id,

                        'relationship' =>
                        $guardian
                            ->pivot
                            ->relationship
                            ?? '',

                        'is_primary' =>
                        (bool)
                        $guardian
                            ->pivot
                            ->is_primary,

                        'receive_notifications' =>
                        (bool)
                        $guardian
                            ->pivot
                            ->receive_notifications,
                    ];
                }
            )
            ->values()
            ->toArray();


        /*
         * لو الطالب القديم ليس له ولي أمر
         */
        if (
            empty($this->guardianLinks)
        ) {

            $this->guardianLinks = [
                [
                    'guardian_id' => '',
                    'relationship' => '',
                    'is_primary' => false,
                    'receive_notifications' => true,
                ],
            ];
        }


        $this->resetValidation();
    }


    /*
    |--------------------------------------------------------------------------
    | Update Student
    |--------------------------------------------------------------------------
    */

    public function updateStudent()
    {
        /*
         * كان Validation معطل في الملف السابق.
         * الآن نفعله.
         */
        $this->validate();


        /*
         * تجهيز أولياء الأمور
         */
        $guardianSyncData =
            $this->guardianSyncData();


        $student =
            Student::findOrFail(
                $this->selectedStudentId
            );


        /*
         * تحديث الطالب وعلاقاته
         * كعملية واحدة
         */
        DB::transaction(
            function () use (
                $student,
                $guardianSyncData
            ) {

                $student->update([

                    'Name' =>
                    $this->name,

                    'Grade' =>
                    $this->grade,

                    'Sex' =>
                    $this->sex,

                    'Phone' =>
                    $this->phone,

                    'Stu_position' =>
                    $this->child_region_id,

                    'wing_id' =>
                    $this->wing_id,

                    'Division' =>
                    $this->division,

                    'region_id' =>
                    $this->region_id,

                    'teacher_id' =>
                    $this->teacher_id
                        ?: null,
                ]);


                /*
                 * sync:
                 *
                 * - يضيف الجديد
                 * - يحدث الموجود
                 * - يحذف الرابط الذي تمت إزالته
                 */
                $student
                    ->guardians()
                    ->sync(
                        $guardianSyncData
                    );
            }
        );


        AdminLoggerService::log(
            'تحديث طالب',
            'Student',
            "تحديث طالب: {$this->name}"
        );


        $this->resetForm();


        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تم تحديث الطالب وأولياء الأمور بنجاح'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Confirm Delete
    |--------------------------------------------------------------------------
    */

    public function confirmDelete(
        $studentId
    ) {

        $student =
            Student::findOrFail(
                $studentId
            );


        $this->deleteId =
            $studentId;

        $this->deleteName =
            $student->Name;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Student
    |--------------------------------------------------------------------------
    */

    public function deleteStudent()
    {
        if (!$this->deleteId) {
            return;
        }


        $student =
            Student::findOrFail(
                $this->deleteId
            );


        $studentName =
            $student->Name;


        /*
         * روابط guardian_student
         * ستزال بواسطة cascadeOnDelete
         * الموجود في Migration.
         */
        $student->delete();


        AdminLoggerService::log(
            'حذف طالب',
            'Student',
            "حذف طالب: {$studentName}"
        );


        $this->deleteId = null;

        $this->deleteName = null;


        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تم حذف الطالب بنجاح'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Student Form
    |--------------------------------------------------------------------------
    */

    public function resetForm()
    {
        $this->reset([
            'editId',
            'selectedStudentId',

            'name',
            'grade',
            'sex',
            'phone',

            'stu_position',

            'wing_id',
            'division',

            'region_id',
            'child_region_id',

            'teacher_id',

            'editMode',
            'showForm',
        ]);


        $this->primary_image = null;


        /*
         * إعادة ولي الأمر للحالة الافتراضية
         */
        $this->guardianLinks = [
            [
                'guardian_id' => '',
                'relationship' => '',
                'is_primary' => false,
                'receive_notifications' => true,
            ],
        ];


        $this->resetValidation();
    }


    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        /*
         * الطلاب
         */
        $students = Student::with([
            'wing',
            'region',
            'teacher',
            'driver',
            'schoolYear',
            'guardians',
        ])

            ->when(
                $this->search,
                function ($query) {

                    $searchTerm =
                        '%'
                        . trim(
                            $this->search
                        )
                        . '%';


                    /*
                     * نجمع البحث كله داخل where
                     * حتى لا تحدث مشاكل OR
                     */
                    $query->where(
                        function ($q) use (
                            $searchTerm
                        ) {

                            /*
                             * بيانات الطالب
                             */
                            $q->where(
                                'Name',
                                'like',
                                $searchTerm
                            )

                                ->orWhere(
                                    'id',
                                    'like',
                                    $searchTerm
                                )

                                ->orWhere(
                                    'Phone',
                                    'like',
                                    $searchTerm
                                )

                                ->orWhere(
                                    'Stu_position',
                                    'like',
                                    $searchTerm
                                )

                                ->orWhere(
                                    'Grade',
                                    'like',
                                    $searchTerm
                                )

                                ->orWhere(
                                    'Division',
                                    'like',
                                    $searchTerm
                                );


                            /*
                             * المنطقة
                             */
                            $q->orWhereHas(
                                'region',
                                function ($regionQuery) use (
                                    $searchTerm
                                ) {

                                    $regionQuery->where(
                                        'Name',
                                        'like',
                                        $searchTerm
                                    );
                                }
                            );


                            /*
                             * السائق
                             */
                            $q->orWhereHas(
                                'driver',
                                function ($driverQuery) use (
                                    $searchTerm
                                ) {

                                    $driverQuery->where(
                                        'Name',
                                        'like',
                                        $searchTerm
                                    );
                                }
                            );


                            /*
                             * المعلم
                             */
                            $q->orWhereHas(
                                'teacher',
                                function ($teacherQuery) use (
                                    $searchTerm
                                ) {

                                    $teacherQuery->where(
                                        'Name',
                                        'like',
                                        $searchTerm
                                    );
                                }
                            );


                            /*
                             * ولي الأمر
                             */
                            $q->orWhereHas(
                                'guardians',
                                function ($guardianQuery) use (
                                    $searchTerm
                                ) {

                                    $guardianQuery
                                        ->where(
                                            'guardians.name',
                                            'like',
                                            $searchTerm
                                        )

                                        ->orWhere(
                                            'guardians.phone',
                                            'like',
                                            $searchTerm
                                        );
                                }
                            );
                        }
                    );
                }
            )

            ->orderBy(
                'id',
                'desc'
            )

            ->paginate(10);


        /*
         * المواقف التابعة للمنطقة
         */
        $children_regions =
            $this->region_id
            ? Region::where(
                'parent_id',
                $this->region_id
            )->get()
            : null;


        /*
         * المناطق الرئيسية
         */
        $parent_regions =
            Region::whereNull(
                'parent_id'
            )->get();


        /*
         * أولياء الأمور
         *
         * نعرض المفعل أولًا،
         * لكن نبقي الموقوفين حتى يظهر ولي الأمر
         * القديم عند تعديل الطالب.
         */
        $guardians =
            Guardian::orderByDesc(
                'is_active'
            )
            ->orderBy(
                'name'
            )
            ->get();


        return view(
            'livewire.students',
            compact(
                'students',
                'children_regions',
                'parent_regions',
                'guardians'
            )
        );
    }
}