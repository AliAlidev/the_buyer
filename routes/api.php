<?php

use App\Http\Controllers\Apis\AuthController;
use App\Http\Controllers\Apis\ProductController;
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

Route::post('login', [AuthController::class, 'login']);

Route::group(['prefix' => 'products', 'middleware' => 'auth:api', 'as' => 'product.'], function () {
    Route::post('store', [ProductController::class, 'store'])->name('store');
    Route::get('get-shapes', [ProductController::class, 'getShapes'])->name('shapes');
    Route::get('get-companies', [ProductController::class, 'getCompanies'])->name('companies');
    Route::get('get-eff-mat', [ProductController::class, 'getEffMaterials'])->name('eff_materials');
    Route::get('get-treat-group', [ProductController::class, 'getTreatementGroup'])->name('treatement_group');
});
