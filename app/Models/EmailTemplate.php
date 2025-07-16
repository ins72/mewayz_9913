<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'name',
        'description',
        'category',
        'subject',
        'html_content',
        'text_content',
        'variables',
        'thumbnail_url',
        'is_default',
        'is_active',
        'usage_count'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'usage_count' => 'integer'
    ];

    /**
     * Get the workspace that owns the template
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the user that created the template
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the campaigns using this template
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class, 'template_id');
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * Get template variables from content
     */
    public function extractVariables(): array
    {
        $pattern = '/\{\{(\w+)\}\}/';
        $variables = [];
        
        if (preg_match_all($pattern, $this->html_content, $matches)) {
            $variables = array_unique($matches[1]);
        }
        
        return $variables;
    }

    /**
     * Replace variables in template content
     */
    public function replaceVariables(array $variables): string
    {
        $content = $this->html_content;
        
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Get preview URL for template
     */
    public function getPreviewUrl(): string
    {
        return route('email-templates.preview', $this->id);
    }

    /**
     * Get category color for UI
     */
    public function getCategoryColor(): string
    {
        return match($this->category) {
            'newsletter' => '#3B82F6',
            'promotional' => '#10B981',
            'transactional' => '#F59E0B',
            'custom' => '#8B5CF6',
            default => '#6B7280'
        };
    }

    /**
     * Get formatted category name
     */
    public function getFormattedCategory(): string
    {
        return match($this->category) {
            'newsletter' => 'Newsletter',
            'promotional' => 'Promotional',
            'transactional' => 'Transactional',
            'custom' => 'Custom',
            default => 'Other'
        };
    }

    /**
     * Check if template can be edited
     */
    public function canBeEdited(): bool
    {
        return !$this->is_default;
    }

    /**
     * Check if template can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_default && $this->campaigns()->count() === 0;
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for filtering by workspace
     */
    public function scopeByWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for default templates
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom templates
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }
}