<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function rate(Request $request)
    {
        $envia = new EnviaController();
        $rate = $envia->rate($request->all());
        $ratex = ['meta'=>'rate', 'data'=>[]];
        if ($rate["meta"] == "error") {
            return response()->json(["status" => "error", 'message' => $rate], 500);
        } else {
            if ($rate["data"] == 'Not implemented') {
                return response()->json(["status" => "error", 'message' => $rate], 500);
            } else {
                foreach ($rate["data"] as $r) {
                    array_push($ratex["data"],[
                        "carrier"=>$r["carrier"],
                        "service"=>$r["service"],
                        "totalPrice"=> (intval($r["totalPrice"]) + env('PLUS_VALUE_INDIVIDUAL')),
                        "deliveryEstimate"=>$r["deliveryEstimate"],
                        "currency"=>$r["currency"]
                    ]
                    );
                }
            }
        }
        return response()->json(["status" => "success", "data" => $ratex], 200);
    }
}
