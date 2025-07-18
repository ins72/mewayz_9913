<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'severity',
        'affected_services',
        'started_at',
        'resolved_at',
        'scheduled_at',
        'is_public'
    ];

    protected $casts = [
        'affected_services' => 'array',
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_public' => 'boolean'
    ];

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}