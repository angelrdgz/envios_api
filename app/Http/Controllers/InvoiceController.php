<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use Auth;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index(Request $request)
    {
        $factura = new FacturaController();
    }
}