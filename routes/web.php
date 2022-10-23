<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\CompanyController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\InvoiceController;
use App\Http\Controllers\Dashboard\ShapeController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Auth;
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

Route::get('recover-password/{token}/{user_id}', [HomeController::class, 'recover_password'])->name('recover_password');

// Route::get('homeadmin', [HomeController::class, 'adminIndex'])->name('home-admin');
// Route::get('homebuyer', [HomeController::class, 'buyerIndex'])->name('home-buyer');

// Route::group(['prefix' => 'admin'], function () {
// });

// Route::get('import_data', [HomeController::class, 'importDataWithShapesAndCompanies']);

// Route::group(['middleware' => 'auth'], function () {

//     Route::get('logout', [LoginController::class, 'logout']);

//     Route::get('/', [HomeController::class, 'listitems'])->name('home1');
//     Route::group(['prefix' => 'inventory'], function () {
//         Route::get('getCurrentPrice/{id}/{merchantId}', [HomeController::class, 'getCurrentPriceForElement'])->name('get-current-price-for-element');
//         Route::get('getCurrentPartPrice/{id}/{merchantId}', [HomeController::class, 'getCurrentPartPriceForElement'])->name('get-current-part-price-for-element');
//         Route::get('getMaxPrice/{id}', [HomeController::class, 'getMaxPriceForElement'])->name('get-max-price-for-element');
//         Route::get('getMaxPartPrice/{id}', [HomeController::class, 'getMaxPartPriceForElement'])->name('get-max-part-price-for-element');
//         Route::get('get_itms_na', [HomeController::class, 'getItemsName'])->name('get-items-name');
//         Route::get('list_itm', [HomeController::class, 'listitems'])->name('list-items');
//         Route::get('fast_inventory_list', [HomeController::class, 'fastinventorylist'])->name('fast-inventory-list');
//         Route::post('store_fast_inventory_list', [HomeController::class, 'storefastinventorylist'])->name('store-fast-inventory-list');
//         Route::get('list_invitem_amnt', [HomeController::class, 'listinventoryitemamounts'])->name('list-inventory-item-amounts');
//         Route::get('create_itm', [HomeController::class, 'createitemindex'])->name('create-item-index');
//         Route::get('add_inv_item_amnt/{id}', [HomeController::class, 'createinventoryitemamountindex'])->name('create-inventory-item-amount-index');
//         Route::post('add_inv_item_amnt', [HomeController::class, 'createinventoryitemamount'])->name('create-inventory-item-amount');
//         Route::get('edit_itm/{id}', [HomeController::class, 'edititemindex'])->name('edit-item-index');
//         Route::get('view_itm/{id}', [HomeController::class, 'viewitemindex'])->name('view-item-index');
//         Route::post('delte_itm', [HomeController::class, 'deleteitem'])->name('delete-item');
//         Route::post('delte_itm_amnt', [HomeController::class, 'deleteitemamount'])->name('delete-item-amount');
//         Route::post('edit_itm/{id}', [HomeController::class, 'edititem'])->name('edit-item');
//         Route::post('create_itm', [HomeController::class, 'createitem'])->name('create-item');
//         Route::post('add_dat', [HomeController::class, 'store'])->name('add-data');
//         Route::post('get_dat_ser', [HomeController::class, 'findBySerialCode'])->name('get-data-by-serial');
//         Route::post('get_dat_name', [HomeController::class, 'findByItemName'])->name('get-data-by-name');
//     });

////////////////////////////////////////// sell invoices
Route::group(['prefix' => 'invoice'], function () {
    Route::get('create_sell_inv', [InvoiceController::class, 'sell_index'])->name('create-sell-invoice');
    Route::post('create_sell_inv', [InvoiceController::class, 'sell'])->name('store-sell-invoice');
    Route::post('sell_get_dat_name', [InvoiceController::class, 'findByItemName'])->name('sell-get-data-by-name');
    Route::post('sell_get_dat_code', [InvoiceController::class, 'findByItemCode'])->name('sell-get-data-by-code');
    Route::get('create_buy_inv', [InvoiceController::class, 'buy_index'])->name('create-buy-invoice');
    Route::post('create_buy_inv', [InvoiceController::class, 'buy'])->name('store-buy-invoice');
});

//     ////////////////////////////////////////// buy invoices
//     Route::group(['prefix' => 'buy'], function () {
//         Route::get('create_buy_inv', [InvoiceController::class, 'buy_index'])->name('create-buy-invoice');
//     });
// });

