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
use App\Http\Controllers\PrescriptionController;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::middleware('permission:manage patients')->group(function () {
        Route::resource('patients', PatientController::class)->only(['create', 'store', 'edit', 'update']);
    });

    Route::resource('patients', PatientController::class)->only(['index']);

    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])
        ->middleware('role:admin')->name('patients.destroy');

    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');

    Route::middleware('role:admin')->group(function () {
        Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
        Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
        Route::get('/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
        Route::put('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
        Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
    });


    Route::middleware('permission:manage appointments')->group(function () {
        Route::resource('appointments', AppointmentController::class)->only(['create', 'store', 'edit', 'update']);
    });
    Route::resource('appointments', AppointmentController::class)->only(['index']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->middleware('role:admin')->name('appointments.destroy');

    // Prescriptions: doctor apni prescriptions likh/edit kar sakta hai, admin sab kuch,
    // receptionist sirf list aur read-only "show" (via controller-level checks).
    Route::resource('prescriptions', PrescriptionController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::delete('/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])
        ->middleware('role:admin')->name('prescriptions.destroy');

    // 1. Index route
    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');

    Route::get('/bills/create', [BillController::class, 'create'])->name('bills.create');
    Route::post('/bills', [BillController::class, 'store'])->name('bills.store');
    Route::get('bills/{bill}/receipt', [BillController::class, 'receipt'])->name('bills.receipt');
    // 3. Edit route
    Route::get('/bills/{bill}/edit', [BillController::class, 'edit'])->name('bills.edit');
    Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.update');

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

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});


use App\Http\Controllers\DoctorAvailabilityController;

Route::middleware(['auth'])->group(function () {
    Route::get('/doctor/availability/{doctor_id?}', [DoctorAvailabilityController::class, 'index'])->name('doctor.availability');
    Route::post('/doctor/availability/{doctor_id?}', [DoctorAvailabilityController::class, 'store'])->name('doctor.availability.store');
    Route::delete('/doctor/availability/{id}', [DoctorAvailabilityController::class, 'destroy'])->name('doctor.availability.destroy');
});

use App\Http\Controllers\ReceptionistController;

Route::middleware(['auth'])->group(function () {
    Route::get('/receptionist/today-doctors', [ReceptionistController::class, 'todayAvailability'])->name('receptionist.today');
});
