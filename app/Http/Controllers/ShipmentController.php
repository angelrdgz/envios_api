<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Shipment;
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
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shipments = Auth::user()->shipments()->get();
        return response()->json(['status' => 'success','data' => $shipments], 200);
    }
}