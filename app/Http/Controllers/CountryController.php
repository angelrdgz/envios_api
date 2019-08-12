<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;
use Auth;

class CountryController extends Controller
{

    public function index()
    {
        $countries = Country::orderBy('name', 'ASC')->get();
        return response()->json(['status' => 'success', 'data' => $countries], 200);
    }
}