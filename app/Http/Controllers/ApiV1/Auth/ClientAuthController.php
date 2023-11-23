<?php

namespace App\Http\Controllers\ApiV1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Notifications\OtpNotification;
use App\Services\OTPService;
use App\Services\RefreshTokenService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ClientAuthController extends Controller
{

    private $metelinks;
    public function __construct()
    {
        $this->metelinks = [
            'getOtp' => route('client.otp'),
            'clientLogin' => route('client.login')
        ];
    }

    public function getOtp(Request $request,OTPService $oTPService)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phone' => "string|nullable",
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();

            return response()->json(
                [
                    'status' => 0,
                    'message' => $message
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $client = Client::where('email',$request->email)
                        ->orWhere('phone',$request->phone)
                        ->first();

        if (!$client) {
            return response()->json(
                [
                    'status' => 0,
                    'message' => "Client not found"
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $client->notify(new OtpNotification($oTPService->generateOtp($client)));

        return response()->successResponse(
            'otp sent',[],
            Response::HTTP_OK,$this->metelinks
        );

    }

    public function otpLogin(Request $request, OTPService $oTPService, RefreshTokenService $refreshTokenService )
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phone' => "string|nullable",
            'otp' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();

            return response()->json(
                [
                    'status' => 0,
                    'message' => $message
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $client = Client::where('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->first();

        if (!$client) {
            return response()->json(
                [
                    'status' => 0,
                    'message' => "client not found"
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $ismatch = $oTPService->verifyOtp($client->id,$request->otp);

        if (!$ismatch[0]) {
            return response()->json(
                [
                    'status' => 0,
                    'message' => $ismatch[1]
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = $client->createToken('clientToken')->plainTextToken;
        $refreshToken = $refreshTokenService->createRefreshToken([
            'client_id' => $client->id
        ]);

        return response()->json(
            [
                'status' => 1,
                'token' => $token,
                'refresh_token' => $refreshToken->refresh_token,
                'expire_at' => Date('d-m-y H:i:s', strtotime(now()->addMinutes(30))),
                'toke_type' => 'Bearer',
                'message' => "Successfully logedin ",
                'client' => $client,
            ],
            Response::HTTP_OK
        );
    }
}
