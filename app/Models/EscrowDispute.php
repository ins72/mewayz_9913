<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EscrowDispute extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'escrow_transaction_id',
        'initiated_by',
        'reason',
        'description',
        'evidence',
        'requested_resolution',
        'status',
        'mediator_id',
        'resolution',
        'resolution_notes',
        'resolved_at',
        'refund_amount',
        'refund_percentage',
    ];

    protected $casts = [
        'id' => 'string',
        'evidence' => 'array',
        'refund_amount' => 'decimal:2',
        'refund_percentage' => 'decimal:2',
        'resolved_at' => 'datetime',
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

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function mediator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mediator_id');
    }

    // Accessors
    public function getStatusBadgeAttribute(): array
    {
        $statusMap = [
            'open' => ['color' => 'warning', 'text' => 'Open'],
            'in_mediation' => ['color' => 'info', 'text' => 'In Mediation'],
            'resolved' => ['color' => 'success', 'text' => 'Resolved'],
            'escalated' => ['color' => 'error', 'text' => 'Escalated'],
        ];

        return $statusMap[$this->status] ?? ['color' => 'secondary', 'text' => ucfirst($this->status)];
    }

    public function getReasonTextAttribute(): string
    {
        $reasonMap = [
            'not_delivered' => 'Item Not Delivered',
            'not_as_described' => 'Item Not as Described',
            'damaged' => 'Item Damaged',
            'unauthorized_charges' => 'Unauthorized Charges',
            'other' => 'Other',
        ];

        return $reasonMap[$this->reason] ?? ucwords(str_replace('_', ' ', $this->reason));
    }

    public function getResolutionTextAttribute(): string
    {
        $resolutionMap = [
            'full_refund' => 'Full Refund',
            'partial_refund' => 'Partial Refund',
            'replacement' => 'Replacement',
            'completion' => 'Complete Service',
            'no_action' => 'No Action Required',
        ];

        return $resolutionMap[$this->requested_resolution] ?? ucwords(str_replace('_', ' ', $this->requested_resolution));
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByInitiator($query, $userId)
    {
        return $query->where('initiated_by', $userId);
    }

    // Business Logic Methods
    public function assignMediator(User $mediator): void
    {
        $this->update([
            'mediator_id' => $mediator->id,
            'status' => 'in_mediation',
        ]);
    }

    public function resolve(string $resolution, array $resolutionData = []): void
    {
        $this->update([
            'status' => 'resolved',
            'resolution' => $resolution,
            'resolution_notes' => $resolutionData['notes'] ?? null,
            'refund_amount' => $resolutionData['refund_amount'] ?? 0,
            'refund_percentage' => $resolutionData['refund_percentage'] ?? 0,
            'resolved_at' => now(),
        ]);
    }

    public function escalate(): void
    {
        $this->update(['status' => 'escalated']);
    }

    public function canBeResolved(): bool
    {
        return in_array($this->status, ['open', 'in_mediation']);
    }

    public function canBeEscalated(): bool
    {
        return $this->status === 'in_mediation';
    }
}