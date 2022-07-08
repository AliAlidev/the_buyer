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
    Route::get('list_itm', [HomeController::class, 'listitems'])->name('list-items');
    Route::get('create_itm', [HomeController::class, 'createitemindex'])->name('create-item-index');
    Route::post('create_itm', [HomeController::class, 'createitem'])->name('create-item');
    Route::post('add_dat', [HomeController::class, 'store'])->name('add-data');
    Route::post('get_dat_ser', [HomeController::class, 'findBySerialCode'])->name('get-data-by-serial');
});
