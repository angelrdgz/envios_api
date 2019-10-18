<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{ 

    public function payment()
    {
        return $this->belongsTo('App\Payment', 'payment_id','id');
    }
}