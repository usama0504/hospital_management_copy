<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Patients: anyone logged in can view; managing needs permission; deleting needs admin
    Route::resource('patients', PatientController::class)->only(['index', 'show']);
    Route::middleware('permission:manage patients')->group(function () {
        Route::resource('patients', PatientController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])
        ->middleware('role:admin')->name('patients.destroy');

    // Doctors: anyone logged in can view; only admin can manage
    Route::resource('doctors', DoctorController::class)->only(['index', 'show']);
    Route::middleware('role:admin')->group(function () {
        Route::resource('doctors', DoctorController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    });

    // Appointments: anyone logged in can view; managing needs permission; deleting needs admin
    Route::resource('appointments', AppointmentController::class)->only(['index', 'show']);
    Route::middleware('permission:manage appointments')->group(function () {
        Route::resource('appointments', AppointmentController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->middleware('role:admin')->name('appointments.destroy');

    // Bills: anyone logged in can view; managing needs permission; deleting needs admin
    Route::resource('bills', BillController::class)->only(['index', 'show']);
    Route::middleware('permission:manage bills')->group(function () {
        Route::resource('bills', BillController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::delete('/bills/{bill}', [BillController::class, 'destroy'])
        ->middleware('role:admin')->name('bills.destroy');

    // User management (approve pending registrations) - admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

use App\Http\Controllers\AuthController;
// Registration Routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');