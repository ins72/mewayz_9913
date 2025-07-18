<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataDeletionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'reason',
        'keep_anonymous_data',
        'status',
        'requested_at',
        'processed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'ip_address',
        'processing_notes'
    ];

    protected $casts = [
        'keep_anonymous_data' => 'boolean',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function canBeCancelled()
    {
        return $this->status === 'pending' && 
               $this->requested_at->addHours(72)->isFuture();
    }

    public function getCancellationDeadline()
    {
        return $this->requested_at->addHours(72);
    }
}