<?php

use App\Helpers\AppHelper;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EditpageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NavController;
use App\Http\Controllers\OptionsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PermsController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\UserConfigsController;

// Root of the project, redirects to login or home depending on the authentication state of the user
Route::get('/', function () {
    return redirect(session()->has("authenticated") ? '/home' : '/login');
});

// Home page of the normal user accounts
Route::get('/home', [HomeController::class, 'index']);

Route::get('/login', [AuthController::class, 'index']);

// Register page
Route::get('/register', [AuthController::class, 'createAccount']);
Route::post('/createaccount', [AuthController::class, 'create'])->name("createaccount");

// invite pages
Route::get('/invite/{token}', [AuthController::class, 'invited']);
Route::post('/invite/register', [AuthController::class, 'invite_finish'])->name('register');

// Page to show when user doesn't have permission to enter a page
Route::get('/no-access', function () {
    return view('errors.404');
});

Route::get('/dasboard', [DashboardController::class, 'index']);

// Search page without reload
Route::get('/search', [SearchController::class, 'index']);
Route::post('/search_no_reload', [NavController::class, 'goToSearch'])->name("search_no_reload");
Route::post('/search_confirm', [SearchController::class, 'search'])->name("search_confirm");

// Page where users create a restaurant to switch account to professional
Route::get('/novo/restaurante', [RestaurantController::class, 'index']);
Route::post('/novo/create', [RestaurantController::class, 'create'])->name("create");
Route::post('/publish', [RestaurantController::class, 'publish'])->name("publish");
Route::post('/professional/getrestaurant', [RestaurantController::class, 'get'])->name("getrestaurant");
Route::post('/professional/saverestaurantinfo', [RestaurantController::class, 'saveInfo'])->name("saverestaurantinfo");

// Authentication method
Route::post('/auth', [AuthController::class, 'auth'])->name("auth");

// Logout method
Route::post('/logout', [AuthController::class, 'logout'])->name("logout");

// Dashboard for the professional user accounts
Route::get('/professional', [DashboardController::class, 'index']);

Route::post('/update_session', [AuthController::class, 'update_session'])->name("update_session");

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
Route::get('/professional/encomendas/viewer', [OrdersController::class, 'kitchen_viewer']);
Route::get('/professional/encomendas/{id}', [OrdersController::class, 'edit']);
Route::post('/professional/getorders', [OrdersController::class, 'get'])->name("getorders");
Route::post('/professional/getorderitems', [OrdersController::class, 'get_items_from_order'])->name("getorderitems");
Route::post('/professional/getordersides', [OrdersController::class, 'get_sides'])->name("getordersides");
Route::post('/professional/changeordersitemstatus', [OrdersController::class, 'change_status'])->name("changeordersitemstatus");
Route::post('/professional/close_order', [OrdersController::class, 'close_order'])->name("close_order");
Route::post('/professional/cancel_order', [OrdersController::class, 'cancel_order'])->name("cancel_order");
Route::post('/professional/fast_close', [OrdersController::class, 'mark_done_fast'])->name("fast_close");

// Statistics for the professional user accounts
Route::get('/professional/stats', [StatsController::class, 'index']);

// Reviews page for pro account
Route::get("/professional/criticas", [ReviewsController::class, 'index']);
Route::post('/professional/criticas/reportar', [ReviewsController::class, 'report_review'])->name("reportar");


// Edit main page for the pro user accounts
Route::get('/professional/conteudo', [EditpageController::class, 'index']);
Route::get('/professional/conteudo/publicar', [EditpageController::class, 'postPage']);
Route::post('/professional/conteudo/publicar/save', [EditpageController::class, 'savePost'])->name("savePost");
Route::post('/professional/conteudo/set', [EditpageController::class, 'setPlateOfDay'])->name("set_plate_of_day");
Route::post('/professional/conteudo/delete', [EditpageController::class, 'deletePost'])->name("deletePost");
Route::post('/professional/conteudo/publicar_rascunho', [EditpageController::class, 'publishDraft'])->name("publicar_rascunho");
Route::post('/professional/conteudo/guardar_imagem', [EditpageController::class, 'saveImage'])->name("guardar_imagem");

