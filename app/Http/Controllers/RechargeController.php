<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;
use App\User;
use App\Company;
use Auth;
use MercadoPago;

class RechargeController extends Controller
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

    public function index(Request $request){
        $recharges = Auth::user()->payments;
        return response()->json(["data"=>$recharges]);
    }

    public static function createCustomer($email)
    {
        MercadoPago\SDK::setAccessToken(env("MP_TOKEN_SANDBOX"));
        $customer = new MercadoPago\Customer();
        $customer->email = $email;
        $customer->save();
        return $customer->id;
    }

    public function makePayment(Request $request)
    {
        MercadoPago\SDK::setAccessToken(env("MP_TOKEN_SANDBOX"));
        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = 140;
        $payment->token = $request->token;
        $payment->description = "Deposito a Ship2go";
        $payment->installments = 1;
        $payment->payment_method_id = $request->paymentMethodId;
        $payment->payer = array(
        "email" =>'angel@x.com'
        );
        // Save and posting the payment
        $payment->save();
        if($payment->status == 'approved'){

            $pay = new Payment();
            $pay->mp_id = $payment->id;
            $pay->user_id = Auth::user()->id;
            $pay->payment_method = $payment->payment_method_id;
            $pay->status = $payment->status;
            $pay->total = $request->total;
            $pay->card = substr($request->cardNumber, 11, 4);
            $pay->save();

            if(!is_null(Auth::user()->company_id)){
                $company = Company::find(Auth::user()->company->id);
                $company->balance = Auth::user()->company->balance + $request->total;
                $company->save();
            }else{
                $user = User::find(Auth::user()->id);
                $user->balance = Auth::user()->balance + $request->total;
                $user->save();
            }

        }
        return response()->json(['status'=>'success', 'message'=>'Payment created successfully'], 200);
    }

    private function getCustomer($token)
    {

    }

    private function getCustomerCards($token)
    {

    }

    private function generateToken($length = 0)
    {
        
    }
}
