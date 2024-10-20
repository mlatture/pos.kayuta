<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DynamicTableController;
use App\Http\Controllers\GiftCardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVendorController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NewReservationController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TaxTypeController;
use App\Http\Controllers\ProcessController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CalendarReservationController;
use App\Http\Controllers\PayBalanceController;
use App\Http\Controllers\StationRegisterController;

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();

Route::prefix('admin')->middleware('auth')->group(function () {


    Route::post('users',[\App\Http\Controllers\UserController::class,'store'])->name('users.store');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::resource('product-vendors', ProductVendorController::class);
    Route::resource('products', ProductController::class);
    Route::get('category-products', [ProductController::class, 'categoryProducts'])->name('category.products');
    Route::resource('categories', CategoryController::class);
    Route::get('get-categories', [CategoryController::class, 'getAllCategories'])->name('category.all');
    Route::resource('sites', SiteController::class);
    Route::resource('customers', CustomerController::class);
    Route::group(['middleware' => 'master_admin'],function(){
        Route::resource('organizations', OrganizationController::class)->except(['show']);
        Route::resource('admin-roles', AdminRoleController::class)->except(['show']);
        Route::resource('admins',AdminController::class)->except(['show']);
    });
    Route::resource('orders', OrderController::class);
    Route::get('orders-generate-invoice/{id}', [OrderController::class, 'generateInvoice'])->name('orders.generate.invoice');
    Route::get('orders-to-return', [OrderController::class, 'ordersToBeReturn'])->name('orders.to.be.return');
    Route::post('orders/process-refund', [ProcessController::class, 'processRefund'])->name('orders.process.refund');
    Route::post('orders/process-gift-card', [ProcessController::class, 'processGiftCard'])->name('orders.process.gift.card');
    Route::post('orders/process-gift-card-balance', [ProcessController::class, 'updateGiftCardBalance'])->name('orders.process.gift.card.balance');
    Route::post('orders/process-credit-card', [ProcessController::class, 'processCreditCard'])->name('orders.process.credit.card');
    Route::post('orders/process-terminal', [ProcessController::class, 'processTerminal'])->name('orders.process.terminal');
    Route::post('orders-submits', [OrderController::class, 'store'])->name('orders.store.process');
    Route::post('orders-update', [OrderController::class, 'update'])->name('orders.update.process');
    Route::post('orders-send-email', [OrderController::class, 'sendInvoiceEmail'])->name('orders.send.invoice');

    Route::get('/reservations', [NewReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservepeople', [NewReservationController::class, 'getReservations']);
    Route::post('/reservations/update/{id}', [NewReservationController::class, 'updateReservation']);
    Route::get('get-customer-info', [CustomerController::class, 'customerInfo'])->name('customer.info');
    //Reservations

    Route::post('/postcustomer', [NewReservationController::class, 'store']);
    Route::get('/getcustomers', [NewReservationController::class, 'getCustomers']);
    Route::get('/getsiteclasses', [NewReservationController::class, 'getSiteClasses']);
    Route::get('/getsitehookups', [NewReservationController::class, 'getSiteHookups']);
    Route::get('/getsite', [NewReservationController::class, 'getSites']);
    Route::get('/getnotreserve', [NewReservationController::class, 'noCart']);
    Route::post('/postinfo', [NewReservationController::class, 'storeInfo']);
    Route::post('reservations/payment/{id}/postpayment', [NewReservationController::class, 'storePayment']);
    Route::post('reservations/payment/{id}/postTerminalPayment', [NewReservationController::class, 'postTerminalPayment']);
    Route::get('reservations/payment/{id}/checkPaymentStatus', [NewReservationController::class, 'checkPaymentStatus']);
    Route::get('reservations/payment/{id}', [NewReservationController::class, 'paymentIndex']);
    Route::get('reservations/invoice/{id}', [NewReservationController::class, 'invoice']);
    Route::post('reservations/invoice/{id}/paybalance', [NewReservationController::class, 'payBalance']);


    Route::delete('reservations/delete/add-to-cart', [NewReservationController::class, 'deleteCart'])->name('reservations.delete.add-to-cart');
    
    Route::post('reservations/invoice/{id}/paybalance', [PayBalanceController::class, 'payBalance']);
    Route::post('reservations/invoice/{id}/payBalanceCredit', [PayBalanceController::class, 'processCreditCardTerminal']);

    Route::resource('reservations', ReservationController::class);
    Route::get('reservations/site-details/{id}', [ReservationController::class, 'siteDetails'])->name('reservations.site-details');
    Route::resource('tax-types', TaxTypeController::class);
    Route::resource('gift-cards', GiftCardController::class);
    Route::get('reports/sales-report', [ReportController::class, 'salesReport'])->name('reports.salesReport');
    Route::get('reports/reservation-report', [ReportController::class, 'reservationReport'])->name('reports.reservationReport');
    Route::get('reports/gift-card-report', [ReportController::class, 'giftCardReport'])->name('reports.giftCardReport');
    Route::get('reports/tax-report', [ReportController::class, 'taxReport'])->name('reports.taxReport');
    Route::get('reports/payment-report', [ReportController::class, 'paymentReport'])->name('reports.paymentReport');

    Route::post('gift-cards/store', [GiftCardController::class, 'store'])->name('gift-cards.store.process');
    Route::post('gift-cards/apply', [GiftCardController::class, 'appltGiftCard'])->name('gift-cards.apply');
    Route::get('check-gift-card', [GiftCardController::class, 'checkGiftCard'])->name('check.gift-card');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty'])->name('cart.changeQty');
    Route::delete('/cart/delete', [CartController::class, 'delete'])->name('cart.delete');
    Route::delete('/cart/empty', [CartController::class, 'empty'])->name('cart.empty');
    Route::get('/cart/partialpayment', [CartController::class, 'showPartialPaymentCustomer'])->name('cart.partialpayment');

});

Route::prefix('admin')->middleware('auth')->group(static function () {

    Route::get('whitelist', [DynamicTableController::class, 'whitelist'])->name('admin.whitelist');
    Route::post('/admin/update-column-order', [DynamicTableController::class, 'updateColumnOrder'])->name('admin.update-column-order');
    Route::get('/cart/partialpayment', [CartController::class, 'showPartialPaymentCustomer'])->name('cart.partialpayment');

    Route::get('reservations/relocate/{id}', [CalendarReservationController::class, 'index']);
    Route::controller(DynamicTableController::class)->group(static function() {
        Route::get('edit-table/{table}', 'edit_table')->name('admin.edit-table');
        Route::put('edit-table/{table}', 'update_table')->name('admin.update-table');
        Route::post('add-table', 'add_table')->name('admin.add-table');
        Route::delete('delete-table/{table}', 'delete_table')->name('admin.delete-table');

        Route::get('dynamic-module-records/{table}', 'dynamic_module_records')->name('admin.dynamic-module-records');
        Route::get('dynamic-module/{table}/{id?}', 'dynamic_module_create_form_data')->name('admin.dynamic-module-create-form-data');
        Route::post('store-dynamic-module/{table}', 'dynamic_module_store_form_data')->name('admin.dynamic-module-store-form-data');
        Route::put('update-dynamic-module/{table}/{id}', 'dynamic_module_update_form_data')->name('admin.dynamic-module-update-form-data');
    });

    Route::post('/registers/set', [StationRegisterController::class, 'set'])->name('registers.set');
    Route::post('/registers/create', [StationRegisterController::class, 'create'])->name('registers.create');
    Route::get('reservations/relocate/{id}', [CalendarReservationController::class, 'index']);
    Route::get('reservations/unavailable-dates', [CalendarReservationController::class, 'getUnavailableDates'])->name('reservations.unavailable-dates');

});
