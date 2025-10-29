<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Teacher;
use App\Services\AdminLoggerService;

class Dashboard extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }
    public function buses()
    {
        return view('buses.index');
    }
    public function drivers()
    {
        return view('drivers.index');
    }

    public function students()
    {
        return view('Students.index');
    }
    public function region()
    {
        return view('regions.index');
    }
    public function presentations()
    {
        return view('presentations.index');
    }
    public function retreats()
    {
        return view('retreats.index');
    }
    public function wages()
    {
        return view('wages.index');
    }
    public function preparationStus()
    {
        return view('preparation_stus.index');
    }
    public function preparationDrivers()
    {
        return view('preparation_drivers.index');
    }
    public function distributionStu()
    {
        return view('distribution-stu.index');
    }
    public function adminlog()
    {
        return view('adminlogs.index');
    }
    public function transferNewyear()
    {
        return view('TransferNewyear.index');
    }

    public function register()
    {
        return view('Register.index');
    }
    
    public function indexteacher()
    {
        $teachers = Teacher::all();
        return view('teacher.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'Sex' => 'required|string|max:10',
        ]);

        Teacher::create($request->only(['Name', 'Sex']));

        return redirect()->route('teacher.index')->with('success', 'تمت إضافة المعلم بنجاح');
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();
        return redirect()->route('teacher.index')->with('success', 'تم حذف المعلم بنجاح');
    }
}