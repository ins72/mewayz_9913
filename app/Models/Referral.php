<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Referral extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'referrer_id',
        'referee_id',
        'workspace_id',
        'referee_email',
        'referral_code',
        'status',
        'qualifying_action',
        'action_value',
        'reward_amount',
        'custom_message',
        'invitation_sent_at',
        'signed_up_at',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'id' => 'string',
        'action_value' => 'decimal:2',
        'reward_amount' => 'decimal:2',
        'invitation_sent_at' => 'datetime',
        'signed_up_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
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
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(ReferralReward::class);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'sent' => 'Invitation Sent',
            'pending' => 'Signed Up',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getQualifyingActionLabelAttribute(): string
    {
        return match($this->qualifying_action) {
            'subscription' => 'Subscription Purchase',
            'purchase' => 'Product Purchase',
            'workspace_creation' => 'Workspace Creation',
            default => 'Unknown Action'
        };
    }

    public function getDaysToCompleteAttribute(): ?int
    {
        if (!$this->signed_up_at || !$this->completed_at) {
            return null;
        }

        return $this->signed_up_at->diffInDays($this->completed_at);
    }

    public function getConversionTimeAttribute(): ?string
    {
        if (!$this->invitation_sent_at || !$this->completed_at) {
            return null;
        }

        return $this->invitation_sent_at->diffForHumans($this->completed_at);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByReferrer($query, $referrerId)
    {
        return $query->where('referrer_id', $referrerId);
    }

    public function scopeByWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeRecentlyCompleted($query, $days = 30)
    {
        return $query->where('status', 'completed')
            ->where('completed_at', '>=', now()->subDays($days));
    }

    // Business Logic
    public function markAsCompleted(string $qualifyingAction, float $actionValue = 0, float $rewardAmount = 0): void
    {
        $this->update([
            'status' => 'completed',
            'qualifying_action' => $qualifyingAction,
            'action_value' => $actionValue,
            'reward_amount' => $rewardAmount,
            'completed_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function hasRefereeSignedUp(): bool
    {
        return !empty($this->referee_id) && !empty($this->signed_up_at);
    }

    public function calculateCommission(float $rate = 0.15): float
    {
        return $this->action_value * $rate;
    }

    public function getTimeToConvert(): ?int
    {
        if (!$this->invitation_sent_at || !$this->signed_up_at) {
            return null;
        }

        return $this->invitation_sent_at->diffInHours($this->signed_up_at);
    }
}