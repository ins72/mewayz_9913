<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EscrowTransaction extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'item_type',
        'item_title',
        'item_description',
        'total_amount',
        'currency',
        'escrow_fee',
        'escrow_fee_percentage',
        'status',
        'payment_method',
        'payment_id',
        'inspection_period_hours',
        'funded_at',
        'delivered_at',
        'completed_at',
        'inspection_deadline',
        'delivery_notes',
        'delivery_proof',
        'terms_conditions',
        'insurance_required',
        'insurance_amount',
        'expires_at',
    ];

    protected $casts = [
        'id' => 'string',
        'total_amount' => 'decimal:2',
        'escrow_fee' => 'decimal:2',
        'escrow_fee_percentage' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'insurance_required' => 'boolean',
        'delivery_proof' => 'array',
        'funded_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'inspection_deadline' => 'datetime',
        'expires_at' => 'datetime',
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
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(EscrowMilestone::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(EscrowDispute::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EscrowDocument::class);
    }

    // Accessors
    public function getTotalWithFeeAttribute(): float
    {
        return $this->total_amount + $this->escrow_fee;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getInspectionTimeLeftAttribute(): ?string
    {
        if (!$this->inspection_deadline) {
            return null;
        }

        $diff = now()->diffInHours($this->inspection_deadline, false);
        
        if ($diff < 0) {
            return 'Expired';
        }
        
        if ($diff < 24) {
            return $diff . ' hours';
        }
        
        return ceil($diff / 24) . ' days';
    }

    public function getStatusBadgeAttribute(): array
    {
        $statusMap = [
            'pending_funding' => ['color' => 'warning', 'text' => 'Pending Funding'],
            'funded' => ['color' => 'info', 'text' => 'Funded'],
            'delivered' => ['color' => 'primary', 'text' => 'Delivered'],
            'completed' => ['color' => 'success', 'text' => 'Completed'],
            'disputed' => ['color' => 'error', 'text' => 'Disputed'],
            'cancelled' => ['color' => 'secondary', 'text' => 'Cancelled'],
            'expired' => ['color' => 'secondary', 'text' => 'Expired'],
        ];

        return $statusMap[$this->status] ?? ['color' => 'secondary', 'text' => ucfirst($this->status)];
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        });
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending_funding', 'funded', 'delivered']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDisputed($query)
    {
        return $query->where('status', 'disputed');
    }

    // Business Logic Methods
    public function canBeFunded(): bool
    {
        return $this->status === 'pending_funding' && !$this->is_expired;
    }

    public function canBeDelivered(): bool
    {
        return $this->status === 'funded';
    }

    public function canBeAccepted(): bool
    {
        return $this->status === 'delivered' && 
               (!$this->inspection_deadline || $this->inspection_deadline->isFuture());
    }

    public function canBeDisputed(): bool
    {
        return in_array($this->status, ['funded', 'delivered']) && 
               !$this->disputes()->where('status', '!=', 'resolved')->exists();
    }

    public function autoComplete(): void
    {
        if ($this->status === 'delivered' && 
            $this->inspection_deadline && 
            $this->inspection_deadline->isPast()) {
            
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    public function calculateRefund(float $percentage = 100): float
    {
        return ($this->total_amount * $percentage) / 100;
    }
}