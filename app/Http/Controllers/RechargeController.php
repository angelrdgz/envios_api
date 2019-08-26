<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Package;
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
        return response()->json($request->all());

        MercadoPago\SDK::setAccessToken(env("MP_TOKEN_SANDBOX"));
        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = 140;
        $payment->token = "3749ea44f4497c0343eaa7845c3476c9";
        $payment->description = "Enormous Silk Pants";
        $payment->installments = 1;
        $payment->payment_method_id = "visa";
        $payment->payer = array(
        "email" => "idell@yahoo.com"
        );
        // Save and posting the payment
        $payment->save();
        //...
        // Print the payment status
        echo $payment->status;
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
