<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use Auth;

class PointController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');        
    }

    public function getOrigenes(Request $request)
    {
        $locations = $request->user()->origenes()->get();
        return response()->json(['status' => 'success', 'data' => $locations], 200);
    }

    public function getDestinations(Request $request)
    {
        $locations =  $request->user()->destinations;
        return response()->json(['status' => 'success', 'data' => $locations], 200);
    }

    public function show($id)
    {
        $location =  Location::find($id);
        return response()->json(['status' => 'success', 'data' => $location], 200);
    }

    public function destroy(Request $request, $id)
    {
        $location =  Location::find($id);
        $location->status = 0;
        $location->save();
        
        if($location->type_id == 1){
            $locations = $request->user()->origenes()->get();
        }else{
            $locations = $request->user()->destinations()->get();
        }
        
        return response()->json(['status' => 'success', 'data' => $locations], 200);
    }
}