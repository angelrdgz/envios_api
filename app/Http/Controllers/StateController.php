<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index(Request $request)
    {
        $newEnvia = new EnviaController();
        $states = $newEnvia->states($request->get('country_code'));
        return response()->json(['status' => 'success', 'data' => $states["data"]], 200);
    }
}
