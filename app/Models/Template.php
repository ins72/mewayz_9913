<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'category_id',
        'user_id',
        'template_data',
        'preview_image',
        'tags',
        'price',
        'status',
        'is_featured',
        'is_premium',
        'downloads',
        'rating',
        'rating_count',
        'metadata',
    ];

    protected $casts = [
        'template_data' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_premium' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = \Str::slug($template->name);
            }
        });
    }

    /**
     * Get the category that owns this template
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TemplateCategory::class);
    }

    /**
     * Get the user who created this template
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if template is free
     */
    public function isFree(): bool
    {
        return $this->price == 0;
    }

    /**
     * Check if template is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if template is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Increment download count
     */
    public function incrementDownloads()
    {
        $this->increment('downloads');
    }

    /**
     * Add rating to template
     */
    public function addRating(float $rating)
    {
        $totalRating = ($this->rating * $this->rating_count) + $rating;
        $this->rating_count++;
        $this->rating = $totalRating / $this->rating_count;
        $this->save();
    }

    /**
     * Get formatted price
     */
    public function getFormattedPrice(): string
    {
        return $this->isFree() ? 'Free' : '$' . number_format($this->price, 2);
    }

    /**
     * Get template preview URL
     */
    public function getPreviewUrl(): string
    {
        return $this->preview_image ?: '/images/template-placeholder.png';
    }

    /**
     * Scope to get published templates
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get featured templates
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get free templates
     */
    public function scopeFree($query)
    {
        return $query->where('price', 0);
    }

    /**
     * Scope to get premium templates
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope to get templates by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get templates by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to search templates
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereJsonContains('tags', $search);
        });
    }

    /**
     * Scope to order by popularity
     */
    public function scopePopular($query)
    {
        return $query->orderBy('downloads', 'desc')
                     ->orderBy('rating', 'desc');
    }

    /**
     * Scope to order by rating
     */
    public function scopeHighRated($query)
    {
        return $query->orderBy('rating', 'desc')
                     ->orderBy('rating_count', 'desc');
    }

    /**
     * Scope to order by newest
     */
    public function scopeNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get template types
     */
    public static function getTypes(): array
    {
        return [
            'email' => 'Email Templates',
            'bio-page' => 'Bio Page Templates',
            'landing-page' => 'Landing Page Templates',
            'course' => 'Course Templates',
            'social-media' => 'Social Media Templates',
            'marketing' => 'Marketing Templates',
        ];
    }

    /**
     * Get template statuses
     */
    public static function getStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'published' => 'Published',
            'rejected' => 'Rejected',
        ];
    }
}