<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAsset extends Model
{
    protected $fillable = [
        'order_id',
        'title',
        'file_path',
        'file_type',
        'file_size',
        'drive_link',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
