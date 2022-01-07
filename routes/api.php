<?php

use App\Http\Controllers\Api\AuthenticationApiController;
use App\Http\Controllers\Api\OrderApiController;
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

Route::post('/register', [AuthenticationApiController::class, 'register'])->name('api_register');
Route::post('/login', [AuthenticationApiController::class, 'login'])->name('api_login');//->middleware("throttle:5,5");

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::post('/order', [OrderApiController::class, 'order'])->name('api_order');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
