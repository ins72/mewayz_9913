<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealTimeSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'session_id',
        'channel_name',
        'session_type',
        'status',
        'participants',
        'permissions',
        'session_data',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'participants' => 'array',
        'permissions' => 'array',
        'session_data' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    /**
     * Get the workspace this session belongs to
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the user who started this session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get participant details
     */
    public function getParticipantDetails(): array
    {
        if (empty($this->participants)) {
            return [];
        }

        return User::whereIn('id', $this->participants)
            ->select('id', 'name', 'email', 'avatar')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? null,
                    'initials' => strtoupper(substr($user->name, 0, 2))
                ];
            })
            ->toArray();
    }

    /**
     * Check if user is participant
     */
    public function isParticipant(int $userId): bool
    {
        return in_array($userId, $this->participants ?? []);
    }

    /**
     * Add participant
     */
    public function addParticipant(int $userId): void
    {
        $participants = $this->participants ?? [];
        
        if (!in_array($userId, $participants)) {
            $participants[] = $userId;
            $this->update(['participants' => $participants]);
        }
    }

    /**
     * Remove participant
     */
    public function removeParticipant(int $userId): void
    {
        $participants = $this->participants ?? [];
        
        if (($key = array_search($userId, $participants)) !== false) {
            unset($participants[$key]);
            $participants = array_values($participants);
            $this->update(['participants' => $participants]);
        }
    }

    /**
     * Get session duration in minutes
     */
    public function getDurationAttribute(): ?int
    {
        if (!$this->started_at) {
            return null;
        }

        $endTime = $this->ended_at ?? now();
        return $this->started_at->diffInMinutes($endTime);
    }

    /**
     * Get message count
     */
    public function getMessageCountAttribute(): int
    {
        return count($this->session_data['messages'] ?? []);
    }

    /**
     * Get update count
     */
    public function getUpdateCountAttribute(): int
    {
        return count($this->session_data['updates'] ?? []);
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for ended sessions
     */
    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    /**
     * Scope for workspace sessions
     */
    public function scopeForWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Scope for session type
     */
    public function scopeOfType($query, $sessionType)
    {
        return $query->where('session_type', $sessionType);
    }
}