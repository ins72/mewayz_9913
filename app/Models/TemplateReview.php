<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TemplateReview extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'template_id',
        'rating',
        'comment',
        'is_verified',
        'helpful_count',
        'metadata',
    ];

    protected $casts = [
        'id' => 'string',
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'helpful_count' => 'integer',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    // Accessors
    public function getRatingStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getIsPositiveAttribute(): bool
    {
        return $this->rating >= 4;
    }

    public function getIsNegativeAttribute(): bool
    {
        return $this->rating <= 2;
    }

    public function getIsNeutralAttribute(): bool
    {
        return $this->rating === 3;
    }

    public function getShortCommentAttribute(): string
    {
        return Str::limit($this->comment, 100);
    }

    // Scopes
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopePositive($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeNegative($query)
    {
        return $query->where('rating', '<=', 2);
    }

    public function scopeNeutral($query)
    {
        return $query->where('rating', 3);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeWithComment($query)
    {
        return $query->whereNotNull('comment');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('helpful_count', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    // Business Logic
    public function markAsVerified(): void
    {
        $this->update(['is_verified' => true]);
    }

    public function markAsHelpful(): void
    {
        $this->increment('helpful_count');
    }

    public function markAsUnhelpful(): void
    {
        $this->decrement('helpful_count');
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isHelpful(): bool
    {
        return $this->helpful_count > 0;
    }

    public function updateHelpfulCount(): void
    {
        // In a real implementation, you would track user votes
        // For now, this is a placeholder
    }
}