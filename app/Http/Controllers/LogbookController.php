<?php

namespace App\Http\Controllers;

use App\Logbook;

use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');        
    }

    public function index(Request $request)
    {
        $logbooks = Logbook::where('user_id', $request->user()->id)->whereRaw('MONTH(created_at) = "'.$request->get('month').'"')->get();
        return response()->json(['status'=>'success','data'=>$logbooks], 200);
    }
}
