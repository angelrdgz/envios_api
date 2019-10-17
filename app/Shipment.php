<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    protected $casts = [
        'picked' => 'boolean',
    ];

    use SoftDeletes;  
    
    public function origen()
    {
        return $this->hasOne('App\Location', 'id', 'origin_id');
    }

    public function destination()
    {
        return $this->hasOne('App\Location', 'id', 'destination_id');
    }
}