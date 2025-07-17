<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EscrowMilestone extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'escrow_transaction_id',
        'title',
        'description',
        'amount',
        'order',
        'status',
        'delivered_at',
        'accepted_at',
        'delivery_notes',
        'delivery_proof',
    ];

    protected $casts = [
        'id' => 'string',
        'amount' => 'decimal:2',
        'delivery_proof' => 'array',
        'delivered_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function escrowTransaction(): BelongsTo
    {
        return $this->belongsTo(EscrowTransaction::class);
    }

    // Accessors
    public function getStatusBadgeAttribute(): array
    {
        $statusMap = [
            'pending' => ['color' => 'warning', 'text' => 'Pending'],
            'delivered' => ['color' => 'info', 'text' => 'Delivered'],
            'accepted' => ['color' => 'success', 'text' => 'Accepted'],
            'disputed' => ['color' => 'error', 'text' => 'Disputed'],
        ];

        return $statusMap[$this->status] ?? ['color' => 'secondary', 'text' => ucfirst($this->status)];
    }

    public function getProgressPercentageAttribute(): float
    {
        switch ($this->status) {
            case 'pending':
                return 0;
            case 'delivered':
                return 75;
            case 'accepted':
                return 100;
            default:
                return 0;
        }
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Business Logic Methods
    public function markAsDelivered(array $deliveryData = []): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'delivery_notes' => $deliveryData['notes'] ?? null,
            'delivery_proof' => $deliveryData['proof'] ?? [],
        ]);
    }

    public function markAsAccepted(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function canBeDelivered(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeAccepted(): bool
    {
        return $this->status === 'delivered';
    }
}