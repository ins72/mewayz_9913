<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Contact extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'workspace_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'job_title',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'website',
        'notes',
        'tags',
        'source',
        'lead_score',
        'status',
        'last_contacted_at',
        'social_profiles',
        'custom_fields',
    ];

    protected $casts = [
        'id' => 'string',
        'tags' => 'array',
        'social_profiles' => 'array',
        'custom_fields' => 'array',
        'last_contacted_at' => 'datetime',
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

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByLeadScore($query, $minScore = 0)
    {
        return $query->where('lead_score', '>=', $minScore);
    }

    public function scopeRecentlyContacted($query, $days = 30)
    {
        return $query->where('last_contacted_at', '>=', now()->subDays($days));
    }

    // Business Logic Methods
    public function updateLeadScore(): void
    {
        $score = 0;

        // Score based on profile completeness
        if ($this->phone) $score += 10;
        if ($this->company) $score += 15;
        if ($this->website) $score += 10;
        if ($this->social_profiles) $score += 5;

        // Score based on engagement
        $recentActivities = $this->activities()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $score += min($recentActivities * 5, 25);

        // Score based on deals
        $activeDeals = $this->deals()
            ->whereIn('stage', ['qualified', 'proposal', 'negotiation'])
            ->count();
        $score += $activeDeals * 20;

        $this->update(['lead_score' => min($score, 100)]);
    }

    public function markAsContacted(): void
    {
        $this->update(['last_contacted_at' => now()]);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        $this->update(['tags' => array_values($tags)]);
    }
}