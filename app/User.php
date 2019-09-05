<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'api_key'
    ];

    public function shipments()
    {
        return $this->hasMany('App\Shipment', 'user_id');
    }

    public function packages()
    {
        return $this->hasMany('App\Package', 'user_id');
    }

    public function payments()
    {
        return $this->hasMany('App\Payment', 'user_id');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice', 'user_id');
    }

    public function origenes()
    {
        return $this->hasMany('App\Location', 'user_id')->where('type_id', 1);
    }

    public function destinations()
    {
        return $this->hasMany('App\Location', 'user_id')->where('type_id', 2);
    }
}
