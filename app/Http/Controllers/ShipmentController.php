<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shipment;
use App\Location;
use Auth;

class ShipmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shipments = Auth::user()->shipments()->with('origen','destination')->get();
        return response()->json(['status' => 'success', 'data' => $shipments], 200);
    }

    public function store(Request $request)
    {        
        $this->validate($request, [
            'shipment' => 'required',
            'extraInfo' => 'required',
        ]);

        $shipInfo = $request->input('shipment');
        $extraInfo = $request->input('extraInfo');
        if (!is_null($extraInfo["origen"]["id"])) {
            $origin = Location::find($extraInfo["origen"]["id"]);
        } else {
            $origin = new Location();
            $origin->user_id = Auth::user()->id;
            $origin->type_id = 1;
        
        }
        $origin->name = $shipInfo["address_from"]["name"];
        $origin->phone = $shipInfo["address_from"]["phone"];
        $origin->email = $shipInfo["address_from"]["email"];
        $origin->address = $shipInfo["address_from"]["address1"];
        $origin->address2 = $shipInfo["address_from"]["address2"];
        $origin->city = $shipInfo["address_from"]["city"];
        $origin->state = $shipInfo["address_from"]["province"];
        $origin->country = $shipInfo["address_from"]["country"];
        $origin->zipcode = $shipInfo["address_from"]["zip"];
        $origin->reference = $shipInfo["address_from"]["reference"];
        $origin->nickname = $extraInfo["origen"]["nickname"];
        
        $origin->save();

        if (!is_null($extraInfo["destination"]["id"])) {
            $destination = Location::find($extraInfo["destination"]["id"]);
        } else {
            $destination = new Location();
            $destination->user_id = Auth::user()->id;
            $destination->type_id = 2;
        }
        $destination->name = $shipInfo["address_to"]["name"];
        $destination->phone = $shipInfo["address_to"]["phone"];
        $destination->email = $shipInfo["address_to"]["email"];
        $destination->address = $shipInfo["address_to"]["address1"];
        $destination->address2 = $shipInfo["address_to"]["address2"];
        $destination->city = $shipInfo["address_to"]["city"];
        $destination->state = $shipInfo["address_to"]["province"];
        $destination->country = $shipInfo["address_to"]["country"];
        $destination->zipcode = $shipInfo["address_to"]["zip"];
        $destination->reference = $shipInfo["address_to"]["reference"];
        $destination->nickname = $extraInfo["destination"]["nickname"];
        $destination->save();

        $srEnvio = new SrEnvioController();

        $srEnvioShip = $srEnvio->shipmentTest($shipInfo);

        $shipment = new Shipment();
        $shipment->api_id = $srEnvioShip["data"]["id"];
        $shipment->user_id = Auth::user()->id;
        $shipment->price = $request->input('price');
        $shipment->origin_id = $origin->id;
        $shipment->destination_id = $destination->id;
        $shipment->save();

        $rates = [];

        foreach ($srEnvioShip["included"] as $key => $item) {
            if($item["type"] == "rates"){
                array_push($rates, $item);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Shipment created successfully', "rates"=>$rates], 200);
    }

    public function show($id)
    {
        $shipment = Shipment::find($id);
        return response()->json(['status' => 'success', 'data' => $shipment], 200);
    }

    public function destroy(Request $request, $id)
    {
        $shipment = Shipment::find($id);
        $shipment->status = 'CANCELLED';
        $shipment->delete();
        return response()->json(['status' => 'success', 'message' => 'Shipment cancelled successfully'], 200);
    }
}
