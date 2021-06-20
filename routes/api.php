<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TransactionController;

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

// UserAuth
Route::post('/user/register', [UserController::class, 'store']);
Route::post('/user/login', [AuthController::class, 'login']);

// SearchInfo
Route::get('/search', [SearchController::class, 'search']);
Route::get('/searchById', [SearchController::class, 'returnUserSearched']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::group(['prefix' => 'user'], function() {
        Route::get('/getInfo', [UserController::class, 'getUserInfo']);
        Route::post('/update', [UserController::class, 'update']);
        Route::post('/emailUpdate', [UserController::class, 'emailUpdate']);
        Route::post('/passwordUpdate', [UserController::class, 'passwordUpdate']);
    });

    // Transactions
    Route::post('/transaction/create', [TransactionController::class, 'store']);
});