Route::get('view-invoice/{number}', [HomeController::class, 'viewInvoice'])->name('view.invoice');
Route::get('download-invoice/{number}', [HomeController::class, 'downloadInvoice'])->name('download.invoice');
Route::get('save-invoice/{number}', [HomeController::class, 'saveInvoice'])->name('save.invoice');

Auth::routes();


Route::group(['middleware' => 'auth'], function () {

    Route::get('logout', [LoginController::class, 'logout']);
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('change-lang', [HomeController::class, 'change_lang'])->name('change_lang');

    ////////////////////////////// Products //////////////////////////////
    Route::group(['prefix' => 'product'], function () {
        Route::get('get_itms_na', [HomeController::class, 'getItemsName'])->name('get-items-name');
        Route::post('get_dat_name', [HomeController::class, 'findByItemName'])->name('get-data-by-name');
        Route::post('get_dat_ser', [HomeController::class, 'findBySerialCode'])->name('get-data-by-serial');
        Route::match(['post', 'get'], 'create', [HomeController::class, 'create'])->name('product-create');
        Route::match(['post', 'get'], 'list_products', [HomeController::class, 'listProducts'])->name('product-list');
        Route::post('delte_product', [HomeController::class, 'deleteProduct'])->name('delete-product');
        Route::get('edit_product/{id}', [HomeController::class, 'editProduct'])->name('edit-product');
        Route::post('update_product/{id}', [HomeController::class, 'editProduct'])->name('update-product');
        Route::get('show_product/{id}', [HomeController::class, 'showProduct'])->name('show-product');
    });

    ////////////////////////////// Companies //////////////////////////////
    Route::group(['prefix' => 'company'], function () {
        Route::match(['get', 'post'], 'company_create', [CompanyController::class, 'create'])->name('company-create');
        Route::match(['post', 'get'], 'list_companies', [CompanyController::class, 'list_companies'])->name('list-companies');
        Route::get('show_company/{id}', [CompanyController::class, 'show_company'])->name('show-company');
        Route::match(['post', 'get'], 'update_company/{id}', [CompanyController::class, 'update_company'])->name('update-company');
        Route::post('delete_company', [CompanyController::class, 'deleteCompany'])->name('delete-company');
        Route::post('translate_to_ar', [CompanyController::class, 'translate_to_ar'])->name('translate_to_ar');
        Route::post('translate_to_en', [CompanyController::class, 'translate_to_en'])->name('translate_to_en');
        Route::get('get_companies/{merchant_type}', [HomeController::class,'get_companies'])->name('get-companies');
    });
    
    ////////////////////////////// Shapes //////////////////////////////
    Route::group(['prefix' => 'shape'], function () {
        Route::match(['get', 'post'], 'shape_create', [ShapeController::class, 'create'])->name('shape-create');
        Route::match(['post', 'get'], 'list_shapes', [ShapeController::class, 'list_shapes'])->name('list-shapes');
        Route::get('show_shape/{id}', [ShapeController::class, 'show_shape'])->name('show-shape');
        Route::match(['post', 'get'], 'update_shape/{id}', [ShapeController::class, 'update_shape'])->name('update-shape');
        Route::post('delete_shape', [ShapeController::class, 'deleteshape'])->name('delete-shape');
        Route::get('get_shapes/{merchant_type}', [HomeController::class,'get_shapes'])->name('get-shapes');
    });

    ////////////////////////////// Users //////////////////////////////
    Route::group(['prefix' => 'user'], function () {
        Route::match(['get', 'post'], 'user_create', [UserController::class, 'create'])->name('user-create');
        Route::match(['post', 'get'], 'list_users', [UserController::class, 'list_users'])->name('list-users');
        Route::get('show_user/{id}', [UserController::class, 'show_user'])->name('show-user');
        Route::match(['post', 'get'], 'update_user/{id}', [UserController::class, 'update_user'])->name('update-user');
        Route::post('delete_user', [UserController::class, 'delete_user'])->name('delete-user');
        Route::match(['get', 'post'], 'products_assign', [UserController::class, 'products_assign'])->name('products-assign');
        Route::post('assing_user_products', [UserController::class, 'assing_user_products'])->name('assing_user_products');
        Route::match(['get', 'post'], 'list_assigned_products/{id}', [UserController::class, 'list_assigned_products'])->name('list_assigned_products');
    });

    ////////////////////////////// Cities //////////////////////////////
    Route::get('get_cities/{province}', [UserController::class, 'get_cities'])->name('get_cities');
});
