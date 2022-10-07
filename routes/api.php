<?php

use App\Http\Controllers\Apis\ApiAuthController;
use App\Http\Controllers\Apis\ApiOrderController;
use App\Http\Controllers\Apis\ApiProductController;
use App\Http\Controllers\Apis\ApiReportController;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [ApiAuthController::class, 'login']);
Route::post('change-password', [ApiAuthController::class, 'change_password'])->name('change_password');
Route::post('check-email', [ApiAuthController::class, 'check_email'])->name('check_email');
Route::post('forgot-password', [ApiAuthController::class, 'forgot_password'])->name('forgot_password');


Route::group(['prefix' => 'products', 'middleware' => 'admin_merchant', 'as' => 'product.'], function () {
    Route::get('listMerchantData', [ApiProductController::class, 'listMerchantData'])->name('list_merchant_data');
    Route::get('listMerchantInActiveData', [ApiProductController::class, 'listMerchantInActiveData'])->name('list_merchant_in_active_data');
    Route::get('listData', [ApiProductController::class, 'listData'])->name('list_data');
    Route::get('listInActiveData', [ApiProductController::class, 'listInActiveData'])->name('list_in_active_data');
    Route::post('store', [ApiProductController::class, 'store'])->name('store');
    Route::post('update/{id_name}', [ApiProductController::class, 'update'])->name('update');
    Route::get('details/{id_name}', [ApiProductController::class, 'details'])->name('details');
    Route::get('get-shapes', [ApiProductController::class, 'getShapes'])->name('shapes');
    Route::get('get-companies', [ApiProductController::class, 'getCompanies'])->name('companies');
    Route::get('get-eff-mat', [ApiProductController::class, 'getEffMaterials'])->name('eff_materials');
    Route::get('get-treat-group', [ApiProductController::class, 'getTreatementGroup'])->name('treatement_group');
    Route::get('get-product-amounts/{id}', [ApiProductController::class, 'getProductAmounts'])->name('amounts');
    Route::get('get-product-price/{id}/{merchantId?}', [ApiProductController::class, 'getCurrentPriceForElement'])->name('price');
    Route::post('delete-product', [ApiProductController::class, 'deleteProduct'])->name('delete');
});

Route::group(['prefix' => 'orders', 'middleware' => 'admin_merchant', 'as' => 'orders.'], function () {
    Route::post('buy', [ApiOrderController::class, 'buy'])->name('buy');
    Route::post('sell', [ApiOrderController::class, 'sell'])->name('sell');
    Route::post('product-return', [ApiOrderController::class, 'productReturn'])->name('product_return');
});

Route::group(['prefix' => 'reports', 'middleware' => 'admin_merchant', 'as' => 'reports.'], function () {
    Route::get('expired', [ApiReportController::class, 'expired'])->name('expired');
    Route::get('expired-till-month/{id}', [ApiReportController::class, 'expiredTillMonth'])->name('expired_till_month');
});

Route::group(['prefix' => 'inventory', 'middleware' => 'admin_merchant', 'as' => 'inventory.'], function () {
    Route::post('inventory-amounts', [ApiOrderController::class, 'inventoryAmounts'])->name('amounts');
});

Route::fallback(function () {
    return 'Invalid route';
});
