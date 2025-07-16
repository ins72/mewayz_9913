<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get features that support this goal
     */
    public function features()
    {
        return Feature::active()->byGoal($this->slug)->orderBy('sort_order')->get();
    }

    /**
     * Get workspaces that have selected this goal
     */
    public function workspaces()
    {
        return Workspace::whereJsonContains('selected_goals', $this->slug)->get();
    }

    /**
     * Scope to get active goals
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
        return '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>';
    }
}