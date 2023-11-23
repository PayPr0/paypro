<?php

namespace App\Http\Controllers\ApiV1\Auth;

use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use App\Services\RefreshTokenService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class OauthController extends Controller
{
    
    public function redirect(Request $request)
    {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => 'client-id',
            'redirect_uri' => config('appUrl').'/callback',
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            // 'prompt' => '', // "none", "consent", or "login"
        ]);

        return redirect(config('appUrl').'/oauth/authorize?' . $query);
    }

    public function callback(Request $request)
    {
        $state = $request->session()->pull('state');

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class,
            'Invalid state value.'
        );

        $response = Http::asForm()->post(config('appUrl').'/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => 'client-id',
            'client_secret' => 'client-secret',
            'redirect_uri' => config('app.url').'/callback',
            'code' => $request->code,
        ]);

        return $response->json();
    }


    public function refresh(Request $request, RefreshTokenService $refreshTokenService)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|min:6'
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

        $refreshToken = RefreshToken::where('refresh_token',$request->refresh_token)
                                    ->first();

        if(!$refreshToken){
            return response()->errorResponse('Refresh Token does not exist',Response::HTTP_NOT_FOUND);
        }

        if($refreshToken->hasExpire){
            return response()->errorResponse('Refresh token exipired', Response::HTTP_FORBIDDEN);
        }

        $token = $refreshToken->business_id ?
                        $refreshToken->business->createToken('businessToken')->plainTextToken :
                        $refreshToken->client->createToken('clientToken')->plainTextToken;
        
        $refreshToken = $refreshTokenService->createRefreshToken([
            'business_id' => $refreshToken->business_id,
            'client_id' => $refreshToken->client_id
        ]);

        return response()->json(
            [
                'status' => 1,
                'message' => "Successfully refreshed token",
                'token' => $token,
                'refresh_token' => $refreshToken->refresh_token,
                'expire_at' => Date('d-m-y H:i:s', strtotime(now()->addMinutes(30))),
                'toke_type' => 'Bearer',
            ],
            Response::HTTP_OK
        );
       
    }
}
