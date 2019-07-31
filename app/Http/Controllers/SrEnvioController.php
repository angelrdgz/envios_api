<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shipment;
use App\Location;
use Auth;

class SrEnvioController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function quote(Request $request)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('SRENVIO_ENDPOINT').'//quotations');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request->all()));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token token='.env('SRENVIO_TOKEN'),
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return response()->json(['status' => 'success', 'data' => json_decode($result, true)], 200);
    }
}