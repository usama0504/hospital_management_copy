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
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PatientVisitController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ContactMessageController;

/*
|--------------------------------------------------------------------------
| Public Website Routes (CarePlus marketing site)
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'home'])->name('public.home');
Route::get('/about', [PublicController::class, 'about'])->name('public.about');
Route::get('/our-departments', [PublicController::class, 'departments'])->name('public.departments');
Route::get('/our-departments/{department}', [PublicController::class, 'departmentShow'])->name('public.departments.show');
Route::get('/our-doctors', [PublicController::class, 'doctors'])->name('public.doctors');
Route::get('/our-doctors/{doctor}', [PublicController::class, 'doctorShow'])->name('public.doctors.show');
Route::post('/our-doctors/{doctor}/reviews', [PublicController::class, 'reviewStore'])
    ->middleware('throttle:10,1')->name('public.doctors.reviews.store');
Route::get('/book-appointment', [PublicController::class, 'appointment'])->name('public.appointment');
Route::get('/book-appointment/slots', [PublicController::class, 'appointmentSlots'])
    ->middleware('throttle:60,1')->name('public.appointment.slots');
Route::post('/book-appointment', [PublicController::class, 'appointmentStore'])
    ->middleware('throttle:20,1')->name('public.appointment.store');
Route::get('/services', [PublicController::class, 'services'])->name('public.services');
Route::get('/contact', [PublicController::class, 'contact'])->name('public.contact');
Route::post('/contact', [PublicController::class, 'contactStore'])->name('public.contact.store');
Route::get('/gallery', [PublicController::class, 'gallery'])->name('public.gallery');
Route::get('/blog', [PublicController::class, 'blog'])->name('public.blog');

// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Forgot / reset password (sirf guests ke liye)
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])
        ->middleware('throttle:5,1')->name('password.store');
});

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
  
      Route::get('/patients/{patient}', [PatientController::class, 'show'])
        ->whereNumber('patient')
        ->name('patients.show');
        
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
       Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])
        ->whereNumber('doctor')
        ->name('doctors.show');


    // 4. Appointment Management
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');

    Route::middleware('permission:manage appointments')->group(function () {
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::get('/appointments/available-slots', [ AppointmentController::class, 'availableSlots' ])->name('appointments.available-slots');
    });

    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('appointments.destroy');


    // 5. Prescription Management
    // Create / edit: requires 'manage prescriptions' permission (doctor, admin).
    // Controller additionally restricts creation to doctors and ownership checks on edit.
    Route::middleware('permission:manage prescriptions')->group(function () {
        Route::get('/prescriptions/create', [PrescriptionController::class, 'create'])->name('prescriptions.create');
        Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
        Route::get('/prescriptions/{prescription}/edit', [PrescriptionController::class, 'edit'])->name('prescriptions.edit');
        Route::put('/prescriptions/{prescription}', [PrescriptionController::class, 'update'])->name('prescriptions.update');
    });

    // Viewing: any logged-in staff; PrescriptionController::authorizeAccess limits what each role sees.
    Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('prescriptions.show');

    Route::delete('/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('prescriptions.destroy');


    // 6. Billing & Payments (requires 'manage bills': admin, receptionist)
    Route::middleware('permission:manage bills')->group(function () {
        Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
        Route::get('/bills/create', [BillController::class, 'create'])->name('bills.create');
        Route::post('/bills', [BillController::class, 'store'])->name('bills.store');
        Route::get('/bills/{bill}/receipt', [BillController::class, 'receipt'])->name('bills.receipt');
        Route::get('/bills/{bill}/edit', [BillController::class, 'edit'])->name('bills.edit');
        Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.update');
    });

    Route::delete('/bills/{bill}', [BillController::class, 'destroy'])
        ->middleware(['permission:manage bills', 'role:admin'])
        ->name('bills.destroy');


    // 7. Doctor Availability
    Route::get('/doctor/availability/{doctor_id?}', [DoctorAvailabilityController::class, 'index'])->name('doctor.availability');
    Route::post('/doctor/availability/{doctor_id?}', [DoctorAvailabilityController::class, 'store'])->name('doctor.availability.store');
    Route::delete('/doctor/availability/{id}', [DoctorAvailabilityController::class, 'destroy'])->name('doctor.availability.destroy');
    Route::patch('/doctor/availability/{id}/toggle', [ DoctorAvailabilityController::class, 'toggle' ])->name('doctor.availability.toggle');


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

        // Public website moderation: doctor reviews
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('/reviews/{review}/hide', [ReviewController::class, 'hide'])->name('reviews.hide');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Soft-deleted records (restore) aur audit trail
        Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
        Route::patch('/trash/{type}/{id}/restore', [TrashController::class, 'restore'])->name('trash.restore');
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

        // Public website contact form messages
        Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::patch('/contact-messages/{contactMessage}/read', [ContactMessageController::class, 'markRead'])->name('contact-messages.read');
        Route::post('/contact-messages/{contactMessage}/reply', [ContactMessageController::class, 'reply'])->name('contact-messages.reply');
        Route::patch('/contact-messages/{contactMessage}/unread', [ContactMessageController::class, 'markUnread'])->name('contact-messages.unread');
        Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/patient-visit/create', [PatientVisitController::class, 'create'])->name('patient.visit.create');
        Route::post('/patient-visit', [PatientVisitController::class, 'store'])->name('patient.visit.store');
    });
});