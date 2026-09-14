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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); // Naya Register Route

/*
|--------------------------------------------------------------------------
| Protected API Routes (Requires Sanctum Bearer Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth Logout Route (Token revoke karne ke liye)
    Route::post('/logout', [AuthController::class, 'logout']);

    // Authenticated User Profile Route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // User Management Routes
    Route::get('/users', [UserController::class, 'index']);
    Route::patch('/users/{id}/approve', [UserController::class, 'approve']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Appointment Management Routes
    Route::get('/appointments/create-data', [AppointmentController::class, 'createData']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);

    // Patient Management Routes
    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::get('/patients/{patient}', [PatientController::class, 'show']);
    Route::put('/patients/{patient}', [PatientController::class, 'update']);
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy']);

    // Bill Management Routes
    Route::get('/bills', [BillController::class, 'index']);
    Route::post('/bills', [BillController::class, 'store']);
    Route::get('/bills/{bill}', [BillController::class, 'show']);
    Route::put('/bills/{bill}', [BillController::class, 'update']);
    Route::delete('/bills/{bill}', [BillController::class, 'destroy']);
    Route::get('/bills/{bill}/receipt', [BillController::class, 'receipt']);

    // Doctor Availability Routes
    Route::get('/doctor-availabilities/{doctor_id?}', [DoctorAvailabilityController::class, 'index']);
    Route::post('/doctor-availabilities', [DoctorAvailabilityController::class, 'store']);
    Route::delete('/doctor-availabilities/{id}', [DoctorAvailabilityController::class, 'destroy']);

    // Doctor Management Routes
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::post('/doctors', [DoctorController::class, 'store']);
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);
    Route::put('/doctors/{doctor}', [DoctorController::class, 'update']);
    Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy']);

    // Profile Management Routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword']);

    // Receptionist / Today's Availability Route
    Route::get('/receptionist/today-availability', [ReceptionistController::class, 'todayAvailability']);

    // Reports Route
    Route::get('/reports', [ReportController::class, 'index']);
});