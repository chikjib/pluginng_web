<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UpdateEmailVerifiedController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//URL::forceScheme('https');


// Route::get('/', function () {
//     return view('home.index', ['pageTitle' => "Login"]);
// });

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', [App\Http\Controllers\HomeController::class, 'about'])->name('about');
Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact'])->name('contact');
Route::get('/faq', [App\Http\Controllers\HomeController::class, 'faq'])->name('faq');

Route::get('/terms', [App\Http\Controllers\HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [App\Http\Controllers\HomeController::class, 'privacy'])->name('privacy');

Route::get('/pricing', [App\Http\Controllers\HomeController::class, 'pricing'])->name('pricing');


Route::get('/login', function () {
    return view('layouts.main');
});

Route::get('/register', function () {
    return view('layouts.main');
});

Route::get('/forgotpassword', function () {
    return view('layouts.main');
});


Route::get('/dashboard', function () {
    return view('layouts.master');
});

Route::get('/dashboard/{any}', function () {
    return view('layouts.master');
})->where('any', '.*');


Route::get('/email-verify/{email}', UpdateEmailVerifiedController::class);
Route::get('/my-cron', [App\Http\Controllers\HomeController::class,'run_cron'])->name('mycron');
// Route::get('/my-reverse-script', [App\Http\Controllers\API\OrderController::class,'updateOrderExternal'])->name('myreverse');
// Route::get('/airtime-nigeria-requery/{ref}',[App\Http\Controllers\HomeController::class,'airtime_nigeria_requery'])->name('angrequery');


Route::webhooks('monify-webhook', 'monify-webhook');
Route::webhooks('rehoboth-webhook', 'rehoboth-webhook');
Route::webhooks('rave-webhook', 'rave-webhook');
Route::webhooks('gtsquadco-webhook', 'gtsquadco-webhook');
Route::webhooks('tbch-webhook', 'tbch-webhook');
Route::webhooks('palmpay-webhook', 'palmpay-webhook');
Route::webhooks('smeplug-webhook', 'smeplug-webhook');
Route::webhooks('wisper-webhook', 'wisper-webhook');
Route::webhooks('autosync-webhook', 'autosync-webhook');
Route::webhooks('vtuplug-webhook', 'vtuplug-webhook');
Route::webhooks('naijasub-webhook', 'naijasub-webhook');
Route::webhooks('amakasub-webhook', 'amakasub-webhook');
Route::webhooks('zoe-data-webhook', 'zoe-data-webhook');
Route::webhooks('integrity-portal-webhook', 'integrity-portal-webhook');
Route::webhooks('sim-server-webhook', 'sim-server-webhook');



Route::get('/export-transactions',[App\Http\Controllers\API\OrderController::class,
            'exportTransactions'])->name('exportTransactions');

Route::get('/get-mtn-tokens',[App\Http\Controllers\API\SettingController::class,
            'updateOurToken'])->name('updateOurToken');
            
Route::get('/mtn-cg-account-active',[App\Http\Controllers\MtnCgController::class,
'keepTokenAlive'])->name('makeAccountActiveCG');

//MTN DIRECT (APP)
Route::get('/mtn-direct-app-login',[App\Http\Controllers\MtnDirectAppController::class,
'automationLoginView'])->name('automationLoginViewApp');

Route::post('/mtn-direct-app-generate-otp',[App\Http\Controllers\MtnDirectAppController::class,
'generateMtnOtp'])->name('generateMtnOtpApp');

Route::post('/mtn-direct-app-validate-otp',[App\Http\Controllers\MtnDirectAppController::class,
'validateMtnOtp'])->name('validateMtnOtpApp');

Route::get('/mtn-direct-app-account-active',[App\Http\Controllers\MtnDirectAppController::class,
'makeAccountActive'])->name('makeAccountActiveApp');
            
// Route::get('/send-notification', [NotificationController::class, 'sendtransationNotification']);


// Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'isAdmin']], function () {
//     //Route::get('dashboard', 'AdminController@index');
//     Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('admin-dashboard');
//     Route::get('profile', [App\Http\Controllers\AdminController::class, 'profile'])->name('admin-profile');

//     Route::get('data', [App\Http\Controllers\AdminController::class, 'products'])->name('products');
//     Route::get('orders', [App\Http\Controllers\AdminController::class, 'orders'])->name('orders');
//     Route::get('drivers', [App\Http\Controllers\AdminController::class, 'drivers'])->name('drivers');
//     Route::get('payments', [App\Http\Controllers\AdminController::class, 'payments'])->name('payments');
//     Route::get('categories', [App\Http\Controllers\AdminController::class, 'categories'])->name('categories');
//     Route::get('settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('settings');

//     Route::get('farmers', [App\Http\Controllers\AdminController::class, 'farmers'])->name('farmers');
//     Route::get('delivery-request', [App\Http\Controllers\AdminController::class, 'delivery_requests'])->name('delivery-request');
//     Route::get('track-order/{id}', [App\Http\Controllers\AdminController::class, 'track_order'])->name('track-order');

//     Route::get('farmer-details/{id}', [App\Http\Controllers\AdminController::class, 'farmer_details'])->name('admin-farmer-details');
//     Route::get('user-details/{id}', [App\Http\Controllers\AdminController::class, 'user_details'])->name('user-details');
//     Route::get('delivery-log/{id}', [App\Http\Controllers\AdminController::class, 'delivery_log'])->name('admin-delivery-log');
//     Route::get('users/{role}', [App\Http\Controllers\AdminController::class, 'users'])->name('users');

//     // Route::get('track-order/{id}', [App\Http\Controllers\AdminController::class, 'track_order'])->name('track-order');


//     Route::get('commissions', [App\Http\Controllers\AdminController::class, 'commissions'])->name('commissions');
//     Route::get('withdrawals', [App\Http\Controllers\AdminController::class, 'withdrawals'])->name('withdrawals');
//     Route::get('map', [App\Http\Controllers\AdminController::class, 'map'])->name('map');
//     Route::get('settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
// });

// Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'isUser']], function () {
//     Route::get('home', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
//     Route::get('commissions', [App\Http\Controllers\DashboardController::class, 'commissions'])->name('agent-commissions');
//     Route::get('withdrawals', [App\Http\Controllers\DashboardController::class, 'withdrawals'])->name('agent-withdrawals');
//     Route::get('profile', [App\Http\Controllers\DashboardController::class, 'profile'])->name('profile');
//     Route::get('data', [App\Http\Controllers\DashboardController::class, 'data'])->name('data');

//     Route::get('airtime', [App\Http\Controllers\DashboardController::class, 'airtime'])->name('airtime');
// });

// Route::get('dashboard/fundaccount', [FundController::class, 'index'])->name('index');
// Route::post('flutterwave/payment', [FundController::class, 'store'])->name('flutterwave.payment');
// Route::get('flutterwave/callback', [FundController::class, 'callback'])->name('flutterwave-callback');



// Route::middleware(['auth', 'isAdmin'])->group(function () {
//     Route::get('/admin', function () {
//         return view('admin.dashboard');
//     })->name('dashboard');
// });


// Route::middleware(['auth', 'isAdmin'])->group(function () {
//     Route::get('/admin', function () {
//         return view('admin.dashboard');
//     })->name('dashboard');
// });

// Route::get('/login', function () {
//     return view('auth.login');
// });

// Route::get('{any}', function () {
//     return view('layouts.master');
// })->where('any', '.*');


//Auth::routes(['verify' => true]);
//URL::forceScheme('https');

// Auth::routes();


// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/users', [App\Http\Controllers\HomeController::class, 'users'])->name('users');
