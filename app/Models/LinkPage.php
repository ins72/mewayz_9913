<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LinkPage extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'title',
        'slug',
        'description',
        'custom_domain',
        'theme_settings',
        'content_blocks',
        'seo_settings',
        'social_settings',
        'is_published',
        'is_password_protected',
        'password',
        'analytics_settings',
        'view_count',
        'click_count',
        'published_at',
    ];

    protected $casts = [
        'id' => 'string',
        'theme_settings' => 'array',
        'content_blocks' => 'array',
        'seo_settings' => 'array',
        'social_settings' => 'array',
        'analytics_settings' => 'array',
        'is_published' => 'boolean',
        'is_password_protected' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // Auto-generate slug if not provided
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . Str::random(6);
            }
        });

        static::updating(function ($model) {
            // Set published_at when publishing
            if ($model->is_published && !$model->published_at) {
                $model->published_at = now();
            }
        });
    }

    // Relationships
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getPublicUrlAttribute(): string
    {
        if ($this->custom_domain) {
            return 'https://' . $this->custom_domain;
        }
        
        return url('/l/' . $this->slug);
    }

    public function getPreviewUrlAttribute(): string
    {
        return url('/preview/' . $this->slug);
    }

    public function getClickThroughRateAttribute(): float
    {
        if ($this->view_count === 0) {
            return 0;
        }
        
        return ($this->click_count / $this->view_count) * 100;
    }

    public function getDefaultThemeAttribute(): array
    {
        return [
            'background_color' => '#ffffff',
            'text_color' => '#333333',
            'accent_color' => '#007bff',
            'font_family' => 'Inter',
            'border_radius' => '8px',
            'button_style' => 'rounded',
        ];
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeByDomain($query, $domain)
    {
        return $query->where('custom_domain', $domain);
    }

    // Business Logic Methods
    public function publish(): void
    {
        $this->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    public function unpublish(): void
    {
        $this->update(['is_published' => false]);
    }

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    public function incrementClicks(): void
    {
        $this->increment('click_count');
    }

    public function addContentBlock(array $block): void
    {
        $blocks = $this->content_blocks ?? [];
        $block['id'] = (string) Str::uuid();
        $block['order'] = count($blocks);
        $blocks[] = $block;
        
        $this->update(['content_blocks' => $blocks]);
    }

    public function updateContentBlock(string $blockId, array $data): void
    {
        $blocks = $this->content_blocks ?? [];
        
        foreach ($blocks as &$block) {
            if ($block['id'] === $blockId) {
                $block = array_merge($block, $data);
                break;
            }
        }
        
        $this->update(['content_blocks' => $blocks]);
    }

    public function removeContentBlock(string $blockId): void
    {
        $blocks = $this->content_blocks ?? [];
        $blocks = array_filter($blocks, fn($block) => $block['id'] !== $blockId);
        
        // Reorder blocks
        $blocks = array_values($blocks);
        foreach ($blocks as $index => &$block) {
            $block['order'] = $index;
        }
        
        $this->update(['content_blocks' => $blocks]);
    }

    public function reorderContentBlocks(array $blockIds): void
    {
        $blocks = $this->content_blocks ?? [];
        $reorderedBlocks = [];
        
        foreach ($blockIds as $index => $blockId) {
            foreach ($blocks as &$block) {
                if ($block['id'] === $blockId) {
                    $block['order'] = $index;
                    $reorderedBlocks[] = $block;
                    break;
                }
            }
        }
        
        $this->update(['content_blocks' => $reorderedBlocks]);
    }

    public function applyTheme(array $theme): void
    {
        $currentTheme = $this->theme_settings ?? $this->default_theme;
        $this->update(['theme_settings' => array_merge($currentTheme, $theme)]);
    }

    public function generateSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        
        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}