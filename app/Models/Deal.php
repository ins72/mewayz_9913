<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Deal extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'workspace_id',
        'contact_id',
        'title',
        'description',
        'value',
        'currency',
        'stage',
        'probability',
        'expected_close_date',
        'actual_close_date',
        'close_reason',
        'products',
        'assigned_to',
        'custom_fields',
    ];

    protected $casts = [
        'id' => 'string',
        'value' => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'products' => 'array',
        'custom_fields' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });

        static::updating(function ($model) {
            // Auto-set close date when deal is won/lost
            if (in_array($model->stage, ['closed_won', 'closed_lost']) && !$model->actual_close_date) {
                $model->actual_close_date = now();
            }
        });
    }

    // Relationships
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    // Accessors
    public function getFormattedValueAttribute(): string
    {
        return number_format($this->value, 2) . ' ' . $this->currency;
    }

    public function getWeightedValueAttribute(): float
    {
        return $this->value * ($this->probability / 100);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->expected_close_date && 
               $this->expected_close_date < now() && 
               !in_array($this->stage, ['closed_won', 'closed_lost']);
    }

    public function getDaysToCloseAttribute(): ?int
    {
        if (!$this->expected_close_date || in_array($this->stage, ['closed_won', 'closed_lost'])) {
            return null;
        }
        
        return now()->diffInDays($this->expected_close_date, false);
    }

    // Scopes
    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('stage', ['closed_won', 'closed_lost']);
    }

    public function scopeWon($query)
    {
        return $query->where('stage', 'closed_won');
    }

    public function scopeLost($query)
    {
        return $query->where('stage', 'closed_lost');
    }

    public function scopeOverdue($query)
    {
        return $query->where('expected_close_date', '<', now())
                    ->whereNotIn('stage', ['closed_won', 'closed_lost']);
    }

    // Business Logic Methods
    public function moveToStage(string $stage, string $reason = null): bool
    {
        $validStages = ['lead', 'qualified', 'proposal', 'negotiation', 'closed_won', 'closed_lost'];
        
        if (!in_array($stage, $validStages)) {
            return false;
        }

        $this->update([
            'stage' => $stage,
            'close_reason' => $reason,
        ]);

        // Update probability based on stage
        $this->updateProbabilityByStage();

        return true;
    }

    public function updateProbabilityByStage(): void
    {
        $stageProbabilities = [
            'lead' => 10,
            'qualified' => 25,
            'proposal' => 50,
            'negotiation' => 75,
            'closed_won' => 100,
            'closed_lost' => 0,
        ];

        $this->update(['probability' => $stageProbabilities[$this->stage] ?? $this->probability]);
    }

    public function calculateExpectedRevenue(): float
    {
        return $this->value * ($this->probability / 100);
    }
}