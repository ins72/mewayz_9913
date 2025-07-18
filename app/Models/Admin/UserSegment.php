<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class UserSegment extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'conditions', 'is_dynamic', 'user_count', 'last_calculated', 'is_active'
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_dynamic' => 'boolean',
        'is_active' => 'boolean',
        'user_count' => 'integer',
        'last_calculated' => 'datetime'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_segment_memberships')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDynamic($query)
    {
        return $query->where('is_dynamic', true);
    }

    public function scopeStatic($query)
    {
        return $query->where('is_dynamic', false);
    }

    public function calculateUsers(): int
    {
        if (!$this->is_dynamic || !$this->conditions) {
            return $this->user_count;
        }

        $query = User::query();
        
        foreach ($this->conditions as $condition) {
            $this->applyCondition($query, $condition);
        }

        $count = $query->count();
        
        // Update the segment
        $this->update([
            'user_count' => $count,
            'last_calculated' => now()
        ]);

        return $count;
    }

    public function refreshMemberships(): void
    {
        if (!$this->is_dynamic || !$this->conditions) {
            return;
        }

        $query = User::query();
        
        foreach ($this->conditions as $condition) {
            $this->applyCondition($query, $condition);
        }

        $userIds = $query->pluck('id')->toArray();
        
        // Sync users with this segment
        $this->users()->sync($userIds);
        
        // Update count
        $this->update([
            'user_count' => count($userIds),
            'last_calculated' => now()
        ]);
    }

    private function applyCondition($query, array $condition): void
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? '=';
        $value = $condition['value'] ?? null;
        $logic = $condition['logic'] ?? 'and';

        if (!$field) {
            return;
        }

        $method = $logic === 'or' ? 'orWhere' : 'where';

        switch ($operator) {
            case '=':
                $query->$method($field, $value);
                break;
            case '!=':
                $query->$method($field, '!=', $value);
                break;
            case '>':
                $query->$method($field, '>', $value);
                break;
            case '<':
                $query->$method($field, '<', $value);
                break;
            case '>=':
                $query->$method($field, '>=', $value);
                break;
            case '<=':
                $query->$method($field, '<=', $value);
                break;
            case 'contains':
                $query->$method($field, 'LIKE', '%' . $value . '%');
                break;
            case 'starts_with':
                $query->$method($field, 'LIKE', $value . '%');
                break;
            case 'ends_with':
                $query->$method($field, 'LIKE', '%' . $value);
                break;
            case 'in':
                $values = is_array($value) ? $value : explode(',', $value);
                $query->$method($field, $values);
                break;
            case 'not_in':
                $values = is_array($value) ? $value : explode(',', $value);
                $query->$method($field, $values);
                break;
            case 'between':
                if (is_array($value) && count($value) === 2) {
                    $query->$method($field, $value);
                }
                break;
            case 'null':
                $query->$method($field);
                break;
            case 'not_null':
                $query->$method($field);
                break;
        }
    }

    public function addUser(User $user): void
    {
        if (!$this->users()->where('user_id', $user->id)->exists()) {
            $this->users()->attach($user->id, ['joined_at' => now()]);
            $this->increment('user_count');
        }
    }

    public function removeUser(User $user): void
    {
        if ($this->users()->where('user_id', $user->id)->exists()) {
            $this->users()->detach($user->id);
            $this->decrement('user_count');
        }
    }

    public function getGrowthRate(): float
    {
        if (!$this->last_calculated) {
            return 0;
        }

        $previousCount = $this->user_count;
        $currentCount = $this->calculateUsers();

        if ($previousCount === 0) {
            return $currentCount > 0 ? 100 : 0;
        }

        return (($currentCount - $previousCount) / $previousCount) * 100;
    }

    public static function getAvailableFields(): array
    {
        return [
            'id' => 'User ID',
            'name' => 'Name',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Registration Date',
            'updated_at' => 'Last Updated',
            'email_verified_at' => 'Email Verified',
            'role' => 'Role'
        ];
    }

    public static function getAvailableOperators(): array
    {
        return [
            '=' => 'Equals',
            '!=' => 'Not Equals',
            '>' => 'Greater Than',
            '<' => 'Less Than',
            '>=' => 'Greater Than or Equal',
            '<=' => 'Less Than or Equal',
            'contains' => 'Contains',
            'starts_with' => 'Starts With',
            'ends_with' => 'Ends With',
            'in' => 'In List',
            'not_in' => 'Not In List',
            'between' => 'Between',
            'null' => 'Is Null',
            'not_null' => 'Is Not Null'
        ];
    }
}