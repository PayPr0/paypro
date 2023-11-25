<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Config::set('paystack.secret', env('PAYSTACK_SECRET_KEY'));
        // Api success response
        Response::macro('successResponse', function ($message = null, $data = [], $code = 200,$mata=[]) {
            return response()->json([
                'message' => $message,
                'data' => $data,
                'meta-links' => $mata
            ], $code);
        });

        // Api error response
        Response::macro('errorResponse', function ($message = null, $errors = [], $code = 400) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], $code);
        });
    }
}
