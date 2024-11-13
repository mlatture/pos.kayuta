<?php

use App\Http\Controllers\API\CheckAvailability;
use App\Http\Controllers\API\DataController;
use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('search-sites', [CheckAvailability::class, 'getSites']);
Route::get('sites-and-reservations', [CheckAvailability::class, 'getReservAndSites']);
Route::get('get_data', [DataController::class, 'getData']);