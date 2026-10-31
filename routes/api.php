<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CommissionController;
use App\Http\Controllers\API\SubcategoryController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PinController;
use App\Http\Controllers\API\WithdrawalController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\SlidesController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\API\BucketOrderController;
use App\Http\Controllers\API\BucketController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//URL::forceScheme('https');


Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', [ 'as' => 'login', 'uses' => 'login']);


    //Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::webhooks('monnify_webhook');
});

Route::post('auth/password/email', [ForgotPasswordController::class, 'sendEmail']);
Route::post('auth/token/verify', [ForgotPasswordController::class, 'verifyToken']);
Route::post('auth/password/reset', [ResetPasswordController::class, 'reset']);




Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [RegisterController::class, 'logout']);
});

Route::middleware('auth:api')->prefix('admin')->group(
    function () {
        Route::post('user/update/wallet', [App\Http\Controllers\API\UserController::class, 'updateWallet'])->middleware('admin.only')->name('updateWallet');
        Route::post('user/update/levels', [App\Http\Controllers\API\UserController::class, 'updateLevel'])->middleware('admin.only')->name('updateLevels');
        Route::resource('allusers', UserController::class)->middleware('admin.only');
        Route::resource('settings', SettingController::class)->middleware('admin.only');
        Route::resource('subcategories', SubcategoryController::class)->middleware('admin.only');

        Route::resource('slides', SlidesController::class)->middleware('admin.only');
        Route::post('slides/uploadimage', [App\Http\Controllers\API\SlidesController::class, 'uploadImage'])->name('slides/uploadimage')->middleware('admin.only');
    }
);


