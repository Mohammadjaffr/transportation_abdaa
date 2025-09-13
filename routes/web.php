<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Auth;
Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Auth::routes(['register' => false]);

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/home', [Dashboard::class, 'index'])->name('home');
Route::get('/buses', [Dashboard::class, 'buses'])->name('buses');
Route::get('/drivers', [Dashboard::class, 'drivers'])->name('drivers'); 
Route::get('/students', [Dashboard::class, 'students'])->name('students');
Route::get('/regions', [Dashboard::class, 'region'])->name('region'); 
Route::get('/preparation-stus', [Dashboard::class, 'preparationStus'])->name('preparation-stus');
Route::get('/preparation-drivers', [Dashboard::class, 'preparationDrivers'])->name('preparation-drivers');
Route::get('/retreats', [Dashboard::class, 'retreats'])->name('retreats');
Route::get('/wages', [Dashboard::class, 'wages'])->name('wages');