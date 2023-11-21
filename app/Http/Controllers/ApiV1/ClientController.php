<?php

namespace App\Http\Controllers\ApiV1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Business;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ClientService $clientService )
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:clients,email',
            'phone' => "string|nullable|unique:clients,phone",
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

        $business = Auth::user();
        if(!Business::find($business->id)){
            
            return response()->json(
                [
                    'status' => 0,
                    'message' => "The login user must be a business"
                ],
                Response::HTTP_BAD_REQUEST
            );
            
        }

        $client = $clientService->createClient($request);
        $client->businesses()->attach($business, [
            'business_client_id' => Str::slug($business->name . $client->id . time())
        ]);

        $businessClient = $client->businesses()
                            ->where('id',$business->id)
                            ->first();
        
        $data = [
            ...array($client),
            'business_client_id' => $businessClient->business_client_id
        ];
        return new ClientResource($data);

    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client,ClientService $clientService)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:clients,email,'.$client->id,
            'phone' => "string|nullable|unique:clients,phone,".$client->id,
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

        
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
    }
}
