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
