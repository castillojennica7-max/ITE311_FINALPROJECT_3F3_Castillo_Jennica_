<?php

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\SalonController as AdminSalonController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner\AppointmentController as OwnerAppointmentController;
use App\Http\Controllers\Owner\CustomerController as OwnerCustomerController;
use App\Http\Controllers\Owner\SalonController as OwnerSalonController;
use App\Http\Controllers\Owner\StaffController as OwnerStaffController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// Public home page (content galing sa Strapi)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Para sa lahat ng naka-login na user
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Para sa customers lang ang pag-book
    Route::middleware('role:customer')->group(function () {
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::get('/salons/{salon}/book', [AppointmentController::class, 'book'])->name('appointments.book');
        Route::post('/salons/{salon}/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    });

    // Para sa salon owners lang ang subscription
    Route::middleware('role:salon_owner')->group(function () {
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/plans/{plan}/subscribe', [SubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('/plans/{plan}/subscribe', [SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::patch('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    });
});

// Para sa platform admin lang
Route::middleware(['auth', 'role:admin'])
    ->prefix('manage')
    ->name('admin.')
    ->group(function () {
        Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::patch('/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.status');
        Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/salons', [AdminSalonController::class, 'index'])->name('salons.index');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    });

// Para sa salon owner na may aktibong subscription
Route::middleware(['auth', 'role:salon_owner', 'subscribed'])
    ->prefix('salon')
    ->name('owner.')
    ->group(function () {
        Route::get('/appointments', [OwnerAppointmentController::class, 'index'])->name('appointments.index');
        Route::patch('/appointments/{appointment}/status', [OwnerAppointmentController::class, 'updateStatus'])->name('appointments.status');

        Route::get('/staff', [OwnerStaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [OwnerStaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [OwnerStaffController::class, 'store'])->name('staff.store');
        Route::delete('/staff/{staff}', [OwnerStaffController::class, 'destroy'])->name('staff.destroy');

        Route::get('/customers', [OwnerCustomerController::class, 'index'])->name('customers.index');

        Route::get('/settings', [OwnerSalonController::class, 'edit'])->name('settings.edit');
        Route::patch('/settings', [OwnerSalonController::class, 'update'])->name('settings.update');
    });

require __DIR__ . '/auth.php';