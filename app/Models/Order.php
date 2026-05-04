<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

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
        'snap_token',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: Discount Price (Total dengan harga diskon)
     * Menghitung total pembayaran menggunakan harga diskon untuk semua item
     */
    public function getDiscountPriceAttribute(): float
    {
        $itemsTotal = $this->items->sum(function ($item) {
            return $item->subtotal;
        });

        return $itemsTotal + $this->shipping_cost;
    }
}