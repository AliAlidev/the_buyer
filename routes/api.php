<?php

use App\Http\Controllers\Apis\ApiAuthController;
use App\Http\Controllers\Apis\ApiOrderController;
use App\Http\Controllers\Apis\ApiProductController;
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

Route::group(['prefix' => 'products', 'middleware' => 'admin_merchant', 'as' => 'product.'], function () {
    Route::post('store', [ApiProductController::class, 'store'])->name('store');
    Route::post('update/{id_name}', [ApiProductController::class, 'update'])->name('update');
    Route::get('details/{id_name}', [ApiProductController::class, 'details'])->name('details');
    Route::get('get-shapes', [ApiProductController::class, 'getShapes'])->name('shapes');
    Route::get('get-companies', [ApiProductController::class, 'getCompanies'])->name('companies');
    Route::get('get-eff-mat', [ApiProductController::class, 'getEffMaterials'])->name('eff_materials');
    Route::get('get-treat-group', [ApiProductController::class, 'getTreatementGroup'])->name('treatement_group');
});

Route::group(['prefix' => 'orders', 'middleware' => 'admin_merchant', 'as' => 'orders.'], function () {
    Route::post('buy', [ApiOrderController::class, 'buy'])->name('buy');
});

Route::fallback(function () {
    return 'Invalid route';
});
