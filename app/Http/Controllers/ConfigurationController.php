<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Configuration;

class ConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');        
    }

    public function index(Request $request)
    {
        $configuration = $request->user()->configuration;
        return response()->json(['status'=>'success','data'=>$configuration], 200);
    }

    public function update(Request $request)
    {
        $configuration = Configuration::where('user_id', $request->user()->id)->update([$request->input("key")=>$request->input("value")]);
        return response(["status"=>"success", "data"=>$configuration],200);
    }
}
