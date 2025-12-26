<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'shipping_name',
        'shipping_address',
        'shipping_phone',
        'total_amount',
        'shipping_cost',
    ];

    // relasi ke order items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
