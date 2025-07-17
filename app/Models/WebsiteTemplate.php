<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class WebsiteTemplate extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'category',
        'preview_image',
        'demo_url',
        'price',
        'is_free',
        'is_active',
        'features',
        'template_data',
        'styles',
        'scripts',
        'created_by',
        'tags',
    ];

    protected $casts = [
        'id' => 'string',
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'features' => 'array',
        'template_data' => 'array',
        'styles' => 'array',
        'scripts' => 'array',
        'tags' => 'array',
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
    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return $this->is_free ? 'Free' : '$' . number_format($this->price, 2);
    }

    // Business Logic Methods
    public function createWebsite(User $user, string $name, string $domain): Website
    {
        $website = Website::create([
            'user_id' => $user->id,
            'name' => $name,
            'domain' => $domain,
            'template_id' => $this->id,
            'settings' => $this->template_data['settings'] ?? [],
            'status' => 'draft',
        ]);

        // Create pages from template
        foreach ($this->template_data['pages'] ?? [] as $pageData) {
            $page = WebsitePage::create([
                'website_id' => $website->id,
                'name' => $pageData['name'],
                'slug' => $pageData['slug'],
                'title' => $pageData['title'],
                'content' => $pageData['content'],
                'meta_description' => $pageData['meta_description'],
                'is_home' => $pageData['is_home'] ?? false,
                'status' => 'draft',
            ]);

            // Create components from template
            foreach ($pageData['components'] ?? [] as $componentData) {
                WebsiteComponent::create([
                    'website_id' => $website->id,
                    'page_id' => $page->id,
                    'type' => $componentData['type'],
                    'content' => $componentData['content'],
                    'settings' => $componentData['settings'] ?? [],
                    'position' => $componentData['position'] ?? 'main',
                    'order' => $componentData['order'] ?? 0,
                ]);
            }
        }

        return $website;
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}