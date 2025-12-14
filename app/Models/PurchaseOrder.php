<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'supplier_id',
        'order_date',
        'expected_delivery_date',
        'delivery_date',
        'status',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at',
        'parent_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'delivery_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected $hidden = [];

    protected $appends = [
        'can_be_edited', 
        'can_be_approved', 
        'can_be_received',
        'can_be_rejected',
        'can_be_marked_ordered',
        'can_be_deleted',
        'can_be_cancelled',
        'can_be_reopened',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(UserNote::class, 'notable');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'parent_id');
    }

    public function subOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'parent_id');
    }

    public function getCanBeEditedAttribute(): bool
    {
        return true;
    }

    public function getCanBeApprovedAttribute(): bool
    {
        return true;
    }

    public function getCanBeRejectedAttribute(): bool
    {
        return true;
    }

    public function getCanBeReceivedAttribute(): bool
    {
        return true;
    }

    public function getCanBeMarkedOrderedAttribute(): bool
    {
        return true;
    }

    public function getCanBeDeletedAttribute(): bool
    {
        return true;
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return true;
    }

    public function getCanBeReopenedAttribute(): bool
    {
        return true;
    }

    public static function getStatuses(): array
    {
        return [
            'nuevo pedido' => 'Nuevo Pedido',
            'disponibilidad' => 'Disponibilidad',
            'preparar pedido' => 'Preparar Pedido',
            'en preparación' => 'En Preparación',
            'facturación' => 'Facturación',
            'en despacho' => 'En Despacho',
            'en ruta' => 'En Ruta',
            'entregado' => 'Entregado',
        ];
    }
}