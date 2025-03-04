<?php

use App\Http\Controllers\API\CheckAvailability;
use App\Http\Controllers\API\DataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReceiptController;
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

Route::post('upload-receipt-logo', [ReceiptController::class, 'uploadReceiptLogo']);

Route::get('search-sites', [CheckAvailability::class, 'getSites']);
Route::get('sites-and-reservations', [CheckAvailability::class, 'getReservAndSites']);

Route::get('/product-image/{filename}', function ($filename) {
    
    $path = storage_path("app/public/products/{$filename}");

    if (!file_exists($path)) {
        abort(404);
    }

    return Response::file($path);
});