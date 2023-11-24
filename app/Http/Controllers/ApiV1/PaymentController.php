<?php

namespace App\Http\Controllers\ApiV1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    /**
     * Validate the invoice and return as a JSON response
     */
    public function invoiceValidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'invoice_id' => 'required'
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
        $client = Client::where('email', $request->email)->first();

        if (!$client) {
            return response()->json(
                [
                    'status' => 0,
                    'message' => "client with email not found"
                ],
                Response::HTTP_NOT_FOUND
            );
        }
       
        $invoice = Invoice::where('client_id', $client->id)
                           ->where('invoice_id', $request->invoice_id)
                           ->first();

        if (!$invoice) {
            return response()->json(
                [
                    'status' => 0,
                    'message' => "Business not found"
                ],
                Response::HTTP_NOT_FOUND
            );
        }


        
        return response()->successResponse("",InvoiceResource::make($invoice), Response::HTTP_OK);
    }
    
}
