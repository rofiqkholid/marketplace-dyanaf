<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $table = 'payment_transactions';

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'item_type',
        'product_id',
        'brand_id',
        'item_name',
        'package_name',
        'amount',
        'payment_method',
        'bank',
        'va_number',
        'qris_url',
        'midtrans_transaction_id',
        'status',
        'notes',
        'order_id',
        'paid_at',
        'points_awarded',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'points_awarded' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
