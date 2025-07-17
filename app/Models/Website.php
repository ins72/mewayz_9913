<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Website extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'name',
        'domain',
        'template_id',
        'description',
        'settings',
        'status',
        'published_at',
        'custom_css',
        'custom_js',
        'favicon',
        'logo',
        'meta_tags',
        'analytics_code',
        'backup_data',
    ];

    protected $casts = [
        'id' => 'string',
        'settings' => 'array',
        'meta_tags' => 'array',
        'backup_data' => 'array',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WebsiteTemplate::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(WebsitePage::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(WebsiteComponent::class);
    }

    // Accessors
    public function getUrlAttribute(): string
    {
        return 'https://' . $this->domain;
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

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
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

    public function createBackup(): void
    {
        $backupData = [
            'pages' => $this->pages()->with('components')->get()->toArray(),
            'settings' => $this->settings,
            'custom_css' => $this->custom_css,
            'custom_js' => $this->custom_js,
            'created_at' => now()->toISOString(),
        ];

        $this->update(['backup_data' => $backupData]);
    }

    public function restoreFromBackup(): bool
    {
        if (!$this->backup_data) {
            return false;
        }

        // Delete existing pages and components
        $this->pages()->delete();
        $this->components()->delete();

        // Restore pages
        foreach ($this->backup_data['pages'] as $pageData) {
            $page = $this->pages()->create([
                'name' => $pageData['name'],
                'slug' => $pageData['slug'],
                'title' => $pageData['title'],
                'content' => $pageData['content'],
                'meta_description' => $pageData['meta_description'],
                'settings' => $pageData['settings'],
                'is_home' => $pageData['is_home'],
                'status' => $pageData['status'],
            ]);

            // Restore components
            foreach ($pageData['components'] as $componentData) {
                $page->components()->create([
                    'website_id' => $this->id,
                    'type' => $componentData['type'],
                    'content' => $componentData['content'],
                    'settings' => $componentData['settings'],
                    'position' => $componentData['position'],
                    'order' => $componentData['order'],
                ]);
            }
        }

        // Restore settings
        $this->update([
            'settings' => $this->backup_data['settings'],
            'custom_css' => $this->backup_data['custom_css'],
            'custom_js' => $this->backup_data['custom_js'],
        ]);

        return true;
    }
}