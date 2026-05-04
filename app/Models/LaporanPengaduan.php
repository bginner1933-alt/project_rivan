<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPengaduan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'order_id',
        'category',
        'message',
        'attachment_path',
    ];
}