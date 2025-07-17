<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Team extends Model
{
    protected $table = 'teams';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'workspace_id',
        'name',
        'description',
        'leader_id',
        'department_id',
        'settings',
        'status',
        'created_by'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Get the workspace that owns the team
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the team leader
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Get the department that owns the team
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the team members
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'team_members', 'team_id', 'user_id')
                    ->withPivot(['role', 'joined_at', 'status'])
                    ->withTimestamps();
    }

    /**
     * Get the user who created the team
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if team is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get team member count
     */
    public function getMemberCount()
    {
        return $this->members()->count();
    }

    /**
     * Check if user is team leader
     */
    public function isLeader(User $user)
    {
        return $this->leader_id === $user->id;
    }

    /**
     * Check if user is team member
     */
    public function isMember(User $user)
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Add member to team
     */
    public function addMember(User $user, $role = 'member')
    {
        return $this->members()->attach($user->id, [
            'role' => $role,
            'joined_at' => now(),
            'status' => 'active'
        ]);
    }

    /**
     * Remove member from team
     */
    public function removeMember(User $user)
    {
        return $this->members()->detach($user->id);
    }

    /**
     * Update member role
     */
    public function updateMemberRole(User $user, $role)
    {
        return $this->members()->updateExistingPivot($user->id, ['role' => $role]);
    }
}