<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;


class Order extends Model
{
   protected $fillable = [
    'user_id',
    'order_number',
    'total_amount',
    'payment_method',
    'payment_status',
    'order_status',
    'customer_name',
    'customer_phone',
    'shipping_address',
];
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
}
