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

    Route::middleware('permission:manage patients')->group(function () {
        Route::resource('patients', PatientController::class)->only(['create', 'store', 'edit', 'update']);
    });

    Route::resource('patients', PatientController::class)->only(['index', 'show']);

    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])
        ->middleware('role:admin')->name('patients.destroy');

    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');

    // 2. Edit & Update routes
    Route::get('/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
    Route::put('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');

    // 3. Show route (Agar show method nahi hai, toh is line ko hata dein ya controller mein show() method bana dein)
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');

    // 4. Delete route
    Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('doctors.destroy');


    // Appointments: anyone logged in can view; managing needs permission; deleting needs admin
    Route::middleware('permission:manage appointments')->group(function () {
        Route::resource('appointments', AppointmentController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::resource('appointments', AppointmentController::class)->only(['index', 'show']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->middleware('role:admin')->name('appointments.destroy');

    // 1. Index route
    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');

    // 2. Create route (Yeh lazmi {bill} wale route se UPAR honi chahiye)
    Route::get('/bills/create', [BillController::class, 'create'])->name('bills.create');
    Route::post('/bills', [BillController::class, 'store'])->name('bills.store');

    // 3. Edit route
    Route::get('/bills/{bill}/edit', [BillController::class, 'edit'])->name('bills.edit');
    Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.update');

    // 4. Show route (Yeh {bill} wali dynamic route hai, isay neeche rakhein)
    Route::get('/bills/{bill}', [BillController::class, 'show'])->name('bills.show');

    // 5. Delete route
    Route::delete('/bills/{bill}', [BillController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('bills.destroy');

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
