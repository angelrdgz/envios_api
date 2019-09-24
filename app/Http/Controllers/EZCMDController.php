<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use Auth;

class EZCMDController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');        
    }

    public function getLocations(Request $request)
    {
        $str = '?';
        foreach($request->all() as $key => $item){
             $str .= $key.'='.$item.'&';
        }
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, env('EZCMD_ENDPOINT')."/".env('EZCMD_API_KEY')."/".env('EZCMD_USER_ID').$str);
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        $result = curl_exec($ch);
        // close curl resource to free up system resources
        curl_close($ch);

        return response()->json(['data'=>json_decode($result, true)],200);
        
    }
}