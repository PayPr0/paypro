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
    private $metalinks;

    public function __construct()
    {
        $this->metalinks = [
            'viewAllClient' => route('clients.index'),
            'updateClient' => route('clients.update'),
            'delectClient' => route('clients.destroy'),
            'getClient' => rout('clients.show')
        ]
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check() && !(Auth::user() instanceof Business)) {
            // The authenticated user is an instance of the Business model
            return response()->errorResponse(
                'Only Businesses can access clients',
                [],
                Response::HTTP_BAD_REQUEST
            );
        }

        $clients = Client::businessClient()->paginate(20);
        return reponse()->successResponse(
            'Retrieved succesfully',
            ClientService::collecion($clients),
            Response::HTTP_OK,
            $this->metalinks
        )
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
            'name' => 'nullable',
            'address'=>'nullable'
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

        if (Auth::check() && !(Auth::user() instanceof Business)) {
            // The authenticated user is an instance of the Business model
            return response()->errorResponse(
                'Only Businesses can access clients',
                [],
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
        if($client){
            return response()->successResponse("Client retrieve sucessfully",ClientResource::make($client),
            Response::HTTP_ACCEPTED,$this->metalinks);
        }

        return response()->errorResponse("Oop Something went wrong");
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

        $update = $clientService->updateClient($request);

        if(!$update){
            return response()->errorResponse("something went wrong",[],Response::HTTP_BAD_REQUEST,);
        }

        $data = [
            'status' => 1,
            'message' => 'Client updated successfully',
            'client' => ClientResource::make($client)
        ];

        return response()->successResponse('Client updated successfully',
            ClientResource::make($client),
            Response::HTTP_OK
        );
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        if($client->delete()){
            return response()->successResponse(
                'Client deleted successfully',
                [],
                Response::HTTP_OK
            );
        }

        return response()->errorResponse("something went wrong", [], Response::HTTP_BAD_REQUEST);
    }
}
