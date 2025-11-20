<?php

use App\Http\Controllers\API\CheckAvailability;
use App\Http\Controllers\API\DataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReceiptController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SeasonalTransactionsController;
use App\Http\Controllers\API\IdeaController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([], function () {

    Route::get('/ideas', [IdeaController::class, 'index']);
    Route::post('/ideas/{idea}/approve', [IdeaController::class, 'approve']);
    Route::post('/ideas/{idea}/replace', [IdeaController::class, 'replace']);
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy']);

    Route::get('/posts/calendar', [PostController::class, 'calendar']);
    Route::patch('/posts/{post}/reschedule', [PostController::class, 'reschedule']);

    Route::post('/events/upload', [EventController::class, 'upload']);
    Route::post('/settings/autopilot', [SettingsController::class, 'toggleAutopilot']);
});


Route::post('upload-receipt-logo', [ReceiptController::class, 'uploadReceiptLogo']);

Route::post('seasonal/payment/{user}', [SeasonalTransactionsController::class, 'storeScheduledPayments']);
Route::post('seasonal/payment/remaining-balance/{user}', [SeasonalTransactionsController::class, 'storeRemainingBalance']);
Route::get('search-sites', [CheckAvailability::class, 'getSites']);
Route::get('sites-and-reservations', [CheckAvailability::class, 'getReservAndSites']);

Route::get('/product-image/{filename}', function ($filename) {
    
    $path = storage_path("app/public/products/{$filename}");

    if (!file_exists($path)) {
        abort(404);
    }

    return Response::file($path);
});

Route::get('/api/sites/search', [SiteController::class, 'search'])->name('api.sites.search');