<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Template extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'creator_id',
        'category_id',
        'name',
        'description',
        'template_type',
        'price',
        'tags',
        'preview_images',
        'template_data',
        'demo_url',
        'download_count',
        'status',
        'is_active',
        'featured',
        'average_rating',
        'review_count',
        'metadata',
    ];

    protected $casts = [
        'id' => 'string',
        'price' => 'decimal:2',
        'preview_images' => 'array',
        'template_data' => 'array',
        'download_count' => 'integer',
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'average_rating' => 'decimal:2',
        'review_count' => 'integer',
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
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TemplateCategory::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(TemplatePurchase::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TemplateReview::class);
    }

    // Accessors
    public function getDownloadUrlAttribute(): string
    {
        return url("/api/templates/{$this->id}/download");
    }

    public function getPreviewUrlAttribute(): ?string
    {
        return $this->demo_url ?: url("/api/templates/{$this->id}/preview");
    }

    public function getIsFreeAttribute(): bool
    {
        return $this->price == 0;
    }

    public function getIsPremiumAttribute(): bool
    {
        return $this->price > 50;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'suspended' => 'Suspended',
            default => 'Unknown'
        };
    }

    public function getTemplateTypeLabelAttribute(): string
    {
        return match($this->template_type) {
            'website' => 'Website Template',
            'email' => 'Email Template',
            'social' => 'Social Media Template',
            'bio' => 'Link in Bio Template',
            'course' => 'Course Template',
            default => 'Unknown Type'
        };
    }

    public function getTagsArrayAttribute(): array
    {
        return $this->tags ? explode(',', $this->tags) : [];
    }

    public function getFirstPreviewImageAttribute(): ?string
    {
        return $this->preview_images && count($this->preview_images) > 0 
            ? $this->preview_images[0] 
            : null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->price == 0 ? 'Free' : '$' . number_format($this->price, 2);
    }

    public function getRatingStarsAttribute(): string
    {
        $fullStars = floor($this->average_rating);
        $halfStar = $this->average_rating - $fullStars >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

        return str_repeat('★', $fullStars) . 
               ($halfStar ? '☆' : '') . 
               str_repeat('☆', $emptyStars);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('template_type', $type);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    public function scopeFree($query)
    {
        return $query->where('price', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('price', '>', 0);
    }

    public function scopePremium($query)
    {
        return $query->where('price', '>', 50);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('download_count', 'desc');
    }

    public function scopeTopRated($query)
    {
        return $query->orderBy('average_rating', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeSearchByName($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeSearchByTags($query, $search)
    {
        return $query->where('tags', 'like', "%{$search}%");
    }

    // Business Logic
    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function makeFeatured(): void
    {
        $this->update(['featured' => true]);
    }

    public function removeFeatured(): void
    {
        $this->update(['featured' => false]);
    }

    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->creator_id === $user->id;
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->creator_id === $user->id;
    }

    public function hasPurchasedBy(User $user): bool
    {
        return $this->purchases()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->exists();
    }

    public function hasReviewedBy(User $user): bool
    {
        return $this->reviews()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function getTotalEarnings(): float
    {
        return $this->purchases()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getMonthlyEarnings(): float
    {
        return $this->purchases()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonth())
            ->sum('amount');
    }

    public function updateRating(): void
    {
        $averageRating = $this->reviews()->avg('rating');
        $reviewCount = $this->reviews()->count();

        $this->update([
            'average_rating' => round($averageRating, 2),
            'review_count' => $reviewCount,
        ]);
    }
}