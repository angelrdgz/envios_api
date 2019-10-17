<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shipment;
use App\User;
use App\Company;
use App\Location;
use App\Logbook;
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
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shipments = $request->user()->shipments()->orderBy('created_at', 'DESC')->with('origen', 'destination')->get();
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
            if ($item["type"] == "rates") {
                array_push($rates, $item);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Shipment created successfully', "rates" => $rates, 'shipment_id' => $srEnvioShip["data"]["id"]], 200);
    }

    public function createLabel(Request $request)
    {
        $this->validate($request, [
            'shipment' => 'required',
        ]);

        $shipInfo = $request->input('shipment');
        $price = $request->input('price');
        $extraInfo = $request->input('extraInfo');
        //$labelInfo = $request->input('label');

        if (!is_null($request->user()->business == 1)) {
            if ($request->user()->company->balance < $price) {
                return response()->json(['status' => 'fail', 'error' => 'Fondos insuficientes'], 406);
            }
        } else {
            if ($request->user()->balance < $price) {
                return response()->json(['status' => 'fail', 'error' => 'Fondos insuficientes', 'data' => $request->user()->balance . ' - ' . $price], 406);
            }
        }


        if (!is_null($extraInfo["origen"]["id"])) {
            $origin = Location::find($extraInfo["origen"]["id"]);
        } else {
            $origin = new Location();
            $origin->user_id = $request->user()->id;
            $origin->type_id = 1;
        }
        $origin->name = $shipInfo["origin"]["name"];
        $origin->phone = $shipInfo["origin"]["phone"];
        $origin->email = $shipInfo["origin"]["email"];
        $origin->company = $shipInfo["origin"]["company"];
        $origin->address = $shipInfo["origin"]["street"];
        $origin->number = $shipInfo["origin"]["number"];
        $origin->district = $shipInfo["origin"]["district"];
        $origin->city = $shipInfo["origin"]["city"];
        $origin->state = $shipInfo["origin"]["state"];
        $origin->country = $shipInfo["origin"]["country"];
        $origin->zipcode = $shipInfo["origin"]["postalCode"];
        $origin->reference = $extraInfo["origen"]["reference"];
        $origin->nickname = $extraInfo["origen"]["nickname"];

        $origin->save();

        if (!is_null($extraInfo["destination"]["id"])) {
            $destination = Location::find($extraInfo["destination"]["id"]);
        } else {
            $destination = new Location();
            $destination->user_id = $request->user()->id;
            $destination->type_id = 2;
        }
        $destination->name = $shipInfo["destination"]["name"];
        $destination->phone = $shipInfo["destination"]["phone"];
        $destination->email = $shipInfo["destination"]["email"];
        $destination->company = $shipInfo["destination"]["company"];
        $destination->address = $shipInfo["destination"]["street"];
        $destination->number = $shipInfo["destination"]["number"];
        $destination->district = $shipInfo["destination"]["district"];
        $destination->city = $shipInfo["destination"]["city"];
        $destination->state = $shipInfo["destination"]["state"];
        $destination->country = $shipInfo["destination"]["country"];
        $destination->zipcode = $shipInfo["destination"]["postalCode"];
        $destination->reference = $extraInfo["destination"]["reference"];
        $destination->nickname = $extraInfo["destination"]["nickname"];
        $destination->save();

        $envia = new EnviaController();
        $ship = $envia->newShipment($shipInfo);

        $shipment = new Shipment();
        $shipment->user_id = $request->user()->id;
        $shipment->price = $price;
        $shipment->carrier = $ship["data"][0]["carrier"];
        $shipment->label_url = $ship["data"][0]["label"];
        $shipment->tracking_number = $ship["data"][0]["trackingNumber"];
        $shipment->tracking_url = $ship["data"][0]["trackUrl"];
        $shipment->origin_id = 1;
        $shipment->destination_id = 2;
        $shipment->save();

        if ($request->user()->business == 1) {
            $company = Company::where('owner_id', $request->user()->id)->first();
            $company->balance = $request->user()->company->balance - $price;
            $company->save();
        } else {
            $user = User::find($request->user()->id);
            $user->balance = $request->user()->balance - $price;
            $user->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Shipment created successfully', 'data' => $ship], 200);
    }

    public function show($id)
    {
        $shipment = Shipment::find($id);
        $srEnvio = new SrEnvioController();
        $srEnvioShip = $srEnvio->getShipment($shipment->api_id);
        //$tracking = $srEnvio->getShipment($shipment->api_id);
        return response()->json(['status' => 'success', 'data' => ['shipment' => $shipment, 'srEnvioShipment' => $srEnvioShip["data"]]], 200);
    }

    public function destroy(Request $request, $id)
    {
        $shipment = Shipment::find($id);
        $envia = new EnviaController();
        $ship = $envia->cancelShipment(["trackingNumbers" => $shipment->tracking_number, "carrier" => $shipment->carrier]);
        $shipment->status = 'CANCELLED';
        $shipment->save();
        return response()->json(['status' => 'success', 'data' => $shipment], 200);
    }

    public function payback(Request $request)
    {
        $shipment = Shipment::find($request->id);
        $shipment->status = 'REFUNDED';
        $shipment->save();

        $user = User::find($shipment->user_id);

        if($user->business == 1){
            $company = Company::where('owner_id', $user->id)->first();
            $company->balance = $company->balance + $shipment->price;
            $company->save();
            
        }else{
            $user->balance = $user->balance + $shipment->price;
            $user->save();
        }

        $logbook = new Logbook();
        $logbook->user_id = $request->user()->id;
        $logbook->type = 'reembolso';
        $logbook->total = $shipment->price;
        $logbook->save();

        $shipment->origen;
        $shipment->destination;

        return response()->json(['status' => 'success', 'data' => $shipment], 200);
    }

    public function search(Request $request)
    {
        if (!is_null($request->input('trackingNumber')) && is_null($request->input('company'))) {
            $shipments = Shipment::where('tracking_number', 'like', '%' . $request->input('trackingNumber') . '%')->with('origen', 'destination')->get();
        } elseif (is_null($request->input('trackingNumber')) && !is_null($request->input('company'))) {
            $company = User::where('email', $request->input('company'))->first();
            if (count($company) > 0) {
                $shipments = Shipment::where('user_id', $company->id)->with('origen', 'destination')->get();
            } else {
                $shipments = [];
            }
        } elseif (!is_null($request->input('trackingNumber')) && !is_null($request->input('company'))) {
            $company = User::where('email', $request->input('company'))->first();
            if (count($company) > 0) {
                $shipments = Shipment::where('tracking_number', 'like', '%' . $request->input('trackingNumber') . '%')->where('user_id', $company->id)->with('origen', 'destination')->get();
            } else {
                $shipments = Shipment::where('tracking_number', 'like', '%' . $request->input('trackingNumber') . '%')->with('origen', 'destination')->get();
            }
        }

        return response()->json(['status' => 'success', 'data' => $shipments]);
    }

    public function pick(Request $request)
    {
        $shipment = Shipment::find($request->id);
        $shipment->picked = 1;
        $shipment->save();
        $shipment->origen;
        $shipment->destination;

        return response()->json(['status' => 'success', 'data' => $shipment]);
    }
}
