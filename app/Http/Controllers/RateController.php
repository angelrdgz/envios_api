<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RateController extends Controller
{
    public function store(Request $request)
    {
        $envia = new EnviaController();
        $rate = $envia->rate($request->all());
        return response()->json(["status"=>"success", "data"=>$rate]);
    }
}
