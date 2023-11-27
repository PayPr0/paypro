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
use App\Models\BusinessClent;
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
    
    /**
     * Search for both client and businesses on invoice
     * 
     * @param Request $request->query()
     * 
     * @return Response
     */

     public function searchBusiness(Request $request)
     {
        $param = $request->query();
        $a =  isset($param['a']) ? $param['a'] : null;
        $b =  isset($param['b']) ? $param['b'] : null;
        $sta = isset($param['sta']) ? statusId($param['sta']) : '';
        $c = isset($param['c']) ? $param['c'] : null;
        $ivid = isset($param['ivid']) ? $param['ivid'] : null;

        $businessClient = BusinessClent::where('client_business_id', 'like', '%' . $c . '%')
            ->first();
       
        $invoices = Invoice::with('payments')
            ->where('client_id', 'like', '%' . $businessClient->client_id ?? null . '%')
            ->where('business_id', auth()->user()->id)
            ->where(function($query)use($a){ return $a ? $query->where('amount',$a) : $query; })
            ->where(function($query)use($b){ return $b ? $query->where('balance',$b) : $query; })
            ->where('invoice_id', 'like', '%' . $ivid . '%')
            ->where('status_id','like','%'. $sta.'%')
            ->where(function ($query) use ($param) {
                if (isset($param['ds']) && !isset($param['de'])) {
                    return  $query->where('created_at', '>=', $param['ds']);
                } elseif (!isset($param['ds']) && isset($param['de'])) {
                    return  $query->where('created_at', '<=', $param['de']);
                } elseif (isset($param['ds']) && isset($param['de'])) {
                    return $query->whereBetween('created_at', [$param['ds'], $param['de']]);
                } else {
                    return $query;
                }
            })
            ->paginate(20);

        return response()->successResponse('', $invoices, Response::HTTP_OK);
     }

    public function searchClient(Request $request)
    {
        $param = $request->query();
        $a =  isset($param['a']) ? $param['a'] : null;
        $b =  isset($param['b']) ? $param['b'] : null;
        $sta = isset($param['sta']) ? statusId($param['sta']) : '';
        $c = isset($param['c']) ? $param['c'] : null;
        $ivid = isset($param['ivid']) ? $param['ivid'] : null;
        $businessClient = BusinessClent::where('client_business_id', 'like', '%' .$c. '%')
                                        ->first();
    
        $invoices = Invoice::with('payments')
            ->where('client_id', auth()->user()->id)
            ->where('business_id', 'like', '%' . $businessClient->business_id ?? null . '%')
            ->where(function($query)use($a){ return $a ? $query->where('amount',$a) : $query; })
            ->where(function($query)use($b){ return $b ? $query->where('balance',$b) : $query; })
            ->where('invoice_id', 'like', '%' . $ivid . '%')
            ->where('status_id','like','%'. $sta.'%')
            ->where(function ($query) use ($param) {
                if (isset($param['ds']) && !isset($param['de'])) {
                    return  $query->where('created_at', '>=', $param['ds']);
                } elseif (!isset($param['ds']) && isset($param['de'])) {
                    return  $query->where('created_at', '<=', $param['de']);
                } elseif (isset($param['ds']) && isset($param['de'])) {
                    return $query->whereBetween('created_at', [$param['ds'], $param['de']]);
                } else {
                    return $query;
                }
            })
            ->paginate(20);

        return response()->successResponse('', $invoices, Response::HTTP_OK);
    }

    

}
