<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OptionsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PermsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserConfigsController;

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

// Food Menu for the professional user accounts
Route::get('/professional/ementa', [MenuController::class, 'index']);

// Orders for the professional user accounts
Route::get('/professional/encomendas', [OrdersController::class, 'index']);

// Statistics for the professional user accounts
Route::get('/professional/stats', [StatsController::class, 'index']);

// User configs for the professional user account
Route::get('/professional/admin/users', [UserConfigsController::class, 'index']);

// Permissions configs for the professional user account
Route::get('/professional/admin/permissions', [PermsController::class, 'index']);

// Admin Options for the professional user account
Route::get('/professional/admin/options', [OptionsController::class, 'index']);