<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

// Public home page (content galing sa Strapi)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Para sa lahat ng naka-login na user
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Para sa admin lang
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('customers', CustomerController::class)
        ->only(['index', 'create', 'store']);

    Route::resource('services', ServiceController::class)
        ->only(['index', 'create', 'store']);

    Route::resource('staff', StaffController::class)
        ->only(['index', 'create', 'store']);
});

require __DIR__ . '/auth.php';