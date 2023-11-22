<?php

use App\Http\Controllers\ApiV1\Auth\BusinessAuthController;
use App\Http\Controllers\ApiV1\BusinessController;
use App\Http\Controllers\ApiV1\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/v1/')->group(function(){
    
    Route::post('businesses/login',[BusinessAuthController::class,'login']);
    Route::post('businesses/register', [BusinessAuthController::class, 'register']);
    Route::get('business-types',[BusinessController::class,'businessType']);
    
    Route::middleware('auth:sanctum')->group(function () {
       
        Route::post('businesses/logout', [BusinessAuthController::class, 'logout']);
        Route::resource('clients',ClientController::class)->except(['edit','create']);
    });

});