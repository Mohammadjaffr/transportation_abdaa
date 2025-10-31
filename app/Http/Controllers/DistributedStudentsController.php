<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DistributedStudentsExport;
use App\Services\AdminLoggerService;

class DistributedStudentsController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['region', 'driver'])
            ->whereNotNull('region_id')
            ->whereNotNull('driver_id')
            ->whereNotNull('Stu_position');

        if ($request->has('search') && $request->search != '') {
            $query->where('Name', 'like', "%{$request->search}%");
        }

        $students = $query->paginate(10);

        AdminLoggerService::log(
            'عرض الطلاب الموزعين',
            'Student',
            '  تم عرض قائمة الطلاب الموزعين، العدد الحالي لطلاب' . $students->count()
        );

        return view('distributed-students.index', compact('students'));
    }


    // تصدير إلى Excel
    public function export()
    {
        return Excel::download(new DistributedStudentsExport, 'الطلاب الموزعين.xlsx');
    }
}