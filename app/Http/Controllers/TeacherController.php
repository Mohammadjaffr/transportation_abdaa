<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Services\AdminLoggerService;

class TeacherController extends Controller
{
    // عرض جميع المعلمين
    public function index()
    {
        $teachers = Teacher::all();
        return view('teacher.index', compact('teachers'));
    }

    // تخزين معلم جديد
    public function store(Request $request)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'Sex'  => 'required|string|max:10',
        ]);

      $teacher=   Teacher::create($request->only(['Name', 'Sex']));
        // تسجيل الإضافة في السجل
        AdminLoggerService::log(
            'إضافة المعلم',
            'Teacher',
            "تمت إضافة المعلم: {$teacher->Name}"
        );
        return redirect()->route('teacher.index')->with('success', 'تمت إضافة المعلم بنجاح');
    }

    // عرض نموذج التعديل
    public function edit($id)
    {
        $teacher = Teacher::findOrFail($id);
        // تسجيل الوصول إلى صفحة التعديل في السجل
        AdminLoggerService::log(
            'وصول إلى صفحة التعديل',
            'Teacher',
            "تم الوصول إلى صفحة التعديل للمعلم: {$teacher->Name}"
        );
        return view('teacher.edit', compact('teacher'));
    }

    // تحديث بيانات المعلم
    public function update(Request $request, $id)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'Sex'  => 'required|string|max:10',
        ]);

        $teacher = Teacher::findOrFail($id);
        $teacher->update($request->only(['Name', 'Sex']));
        // تسجيل التحديث في السجل
        AdminLoggerService::log(
            'تحديث بيانات المعلم',
            'Teacher',
            "تم تحديث بيانات المعلم: {$teacher->Name}"
        );

        return redirect()->route('teacher.index')->with('success', 'تم تحديث بيانات المعلم بنجاح');
    }

    // حذف المعلم
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        // تسجيل الحذف في السجل
        AdminLoggerService::log(
            'حذف المعلم',
            'Teacher',
            "تم حذف المعلم: {$teacher->Name}"
        );
        $teacher->delete();

        return redirect()->route('teacher.index')->with('success', 'تم حذف المعلم بنجاح');
    }
}