<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

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
        'email_verified_at',
        'type_id',
        'hash',
        'remember_token',
        'created_at',
        'updated_at',
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
