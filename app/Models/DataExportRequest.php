<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataExportRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'data_types',
        'format',
        'reason',
        'status',
        'requested_at',
        'processed_at',
        'completed_at',
        'file_path',
        'download_url',
        'expires_at',
        'ip_address',
        'processing_notes'
    ];

    protected $casts = [
        'data_types' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canDownload()
    {
        return $this->status === 'completed' && 
               $this->file_path && 
               !$this->isExpired();
    }
}