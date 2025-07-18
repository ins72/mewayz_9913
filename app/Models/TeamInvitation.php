<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'email',
        'role',
        'invited_by',
        'token',
        'expires_at',
        'accepted_at',
        'declined_at',
        'status'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime'
    ];

    /**
     * Get the workspace this invitation belongs to
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the user who sent the invitation
     */
    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get the user who accepted the invitation
     */
    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    /**
     * Scope for pending invitations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->whereNull('accepted_at')
            ->whereNull('declined_at')
            ->where('expires_at', '>', now());
    }

    /**
     * Scope for expired invitations
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope for accepted invitations
     */
    public function scopeAccepted($query)
    {
        return $query->whereNotNull('accepted_at');
    }

    /**
     * Scope for declined invitations
     */
    public function scopeDeclined($query)
    {
        return $query->whereNotNull('declined_at');
    }

    /**
     * Check if invitation is expired
     */
    public function isExpired()
    {
        return $this->expires_at < now();
    }

    /**
     * Check if invitation is pending
     */
    public function isPending()
    {
        return $this->status === 'pending' && 
               !$this->accepted_at && 
               !$this->declined_at && 
               !$this->isExpired();
    }

    /**
     * Accept the invitation
     */
    public function accept($userId)
    {
        $this->update([
            'accepted_at' => now(),
            'accepted_by' => $userId,
            'status' => 'accepted'
        ]);
    }

    /**
     * Decline the invitation
     */
    public function decline()
    {
        $this->update([
            'declined_at' => now(),
            'status' => 'declined'
        ]);
    }

    /**
     * Regenerate the invitation token
     */
    public function regenerateToken()
    {
        $this->update([
            'token' => Str::random(32),
            'expires_at' => now()->addDays(7)
        ]);
    }
}