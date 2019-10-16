<?php

namespace App\Http\Controllers;

use App\Company;
use App\User;
use Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');        
    }

    public function index(Request $request)
    {
       $companies = Company::with('user')->get();
       return response()->json(['status'=>'success','data'=>$companies]);
    }

    public function show(Request $request, $id)
    {
        $company = Company::find($id);
        $company->user;

        return response()->json(['status'=>'success','data'=>$company]);
    }

    public function update(Request $request, $id){
        $company = Company::find($id);
        $company->name = $request->input('company')['name'];
        $company->address = $request->input('company')['address'];
        $company->save();

        $user = User::where('id', $company->owner_id)->first();
        $user->name = $request->input('user')['name'];
        $user->email = $request->input('user')['email'];
        $user->phone = $request->input('user')['phone'];

        if($request->input('user')['password'] != ''){
            $user->password = Hash::make($request->input('user')['password']);
        }

        $user->save();

        return response()->json(['status'=>'success','data'=>$company]);
        
    }

    public function active(Request $request, $id)
    {
        $company = Company::find($id);
        $company->status = 1;
        $company->save();
        $company->user;

        return response()->json(['status'=>'success','data'=>$company]);
    }
    public function unactive(Request $request, $id)
    {
        $company = Company::find($id);
        $company->status = 0;
        $company->save();
        $company->user;

        return response()->json(['status'=>'success','data'=>$company]);
    }
}
