<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'item_type',
        'product_id',
        'brand_id',
        'item_name',
        'package_name',
        'price',
        'quantity',
        'total_amount',
        'status',
        'notes',
        'customer_email',
        'snap_token',
        'midtrans_transaction_id',
        'payment_type',
        'bank',
        'va_number',
        'qris_url',
        'payment_status',
        'paid_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function assets()
    {
        return $this->hasMany(OrderAsset::class);
    }

    /**
     * Generate format nomor order unik:
     * Contoh: SEN100226-1-P29 (Hari 3 huruf Indonesia, tanggal bulan tahun 2 digit, ID item, Tipe [P/B], angka acak)
     */
    public static function generateOrderNumber(string $itemType, int $itemId): string
    {
        $dayMap = [
            'Sunday' => 'MIN',
            'Monday' => 'SEN',
            'Tuesday' => 'SEL',
            'Wednesday' => 'RAB',
            'Thursday' => 'KAM',
            'Friday' => 'JUM',
            'Saturday' => 'SAB',
        ];

        $dayName = date('l');
        $dayCode = $dayMap[$dayName] ?? strtoupper(substr($dayName, 0, 3));
        $dateStr = date('dmy'); // 121026 (12 Okt 26)
        $typeCode = $itemType === 'brand' ? 'B' : 'P';
        
        do {
            $randomDigits = rand(10, 99);
            $orderNumber = "{$dayCode}{$dateStr}-{$itemId}-{$typeCode}{$randomDigits}";
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
