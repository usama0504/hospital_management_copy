<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\DoctorAvailabilityController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReceptionistController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\PrescriptionController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1'); // Naya Register Route

/*
|--------------------------------------------------------------------------
| Protected API Routes (Requires Sanctum Bearer Token)
|--------------------------------------------------------------------------
| Role/permission rules web.php jaisi hi hain. Pehle yahan sirf 'auth:sanctum'
| tha, is wajah se koi bhi logged-in user (doctor/receptionist) users approve
| ya delete, doctors create/delete kar sakta tha.
*/
Route::middleware(['auth:sanctum', 'not.patient'])->group(function () {

    // Auth Logout Route (Token revoke karne ke liye)
    Route::post('/logout', [AuthController::class, 'logout']);

    // Authenticated User Profile Route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // User Management Routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::patch('/users/{id}/approve', [UserController::class, 'approve']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    // Appointment Management Routes
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::middleware('permission:manage appointments')->group(function () {
        Route::get('/appointments/create-data', [AppointmentController::class, 'createData']);
        Route::post('/appointments', [AppointmentController::class, 'store']);
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update']);
    });
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->middleware('role:admin');

    // Patient Management Routes
    Route::get('/patients', [PatientController::class, 'index']);
    Route::middleware('permission:manage patients')->group(function () {
        Route::post('/patients', [PatientController::class, 'store']);
        Route::put('/patients/{patient}', [PatientController::class, 'update']);
    });
    Route::get('/patients/{patient}', [PatientController::class, 'show']);
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])
        ->middleware('role:admin');

    // Bill Management Routes (admin + receptionist; delete sirf admin)
    Route::middleware('permission:manage bills')->group(function () {
        Route::get('/bills', [BillController::class, 'index']);
        Route::post('/bills', [BillController::class, 'store']);
        Route::get('/bills/{bill}', [BillController::class, 'show']);
        Route::put('/bills/{bill}', [BillController::class, 'update']);
        Route::get('/bills/{bill}/receipt', [BillController::class, 'receipt']);
        Route::delete('/bills/{bill}', [BillController::class, 'destroy'])
            ->middleware('role:admin');
    });

    // Prescription Management Routes
    Route::get('/prescriptions', [PrescriptionController::class, 'index']);
    Route::middleware('permission:manage prescriptions')->group(function () {
        Route::post('/prescriptions', [PrescriptionController::class, 'store']);
        Route::put('/prescriptions/{prescription}', [PrescriptionController::class, 'update']);
    });
    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show']);
    Route::delete('/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])
        ->middleware('role:admin');

    // Doctor Availability Routes (controller khud role ke hisaab se limit karta hai)
    Route::get('/doctor-availabilities/{doctor_id?}', [DoctorAvailabilityController::class, 'index']);
    Route::post('/doctor-availabilities', [DoctorAvailabilityController::class, 'store']);
    Route::delete('/doctor-availabilities/{id}', [DoctorAvailabilityController::class, 'destroy']);

    // Doctor Management Routes (list/detail sab ke liye, create/edit/delete sirf admin)
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::middleware('role:admin')->group(function () {
        Route::post('/doctors', [DoctorController::class, 'store']);
        Route::put('/doctors/{doctor}', [DoctorController::class, 'update']);
        Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy']);
    });
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);

    // Profile Management Routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword']);

    // Receptionist / Today's Availability Route
    Route::get('/receptionist/today-availability', [ReceptionistController::class, 'todayAvailability']);

    // Reports Route
    Route::get('/reports', [ReportController::class, 'index']);
});
