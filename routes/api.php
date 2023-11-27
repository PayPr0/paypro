<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ApiV1\Auth\BusinessAuthController;
use App\Http\Controllers\ApiV1\Auth\ClientAuthController;
use App\Http\Controllers\ApiV1\Auth\OauthController;
use App\Http\Controllers\ApiV1\BusinessController;
use App\Http\Controllers\ApiV1\ClientController;
use App\Http\Controllers\ApiV1\InvoiceController;
use App\Http\Controllers\ApiV1\PaymentController;
use App\Http\Controllers\ApiV1\StatusController;
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
    Route::post('auth/refresh-token',[OauthController::class,'refresh'])->name('auth.token.refresh');
    //Business
    Route::post('businesses/login',[BusinessAuthController::class,'login']);
    Route::post('businesses/register', [BusinessAuthController::class, 'register']);
    Route::get('business-types',[BusinessController::class,'businessType']);

    //Client
    Route::post('send-otp', [ClientAuthController::class, 'getOtp'])->name('client.otp');
    Route::post('client/login', [ClientAuthController::class, 'otpLogin'])->name('client.login');

    //Payments
    Route::post('payments/validate-invoice',[PaymentController::class,'invoiceValidate'])->name('payments.validate.invoice');
    Route::post('payments/metadata-callback',[PaymentController::class, 'paymentCallback'])->name('payments.metadata');
    
    Route::middleware('auth:sanctum')->group(function () {
        
        Route::post('businesses/logout', [BusinessAuthController::class, 'logout']);

        //client
        Route::get('clients/search',[ClientController::class,'search'])->name('clients.search');
        Route::resource('clients', ClientController::class)->except(['edit', 'create']);

        //Invoice
        Route::get('invoice/business-search',[InvoiceController::class,'searchBusiness'])->name('invoice.business.search');
        Route::get('invoice/client-search',[InvoiceController::class,'searchClient'])->name('invoice.client.search');
        Route::resource('invoice', InvoiceController::class)->except(['edit', 'create']);

        //payments
        Route::resource('payments',PaymentController::class)->only(['index']);

        //analytics
        Route::get('analytics/daily-transaction',[AnalyticsController::class,'dailyTransaction'])->name('analytics.daily.transaction');
        Route::get('analytics/payment-method',[AnalyticsController::class, 'paymentMethod'])->name('analytics.payment.method');
        Route::get('analytics/total-balance', [AnalyticsController::class, 'totalBalance'])->name('analytics.balance');
        Route::get('analytics/pending-balance', [AnalyticsController::class, 'pendingBalance'])->name('analytics.pending');
        Route::get('analytics/no-of-client', [AnalyticsController::class, 'noOfClient'])->name('analytics.client.count');

        //status
        Route::get('statuses',StatusController::class);

    });

});