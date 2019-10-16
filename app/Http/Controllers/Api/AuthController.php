<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Configuration;
use App\Company;
use App\Http\Controllers\FacturaController;
use App\UserInformation;
use Hash;
use Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login (Request $request)
    {

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
                if(is_null($user->email_verified_at)){
                    return response(['status' => 'fail', 'data' => [], 'message'=>'Su cuenta no ha sido verificada.'], 400);
                }
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $user->company;

                if(!is_null($user->company)){
                    if($user->company->status == 0){
                        return response(['status' => 'fail', 'errors' => ["credentials" => "Su compañia ha sido desactivada"]], 401);
                    }
                }
                return response(['status' => 'success', 'api_key' => $token, 'user' => $user], 200);
            } else {
                $response = "Password missmatch";
                return response(['status' => 'fail', 'errors' => ["credentials" => "Email o contraseña incorrectos"]], 401);
            }
    
        } else {
            return response(['status' => 'fail', 'errors' => ["credentials" => "Email o contraseña incorrectos"]], 401);
        }
    
    }

    public function register (Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',            
            'business' => 'required',
            'phone' => 'required',
        ],
        [
            'name.required' => 'El nombre es requerido',
            'email.required' => 'El email es requerido',
            'email.unique' => 'El email ya esta registrado',
            'password.required' => 'La contraseña es requerida',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'phone.required' => 'El teléfono es requerido',
        ]);
    
        if ($validator->fails())
        {
            if($request->input('business') == 1){
                if($request->input('company') == ''){
                    $validator->getMessageBag()->add('company', 'El campo compañia es requerido'); 
                }
    
                if($request->input('shipments') == ''){
                    $validator->getMessageBag()->add('shipments', 'Seleccione el número de envíos por mes'); 
                }
            }

            return response(['errors'=>$validator->errors()], 422);
        }

        if($request->input('business') == 1){
            if($request->input('company') == ''){
                $validator->getMessageBag()->add('company', 'El campo compañia es requerido'); 
            }

            if($request->input('shipments') == ''){
                $validator->getMessageBag()->add('shipments', 'Seleccione el número de envíos por mes'); 
            }
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

        $configuration = new Configuration();
        $configuration->user_id = $user->id;
        $configuration->save();

        $info = new UserInformation();
        $info->user_id = $user->id;
        $info->save();

        if($request->input('business') == 1){
          $company = new Company();
          $company->name = $request->input('company');
          $company->owner_id = $user->id;
          $company->balance = 0;
          $company->shipments = $request->input('shipments');
          $company->save();
        }

        $link = env("FRONT_END_URL").'/active-account/'.$user->hash;
        try {
            Mail::send('emails.active', ["user"=>$user,"link"=>$link], function ($message) use($user) {
                $message->to($user->email, $user->name);
                $message->subject('Activa tu cuenta - Ship2Go');
                $message->from('no-reply@ship2go.com', 'Ship2Go');
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
        
        return response(['status' => 'success', 'result' => $user]);
    
    }

    public function resendEmail(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        if(!$user){
            return response(['status' => 'success', 'data' => [], 'message'=>'Este email no esta registrado'],400);
        }
        $link = env("FRONT_END_URL").'/active-account/'.$user->hash;
        Mail::send('emails.active', ["user"=>$user,"link"=>$link], function ($message) use($user) {
            $message->to($user->email, $user->name);
            $message->subject('Activa tu cuenta - Ship2Go');
            $message->from('no-reply@ship2go.com', 'Ship2Go');
        });
        return response(['status' => 'success', 'result' => []],200);

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

            $user->company;

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

    public function businessInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'lastname' => 'required',
            'phone' => 'required',
            'email' => 'required|string|email|max:255',
            'business_name' => 'required',
            'rfc' => 'required',
            'address' => 'required',
            'num_ext' => 'required',
            'zip_code' => 'required',
            'neight' => 'required',
            'state' => 'required',
            'city' => 'required'
        ],
        [
            'name.required' => 'El nombre es requerido',
            'lastname.required' => 'El apellido es requerido',
            'email.required' => 'El email es requerido',
            'email.email' => 'Debe ingresar un email valido',
            'business_name' => 'La razón social es requerida',
            'rfc.required' => 'El RFC es requerido',
            'address.required' => 'La dirección es requerida',
            'num_ext.required' => 'El número externo es requerido',
            'zip_code.required' => 'El código postal es requerido',
            'neight.required' => 'La colonia es requerida',
            'state.required' => 'El estado es requerido',
            'city.required' => 'La ciudad es requerida',           
            'phone.required' => 'El teléfono es requerido',
        ]);
    
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()], 422);
        }

        $info = UserInformation::where('user_id', $request->user()->id)->first();
        
        if(!$info){
            $factura = new FacturaController();
            $client = $factura->newCliente($request->all());
            User::where('id', $request->user()->id)->update(['uid_factura'=>$client['Data']['UID']]);
        }

        $info->name = $request->input('name');
        $info->lastname = $request->input('lastname');
        $info->email = $request->input('email');
        $info->phone = $request->input('phone');
        $info->business_name = $request->input('business_name');
        $info->rfc = $request->input('rfc');
        $info->address = $request->input('address');
        $info->num_ext = $request->input('num_ext');
        $info->zip_code = $request->input('zip_code');
        $info->neight = $request->input('neight');
        $info->state = $request->input('state');
        $info->city = $request->input('city');
        $info->delegation = $request->input('delegation');
        $info->save();
        return response()->json($info);
    }

    public function contact(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required|min:10|max:12',
                'comments' => 'required',
            ],
            [
                'email.required' => 'El nombre es requerido',
                'email.required' => 'El email es requerido',
                'email.email' => 'Debe ingresar un email valido',
                'phone.required' => 'El teléfono es requerido',
                'phone.min' => 'Debe de ingresar un teléfono valido',
                'comments.required' => 'Los comentarios son requeridos'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'errors' => $validator->errors()], 422);
        }

        Mail::send('emails.contact', ["name"=>$request->name,"email"=>$request->email,"phone"=>$request->phone,"comments"=>$request->comments], function ($message) {
            $message->to('contacto@ship2go.com.mx');
            $message->subject('Contacto - Ship2Go');
            $message->from('no-reply@ship2go.com', 'Ship2Go');
        });
        return response(['status' => 'success', 'result' => []],200);

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
