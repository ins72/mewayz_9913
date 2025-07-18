<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FeatureFlag extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'is_enabled', 'conditions', 'user_segments',
        'rollout_percentage', 'start_date', 'end_date'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'conditions' => 'array',
        'user_segments' => 'array',
        'rollout_percentage' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_enabled', true)
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    public function isActiveForUser(User $user): bool
    {
        if (!$this->is_enabled) {
            return false;
        }

        // Check date range
        if ($this->start_date && now()->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return false;
        }

        // Check rollout percentage
        if ($this->rollout_percentage < 100) {
            $hash = md5($this->slug . $user->id);
            $userPercentage = hexdec(substr($hash, 0, 8)) / 0xFFFFFFFF * 100;
            
            if ($userPercentage > $this->rollout_percentage) {
                return false;
            }
        }

        // Check user segments
        if ($this->user_segments && count($this->user_segments) > 0) {
            $userSegments = $user->segments()->whereIn('slug', $this->user_segments)->exists();
            if (!$userSegments) {
                return false;
            }
        }

        // Check additional conditions
        if ($this->conditions) {
            return $this->evaluateConditions($user);
        }

        return true;
    }

    private function evaluateConditions(User $user): bool
    {
        foreach ($this->conditions as $condition) {
            if (!$this->evaluateCondition($user, $condition)) {
                return false;
            }
        }

        return true;
    }

    private function evaluateCondition(User $user, array $condition): bool
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? '=';
        $value = $condition['value'] ?? null;

        if (!$field) {
            return true;
        }

        $userValue = $user->getAttribute($field);

        switch ($operator) {
            case '=':
                return $userValue == $value;
            case '!=':
                return $userValue != $value;
            case '>':
                return $userValue > $value;
            case '<':
                return $userValue < $value;
            case '>=':
                return $userValue >= $value;
            case '<=':
                return $userValue <= $value;
            case 'contains':
                return str_contains($userValue, $value);
            case 'in':
                $values = is_array($value) ? $value : explode(',', $value);
                return in_array($userValue, $values);
            case 'not_in':
                $values = is_array($value) ? $value : explode(',', $value);
                return !in_array($userValue, $values);
            default:
                return true;
        }
    }

    public function getActiveUsers(): int
    {
        if (!$this->is_enabled) {
            return 0;
        }

        $query = User::query();

        if ($this->user_segments && count($this->user_segments) > 0) {
            $query->whereHas('segments', function ($q) {
                $q->whereIn('slug', $this->user_segments);
            });
        }

        $totalUsers = $query->count();
        
        if ($this->rollout_percentage < 100) {
            return intval($totalUsers * ($this->rollout_percentage / 100));
        }

        return $totalUsers;
    }

    public function getUsageStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = $this->getActiveUsers();
        $usagePercentage = $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0;

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'usage_percentage' => round($usagePercentage, 2),
            'rollout_percentage' => $this->rollout_percentage
        ];
    }

    public static function isFeatureEnabled(string $slug, User $user = null): bool
    {
        $flag = self::where('slug', $slug)->first();
        
        if (!$flag) {
            return false;
        }

        if (!$user) {
            return $flag->is_enabled;
        }

        return $flag->isActiveForUser($user);
    }

    public static function getEnabledFeatures(User $user = null): array
    {
        $flags = self::active()->get();
        $enabledFeatures = [];

        foreach ($flags as $flag) {
            if (!$user || $flag->isActiveForUser($user)) {
                $enabledFeatures[] = $flag->slug;
            }
        }

        return $enabledFeatures;
    }
}