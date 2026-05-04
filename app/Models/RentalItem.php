<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RentalItem;
use App\Models\User;

class RentalItem extends Model
{
    protected $fillable = [
        'rental_id',
        'product_id',
        'quantity',
        'price_per_unit',
        'unit',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(RentalItem::class);
    }
}