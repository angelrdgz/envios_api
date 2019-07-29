<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use Auth;

class LocationController extends Controller
{

    public function getOrigenes()
    {
        $locations = Location::where('type_id', 1)->get();//Auth::user()->shipments()->get();
        return response()->json(['status' => 'success', 'data' => $locations], 200);
    }

    public function getDestinations()
    {
        $locations = Location::where('type_id', 2)->get();//Auth::user()->shipments()->get();
        return response()->json(['status' => 'success', 'data' => $locations], 200);
    }
}