<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarrierController extends Controller
{
    public function index(Request $request){
        $newEnvia = new EnviaController;
        $carriers = $newEnvia->carriers($request->get("country_code"));
        return response()->json($carriers);
    }
}
