<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use Auth;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index(Request $request)
    {
        $invoices = Invoice::where('user_id', Auth::user()->id)->whereRaw('MONTH(created_at) = "'.date('m').'"')->get();
        return response()->json(['status'=>'success','data'=>$invoices], 200);
    }
}