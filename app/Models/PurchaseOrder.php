<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'notes',
        'rejection_reason',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'delivery_date' => 'date',
        'approved_at' => 'datetime',
        'notes' => 'array',
    ];

    protected $hidden = [
        'subtotal',
        'tax',
        'shipping',
        'total',
    ];

    /**
     * Los accessors que deben ser incluidos en el JSON
     */
    protected $appends = [
        'can_be_edited', 
        'can_be_approved', 
        'can_be_received',
        'can_be_rejected',
        'can_be_marked_ordered',
        'can_be_deleted',
        'can_be_cancelled',
        'can_be_reopened',
        'total' // Add total to appends
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeOrdered($query)
    {
        return $query->where('status', 'ordered');
    }

    /**
     * Accessor para determinar si la orden puede ser editada
     */
    public function getCanBeEditedAttribute(): bool
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    /**
     * Accessor para determinar si la orden puede ser aprobada
     */
    public function getCanBeApprovedAttribute(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Accessor para determinar si la orden puede ser rechazada
     */
    public function getCanBeRejectedAttribute(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Accessor para determinar si la orden puede ser recibida
     */
    public function getCanBeReceivedAttribute(): bool
    {
        return in_array($this->status, ['ordered', 'approved']);
    }

    /**
     * Accessor para determinar si la orden puede ser marcada como ordenada
     */
    public function getCanBeMarkedOrderedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Accessor para determinar si la orden puede ser eliminada
     */
    public function getCanBeDeletedAttribute(): bool
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    /**
     * Accessor para determinar si la orden puede ser cancelada
     */
    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->status, ['draft', 'pending', 'approved']);
    }

    /**
     * Accessor para determinar si la orden puede ser reabierta
     */
    public function getCanBeReopenedAttribute(): bool
    {
        return in_array($this->status, ['rejected', 'cancelled']);
    }

    /**
     * Aprobar la orden de compra
     */
    public function approve(int $approvedBy): bool
    {
        if (!$this->can_be_approved) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);

        return true;
    }

    /**
     * Rechazar la orden de compra
     */
    public function reject(string $reason, int $rejectedBy): bool
    {
        if (!$this->can_be_rejected) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => $rejectedBy,
            'approved_at' => now(),
        ]);

        return true;
    }

    /**
     * Marcar como ordenada (enviada al proveedor)
     */
    public function markAsOrdered(): bool
    {
        if (!$this->can_be_marked_ordered) {
            return false;
        }

        $this->update([
            'status' => 'ordered',
        ]);

        return true;
    }

    /**
     * Recibir la orden (completar recepción)
     */
    public function receive(): bool
    {
        if (!$this->can_be_received) {
            return false;
        }

        $this->update([
            'status' => 'received',
            'delivery_date' => now(),
        ]);

        return true;
    }

    /**
     * Cancelar la orden
     */
    public function cancel(string $reason = null): bool
    {
        if (!$this->can_be_cancelled) {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Reabrir una orden rechazada o cancelada
     */
    public function reopen(): bool
    {
        if (!$this->can_be_reopened) {
            return false;
        }

        $this->update([
            'status' => 'pending',
            'rejection_reason' => null,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return true;
    }

    /**
     * Enviar borrador a aprobación
     */
    public function submit(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $this->update([
            'status' => 'pending',
        ]);

        return true;
    }

    public function calculateTotals(): void
    {
        // This logic is no longer needed
    }

    /**
     * Método helper para obtener todos los estados válidos
     */
    public static function getStatuses(): array
    {
        return [
            'draft' => 'Borrador',
            'pending' => 'Pendiente',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'ordered' => 'Ordenada',
            'received' => 'Recibida',
            'cancelled' => 'Cancelada',
        ];
    }
}