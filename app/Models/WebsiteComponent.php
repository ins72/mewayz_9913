<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WebsiteComponent extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'website_id',
        'page_id',
        'type',
        'content',
        'settings',
        'position',
        'order',
        'is_active',
        'custom_css',
        'custom_js',
        'responsive_settings',
    ];

    protected $casts = [
        'id' => 'string',
        'content' => 'array',
        'settings' => 'array',
        'responsive_settings' => 'array',
        'is_active' => 'boolean',
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

    public function page(): BelongsTo
    {
        return $this->belongsTo(WebsitePage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Business Logic Methods
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function duplicate(): self
    {
        $duplicate = $this->replicate();
        $duplicate->id = (string) Str::uuid();
        $duplicate->order = $this->page->components()->max('order') + 1;
        $duplicate->save();

        return $duplicate;
    }

    public function moveToPosition(string $position): void
    {
        $this->update(['position' => $position]);
    }

    public function updateOrder(int $order): void
    {
        $this->update(['order' => $order]);
    }

    public function renderContent(): string
    {
        // This would render the component based on its type and content
        // For now, return a basic HTML structure
        $content = $this->content;
        
        switch ($this->type) {
            case 'heading':
                return "<h{$content['level']}>{$content['text']}</h{$content['level']}>";
            
            case 'paragraph':
                return "<p>{$content['text']}</p>";
            
            case 'image':
                return "<img src='{$content['src']}' alt='{$content['alt']}' class='responsive-image'>";
            
            case 'button':
                return "<a href='{$content['link']}' class='btn btn-{$content['style']}'>{$content['text']}</a>";
            
            default:
                return "<div class='component-{$this->type}'>" . json_encode($content) . "</div>";
        }
    }
}