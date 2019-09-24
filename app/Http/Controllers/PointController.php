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

    public function getOrigenes()
    {
        $locations = Auth::user()->origenes()->get();
        return response()->json(['status' => 'success', 'data' => $locations], 200);
    }

    public function getDestinations()
    {
        $locations =  Auth::user()->destinations;
        return response()->json(['status' => 'success', 'data' => $locations], 200);
    }

    public function show($id)
    {
        $location =  Location::find($id);
        return response()->json(['status' => 'success', 'data' => $location], 200);
    }
}