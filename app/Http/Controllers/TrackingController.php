<?php

namespace App\Http\Controllers;
use App\Shipment;

use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show(Request $request, $id){
        $shipment = Shipment::find($id);
        $envia = new EnviaController();
        $track = $envia->tracking(["trackingNumbers"=>[$shipment->tracking_number]]);
        return response()->json(["status"=>'success', 'data'=>$track]);
    }
}
