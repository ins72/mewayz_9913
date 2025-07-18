<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeatureAssignment extends Model
{
    protected $fillable = [
        'plan_id', 'feature_id', 'is_enabled', 'limits', 'config'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'limits' => 'array',
        'config' => 'array'
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(PlanFeature::class, 'feature_id');
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeDisabled($query)
    {
        return $query->where('is_enabled', false);
    }

    public function getLimit(string $limitKey, $default = null)
    {
        $limits = $this->limits ?? [];
        return $limits[$limitKey] ?? $default;
    }

    public function setLimit(string $limitKey, $value): void
    {
        $limits = $this->limits ?? [];
        $limits[$limitKey] = $value;
        $this->limits = $limits;
    }

    public function getConfig(string $configKey, $default = null)
    {
        $config = $this->config ?? [];
        return $config[$configKey] ?? $default;
    }

    public function setConfig(string $configKey, $value): void
    {
        $config = $this->config ?? [];
        $config[$configKey] = $value;
        $this->config = $config;
    }

    public function hasLimit(string $limitKey): bool
    {
        $limits = $this->limits ?? [];
        return isset($limits[$limitKey]);
    }

    public function hasConfig(string $configKey): bool
    {
        $config = $this->config ?? [];
        return isset($config[$configKey]);
    }

    public function getFormattedLimits(): array
    {
        $limits = $this->limits ?? [];
        $formatted = [];

        foreach ($limits as $key => $value) {
            $formatted[] = [
                'key' => $key,
                'value' => $value,
                'formatted' => $this->formatLimitValue($key, $value)
            ];
        }

        return $formatted;
    }

    private function formatLimitValue(string $key, $value): string
    {
        // Format common limit types
        switch ($key) {
            case 'storage':
            case 'bandwidth':
                return $this->formatBytes($value);
            case 'users':
            case 'workspaces':
            case 'projects':
                return number_format($value);
            case 'api_calls':
                return number_format($value) . ' per month';
            default:
                return is_numeric($value) ? number_format($value) : (string) $value;
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}