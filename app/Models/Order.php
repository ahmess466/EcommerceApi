<?php

namespace App\Models;

use App\Http\Controllers\OrderController;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable =[
        'user_id',
        'location_id',
        'total_price',
        'status',
        'date_of_delivery'
    ];
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function location(){
        return $this->belongsTo(Location::class,'location_id');
    }
    public function items(){
        return $this->hasMany(OrderItem::class,'order_id');
    }
}
