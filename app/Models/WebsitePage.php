<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class WebsitePage extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'website_id',
        'name',
        'slug',
        'title',
        'content',
        'meta_description',
        'meta_keywords',
        'settings',
        'is_home',
        'status',
        'published_at',
        'custom_css',
        'custom_js',
        'schema_markup',
    ];

    protected $casts = [
        'id' => 'string',
        'content' => 'array',
        'settings' => 'array',
        'schema_markup' => 'array',
        'is_home' => 'boolean',
        'published_at' => 'datetime',
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
    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(WebsiteComponent::class);
    }

    // Accessors
    public function getUrlAttribute(): string
    {
        if ($this->is_home) {
            return $this->website->url;
        }
        return $this->website->url . '/' . $this->slug;
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published';
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeHomePage($query)
    {
        return $query->where('is_home', true);
    }

    // Business Logic Methods
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function unpublish(): void
    {
        $this->update([
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function addComponent(string $type, array $content, array $settings = []): WebsiteComponent
    {
        $maxOrder = $this->components()->max('order') ?? 0;

        return $this->components()->create([
            'website_id' => $this->website_id,
            'type' => $type,
            'content' => $content,
            'settings' => $settings,
            'position' => 'main',
            'order' => $maxOrder + 1,
        ]);
    }

    public function reorderComponents(array $componentIds): void
    {
        foreach ($componentIds as $index => $componentId) {
            $this->components()
                ->where('id', $componentId)
                ->update(['order' => $index + 1]);
        }
    }

    public function generateSchemaMarkup(): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $this->title,
            'description' => $this->meta_description,
            'url' => $this->url,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => $this->website->name,
                'url' => $this->website->url,
            ],
        ];

        return $schema;
    }
}