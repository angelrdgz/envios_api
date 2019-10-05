<?php

namespace App\Http\Controllers;

use App\Shipment;
use App\Invoice;
use App\Payment;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');        
    }

    public function index(Request $request)
    {
        $data = [];
        $deliveryShip = Shipment::where('user_id', $request->user()->id)->where('status', 'deliveried')->whereRaw('MONTH(created_at) = "'.$request->get('month').'"')->count();
        $cancelShip = Shipment::where('user_id', $request->user()->id)->where('status', 'CANCELLED')->whereRaw('MONTH(created_at) = "'.$request->get('month').'"')->count(); 
        $processShip = Shipment::where('user_id', $request->user()->id)->where('status', 'AWAITING')->whereRaw('MONTH(created_at) = "'.$request->get('month').'"')->count(); 
        $totalRecharges = Payment::where('user_id', $request->user()->id)->whereRaw('MONTH(created_at) = "'.$request->get('month').'"')->count(); 
        $totalInvoices = Invoice::where('user_id', $request->user()->id)->whereRaw('MONTH(created_at) = "'.$request->get('month').'"')->count(); 
        $data['totalDeliveied'] = $deliveryShip; 
        $data['totalCancelled'] = $cancelShip; 
        $data['totalProcess'] = $processShip;
        $data['totalRecharges'] = $totalRecharges;
        $data['totalInvoices'] = $totalInvoices; 
        return response()->json(['status'=>'success','data'=>$data], 200);
    }
}
