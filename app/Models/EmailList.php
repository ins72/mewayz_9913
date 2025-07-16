<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class EmailList extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'name',
        'description',
        'subscriber_count',
        'tags',
        'segmentation_rules',
        'is_active'
    ];

    protected $casts = [
        'tags' => 'array',
        'segmentation_rules' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the workspace that owns the list
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the user that created the list
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscribers in this list
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(EmailSubscriber::class, 'email_list_subscribers', 'list_id', 'subscriber_id')
            ->withPivot(['subscribed_at', 'unsubscribed_at'])
            ->withTimestamps();
    }

    /**
     * Get active subscribers only
     */
    public function activeSubscribers(): BelongsToMany
    {
        return $this->subscribers()->wherePivot('unsubscribed_at', null);
    }

    /**
     * Add subscriber to list
     */
    public function addSubscriber(EmailSubscriber $subscriber)
    {
        if (!$this->subscribers()->where('subscriber_id', $subscriber->id)->exists()) {
            $this->subscribers()->attach($subscriber->id, [
                'subscribed_at' => now()
            ]);
            $this->updateSubscriberCount();
        }
    }

    /**
     * Remove subscriber from list
     */
    public function removeSubscriber(EmailSubscriber $subscriber)
    {
        $this->subscribers()->updateExistingPivot($subscriber->id, [
            'unsubscribed_at' => now()
        ]);
        $this->updateSubscriberCount();
    }

    /**
     * Update subscriber count
     */
    public function updateSubscriberCount()
    {
        $this->update([
            'subscriber_count' => $this->activeSubscribers()->count()
        ]);
    }

    /**
     * Get growth metrics for the list
     */
    public function getGrowthMetrics($period = 30)
    {
        $startDate = now()->subDays($period);
        
        $newSubscribers = $this->subscribers()
            ->wherePivot('subscribed_at', '>=', $startDate)
            ->count();
            
        $unsubscribers = $this->subscribers()
            ->wherePivot('unsubscribed_at', '>=', $startDate)
            ->count();
            
        return [
            'new_subscribers' => $newSubscribers,
            'unsubscribers' => $unsubscribers,
            'net_growth' => $newSubscribers - $unsubscribers,
            'growth_rate' => $this->subscriber_count > 0 ? 
                (($newSubscribers - $unsubscribers) / $this->subscriber_count) * 100 : 0
        ];
    }

    /**
     * Apply segmentation rules to get filtered subscribers
     */
    public function getSegmentedSubscribers()
    {
        $query = $this->activeSubscribers();
        
        if ($this->segmentation_rules) {
            foreach ($this->segmentation_rules as $rule) {
                switch ($rule['type']) {
                    case 'tag':
                        $query->whereJsonContains('tags', $rule['value']);
                        break;
                    case 'location':
                        $query->where('location', $rule['value']);
                        break;
                    case 'subscribed_after':
                        $query->wherePivot('subscribed_at', '>=', $rule['value']);
                        break;
                    case 'subscribed_before':
                        $query->wherePivot('subscribed_at', '<=', $rule['value']);
                        break;
                }
            }
        }
        
        return $query->get();
    }

    /**
     * Get list performance metrics
     */
    public function getPerformanceMetrics()
    {
        $campaigns = EmailCampaign::whereJsonContains('recipient_lists', $this->id)->get();
        
        return [
            'total_campaigns' => $campaigns->count(),
            'avg_open_rate' => $campaigns->avg('open_rate') ?? 0,
            'avg_click_rate' => $campaigns->avg('click_rate') ?? 0,
            'total_sent' => $campaigns->sum('total_recipients'),
            'last_campaign' => $campaigns->sortByDesc('created_at')->first()
        ];
    }

    /**
     * Check if list can be deleted
     */
    public function canBeDeleted(): bool
    {
        return EmailCampaign::whereJsonContains('recipient_lists', $this->id)->count() === 0;
    }

    /**
     * Scope for filtering by workspace
     */
    public function scopeByWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Scope for active lists
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for filtering by tags
     */
    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }
}