<?php

namespace App\Http\Controllers\ApiV1;

use App\Models\Invoice;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Http\Requests\CreateInvoiceRequest;
use App\Notifications\InvoiceNotification;

class InvoiceController extends Controller
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
    public function store(CreateInvoiceRequest $request, InvoiceService $invoiceService)
    {
       
        $business = Business::find(auth()->user()->id);

        $client =  $business->clients()->wherePivot('client_id', $request->client_id)->first();

            if (!$client) {
                abort(404);
        };
        
        $invoiceId = uniqid(Str::substr($business->name, 0, 3));

        $invoice =  $invoiceService->createInvoiceForClient($invoiceId, $business->id,
        $client->id, $request->amount, $request->description, $request->amount);

        $client->notify(new InvoiceNotification($invoice));

        return response()->successResponse(
            'invoice created successfully',
            InvoiceResource::make($invoice),
            Response::HTTP_CREATED
        );
        

    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

}
