<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Str::slug($category->name);
            }
        });
    }

    /**
     * Get templates in this category
     */
    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    /**
     * Get published templates in this category
     */
    public function publishedTemplates(): HasMany
    {
        return $this->templates()->published();
    }

    /**
     * Get template count in this category
     */
    public function getTemplateCount(): int
    {
        return $this->templates()->published()->count();
    }

    /**
     * Scope to get active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the icon HTML for display
     */
    public function getIconHtml(): string
    {
        if (empty($this->icon)) {
            return '<div class="w-6 h-6 bg-gray-400 rounded"></div>';
        }

        // If it's an SVG string, return it directly
        if (str_starts_with($this->icon, '<svg')) {
            return $this->icon;
        }

        // If it's a CSS class (like Font Awesome), return as icon
        if (str_starts_with($this->icon, 'fa-') || str_starts_with($this->icon, 'heroicon-')) {
            return '<i class="' . $this->icon . '"></i>';
        }

        // Default SVG icon
        return '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>';
    }

    /**
     * Get color style for display
     */
    public function getColorStyle(): string
    {
        return "color: {$this->color}; background-color: {$this->color}20;";
    }
}