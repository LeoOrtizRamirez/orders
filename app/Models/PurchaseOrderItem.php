<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'received_quantity',
        'checked',
    ];

    protected $casts = [
        'quantity' => 'float',
        'received_quantity' => 'float',
        'checked' => 'boolean',
    ];

    protected $hidden = [
        'unit_price',
        'total_price',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function itemNotes(): MorphMany
    {
        return $this->morphMany(UserNote::class, 'notable');
    }

    public function calculateTotal(): void
    {
        // This logic is no longer needed
    }

    public function getPendingQuantity(): float
    {
        return $this->quantity - $this->received_quantity;
    }

    public function isFullyReceived(): bool
    {
        return $this->received_quantity >= $this->quantity;
    }
}