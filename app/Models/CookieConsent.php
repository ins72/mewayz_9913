<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CookieConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'consent_type',
        'cookies_accepted',
        'ip_address',
        'user_agent',
        'consented_at',
        'expires_at'
    ];

    protected $casts = [
        'cookies_accepted' => 'array',
        'consented_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }
}