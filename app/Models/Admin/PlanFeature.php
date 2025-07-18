<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanFeature extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'category', 'type', 'config',
        'is_active', 'sort_order'
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'plan_feature_assignments')
                    ->withPivot(['is_enabled', 'limits', 'config'])
                    ->withTimestamps();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(PlanFeatureAssignment::class, 'feature_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getTypeIcon(): string
    {
        $icons = [
            'boolean' => 'fas fa-toggle-on',
            'numeric' => 'fas fa-hashtag',
            'text' => 'fas fa-text-width',
            'select' => 'fas fa-list',
            'multiselect' => 'fas fa-list-ul'
        ];

        return $icons[$this->type] ?? 'fas fa-cog';
    }

    public function getCategoryColor(): string
    {
        $colors = [
            'general' => 'primary',
            'storage' => 'info',
            'users' => 'success',
            'api' => 'warning',
            'integrations' => 'danger',
            'analytics' => 'dark',
            'support' => 'secondary'
        ];

        return $colors[$this->category] ?? 'primary';
    }

    public function getDefaultConfig(): array
    {
        $defaults = [
            'boolean' => ['default' => false],
            'numeric' => ['default' => 0, 'min' => 0, 'max' => null],
            'text' => ['default' => '', 'max_length' => 255],
            'select' => ['default' => null, 'options' => []],
            'multiselect' => ['default' => [], 'options' => []]
        ];

        return array_merge($defaults[$this->type] ?? [], $this->config ?? []);
    }

    public function validateValue($value): bool
    {
        $config = $this->getDefaultConfig();

        switch ($this->type) {
            case 'boolean':
                return is_bool($value);
            
            case 'numeric':
                if (!is_numeric($value)) {
                    return false;
                }
                
                if (isset($config['min']) && $value < $config['min']) {
                    return false;
                }
                
                if (isset($config['max']) && $value > $config['max']) {
                    return false;
                }
                
                return true;
            
            case 'text':
                if (!is_string($value)) {
                    return false;
                }
                
                if (isset($config['max_length']) && strlen($value) > $config['max_length']) {
                    return false;
                }
                
                return true;
            
            case 'select':
                $options = $config['options'] ?? [];
                return in_array($value, $options);
            
            case 'multiselect':
                if (!is_array($value)) {
                    return false;
                }
                
                $options = $config['options'] ?? [];
                foreach ($value as $item) {
                    if (!in_array($item, $options)) {
                        return false;
                    }
                }
                
                return true;
        }

        return true;
    }

    public function getUsageStats(): array
    {
        $totalPlans = SubscriptionPlan::count();
        $plansWithFeature = $this->assignments()->where('is_enabled', true)->count();
        $usagePercentage = $totalPlans > 0 ? ($plansWithFeature / $totalPlans) * 100 : 0;

        return [
            'total_plans' => $totalPlans,
            'plans_with_feature' => $plansWithFeature,
            'usage_percentage' => round($usagePercentage, 2)
        ];
    }

    public static function getCategories(): array
    {
        return [
            'general' => 'General Features',
            'storage' => 'Storage & Files',
            'users' => 'User Management',
            'api' => 'API & Integrations',
            'integrations' => 'Third-party Integrations',
            'analytics' => 'Analytics & Reporting',
            'support' => 'Support & Help'
        ];
    }

    public static function getTypes(): array
    {
        return [
            'boolean' => 'On/Off Toggle',
            'numeric' => 'Numeric Value',
            'text' => 'Text Value',
            'select' => 'Single Choice',
            'multiselect' => 'Multiple Choice'
        ];
    }
}