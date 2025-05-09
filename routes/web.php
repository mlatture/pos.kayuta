<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GiftCardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVendorController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\NewReservationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TaxTypeController;
use App\Http\Controllers\ProcessController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CalendarReservationController;
use App\Http\Controllers\PayBalanceController;
use App\Http\Controllers\StationRegisterController;
use App\Http\Controllers\DynamicTableController;
use App\Http\Controllers\API\CheckAvailability;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\CartReservationController;
use App\Http\Controllers\RateTierController;
use App\Http\Controllers\AddOnsController;
use App\Http\Controllers\BusinessSettingController;
Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();

Route::prefix('admin')->middleware('auth')->group(function () {


    Route::post('users',[UserController::class,'store'])->name('users.store');
    Route::get('users_details', [UserController::class, 'getUserName'])->name('user.name');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [SettingController::class, 'index'])->name('settinpgs.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::resource('product-vendors', ProductVendorController::class);
    Route::resource('products', ProductController::class);
    Route::get('category-products', [ProductController::class, 'categoryProducts'])->name('category.products');
    Route::post('/products/toggle-suggested-addon', [ProductController::class, 'toggleSuggestedAddon'])
    ->name('products.toggle-suggested-addon');    Route::resource('categories', CategoryController::class);
    Route::post('/products/update', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');
    Route::patch('/products/{product}/toggle-quick-pick', [ProductController::class, 'toggleQuickPick']);
    Route::patch('/products/{product}/toggle-show-category', [ProductController::class, 'toggleShowCategory']);


    Route::get('get-categories', [CategoryController::class, 'getAllCategories'])->name('category.all');
    Route::patch('/categories/{category}/toggle-show-in-pos', [CategoryController::class, 'toggleShowInPOS']);
    Route::resource('sites', SiteController::class);
    Route::get('sites/add-image/{id}', [SiteController::class, 'addImage'])->name('sites.add-image');  
    Route::delete('/sites/{site}/images/{filename}', [SiteController::class, 'deleteImage'])->name('sites.delete.image');
    Route::post('sites/upload-image/{id}', [SiteController::class, 'uploadImages'])->name('sites.upload.images');
    Route::resource('rate-tier' , RateTierController::class);
    Route::get('rate-tier/add-image/{id}', [RateTierController::class, 'addImage'])->name('rate-tier.add-image');
    Route::post('rate-tier/upload-image/{id}', [RateTierController::class, 'uploadImage'])->name('rate-tier.upload.images');
    Route::delete('rate_tiers/{id}/images/{filename}', [RateTierController::class, 'deleteImage'])->name('rate_tier.delete.image');

    // Route::get('/add-ons', [AddOnsController::class, 'index'])->name('addons.index');
    // Route::get('/add-ons/edit/{id}', [AddOnsController::class, 'edit'])->name('addons.edit');
    // Route::put('/add-ons/update/{id}', [AddOnsController::class, 'update'])->name('addons.update');
    // Route::delete('/add-ons/delete/{id}', [AddOnsController::class, 'destroy'])->name('addons.destroy');
    Route::resource('addons', AddOnsController::class);

    Route::get('sites/view/{site}', [SiteController::class, 'view'])->name('sites.view');
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
    Route::post('orders-submits', [OrderController::class, 'store'])->name('orders.store');
    Route::post('orders-update', [OrderController::class, 'update'])->name('orders.update');
    Route::post('orders-send-email', [OrderController::class, 'sendInvoiceEmail'])->name('orders.send.invoice');
    
    Route::get('reservations/book-site/{bookingId}', [ReservationController::class, 'bookSite'])->name('reservations.book.site');
    Route::get('reservations/site-detail/{siteId}/{bookingId}', [ReservationController::class, 'siteDetail'])->name('reservations.site.detail');
    Route::get('reservations/checkout/{bookingId}', [ReservationController::class, 'checkout'])->name('reservations.checkout');
    Route::post('reservations/add-to-cart', [ReservationController::class, 'addToCart'])->name('reservations.add-to-cart');
    Route::post('reservations/update-dates', [ReservationController::class, 'updateDates'])->name('reservations.updateDates');
    Route::post('reservations/update-sites', [ReservationController::class, 'updateSites'])->name('reservations.update-sites');
    Route::get('reservations/remove-cart/{bookingId}/{cartId}', [ReservationController::class, 'removeCart'])->name('reservations.remove-cart');
    Route::post('reservations/apply-coupon', [ReservationController::class, 'applyCoupon'])->name('reservations.apply-coupon');
    Route::post('reservations/do-checkout/{bookingId}', [ReservationController::class, 'doCheckout'])->name('reservations.do-checkout');
    
    Route::get('/reservations', [NewReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservepeople', [NewReservationController::class, 'getReservations']);
    Route::post('/reservations/update/{id}', [NewReservationController::class, 'updateReservation']);
    Route::get('get-customer-info', [CustomerController::class, 'customerInfo'])->name('customer.info');
    //Reservations
    Route::post('/postcustomer', [NewReservationController::class, 'store']);
    Route::get('/getcustomers', [NewReservationController::class, 'getCustomers']);
    Route::get('/getaddons', [NewReservationController::class, 'getAddon']);
    Route::get('/getsiteclasses', [NewReservationController::class, 'getSiteClasses']);
    Route::get('/getsitehookups', [NewReservationController::class, 'getSiteHookups']);
    Route::get('/getsite', [NewReservationController::class, 'getSites']);
    Route::get('/getnotreserve', [NewReservationController::class, 'noCart']);
    Route::post('/postinfo', [NewReservationController::class, 'storeInfo']);
    Route::post('reservations/payment/{id}/postpayment', [NewReservationController::class, 'storePayment']);
    Route::post('reservations/payment/{id}/postTerminalPayment', [NewReservationController::class,'processPayment']);
    Route::get('reservations/payment/{id}/checkPaymentStatus', [NewReservationController::class, 'checkPaymentStatus']);
    Route::get('reservations/payment/{confirmationNumber}', [NewReservationController::class, 'paymentIndex'])->name('reservations.payment.index');
    Route::get('reservations/invoice/{confirmationNumber}', [NewReservationController::class, 'invoice']);
    Route::delete('reservations/delete/add-to-cart', [NewReservationController::class, 'deleteCart'])->name('reservations.delete.add-to-cart');
    Route::get('reservation-in-cart', [NewReservationController::class, 'reservationInCart'])->name('reservations.reservation-in-cart');
    Route::delete('reservation-in-cart', [NewReservationController::class, 'clearAbandoned'])->name('cart-reservation.clear-abandoned');
    Route::get('reservations/quote-site', [NewReservationController::class, 'quoteSite'])->name('reservations.quoteSite');
    Route::get('reservations/create-reservation', [NewReservationController::class, 'createReservation'])->name('reservations.create-reservation');
    Route::get('lookup-customer', [NewReservationController::class, 'lookupCustomer'])->name('reservations.lookup-customer');
    Route::post('new-reservation/create', [NewReservationController::class, 'createNewReservation'])->name('reservations.create-new-reservation');
    Route::patch('reservations/update-availability', [NewReservationController::class, 'patchAvailability'])->name('reservations.update-availability');
    Route::get('reservations/history/{id}', [NewReservationController::class, 'reservationHistory'])->name('reservations.history');
    Route::patch('reservations/refund', [NewReservationController::class, 'refund']);
    //Cart Reservations
    Route::get('reservaitons-details', [ReservationController::class, 'details'])->name('reservations.details');
    Route::resource('cart-reservation', CartReservationController::class);
    Route::post('reservation/cancel', [CartReservationController::class, 'cancel'])->name('cancel.reservation');
    Route::get('/reservations/load-more-dates', [ReservationController::class, 'loadMoreDates'])
        ->name('reservations.load-more-dates');

    Route::post('reservations/invoice/{id}/paybalance', [PayBalanceController::class, 'payBalance']);
    Route::post('reservations/invoice/{id}/payBalanceCredit', [PayBalanceController::class, 'processCreditCardTerminal']);
    Route::put('reservations/update_checked_in', [NewReservationController::class, 'updateCheckedIn']);
    Route::put('reservations/update_checked_out', [NewReservationController::class, 'updateCheckedOut']);
    Route::resource('reservations', ReservationController::class);
    Route::get('reservations/site-details/{id}', [ReservationController::class, 'siteDetails'])->name('reservations.site-details');
    Route::resource('tax-types', TaxTypeController::class);
    Route::resource('gift-cards', GiftCardController::class);
    Route::get('reports/sales-report', [ReportController::class, 'salesReport'])->name('reports.salesReport');
    Route::get('reports/income-per-site-report', [ReportController::class, 'incomePerSiteReport'])->name('reports.incomePerSiteReport');
    Route::get('reports/z-out-report', [ReportController::class, 'zOutReport'])->name('reports.zOutReport');
    Route::get('reports/reservation-report', [ReportController::class, 'reservationReport'])->name('reports.reservationReport');
    Route::get('reports/gift-card-report', [ReportController::class, 'giftCardReport'])->name('reports.giftCardReport');
    Route::get('reports/tax-report', [ReportController::class, 'taxReport'])->name('reports.taxReport');
    Route::get('reports/payment-report', [ReportController::class, 'paymentReport'])->name('reports.paymentReport');

    Route::post('gift-cards/store', [GiftCardController::class, 'store'])->name('gift-cards.store');
    Route::post('gift-cards/apply', [GiftCardController::class, 'appltGiftCard'])->name('gift-cards.apply');
    Route::get('check-gift-card', [GiftCardController::class,'checkGiftCard'])->name('check.gift-card');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty'])->name('cart.changeQty');
    Route::delete('/cart/delete', [CartController::class, 'delete'])->name('cart.delete');
    Route::delete('/cart/empty', [CartController::class, 'empty'])->name('cart.empty');
    Route::get('cart/get-product-for-receipt', [CartController::class, 'getProductForReceipt'])->name('cart.get-product-for-receipt');
    Route::get('/cart/partialpayment', [CartController::class, 'showPartialPaymentCustomer'])->name('cart.partialpayment');
    Route::get('/cart/processCheckPayment', [CartController::class, 'processCheckPayment'])->name('cart.processCheckPayment');
    Route::post('/registers/set', [StationRegisterController::class, 'set'])->name('registers.set');
    Route::post('/registers/create', [StationRegisterController::class, 'create'])->name('registers.create');
    Route::put('/registers/rename', [StationRegisterController::class, 'rename'])->name('registers.rename');
    Route::get('/registers/get_name', [StationRegisterController::class, 'getStation'])->name('registers.station_name');
    Route::get('/registers/get', [StationRegisterController::class, 'getRegister'])->name('registers.get');
    Route::get('reservations/relocate/{id}', [CalendarReservationController::class, 'index']);
    Route::get('reservations/edit/{id}', [CalendarReservationController::class, 'editReservations'])->name('reservations.edit');
    Route::get('reservations/unavailable-dates', [CalendarReservationController::class, 'getUnavailableDates'])->name('reservations.unavailable-dates');
    Route::post('filter-sites', [CalendarReservationController::class, 'filterSites'])->name('filter.sites');

    Route::put('update-sites-pricing', [CalendarReservationController::class, 'updateSitePricing'])->name('update.pricing');

    Route::post('insert-cards-on-files', [OrderController::class, 'insertCardsOnFiles'])->name('insert.cards.on.files');

    Route::post('/reports/z-out/download-pdf', [PDFController::class, 'generate_zOutPDF'])->name('reports.downloadPdf');

    // Survey Methods

    Route::get('/surveys/index', [SurveyController::class, 'index'])->name('surveys.index');
    Route::post('/surveys/publish', [SurveyController::class, 'store'])->name('surveys.store');
    Route::post('/surveys/store/responses', [SurveyController::class, 'storeResponses'])->name('surveys.store_responses');
    // Route::get('/surveys/{surveyId}/{email}/{siteId}/response-survey/{token}', [SurveyController::class, 'responsesurvey'])->name('surveys.response_survey');
    Route::get('/surveys/published', [SurveyController::class, 'getPublishedSurvey'])->name('surveys.get_published_survey');
    Route::get('/surveys/{survey}/edit', [SurveyController::class, 'edit'])->name('surveys.edit');
    Route::patch('update-survey-status', [SurveyController::class, 'updateStatus'])->name('surveys.update_status');
    Route::patch('/surveys/{survey}', [SurveyController::class, 'update'])->name('surveys.update');
    Route::delete('/surveys/publish-survey', [SurveyController::class, 'destroy'])->name('surveys.destroy');
    Route::get('/surveys/show-responses', [SurveyController::class, 'showResponses'])->name('surveys.show_responses');
    Route::post('/reports/z-out/download-pdf', [PDFController::class, 'generate_zOutPDF'])->name('reports.downloadPdf');
});



Route::prefix('admin')->middleware('auth')->group(static function () {

    Route::get('whitelist', [DynamicTableController::class, 'whitelist'])->name('admin.whitelist');
    Route::post('/admin/update-column-order', [DynamicTableController::class, 'updateColumnOrder'])->name('admin.update-column-order');
    Route::get('business-settings/index', [BusinessSettingController::class, 'index'])->name('admin.business-settings.index');
    Route::post('general-settings/update', [BusinessSettingController::class, 'generalUpdate'])->name('admin.general-settings.update');
    Route::post('cookie-settings/update', [BusinessSettingController::class, 'cookieUpdate'])->name('admin.cookie-settings.update');
    Route::controller(DynamicTableController::class)->group(static function() {
        Route::get('edit-table/{table}', 'edit_table')->name('admin.edit-table');
        Route::put('edit-table/{table}', 'update_table')->name('admin.update-table');
        Route::delete('delete-table/{table}', 'delete_table')->name('admin.delete-table');

        Route::get('dynamic-module-records/{table}', 'dynamic_module_records')->name('admin.dynamic-module-records');
        Route::get('dynamic-module/{table}/{id?}', 'dynamic_module_create_form_data')->name('admin.dynamic-module-create-form-data');
        Route::post('store-dynamic-module/{table}', 'dynamic_module_store_form_data')->name('admin.dynamic-module-store-form-data');
        Route::put('update-dynamic-module/{table}/{id}', 'dynamic_module_update_form_data')->name('admin.dynamic-module-update-form-data');
    });

    Route::get('reservations/relocate/{id}', [CalendarReservationController::class, 'index']);
    Route::get('reservations/unavailable-dates', [CalendarReservationController::class, 'getUnavailableDates'])->name('reservations.unavailable-dates');
    Route::get('get-data', [CheckAvailability::class, 'getData'])->name('get.data.to.push');
});
