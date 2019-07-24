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
        //$this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shipments = Shipment::all();//Auth::user()->shipments()->get();
        return response()->json(['status' => 'success', 'data' => $shipments], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'api_id' => 'required',
            'price' => 'required'
        ]);

        if (!is_null($request->input('origin_id'))) {
            $origin = Location::find($request->input('origin_id'));
        } else {
            $origin = new Location();
            $origin->user_id = Auth::user()->id;
            $origin->type_id = 1;
        }
        $origin->address = $request->input('origin_address');
        $origin->address2 = $request->input('origin_address2');
        $origin->city = $request->input('origin_city');
        $origin->state = $request->input('origin_state');
        $origin->country = $request->input('origin_country');
        $origin->zipcode = $request->input('origin_zipcode');
        $origin->reference = $request->input('origin_reference');
        $origin->nickname = $request->input('origin_nickname');
        $origin->save();

        if (!is_null($request->input('destination_id'))) {
            $destination = Location::find($request->input('destination_id'));
        } else {
            $destination = new Location();
            $destination->user_id = Auth::user()->id;
            $destination->type_id = 2;
        }
        $destination->address = $request->input('destination_address');
        $destination->address2 = $request->input('destination_address2');
        $destination->city = $request->input('destination_city');
        $destination->state = $request->input('destination_state');
        $destination->country = $request->input('destination_country');
        $destination->zipcode = $request->input('destination_zipcode');
        $destination->reference = $request->input('destination_reference');
        $destination->nickname = $request->input('destination_nickname');
        $destination->save();

        $shipment = new Shipment();
        $shipment->api_id = $request->input('api_id');
        $shipment->user_id = Auth::user()->id;
        $shipment->price = $request->input('price');
        $shipment->origin_id = $origin->id;
        $shipment->destination_id = $destination->id;
        $shipment->save();

        return response()->json(['status' => 'success', 'message' => 'Shipment created successfully'], 200);
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
