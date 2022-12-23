<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;


// Root of the project, redirects to login or home depending on the authentication state of the user
Route::get('/', function () {
    // TODO: If user is already logged in redirect to home instead
    return view('frontend/login');
});

// Home page of the normal user accounts
Route::get('/home', function () {
    // TODO: change this to a controller
    return view('frontend/home');
});

// Authentication method
Route::post('/auth', [AuthController::class, 'auth'])->name("auth");

// Dashboard for the professional user accounts
Route::get('/professional/dashboard', [DashboardController::class, 'index']);
Route::get('/professional', [DashboardController::class, 'index']);

