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

    public function isReadOnly(): bool
    {
        $normalizedStatus = mb_strtolower($this->status, 'UTF-8');
        if ($normalizedStatus === 'facturacion') $normalizedStatus = 'facturación';

        $readOnlyStatuses = [
            'en despacho',
            'en ruta',
            'entregado'
        ];

        return in_array($normalizedStatus, $readOnlyStatuses);
    }

    public function getCanBeEditedAttribute(): bool
    {
        return !$this->isReadOnly() || (auth()->check() && auth()->user()->can('override_order_restrictions'));
    }

    public function getCanBeApprovedAttribute(): bool
    {
        return $this->status === 'pending'; // Ejemplo, ajustar según flujo real si es necesario
    }

    public function getCanBeRejectedAttribute(): bool
    {
         return $this->status === 'pending';
    }

    public function getCanBeReceivedAttribute(): bool
    {
         return $this->status === 'ordered';
    }

    public function getCanBeMarkedOrderedAttribute(): bool
    {
         return $this->status === 'approved';
    }

    public function getCanBeDeletedAttribute(): bool
    {
        return !$this->isReadOnly() || (auth()->check() && auth()->user()->can('override_order_restrictions'));
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return !$this->isReadOnly() || (auth()->check() && auth()->user()->can('override_order_restrictions'));
    }

    public function getCanBeReopenedAttribute(): bool
    {
        return $this->status === 'cancelled' || $this->status === 'rejected';
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