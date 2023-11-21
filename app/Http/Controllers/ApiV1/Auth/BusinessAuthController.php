<?php

namespace App\Http\Controllers\ApiV1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessRequest;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use App\Services\BusinessService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BusinessAuthController extends Controller
{
    /**
     * login in a business owner
     * 
     * @param email
     * @param password
     * 
     * @return Response {'token':'gkhlmskljdisljkdjskildisjdisdjskild'}
     */
    public function login(Request $request)
    {   
        $validator = Validator::make($request->all(),[
            'email' => 'nullable|email',
            'phone' =>"string|nullable",
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
           
            return response()->json([
                'status'=>0,
                'message'=>$message
            ],
            Response::HTTP_BAD_REQUEST
            );
        }

        $business = Business::where('email',$request->email)
                            ->orWhere('phone',$request->phone)
                            ->first();

        if(!$business->id){
            return response()->json([
                'status'=>0,
                'message'=>"Business not found"
            ],
            Response::HTTP_NOT_FOUND
            );
        }

        $ismatch = Hash::check($request->password, $business->password);

        if(!$ismatch){
            return response()->json([
                'status'=> 0,
                'message'=>"Password didn't match"
            ],
            Response::HTTP_UNAUTHORIZED
            );
        }

        $token = $business->createToken('businessToken')->plainTextToken;
        
        return response()->json([
            'status' => 1,
            'token' => $token,
            'message'=> "Successfully loged in",
            'business' => $business
            ],
            Response::HTTP_OK
        );

    }

    /**
     * log out a business owner
     * 
     * @return Response {'message':'Successfully logged out'}
     */
    public function logout()
    {
        $business = Auth::user();
        // dd($business);
        $business->tokens()->delete();
        
        return response()->json([
            "status" => 1,
            "message" => "Business logged out successfully"
        ]);
    }

    /**
     * register a new business
     * 
     * @param Request $request
     * @return Response {'status':1,'message':'Business registered successfully'}
     */
    public function register(BusinessRequest $request, BusinessService $businessService)
    {
        try{

            $business = $businessService->createBusiness($request);
            
            return new BusinessResource($business);
        }catch(Exception $e){
            return response()->json([
                'status'=>0,
                'message'=> "an error occur",      
            ],Response::HTTP_BAD_REQUEST);
        }
    }
}
