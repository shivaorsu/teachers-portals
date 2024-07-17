<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\StudentController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/home', [StudentController::class, 'index'])->name('home'); // This should be a GET route
    Route::post('/students', [StudentController::class, 'store'])->name('students.store'); // POST route for form submission
    Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update'); // PUT route for updating records
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy'); // DELETE route for deleting records
    Route::post('/students/check-duplicate', [StudentController::class, 'checkDuplicate'])->name('students.checkDuplicate'); // POST route for AJAX check
});

