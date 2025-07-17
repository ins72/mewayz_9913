<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TemplateCategory extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'id' => 'string',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    // Relationships
    public function templates(): HasMany
    {
        return $this->hasMany(Template::class, 'category_id');
    }

    // Accessors
    public function getTemplateCountAttribute(): int
    {
        return $this->templates()->count();
    }

    public function getActiveTemplateCountAttribute(): int
    {
        return $this->templates()->active()->count();
    }

    public function getApprovedTemplateCountAttribute(): int
    {
        return $this->templates()->approved()->active()->count();
    }

    public function getUrlAttribute(): string
    {
        return url("/templates/category/{$this->slug}");
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderBySortOrder($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    // Business Logic
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function updateSortOrder(int $order): void
    {
        $this->update(['sort_order' => $order]);
    }

    public function getPopularTemplates(int $limit = 10)
    {
        return $this->templates()
            ->approved()
            ->active()
            ->popular()
            ->limit($limit)
            ->get();
    }

    public function getRecentTemplates(int $limit = 10)
    {
        return $this->templates()
            ->approved()
            ->active()
            ->recent()
            ->limit($limit)
            ->get();
    }

    public function getFeaturedTemplates(int $limit = 5)
    {
        return $this->templates()
            ->approved()
            ->active()
            ->featured()
            ->limit($limit)
            ->get();
    }
}