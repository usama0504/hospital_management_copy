<?php

use Illuminate\Support\Facades\Route;

// Controllers Import
use App\Http\Controllers\Auth\AuthenticatedSessionController; // ya apka AuthController
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\DoctorAvailabilityController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientVisitController;
use App\Http\Controllers\DepartmentController;

/*
|--------------------------------------------------------------------------
| Public Website Routes (CarePlus marketing site)
|--------------------------------------------------------------------------
*/

Route::get('/', ['App\Http\Controllers\PublicController', 'home'])->name('public.home');
Route::get('/about', ['App\Http\Controllers\PublicController', 'about'])->name('public.about');
Route::get('/departments', ['App\Http\Controllers\PublicController', 'departments'])->name('public.departments');
Route::get('/departments/{department}', ['App\Http\Controllers\PublicController', 'departmentShow'])->name('public.departments.show');
Route::get('/our-doctors', ['App\Http\Controllers\PublicController', 'doctors'])->name('public.doctors');
Route::get('/our-doctors/{doctor}', ['App\Http\Controllers\PublicController', 'doctorShow'])->name('public.doctors.show');
Route::get('/book-appointment', ['App\Http\Controllers\PublicController', 'appointment'])->name('public.appointment');
Route::post('/book-appointment', ['App\Http\Controllers\PublicController', 'appointmentStore'])->name('public.appointment.store');
Route::get('/services', ['App\Http\Controllers\PublicController', 'services'])->name('public.services');
Route::get('/contact', ['App\Http\Controllers\PublicController', 'contact'])->name('public.contact');
Route::post('/contact', ['App\Http\Controllers\PublicController', 'contactStore'])->name('public.contact.store');
Route::get('/gallery', ['App\Http\Controllers\PublicController', 'gallery'])->name('public.gallery');
Route::get('/blog', ['App\Http\Controllers\PublicController', 'blog'])->name('public.blog');

// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // ya apka logout route


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Protected by 'auth' middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // 1. Dashboard & Reports
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // 2. Patient Management
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');

    Route::middleware('permission:manage patients')->group(function () {
        Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    });

    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('patients.destroy');


    // 3. Doctor Management
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');

    Route::middleware('role:admin')->group(function () {
        Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
        Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
        Route::get('/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
        Route::put('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
        Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
    });


    // 4. Appointment Management
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');

    Route::middleware('permission:manage appointments')->group(function () {
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::get('/appointments/available-slots', [AppointmentController::class, 'availableSlots'])->name('appointments.available-slots');
    });

    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('appointments.destroy');


    // 5. Prescription Management
    Route::resource('prescriptions', PrescriptionController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    Route::delete('/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('prescriptions.destroy');


    // 6. Billing & Payments
    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    Route::get('/bills/create', [BillController::class, 'create'])->name('bills.create');
    Route::post('/bills', [BillController::class, 'store'])->name('bills.store');
    Route::get('/bills/{bill}/receipt', [BillController::class, 'receipt'])->name('bills.receipt');
    Route::get('/bills/{bill}/edit', [BillController::class, 'edit'])->name('bills.edit');
    Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.update');

    Route::delete('/bills/{bill}', [BillController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('bills.destroy');


    // 7. Doctor Availability
    Route::get('/doctor/availability/{doctor_id?}', [DoctorAvailabilityController::class, 'index'])->name('doctor.availability');
    Route::post('/doctor/availability/{doctor_id?}', [DoctorAvailabilityController::class, 'store'])->name('doctor.availability.store');
    Route::delete('/doctor/availability/{id}', [DoctorAvailabilityController::class, 'destroy'])->name('doctor.availability.destroy');
    Route::patch('/doctor/availability/{id}/toggle', [DoctorAvailabilityController::class, 'toggle'])->name('doctor.availability.toggle');


    // 8. Receptionist Features
    Route::get('/receptionist/today-doctors', [ReceptionistController::class, 'todayAvailability'])->name('receptionist.today');

    // Departments Management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('departments', DepartmentController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });

    // 9. User Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');


    // 10. Admin Only (User Approvals & Management)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/patient-visit/create', [PatientVisitController::class, 'create'])->name('patient.visit.create');
        Route::post('/patient-visit', [PatientVisitController::class, 'store'])->name('patient.visit.store');
    });
});
