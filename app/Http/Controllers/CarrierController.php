<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarrierController extends Controller
{
    public function index(Request $request){
        $newEnvia = new EnviaController;
        $carriers = $newEnvia->carriers();
        return response()->json($carriers);
    }
}
