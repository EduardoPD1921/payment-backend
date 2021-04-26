<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;

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

// Route::group(['middleware' => 'auth:sanctum'], function() {
//     Route::get('/test', [Test::class, 'tester']);
// });

// UserAuth
Route::post('/user/register', [UserController::class, 'store']);
Route::post('/user/login', [AuthController::class, 'login']);

// SearchInfo
Route::get('/search', [SearchController::class, 'search']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    
    // UserInfo
    Route::get('/user/getInfo', [UserController::class, 'getUserInfo']);
    Route::post('/user/update', [UserController::class, 'update']);
    Route::post('/user/emailUpdate', [UserController::class, 'emailUpdate']);
    Route::post('/user/passwordUpdate', [UserController::class, 'passwordUpdate']);
});