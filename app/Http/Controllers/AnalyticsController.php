<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Services\AnalyticsService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnalyticsController extends Controller
{
    private $metalinks, $analyticSevice;

    public function __construct(AnalyticsService $analyticSevice)
    {
        $this->metalinks = [
            'dailyTransacton' => route('analytics.daily.transaction',['b'=>1]),
            'paymentMethod' => route('analytics.payment.method',['b'=>1]),
            'availableBalance' => route('analytics.balance',['b'=>1]),
            'pendingBalance' => route('analytics.pending', ['b' => 1]),
            'totalClient' => route('analytics.client.count',['b'=>1])
        ];
        $this->analyticSevice = $analyticSevice;
    }
    public function dailyTransaction(Request $request)
    {
        $param = $request->query();
        $business = Business::find($param['b']);
        if(!$business){
            return response()->errorResponse(
                'Businesses not found',
                [],
                Response::HTTP_NOT_FOUND
            );
        }
        
        $data = $this->analyticSevice->dailyTransaction($param['b']);

        return response()->successResponse('', $data, Response::HTTP_OK, $this->metalinks);
    }

    public function paymentMethod(Request $request)
    {
        $param = $request->query();
        $business = Business::find($param['b']);
        if (!$business) {
            return response()->errorResponse(
                'Businesses not found',
                [],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = $this->analyticSevice->paymentByMethod($param['b']);

        return response()->successResponse('', $data, Response::HTTP_OK, $this->metalinks);
    }

    public function totalBalance(Request $request, WalletService $walletService)
    {
        $param = $request->query();
        $business = Business::find($param['b']);
        if(!$business){
            return response()->errorResponse(
                'Businesses not found',
                [],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = $walletService->walletBalance($business);

        return response()->successResponse('', ['balance'=>$data], Response::HTTP_OK, $this->metalinks);
    }

    public function pendingBalance(Request $request)
    {
        $param = $request->query();
        $business = Business::find($param['b']);
        if (!$business) {
            return response()->errorResponse(
                'Businesses not found',
                [],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = $this->analyticSevice->totalPendingBalance($param['b']);
        
        return response()->successResponse('', $data, Response::HTTP_OK, $this->metalinks);
    }

    public function noOfClient(Request $request)
    {
        $param = $request->query();
        $business = Business::find($param['b']);
        if (!$business) {
            return response()->errorResponse(
                'Businesses not found',
                [],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = $this->analyticSevice->noOfClients($param['b']);

        return response()->successResponse('', $data, Response::HTTP_OK, $this->metalinks);
    }
}
