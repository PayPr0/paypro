<?php

namespace App\Http\Controllers\ApiV1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public $metalinks;
    public function __construct()
    {
      $this->metalinks =[
        'payments' => route('payments.index'),
        'paymentsSearch' => route('payments.index',['s'=>'pending'])
      ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $param = $request->query();

        $payments = Payment::where('client_id', 'like', '%' . isset($param['c']) ?? null . '%')
                            ->where('business_id', 'like', '%' . isset($param['b']) ?? null . '%')
                            ->where('amount', 'like', '%' . isset($param['a']) ?? null . '%')
                            ->where(function($query)use($param){
                                if (isset($param['ds']) && !isset($param['de'])) {
                                   return  $query->where('created_at', '>=', $param['ds']);
                                } elseif (!isset($param['ds']) && isset($param['de'])) {
                                   return  $query->where('created_at', '<=', $param['de']);
                                } elseif (isset($param['ds']) && isset($param['de'])) {
                                    return $query->whereBetween('created_at', [$param['ds'], $param['de']]);
                                }else{
                                    return $query;
                                }
                            })
                            ->paginate(20);
        
        return response()->successResponse('',$payments,Response::HTTP_OK,$this->metalinks);
                            
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
    
    public function paymentCallback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required',
            "client_id" => 'required',
            "payment_ref"=> "required",
            "amount" => "required",
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

        $paymentService = app(PaymentService::class);
        
        $validatePayment = $paymentService->verifyTransaction($request->payment_ref);

        $invoice = Invoice::where('client_id',$request->client_id)
                            ->where('invoice_id',$request->invoice_id)
                            ->first();
        
        if(!$invoice){
            return response()->errorResponse('invoice does not exist', [], Response::HTTP_BAD_REQUEST);
        }

        if(!$validatePayment) {
            return response()->errorResponse('payment not succesful',[],Response::HTTP_BAD_REQUEST);
        }

        if($invoice->client->email != $validatePayment['email'])
        {
            return response()->errorResponse('payment email does not own this invoice', [], Response::HTTP_BAD_REQUEST);
        }
        
        $data = [
            'invoice_id' => $invoice->id,
            'business_id' => $invoice->business_id,
            'is_online' => $validatePayment['channel'] != 'cash',
            'payment_method' => $validatePayment['channel'],
            'amount' => $validatePayment['amount'],
            'client_id' => $request->client_id,
        ];

        DB::beginTransaction();
        $payment = $paymentService->createPayment($data);

        $balance = $invoice->balance - $validatePayment['amount'];
        $status = $invoice->balance == 0 ? statusId(config('status.paid')):
                             statusId(config('status.Pending'));
        app(InvoiceService::class)->updateInvoice($request->invoice_id,[
            'balance' => $balance,
            'status_id' => $status
        ]);
           
        DB::commit();

        return response()->successResponse('payment validated',
        InvoiceResource::make($invoice)
        ,Response::HTTP_OK,
        $this->metalinks);

    }
}
