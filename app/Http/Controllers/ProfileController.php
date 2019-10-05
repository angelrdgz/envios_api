<?php

namespace App\Http\Controllers;
use Hash;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function update(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
            ],
            [
                'email.required' => 'Su nombre es requerido',
            ]
        );

        if ($validator->fails()) {
            if($request->input('password') != ''){
                if($request->input('password') != $request->input('password_confirmation')){
                    $validator->getMessageBag()->add('password', 'Las contraseñas no coindiden'); 
                }
            }
            return response()->json(['status' => 'fail', 'errors' => $validator->errors()], 422);
        }else{
            if($request->input('password') != ''){
                if($request->input('password') != $request->input('password_confirmation')){
                    $validator->getMessageBag()->add('password', 'Las contraseñas no coindiden'); 
                    return response()->json(['status' => 'fail', 'errors' => $validator->errors()], 422);
                }
            } 
        }

        $user = User::find($request->user()->id);
        $user->name = $request->input('name');

        if($request->input('password') != ''){
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        $user->company;

        return response()->json(['status'=>'success','data'=>$user]);

        
    }
}
