<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'category',
        'logo',
        'description',
        'packages',
        'stock',
        'restock_date',
        'is_active',
    ];

    protected $casts = [
        'packages' => 'array',
        'restock_date' => 'date',
        'is_active' => 'boolean',
    ];
}
