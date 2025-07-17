<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'workspace_id',
        'contact_id',
        'deal_id',
        'user_id',
        'type',
        'subject',
        'description',
        'due_date',
        'completed_at',
        'priority',
        'status',
        'metadata',
    ];

    protected $casts = [
        'id' => 'string',
        'due_date' => 'datetime',
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
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && 
               $this->due_date < now() && 
               $this->status !== 'completed';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date || $this->status === 'completed') {
            return null;
        }
        
        return now()->diffInDays($this->due_date, false);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('due_date', today());
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    // Business Logic Methods
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update contact's last contacted date if it's a contact activity
        if ($this->contact_id && in_array($this->type, ['call', 'email', 'meeting'])) {
            $this->contact->markAsContacted();
        }
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function reschedule(\DateTime $newDate): void
    {
        $this->update(['due_date' => $newDate]);
    }

    public function addNote(string $note): void
    {
        $metadata = $this->metadata ?? [];
        $metadata['notes'] = $metadata['notes'] ?? [];
        $metadata['notes'][] = [
            'note' => $note,
            'added_by' => auth()->id(),
            'added_at' => now()->toISOString(),
        ];
        
        $this->update(['metadata' => $metadata]);
    }
}