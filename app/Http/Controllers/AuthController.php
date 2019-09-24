<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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

    public function testEmail()
    {
        return view('emails.recharge', ["total"=>5200, "comision"=>120, "balance"=>18000,'user'=>'Angel Garcia','service'=>'Mercado Pago']);
    }

    public function login(Request $request)
    {
        if(Auth::attempt(["email" => $request->email, "password" => $request->password])){ 
            $user = Auth::user();
            $success["token"] = $user->createToken("myApp")->accessToken;
            $success["user"] = $user;
            return response()->json(["success" => $success], 200); 
        } else{ 
            return response()->json(["error"=>"Unauthorised"], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            "name" => "required", 
            "email" => "required|email", 
            "password" => "required", 
            "confirm_password" => "required|same:password",
        ]); 

        if ($validator->fails()) { 
            return response()->json(["error"=>$validator->errors()], 401); 
        } 

        $input = $request->all(); 
        $input["password"] = Hash::make($input["password"]);
        $user = User::create($input); 

        $success["token"] = $user->createToken("myApp")->accessToken;
        $success["name"] = $user->name;
        return response()->json(["success"=>$success], 200); 
    }

    public function activeAccount($hash)
    {
        $user = User::where('hash', $hash)->first();
        if(is_null($user)){            
            return response()->json(['status' => 'fail', 'errors' => ["error"=>"Not found"]], 422);
        }else{
            $apikey = base64_encode(str_random(40));
            $user->api_key = $apikey;
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->hash = NULL;
            $user->save();

            Mail::send('emails.welcome', ["user"=>$user], function ($message) use($user) {
                $message->to($user->email, $user->name);
                $message->subject('Bienvenido a Ship2Go - Ship2Go');
                $message->from('no-reply@ship2go.com', 'Ship2Go');
            });
            
            return response()->json(['status' => 'success', 'api_key' => $apikey, 'user' => $user]);
        }

       
    }

    public function getUser(Request $request)
    {
        $user = Auth::user()->todo()->get();
        return response()->json(['status' => 'success', 'result' => $user]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'errors' => $validator->errors()], 422);
        }

      $user = User::where('email', $request->email)->first();

      if(is_null($user)){
        return response()->json(['status' => 'fail', 'message' => 'User not found'], 422);
      }else{
        $user->hash = $this->randomString(16);
        $user->save();


        $link = env("FRONT_END_URL").'/restore-password/'.$user->hash;

        Mail::send('emails.forgot', ["link"=>$link], function ($message) use($user) {
            $message->to($user->email, $user->name);
            $message->subject('Reestablecer Contraseña - Ship2Go');
            $message->from('no-reply@ship2go.com', 'Ship2Go');
        });

      }

      

    return response()->json(['status' => 'success', 'result' => $user]);
    }

    public function restorePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'errors' => $validator->errors()], 422);
        }

      $user = User::where('hash', $request->hash)->first();

      if(is_null($user)){
        return response()->json(['status' => 'fail', 'message' => 'User not found'], 422);
      }else{
        $apikey = base64_encode(str_random(40));
        $user->api_key = $apikey;
        $user->password = Hash::make($request->password);
        $user->hash = NULL;
        $user->save();

        return response()->json(['status' => 'success', 'api_key' => $apikey, 'user' => $user]);

      }

    }

    private function randomString($length = 16)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
