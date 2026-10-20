<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('customers', CustomerController::class)
    ->only(['index', 'create', 'store']);

Route::resource('services', ServiceController::class)
    ->only(['index', 'create', 'store']);

Route::resource('staff', StaffController::class)
    ->only(['index', 'create', 'store']);