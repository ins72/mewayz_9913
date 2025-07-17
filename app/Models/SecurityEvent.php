<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SecurityEvent extends Model
{
    protected $table = 'security_events';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'workspace_id',
        'event_type',
        'severity',
        'title',
        'description',
        'context',
        'ip_address',
        'user_agent',
        'is_resolved',
        'resolved_by',
        'resolved_at'
    ];
    
    protected $casts = [
        'context' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
    
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
    
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }
    
    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }
    
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }
    
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }
    
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }
    
    public function scopeHigh($query)
    {
        return $query->where('severity', 'high');
    }
    
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
    
    public static function logEvent(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'workspace_id' => $data['workspace_id'] ?? null,
            'event_type' => $data['event_type'],
            'severity' => $data['severity'],
            'title' => $data['title'],
            'description' => $data['description'],
            'context' => $data['context'] ?? [],
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent()
        ]);
    }
    
    public function resolve(?string $userId = null): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_by' => $userId ?? auth()->id(),
            'resolved_at' => now()
        ]);
    }
    
    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }
    
    public function isHigh(): bool
    {
        return $this->severity === 'high';
    }
    
    public function isMedium(): bool
    {
        return $this->severity === 'medium';
    }
    
    public function isLow(): bool
    {
        return $this->severity === 'low';
    }
    
    public function getSeverityColorAttribute(): string
    {
        $colors = [
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green'
        ];
        
        return $colors[$this->severity] ?? 'gray';
    }
    
    public function getFormattedEventTypeAttribute(): string
    {
        $types = [
            'failed_login' => 'Failed Login Attempt',
            'multiple_failed_logins' => 'Multiple Failed Logins',
            'suspicious_activity' => 'Suspicious Activity',
            'permission_escalation' => 'Permission Escalation',
            'data_breach' => 'Data Breach',
            'malware_detected' => 'Malware Detected',
            'unauthorized_access' => 'Unauthorized Access',
            'account_lockout' => 'Account Lockout',
            'password_change' => 'Password Change',
            'mfa_disabled' => 'MFA Disabled'
        ];
        
        return $types[$this->event_type] ?? ucfirst(str_replace('_', ' ', $this->event_type));
    }
}