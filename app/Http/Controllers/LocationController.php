<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use Auth;

class LocationController extends Controller
{

    public function getOrigenes()
    {
        $locations = Auth::user()->origenes()->get();
        return response()->json(['status' => 'success', 'data' => $locations], 200);
    }

    public function getDestinations()
    {
        $locations =  Auth::user()->destinations()->get();
        return response()->json(['status' => 'success', 'data' => $locations], 200);
    }
}