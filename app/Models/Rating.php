<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'star',
        'message',
    ];
    public function rating()
{
    return $this->hasOne(Rating::class);
}
}