Route::middleware('auth:api')->group(function () {
    // Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('bucket-orders', BucketOrderController::class);
    Route::put('update/bucket-order/{id}', [App\Http\Controllers\API\BucketOrderController::class, 'updateOrder'])->middleware('admin.only')->name('update_bucket_order');
    Route::post('bucket-order/reports', [App\Http\Controllers\API\BucketOrderController::class, 'getReport'])->middleware('admin.only');

    Route::post('bucket-orders/update-status', [BucketOrderController::class,'updateStatus'])->middleware('admin.only')->name('update_bucket_status');

    Route::get('bucket-orders/bucket/{id}', [App\Http\Controllers\API\BucketOrderController::class, 'getBucketTransaction'])->middleware('admin.only')->name('bucket_transaction');
    
    Route::post('orders/update-status', [OrderController::class,'updateStatus'])->middleware('admin.only')->name('update_status');
    
    Route::post('order/reports', [App\Http\Controllers\API\OrderController::class, 'getReport'])->middleware('admin.only');
    Route::post('accounting/reports', [App\Http\Controllers\API\OrderController::class, 'getAccountingReport'])->middleware('admin.only');

    
    //Route::post('order/admin/reports', [App\Http\Controllers\API\OrderController::class, 'getReportAdmin']);
    Route::get('switch-off-bucket-purchase/{id}', [App\Http\Controllers\API\BucketController::class, 'switchOffBucketPurchase'])->middleware('admin.only')->name('switchOffBucketPurchase');
    
    Route::resource('subcategory', SubcategoryController::class)->except(['destroy', 'store']);
    Route::get('accounting', [SubcategoryController::class, 'load_accounting'])->name('accounting_setup')->middleware('admin.only');

    Route::get('total-wallet', [UserController::class, 'getTotalWallet'])->name('user_wallet')->middleware('admin.only');
    
    Route::resource('pins', PinController::class)->except(['destroy', 'store']);
    Route::post('add-pins', [PinController::class,'create'])->middleware('admin.only')->name('add_pin');
    Route::put('update-pin/{id}', [PinController::class,'update'])->middleware('admin.only')->name('update_pin');
    Route::get('delete-pins/{id}/{title}', [PinController::class,'destroy'])->middleware('admin.only')->name('delete_pin');
    Route::get('get/plans', [App\Http\Controllers\API\SubcategoryController::class, 'load_public_plans'])->name('subcategory/load_public_plans');
    
    Route::resource('bucket', BucketController::class)->except(['destroy', 'store']);
    Route::get('get-all-buckets', [App\Http\Controllers\API\BucketController::class, 'load_admin_bucket'])->middleware('admin.only')->name('load_admin_bucket');

    Route::get('buckets', [App\Http\Controllers\API\BucketController::class, 'load_bucket'])->name('load_bucket');
    Route::get('buckets/balance', [App\Http\Controllers\API\BucketController::class, 'load_bucket_balance'])->name('load_bucket_balance');

    Route::get('get_all_bucket_balances', [App\Http\Controllers\API\BucketController::class, 'get_all_bucket_balances'])->name('get_all_bucket_balances');

    Route::get('bucket/{id}', [App\Http\Controllers\API\BucketController::class, 'show'])->name('show');

    Route::put('update-bucket/{id}', [BucketController::class,'update'])->middleware('admin.only')->name('update_bucket');
    Route::get('delete-bucket/{id}', [BucketController::class,'destroy'])->middleware('admin.only')->name('delete_bucket');



    Route::post('purchase/bucket', [App\Http\Controllers\API\BucketOrderController::class, 'PurchaseBucket'])->name('purchase_bucket');

    Route::post('purchase/bucket/data', [App\Http\Controllers\API\BucketOrderController::class, 'PurchaseData'])->name('purchase_data');


    Route::get('get/subcategory/plans/{category_id}', [App\Http\Controllers\API\SubcategoryController::class, 'load_plans'])->name('subcategory/load_plans');
    

    Route::resource('categories', CategoryController::class);
    Route::resource('commissions', CommissionController::class);
    Route::resource('withdrawals', WithdrawalController::class);
    Route::resource('users', UserController::class)->except(['destroy', 'store']);

    Route::post('change-password', [App\Http\Controllers\API\UserController::class, 'change_password'])->name('change-password');
    Route::post('users/uploadimage/{id}', [App\Http\Controllers\API\UserController::class, 'uploadImage'])->name('users/uploadimage');
    Route::post('categories/uploadimage/{id}', [App\Http\Controllers\API\CategoryController::class, 'uploadImage'])->middleware('admin.only')->name('categories/uploadimage');
    Route::post('orders/postPayment', [App\Http\Controllers\API\OrderController::class, 'postPayment'])->middleware('admin.only')->name('orders/postPayment');

    Route::get('load-home', [App\Http\Controllers\API\UserController::class, 'loadHomePage'])->name('loadHomePage');
    
    // MTN LOGINS
    Route::get('load-logins', [App\Http\Controllers\API\MtnLoginController::class, 'load_mtn_logins'])->middleware('admin.only')->name('loadMtnLogins');

    Route::get('activate-number/{id}', [App\Http\Controllers\API\MtnLoginController::class, 'activate_number'])->middleware('admin.only')->name('activateNumber');

    Route::get('delete-number/{id}', [App\Http\Controllers\API\MtnLoginController::class,'destroy'])->middleware('admin.only')->name('delete_login');


    Route::post('purchase/airtime', [App\Http\Controllers\API\OrderController::class, 'purchaseAirtime'])->name('orders/purchaseAirtime');
    Route::post('purchase/data', [App\Http\Controllers\API\OrderController::class, 'purchaseData'])->name('orders/purchaseData');
    Route::post('purchase/cable', [App\Http\Controllers\API\OrderController::class, 'purchaseCable'])->name('orders/purchaseCable');
    Route::post('purchase/electricity', [App\Http\Controllers\API\OrderController::class, 'purchaseElectricity'])->name('orders/purchaseElectricity');
    Route::post('purchase/exam', [App\Http\Controllers\API\OrderController::class, 'purchaseExam'])->name('orders/purchaseExam'); 
    Route::post('fund/withdraw', [App\Http\Controllers\API\OrderController::class, 'withdraw'])->name('orders/withdraw');

    Route::post('send/sms', [App\Http\Controllers\API\OrderController::class, 'sendSMS'])->name('orders/sendSMS');
    Route::post('purchase/a2cash', [App\Http\Controllers\API\OrderController::class, 'purchaseA2cash'])->name('orders/purchaseA2cash');
    Route::get('fetch/bouquet', [App\Http\Controllers\API\OrderController::class, 'fetchBouquet'])->name('orders/fetchBouquet');
    Route::post('verify/card', [App\Http\Controllers\API\OrderController::class, 'verifyCard'])->name('orders/verifyCard');
    Route::get('reserve/account/{bank}', [App\Http\Controllers\API\UserController::class, 'generate_reserved_account'])->name('users/generate_reserved_account');

    Route::post('payment/initiate', [App\Http\Controllers\API\PaymentController::class, 'initiatePayment'])->name('initiatePayment');
    Route::post('payment/manual/initiate', [App\Http\Controllers\API\PaymentController::class, 'initiateManualPayment'])->name('initiateManualPayment');

    Route::get('generate/api/key', [App\Http\Controllers\API\UserController::class, 'generateApiKey'])->name('users/generateApiKey');

    Route::put('update/order/{id}', [App\Http\Controllers\API\OrderController::class, 'updateOrder'])->middleware('admin.only')->name('updateOrder');
    Route::put('update/order/confirmed/{id}', [App\Http\Controllers\API\OrderController::class, 'updateConfirmedOrder'])->middleware('admin.only')->name('updateConfirmedOrder');
    Route::post('update/profile/bvn', [App\Http\Controllers\API\UserController::class, 'updateBvn'])->name('users/updateBvn');
    Route::post('update/profile/nin', [App\Http\Controllers\API\UserController::class, 'updateNin'])->name('users/updateNin');
    
    Route::get('verify-payment/{transaction_reference}', [App\Http\Controllers\API\UserController::class, 'VerifyPayment'])->name('users/VerifyPayment');

    Route::post('generate-payment-link', [App\Http\Controllers\API\UserController::class, 'InitializeMonnifyPay'])->name('users/InitializeMonnifyPay');


    Route::get('payment/confirm/{ref}', [App\Http\Controllers\API\PaymentController::class, 'ConfirmPayment'])->name('ConfirmPayment');
    Route::post('user/update/level', [App\Http\Controllers\API\UserController::class, 'updateLevel'])->name('updateLevel');
    Route::post('user/admin-update/level', [App\Http\Controllers\API\UserController::class, 'updateLevelAdmin'])->middleware('admin.only')->name('updateLevelAdmin');
    Route::post('user/bonus/transfer', [App\Http\Controllers\API\UserController::class, 'transferBonus'])->name('users/transferBonus');

    Route::get('user/settings', [App\Http\Controllers\API\UserController::class, 'usersettings'])->name('users/usersettings');
    
    Route::get('requery/{ref}', [App\Http\Controllers\API\OrderController::class,'getTransaction'])->name('orders/getTransaction');
    Route::get('requery/order/{id}',[App\Http\Controllers\API\OrderController::class, 'requeryUserTransaction'])->middleware('admin.only')->name('orders/requeryUserTransaction');
    Route::put('/update-webhook',[App\Http\Controllers\API\UserController::class,'updateWebhook'])->name('update-webhook');
    
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//URL::forceScheme('https');
