<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'severity',
        'affected_services',
        'started_at',
        'resolved_at'
    ];

    protected $casts = [
        'affected_services' => 'array',
        'started_at' => 'datetime',
        'resolved_at' => 'datetime'
    ];

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['investigating', 'identified', 'monitoring']);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeRecent($query)
    {
        return $query->where('started_at', '>=', now()->subDays(30));
    }

    public function isActive()
    {
        return in_array($this->status, ['investigating', 'identified', 'monitoring']);
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function getDurationAttribute()
    {
        if ($this->resolved_at) {
            return $this->started_at->diffForHumans($this->resolved_at);
        }
        return $this->started_at->diffForHumans();
    }
}