<?php

namespace App\Http\Controllers\ApiV1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Business;
use App\Models\BusinessClent;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    private $metalinks;

    public function __construct()
    {
        $this->metalinks = [
            'viewAllClient' => route('clients.index'),
            'updateClient' => route('clients.update',"client_id"),
            'createClient' => route('clients.store'),
            'delectClient' => route('clients.destroy',"client_id"),
            'getClient' => route('clients.show',"client_id"),
            'search' => route('clients.search')
        ];
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
        $business = Business::find(auth()->user()->id);
        $clients =$business->clients()->paginate(20);;

        return response()->successResponse(
            'Retrieved succesfully',
            ClientResource::collection($clients),
            Response::HTTP_OK,
            $this->metalinks
        );
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

        $business = Business::find(auth()->user()->id);

        try{
            DB::beginTransaction();

            $client = $clientService->createClient($request);
            
            $client->businesses()->attach($business, [
                'client_business_id' => Str::slug($business->name."/".$client->id . time())
            ]);
            
            DB::commit();

            // $client1 =  $business->clients()->wherePivot('client_id',$client->id)->first();

            $client1 = Business::join('business_clents',"businesses.id", "=" ,"business_clents.business_id")
                                ->where('business_clents.client_id',$client->id)
                                ->first('client_business_id');

            $data = [
                'client' => ClientResource::make($client),
                'business_client_id' => $client1->client_business_id
            ];
            return response()->successResponse(
                'Client created',
                $data,
                Response::HTTP_OK,
                $this->metalinks
            );
        }catch(\Exception $e)
        {
            DB::rollBack();

            return response()->errorResponse(
                'something went wrong',
                [],
                Response::HTTP_BAD_REQUEST
            );
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        if($client){
            return response()->successResponse(
                "Client retrieve sucessfully",
                ClientResource::make($client),
            Response::HTTP_ACCEPTED,$this->metalinks);
        }

        return response()->errorResponse("Oop client does not exit");
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

        $update = $clientService->updateClient($client,$request);

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

    /**
     * Search by email / phone number  or by bussiness customer id
     */

     public function search(Request $request)
     {
        $param = $request->query();

        if(isset($param['bcid']))
        {
            $data = BusinessClent::with('client')->where('client_business_id','like', '%'. $param['bcid'].'%')
                                    ->where('business_id', auth()->user()->id)
                                    ->first()->toArray();

            return response()->successResponse('',$data,Response::HTTP_OK,$this->metalinks);
        }

        $e = isset($param['e']) ? $param['e'] : null;
        $p = isset($param['p']) ? $param['p'] : null ;

        $client = Client::where('email','like','%'. $e .'%')
                        ->orWhere('phone', 'like', '%' . $p. '%')
                        ->first();

        $data = BusinessClent::with('client')->where('client_id', $client->id)
            ->where('business_id',auth()->user()->id)
        ->first()->toArray();

        return response()->successResponse('', $data, Response::HTTP_OK, $this->metalinks);

     }

}
