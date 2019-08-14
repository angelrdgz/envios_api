<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mail;

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
        $data = array('name'=>"Virat Gandhi");
   
      Mail::send('emails.welcome', $data, function($message) {
         $message->to('abc@gmail.com', 'Tutorials Point')->subject('Laravel Basic Testing Mail');
         $message->from('xyz@gmail.com','Virat Gandhi');
      });
      echo "Basic Email Sent. Check your inbox.";

        $validator = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required'
        ],
        [
            'email.required' => 'Su email es requerido',
            'password.required' => 'Su contraseña es requerida',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'fail','errors'=>$validator->errors()], 422);
        }
        $user = User::where('email', $request->input('email'))->first();
        if (is_null($user)) {
            return response()->json(['status' => 'fail', 'errors'=>["credentials"=>"Email o contraseña incorrectos"]], 422);
        } else {
            if (Hash::check($request->input('password'), $user->password)) {
                $apikey = base64_encode(str_random(40));
                User::where('email', $request->input('email'))->update(['api_key' => "$apikey"]);;
                return response()->json(['status' => 'success', 'api_key' => $apikey,'user'=>$user]);
            } else {
                return response()->json(['status' => 'fail', 'errors'=>["credentials"=>"Email o contraseña incorrectos"]], 422);
            }
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'lastname' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'type_id' => 'required',
            'business' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'fail','errors'=>$validator->errors()], 422);
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->lastname = $request->input('lastname');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->type_id = $request->input('type_id');
        $user->business = $request->input('business');
        $user->phone = $request->input('phone');
        $user->mp_token = RechargeController::createCustomer($request->input('email'));
        $user->save();

        return response()->json(['status' => 'success', 'result' => $user]);
    }

    public function getUser(Request $request)
    {

        $user = Auth::user()->todo()->get();
        return response()->json(['status' => 'success', 'result' => $user]);
    }
}
