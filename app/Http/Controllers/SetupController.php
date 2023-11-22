<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SetupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {

            Log::debug('Starting: Run database migration');

            // run the migration
            Artisan::call('migrate', [
                '--force' => true
            ]);

            Log::debug('Finished: Run database migration');
        } catch (\Exception $e) {
            // log the error
            Log::error($e);

            return response('not ok', 500);
        }

        return response('ok', 200);
    }
}
