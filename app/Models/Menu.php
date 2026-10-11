<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'url',
        'icon',
        'order',
        'is_active',
        'is_maintenance',
        'role',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_maintenance' => 'boolean',
        'order' => 'integer',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->where('is_active', true)->orderBy('order');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id')->where('is_active', true)->orderBy('order');
    }
}
