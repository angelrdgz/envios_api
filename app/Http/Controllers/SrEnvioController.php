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

        curl_setopt($ch, CURLOPT_URL, env('SRENVIO_ENDPOINT').'/quotations');
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

    public function shipment(Request $request)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('SRENVIO_ENDPOINT').'/shipments');
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

    public function shipmentTest($data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('SRENVIO_ENDPOINT').'/shipments');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token token='.env('SRENVIO_TOKEN'),
            "Content-Type: application/json"
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result, true);
    }

    public function labelTest($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('SRENVIO_ENDPOINT').'/labels');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token token='.env('SRENVIO_TOKEN'),
            "Content-Type: application/json"
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result, true);
    }

    public function label(Request $request)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('SRENVIO_ENDPOINT').'/labels');
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

    public function cancelShipment($data){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('SRENVIO_ENDPOINT').'/cancel_label_requests');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token token='.env('SRENVIO_TOKEN'),
            "Content-Type: application/json"
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result, true);

    }

    public function getShipment($id){
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, env('SRENVIO_ENDPOINT').'/shipments/'.$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }
}
