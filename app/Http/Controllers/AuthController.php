<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->input('email'))->first();
        if (is_null($user)) {
            return response()->json(['status' => 'fail'], 401);
        } else {
            if (Hash::check($request->input('password'), $user->password)) {
                $apikey = base64_encode(str_random(40));
                User::where('email', $request->input('email'))->update(['api_key' => "$apikey"]);;
                return response()->json(['status' => 'success', 'api_key' => $apikey,'user'=>$user]);
            } else {
                return response()->json(['status' => 'fail'], 401);
            }
        }
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'password' => 'required',
            'type_id' => 'required',
            'business' => 'required',
            'phone' => 'required',
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->lastname = $request->input('lastname');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->type_id = $request->input('type_id');
        $user->business = $request->input('business');
        $user->phone = $request->input('phone');
        $user->save();

        return response()->json(['status' => 'success', 'result' => $user]);
    }

    public function getUser(Request $request)
    {

        $user = Auth::user()->todo()->get();
        return response()->json(['status' => 'success', 'result' => $user]);
    }
}
