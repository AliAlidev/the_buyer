<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SellController;
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

Route::get('import_data', [HomeController::class,'importDataWithShapesAndCompanies']);

Route::get('/', [HomeController::class, 'listitems'])->name('home');
Route::group(['prefix' => 'inventory'], function () {
    Route::get('getCurrentPrice/{id}/{merchantId}', [HomeController::class, 'getCurrentPriceForElement'])->name('get-current-price-for-element');
    Route::get('getCurrentPartPrice/{id}/{merchantId}', [HomeController::class, 'getCurrentPartPriceForElement'])->name('get-current-part-price-for-element');
    Route::get('getMaxPrice/{id}', [HomeController::class, 'getMaxPriceForElement'])->name('get-max-price-for-element');
    Route::get('getMaxPartPrice/{id}', [HomeController::class, 'getMaxPartPriceForElement'])->name('get-max-part-price-for-element');
    Route::get('get_itms_na', [HomeController::class, 'getItemsName'])->name('get-items-name');
    Route::get('list_itm', [HomeController::class, 'listitems'])->name('list-items');
    Route::get('fast_inventory_list', [HomeController::class, 'fastinventorylist'])->name('fast-inventory-list');
    Route::post('store_fast_inventory_list', [HomeController::class, 'storefastinventorylist'])->name('store-fast-inventory-list');
    Route::get('list_invitem_amnt', [HomeController::class, 'listinventoryitemamounts'])->name('list-inventory-item-amounts');
    Route::get('create_itm', [HomeController::class, 'createitemindex'])->name('create-item-index');
    Route::get('add_inv_item_amnt/{id}', [HomeController::class, 'createinventoryitemamountindex'])->name('create-inventory-item-amount-index');
    Route::post('add_inv_item_amnt', [HomeController::class, 'createinventoryitemamount'])->name('create-inventory-item-amount');
    Route::get('edit_itm/{id}', [HomeController::class, 'edititemindex'])->name('edit-item-index');
    Route::get('view_itm/{id}', [HomeController::class, 'viewitemindex'])->name('view-item-index');
    Route::post('delte_itm', [HomeController::class, 'deleteitem'])->name('delete-item');
    Route::post('delte_itm_amnt', [HomeController::class, 'deleteitemamount'])->name('delete-item-amount');
    Route::post('edit_itm/{id}', [HomeController::class, 'edititem'])->name('edit-item');
    Route::post('create_itm', [HomeController::class, 'createitem'])->name('create-item');
    Route::post('add_dat', [HomeController::class, 'store'])->name('add-data');
    Route::post('get_dat_ser', [HomeController::class, 'findBySerialCode'])->name('get-data-by-serial');
    Route::post('get_dat_name', [HomeController::class, 'findByItemName'])->name('get-data-by-name');
});

////////////////////////////////////////// sell invoices
Route::group(['prefix' => 'sell'], function () {
    Route::get('create_sell_inv', [InvoiceController::class, 'sell_index'])->name('create-sell-invoice');
    Route::post('create_sell_inv', [InvoiceController::class, 'store'])->name('store-sell-invoice');
    Route::post('sell_get_dat_name', [InvoiceController::class, 'findByItemName'])->name('sell-get-data-by-name');
    Route::post('sell_get_dat_code', [InvoiceController::class, 'findByItemCode'])->name('sell-get-data-by-code');
});

////////////////////////////////////////// buy invoices
Route::group(['prefix' => 'buy'], function () {
    Route::get('create_buy_inv', [InvoiceController::class, 'buy_index'])->name('create-buy-invoice');
});

