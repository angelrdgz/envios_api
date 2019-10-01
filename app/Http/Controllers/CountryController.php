<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;
use Auth;

class CountryController extends Controller
{

    public function index()
    {
        $newEnvia = new EnviaController();
        $countries = $newEnvia->countries();
        return response()->json(['status' => 'success', 'data' => $countries["data"]], 200);
    }
}