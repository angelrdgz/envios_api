<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{  
    public function user()
    {
        return $this->belongsTo('App\User', 'owner_id','id');
    }
}