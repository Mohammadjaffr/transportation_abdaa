<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Auth;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect()->route('login');
});


Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [Dashboard::class, 'index'])->name('home');
    Route::get('/buses', [Dashboard::class, 'buses'])->name('buses');
    Route::get('/drivers', [Dashboard::class, 'drivers'])->name('drivers');
    Route::get('/students', [Dashboard::class, 'students'])->name('students');
    Route::get('/regions', [Dashboard::class, 'region'])->name('region');
    Route::get('/preparation-stus', [Dashboard::class, 'preparationStus'])->name('preparation-stus');
    Route::get('/preparation-drivers', [Dashboard::class, 'preparationDrivers'])->name('preparation-drivers');
    Route::get('/retreats', [Dashboard::class, 'retreats'])->name('retreats');
    Route::get('/wages', [Dashboard::class, 'wages'])->name('wages');
    Route::get('/distribution-stu', [Dashboard::class, 'distributionStu'])->name('distribution-stu');
    Route::get('/adminlogs', [Dashboard::class, 'adminlog'])->name('adminlogs');
    Route::get('/transfer-newyear', [Dashboard::class, 'transferNewyear'])->name('transfer-newyear');
    Route::get('/register', [Dashboard::class, 'register'])->name('register.form');
    Route::post('/register', [Dashboard::class, 'storeRegister'])->name('register.store');



    Route::get('/students/import', function () {
        return view('students.import');
    });
    Route::post('/students/import', function (Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return back()->with('success', 'تم استيراد الطلاب بنجاح');
    });
});
