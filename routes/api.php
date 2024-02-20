<?php

use App\Http\Controllers\customerController;
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
// password_update
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('forgot_password', [customerController::class, "forgotPassword"]);
    
    Route::post('password_update', [customerController::class, "updatePassword"]);

    Route::get('/users', [customerController::class, 'getAllUsers'])->middleware('checkRole:admin');

    Route::get('/users/{id}', [customerController::class, 'show'])->middleware('checkRole:admin');

    Route::get('/deleteuser/{id}', [customerController::class, 'delete'])->middleware('checkRole:admin');

    Route::post('update/{id}', [customerController::class, 'update'])->middleware('checkRole:admin');


});
Route::post('register', [customerController::class, "register"]);

Route::post('verifyOtp', [customerController::class, "verifyOtp"]);

Route::post('login', [customerController::class, 'login']);










