<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Package;
use Auth;

class PackageController extends Controller
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
        $packages = Auth::user()->packages()->get();
        return response()->json(['status' => 'success', 'data' => $packages], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'height' => 'required',
            'width' => 'required',
            'length' => 'required',
            'weight' => 'required',
            'contents' => 'required',
        ]);

        $packages = new Package();
        $packages->user_id = Auth::user()->id;
        $packages->name = $request->input('name');
        $packages->type = $request->input('type');
        $packages->height = $request->input('height');
        $packages->width = $request->input('width');
        $packages->length = $request->input('length');
        $packages->weight = $request->input('weight');
        $packages->contents = $request->input('contents');
        $packages->save();

        return response()->json(['status' => 'success', 'message' => 'Packages created successfully'], 200);
    }

    public function show($id)
    {
        $package = Package::find($id);
        return response()->json(['status' => 'success', 'data' => $package], 200);
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'height' => 'required',
            'width' => 'required',
            'length' => 'required',
            'weight' => 'required',
            'contents' => 'required',
        ]);

        $packages = Package::find($id);
        $packages->name = $request->input('name');
        $packages->type = $request->input('type');
        $packages->height = $request->input('height');
        $packages->width = $request->input('width');
        $packages->length = $request->input('length');
        $packages->weight = $request->input('weight');
        $packages->contents = $request->input('contents');
        $packages->save();

        return response()->json(['status' => 'success', 'message' => 'Packages created successfully'], 200);

    }

    public function destroy(Request $request, $id)
    {
        $shipment = Package::find($id);
        $shipment->delete();
        return response()->json(['status' => 'success', 'message' => 'Shipment cancelled successfully'], 200);
    }
}
