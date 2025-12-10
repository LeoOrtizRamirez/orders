<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'stock',
        'min_stock',
        'reorder_point',
        'unit',
        'category',
        'brand',
        'supplier',
        'specifications',
        'is_active',
        'order',
    ];

    protected $casts = [
        'stock' => 'integer',
        'min_stock' => 'integer',
        'reorder_point' => 'integer',
        'specifications' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected $hidden = [
        'price',
        'cost',
    ];

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock', '<=', 'reorder_point');
    }

    public function updateStock(int $quantity): bool
    {
        $this->stock += $quantity;
        return $this->save();
    }
}