// User configs for the professional user account
Route::get('/professional/admin/users', [UserConfigsController::class, 'index']);
Route::get('/professional/admin/users/pending', [UserConfigsController::class, 'pending_page']);
Route::get('/professional/admin/users/{id}', [UserConfigsController::class, 'user_details']);
Route::post('/professional/admin/getallusers', [UserConfigsController::class, 'get_all'])->name("getallusers");
Route::post('/professional/admin/change_type', [UserConfigsController::class, 'changeType'])->name("change_type");
Route::post('/professional/admin/change_state', [UserConfigsController::class, 'changeUserState'])->name("change_state");
Route::post('/professional/admin/invite_users', [UserConfigsController::class, 'invite'])->name("invite_users");
Route::post('/professional/admin/get_pending', [UserConfigsController::class, 'getPending'])->name("get_pending");
Route::post('/professional/admin/delete_invite', [UserConfigsController::class, 'delete_invites'])->name("delete_invite");

// Permissions configs for the professional user account
Route::get('/professional/admin/permissions', [PermsController::class, 'index']);
Route::get('/professional/admin/permissions/criar', [PermsController::class, 'new']);
Route::get('/professional/admin/permissions/{id}', [PermsController::class, 'edit_page']);
Route::post('/professional/admin/permissions/get_types', [PermsController::class, 'getTypes'])->name("get_types");
Route::post('/professional/admin/permissions/save_types', [PermsController::class, 'save'])->name("save_types");
Route::post('/professional/admin/permissions/edit_types', [PermsController::class, 'edit'])->name("edit_types");
Route::post('/professional/admin/permissions/remove_types', [PermsController::class, 'remove'])->name("remove_types");

// Admin Options for the professional user account
Route::get('/professional/admin/options', [OptionsController::class, 'index']);

//Settings
Route::get('/professional/configuracoes/user', [SettingsController::class, 'index']);
Route::post('/professional/updateusersettings', [SettingsController::class, 'update'])->name("updateusersettings");
Route::post('/professional/fileupload', [SettingsController::class, 'fileupload'])->name("fileupload");

// Restaurant page front-end
Route::get('/restaurante/{id}', [RestaurantController::class, 'restaurant_page']);
Route::get('/restaurante/{id}/menu', [RestaurantController::class, 'menu_page']);
Route::get('/restaurante/{id}/publicacoes', [RestaurantController::class, 'posts']);
Route::get('/restaurante/{id}/reviews', [RestaurantController::class, 'reviews_page']);
Route::post('/review/add', [ReviewsController::class, 'add'])->name("addreviews");
Route::post('/review/remove', [ReviewsController::class, 'deleteReview'])->name("removereviews");

// Shopping Cart routes
Route::get('/carinho', [CartController::class, 'index'])->name("payment");
Route::post('/addToCart', [CartController::class, 'addToCart'])->name("addToCart");
Route::post('/getCartItems', [CartController::class, 'get'])->name("getCartItems");
Route::post('/getnote', [CartController::class, 'getNote'])->name("getnote");
Route::post('/addnote', [CartController::class, 'addNotes'])->name("addnote");
Route::post('/addside', [CartController::class, 'addSides'])->name("addside");
Route::post('/getsides', [CartController::class, 'getSides'])->name("getsides");
Route::post('/createorder', [OrdersController::class, 'create_order'])->name("createorder");

// Checkout routes
Route::get('/carrinho/confirmar', [StripeController::class, 'index']);
Route::post('/checkout', [StripeController::class, 'checkout'])->name("checkout");
Route::get('/carrinho/confirmar/sucesso', [StripeController::class, 'success'])->name('order_success');

Route::get("/pedidos", [OrdersController::class, 'myOrders']);

Route::post('/checkNotifications', [NavController::class, 'checkForNotification'])->name("checkNotifications");

// Admin
Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/dashboard', [AdminController::class, 'index']);
Route::get('/admin/restaurantes', [AdminController::class, 'restaurant']);
Route::get('/admin/users', [AdminController::class, 'users']);
Route::get('/admin/denuncias', [AdminController::class, 'reports']);
Route::post('/admin/denuncias/get', [AdminController::class, 'getReports'])->name("getReports");
Route::post('/admin/denuncias/ignore', [AdminController::class, 'ignoreReport'])->name("ignoreReport");
Route::post('/admin/denuncias/remove', [AdminController::class, 'removeReport'])->name("removeReport");
Route::post('/admin/users/get', [AdminController::class, 'getUsers'])->name("getUsers");
Route::post('/admin/users/appadmin', [AdminController::class, 'switchAppAdmin'])->name("switchAppAdmin");
Route::post('/admin/users/ban', [AdminController::class, 'banUser'])->name("banUser");
Route::post('/admin/restaurantes/get', [AdminController::class, 'getRestaurants'])->name("getRestaurants");
Route::post('/admin/restaurantes/switch', [AdminController::class, 'switchRestaurant'])->name("switchRestaurant");

// any other route that isn't declared goes to 404 page
Route::get('/{any}', function () {
    abort(404, view("errors.404"));
})->where('any', '.*');
