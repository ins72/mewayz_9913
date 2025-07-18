<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'status',
        'response_time',
        'last_checked'
    ];

    protected $casts = [
        'response_time' => 'decimal:2',
        'last_checked' => 'datetime'
    ];

    public function scopeOperational($query)
    {
        return $query->where('status', 'operational');
    }

    public function scopeDegraded($query)
    {
        return $query->where('status', 'degraded');
    }

    public function scopeDown($query)
    {
        return $query->where('status', 'down');
    }

    public function isOperational()
    {
        return $this->status === 'operational';
    }

    public function isDegraded()
    {
        return $this->status === 'degraded';
    }

    public function isDown()
    {
        return $this->status === 'down';
    }
}