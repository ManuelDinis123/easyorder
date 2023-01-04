<?php

use App\Helpers\AppHelper;
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
    return view(session()->has("authenticated") ? 'frontend/home' : 'frontend/login');
});



// Home page of the normal user accounts
Route::get('/home', function () {    
    if (!AppHelper::hasLogin()) return redirect("/no-access");
    return view('frontend/home');
});

// Register page
Route::get('/register', [AuthController::class, 'createAccount']);
Route::post('/createaccount', [AuthController::class, 'create'])->name("createaccount");

// Page to show when user doesn't have permission to enter a page
Route::get('/no-access', function () {
    return view('errors.404');
});

// Authentication method
Route::post('/auth', [AuthController::class, 'auth'])->name("auth");

// Logout method
Route::post('/logout', [AuthController::class, 'logout'])->name("logout");

// Dashboard for the professional user accounts
Route::get('/professional/dashboard', [DashboardController::class, 'index']);
Route::get('/professional', [DashboardController::class, 'index']);

// Food Menu for the professional user accounts
Route::get('/professional/ementa', [MenuController::class, 'index']);
Route::get('/professional/ementa/{id}', [MenuController::class, 'edit']);
Route::post('/professional/getmenu', [MenuController::class, 'get'])->name("getmenu");
Route::post('/professional/createmenuitem', [MenuController::class, 'create'])->name("createmenuitem");
Route::post('/professional/updatemenuitem', [MenuController::class, 'update'])->name("updatemenuitem");
Route::post('/professional/deletemenuitem', [MenuController::class, 'remove'])->name("deletemenuitem");
Route::post('/professional/gettags', [MenuController::class, 'get_tags'])->name("gettags");
Route::post('/professional/getingredients', [MenuController::class, 'fetch_ingredients'])->name("getingredients");
Route::post('/professional/addingredients', [MenuController::class, 'add_ingredients'])->name("addingredients");
Route::post('/professional/updateingredients', [MenuController::class, 'update_ingredients'])->name("updateingredients");
Route::post('/professional/deleteingredient', [MenuController::class, 'delete_ingredients'])->name("deleteingredient");


// Orders for the professional user accounts
Route::get('/professional/encomendas', [OrdersController::class, 'index']);
Route::post('/professional/getorders', [OrdersController::class, 'get'])->name("getorders");

// Statistics for the professional user accounts
Route::get('/professional/stats', [StatsController::class, 'index']);

// User configs for the professional user account
Route::get('/professional/admin/users', [UserConfigsController::class, 'index']);

// Permissions configs for the professional user account
Route::get('/professional/admin/permissions', [PermsController::class, 'index']);

// Admin Options for the professional user account
Route::get('/professional/admin/options', [OptionsController::class, 'index']);


// any other route that isn't declared goes to 404 page
Route::get('/{any}', function () {
    abort(404, view("errors.404"));
})->where('any', '.*');
