<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'name',
        'subject',
        'content',
        'template_id',
        'recipient_lists',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'delivered_count',
        'opened_count',
        'clicked_count',
        'unsubscribed_count',
        'bounced_count',
        'open_rate',
        'click_rate',
        'settings'
    ];

    protected $casts = [
        'recipient_lists' => 'array',
        'settings' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'open_rate' => 'decimal:2',
        'click_rate' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the workspace that owns the campaign
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the user that created the campaign
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template used for this campaign
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    /**
     * Get the analytics for this campaign
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(EmailCampaignAnalytics::class, 'campaign_id');
    }

    /**
     * Get the email lists for this campaign
     */
    public function emailLists(): BelongsToMany
    {
        return $this->belongsToMany(EmailList::class, 'email_list_campaigns', 'campaign_id', 'list_id');
    }

    /**
     * Calculate and update campaign metrics
     */
    public function updateMetrics()
    {
        $analytics = $this->analytics;
        
        $this->delivered_count = $analytics->where('event_type', 'delivered')->count();
        $this->opened_count = $analytics->where('event_type', 'opened')->count();
        $this->clicked_count = $analytics->where('event_type', 'clicked')->count();
        $this->unsubscribed_count = $analytics->where('event_type', 'unsubscribed')->count();
        $this->bounced_count = $analytics->where('event_type', 'bounced')->count();
        
        // Calculate rates
        if ($this->total_recipients > 0) {
            $this->open_rate = ($this->opened_count / $this->total_recipients) * 100;
            $this->click_rate = ($this->clicked_count / $this->total_recipients) * 100;
        }
        
        $this->save();
    }

    /**
     * Get formatted metrics for display
     */
    public function getFormattedMetrics()
    {
        return [
            'total_recipients' => number_format($this->total_recipients),
            'delivered_count' => number_format($this->delivered_count),
            'opened_count' => number_format($this->opened_count),
            'clicked_count' => number_format($this->clicked_count),
            'unsubscribed_count' => number_format($this->unsubscribed_count),
            'bounced_count' => number_format($this->bounced_count),
            'open_rate' => number_format($this->open_rate, 2) . '%',
            'click_rate' => number_format($this->click_rate, 2) . '%',
            'delivery_rate' => $this->total_recipients > 0 ? number_format(($this->delivered_count / $this->total_recipients) * 100, 2) . '%' : '0%',
        ];
    }

    /**
     * Check if campaign can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'scheduled']);
    }

    /**
     * Check if campaign can be sent
     */
    public function canBeSent(): bool
    {
        return in_array($this->status, ['draft', 'scheduled']);
    }

    /**
     * Get status color for UI
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => '#6B7280',
            'scheduled' => '#3B82F6',
            'sending' => '#F59E0B',
            'sent' => '#10B981',
            'paused' => '#EF4444',
            'cancelled' => '#EF4444',
            default => '#6B7280'
        };
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by workspace
     */
    public function scopeByWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }
}