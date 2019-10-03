<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index(Request $request){
        $newEnvia = new EnviaController;
        $carriers = $newEnvia->carriers($request->get("country_code"));
        return response()->json($carriers);
    }

    public function store(Request $request){
        $newEnvia = new EnviaController;
        $quotes = $newEnvia->rate($request->all());
        return response()->json(["status"=>"success","data"=>$quotes["data"]]);
    }
}
