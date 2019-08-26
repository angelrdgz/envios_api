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
        ]);

        $shipInfo = $request->input('shipment');

        $srEnvio = new SrEnvioController();

        $srEnvioShip = $srEnvio->shipmentTest($shipInfo);

        $rates = [];

        foreach ($srEnvioShip["included"] as $key => $item) {
            if($item["type"] == "rates"){
                array_push($rates, $item);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Shipment created successfully', "rates"=>$rates,'shipment_id'=>$srEnvioShip["data"]["id"]], 200);
    }

    public function createLabel(Request $request)
    {
        $this->validate($request, [
            'shipment' => 'required',
            'extraInfo' => 'required',
            'label'=>'required'
        ]);

        $shipInfo = $request->input('shipment');
        $extraInfo = $request->input('extraInfo');
        $labelInfo = $request->input('label');

        if(!is_null(Auth::user()->company_id)){
            if( Auth::user()->company->balance < $labelInfo["price"]){
                return response()->json(['status' => 'fail', 'error' => 'Fondos insuficientes'], 422);
            }
        }else{
            if( Auth::user()->balance < $labelInfo["price"]){
                return response()->json(['status' => 'fail', 'error' => 'Fondos insuficientes'], 422);
            }
        }

        
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
        $origin->company = $shipInfo["address_from"]["company"];
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
        $destination->company = $shipInfo["address_to"]["company"];
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
        $sid = $labelInfo["shipment_id"];
        $price = $labelInfo["price"];
        $carrier = $labelInfo["carrier"];

        $labelInfo["rate_id"] = intval($labelInfo["rate_id"]);
        unset($labelInfo["shipment_id"]);
        unset($labelInfo["price"]);
        unset($labelInfo["carrier"]);

        $srEnvioShip = $srEnvio->labelTest($labelInfo);

        $shipment = new Shipment();
        $shipment->api_id = $sid;
        $shipment->user_id = Auth::user()->id;
        $shipment->price = $price;
        $shipment->carrier = $carrier;
        $shipment->label_id = $srEnvioShip["data"]["id"];
        $shipment->label_url = $srEnvioShip["data"]["attributes"]["label_url"];
        $shipment->tracking_number = $srEnvioShip["data"]["attributes"]["tracking_number"];
        $shipment->tracking_url = $srEnvioShip["data"]["attributes"]["tracking_url_provider"];
        $shipment->origin_id = $origin->id;
        $shipment->destination_id = $destination->id;
        $shipment->save();

        if(!is_null(Auth::user()->company_id)){
            $company = Company::find(Auth::user()->company->id);
            $company->balance = Auth::user()->company->balance - $price;
            $company->save();
        }else{
            $user = User::find(Auth::user()->id);
            $user->balance = Auth::user()->balance - $price;
            $user->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Shipment created successfully', "rates"=>$rates,'shipment_id'=>$srEnvioShip["data"]["id"]], 200);
    }

    public function show($id)
    {
        $shipment = Shipment::find($id);
        return response()->json(['status' => 'success', 'data' => $shipment], 200);
    }

    public function destroy(Request $request, $id)
    {
        $shipment = Shipment::find($id);

        $srEnvio = new SrEnvioController();
        $srEnvioShip = $srEnvio->cancelShipment(["tracking_number"=>$shipment->tracking_number,"reason"=>"Datos de dirección erróneos."]);
        
        $shipment->status = 'CANCELLED';
        $shipment->save();
        return response()->json(['status' => 'success', 'message' => $srEnvioShip], 200);
    }
}
