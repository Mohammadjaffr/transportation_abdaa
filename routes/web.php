<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Auth;
Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
Route::get('/buses', [Dashboard::class, 'buses'])->name('buses');
Route::get('/drivers', [Dashboard::class, 'drivers'])->name('drivers'); 
Route::get('/students', [Dashboard::class, 'students'])->name('students');
Route::get('/locations', [Dashboard::class, 'locations'])->name('locations'); 
Route::get('/presentations', [Dashboard::class, 'presentations'])->name('presentations'); 
Route::get('/retreats', [Dashboard::class, 'retreats'])->name('retreats');
Route::get('/wages', [Dashboard::class, 'wages'])->name('wages');