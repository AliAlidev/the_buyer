<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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

Route::get('homeadmin', [HomeController::class, 'adminIndex'])->name('home-admin');
Route::get('homebuyer', [HomeController::class, 'buyerIndex'])->name('home-buyer');

Route::group(['prefix' => 'admin'], function () {
});

Route::group(['prefix' => 'buyer'], function () {
    Route::post('add_data', [HomeController::class, 'store'])->name('add-data');
});
