<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use Auth;
use Route;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');        
    }

    public function index(Request $request)
    {
        $invoices = Invoice::where('user_id', Auth::user()->id)->whereRaw('MONTH(created_at) = "'.$request->get('month').'"')->get();
        //$factura = new FacturaController();
        //$invoices = $factura->invoices();
        return response()->json(['status'=>'success','data'=>$invoices], 200);
    }

    public function store(Request $request){
        $factura = new FacturaController();
        $newInvoices = $factura->createInvoice([]);
        return response()->json($newInvoices);
    }

    public function show(Request $request, $id){
        $type = $request->get('type');
        $invoice = Invoice::find($id);
        $factura = new FacturaController();
        $file = $factura->invoice($invoice->uid, $type);
        return $file;
    }

    public function destroy(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $factura = new FacturaController();
        $file = $factura->cancelInvoice($invoice->uid);

        if($file["response"] == 'success'){
            $invoice->status = 'canceled';
            $invoice->save();
            return response()->json(['status'=>'success','message'=>'Invoice cancelled successfuly']);
        }else{
            return response()->json(['status'=>'fail','message'=>$file["message"]]);
        }

    }
}