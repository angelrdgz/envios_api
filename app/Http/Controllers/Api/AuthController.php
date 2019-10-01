<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Company;
use App\Http\Controllers\FacturaController;
use App\UserInformation;
use Hash;
use Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login (Request $request) {

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required',
                'password' => 'required'
            ],
            [
                'email.required' => 'Su email es requerido',
                'password.required' => 'Su contraseña es requerida',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
    
        if ($user) {
    
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                return response(['status' => 'success', 'api_key' => $token, 'user' => $user], 200);
            } else {
                $response = "Password missmatch";
                return response(['status' => 'fail', 'errors' => ["credentials" => "Email o contraseña incorrectos"]], 422);
            }
    
        } else {
            return response(['status' => 'fail', 'errors' => ["credentials" => "Email o contraseña incorrectos"]], 422);
        }
    
    }

    public function register (Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',            
            'business' => 'required',
            'phone' => 'required',
        ]);
    
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->type_id = 2;
        $user->business = $request->input('business');
        $user->phone = $request->input('phone');
        $user->hash = $this->randomString(16);
        //$user->mp_token = RechargeController::createCustomer($request->input('email'));
        $user->save();

        if($request->input('business') == 1){
          $company = new Company();
          $company->name = $request->input('company');
          $company->owner_id = $user->id;
          $company->balance = 0;
          $company->save();
        }

        $link = env("FRONT_END_URL").'/active-account/'.$user->hash;
        Mail::send('emails.active', ["user"=>$user,"link"=>$link], function ($message) use($user) {
            $message->to($user->email, $user->name);
            $message->subject('Activa tu cuenta - Ship2Go');
            $message->from('no-reply@ship2go.com', 'Ship2Go');
        });
        return response(['status' => 'success', 'result' => $user]);
    
    }

    public function activeAccount($hash)
    {
        $user = User::where('hash', $hash)->first();
        if(is_null($user)){            
            return response(['status' => 'fail', 'errors' => ["error"=>"Not found"]], 401);
        }else{
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->hash = NULL;
            $user->save();

            $token = $user->createToken('Laravel Password Grant Client')->accessToken;

            Mail::send('emails.welcome', ["user"=>$user], function ($message) use($user) {
                $message->to($user->email, $user->name);
                $message->subject('Bienvenido a Ship2Go - Ship2Go');
                $message->from('no-reply@ship2go.com', 'Ship2Go');
            });
            
            return response(['status' => 'success', 'api_key' => $token, 'user' => $user], 200);
        }
       
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['status' => 'fail', 'errors' => $validator->errors()], 422);
        }

      $user = User::where('email', $request->email)->first();

      if(is_null($user)){
        return response(['status' => 'fail', 'message' => 'User not found'], 422);
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

      

    return response(['status' => 'success', 'result' => $user]);
    }

    public function restorePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['status' => 'fail', 'errors' => $validator->errors()], 422);
        }

      $user = User::where('hash', $request->hash)->first();

      if(is_null($user)){
        return response(['status' => 'fail', 'message' => 'User not found'], 422);
      }else{
        $user->password = Hash::make($request->password);
        $user->hash = NULL;
        $user->save();

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

        return response(['status' => 'success', 'api_key' => $token, 'user' => $user]);

      }

    }

    public function logout (Request $request) {

        $token = $request->user()->token();
        $token->revoke();
    
        $response = 'You have been succesfully logged out!';
        return response($response, 200);
    
    }

    public function getBusinessInfo(Request $request)
    {
        $info = UserInformation::where('user_id', $request->user()->id)->first();
        return response()->json(['status'=>'success','data'=>$info]);
    }

    public function businessInfo(Request $request){
        $info = UserInformation::where('user_id', $request->user()->id)->first();
        $factura = new FacturaController();
        if($info){
            
        }else{
            $client = $factura->newCliente($request->all());
        }
        return response()->json($client);
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
