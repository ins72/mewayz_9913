<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGeneratedContent extends Model
{
    use HasFactory;

    protected $table = 'ai_generated_content';
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'workspace_id',
        'type',
        'prompt',
        'generated_content',
        'model_used',
        'tokens_used',
        'parameters',
        'quality_score',
        'is_used',
        'performance_metrics'
    ];

    protected $casts = [
        'generated_content' => 'json',
        'parameters' => 'json',
        'performance_metrics' => 'json',
        'is_used' => 'boolean',
        'quality_score' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the AI content
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace that owns the AI content
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Scope for content by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for content by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent content
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for used content
     */
    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    /**
     * Mark content as used
     */
    public function markAsUsed()
    {
        $this->update(['is_used' => true]);
    }

    /**
     * Get formatted content based on type
     */
    public function getFormattedContent()
    {
        $content = $this->generated_content;
        
        if (is_string($content)) {
            return $content;
        }

        if (is_array($content)) {
            switch ($this->type) {
                case 'email':
                    return $content['subject'] . "\n\n" . $content['body'];
                case 'image':
                    return $content['image_url'];
                case 'social_post':
                    return $content;
                default:
                    return json_encode($content);
            }
        }

        return '';
    }

    /**
     * Get content statistics
     */
    public function getStats()
    {
        $metrics = $this->performance_metrics ?? [];
        
        return [
            'type' => $this->type,
            'tokens_used' => $this->tokens_used,
            'model_used' => $this->model_used,
            'quality_score' => $this->quality_score,
            'is_used' => $this->is_used,
            'created_at' => $this->created_at,
            'performance' => $metrics
        ];
    }

    /**
     * Update performance metrics
     */
    public function updatePerformance($metrics)
    {
        $this->update([
            'performance_metrics' => array_merge($this->performance_metrics ?? [], $metrics)
        ]);
    }

    /**
     * Get content by model
     */
    public function scopeByModel($query, $model)
    {
        return $query->where('model_used', $model);
    }

    /**
     * Get total tokens used by user
     */
    public static function getTotalTokensUsed($userId, $period = 'month')
    {
        $query = self::where('user_id', $userId);
        
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }
        
        return $query->sum('tokens_used');
    }

    /**
     * Get content generation trends
     */
    public static function getGenerationTrends($userId, $days = 30)
    {
        $trends = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = self::where('user_id', $userId)
                        ->whereDate('created_at', $date)
                        ->count();
            
            $trends[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count
            ];
        }
        
        return $trends;
    }

    /**
     * Get most used content types
     */
    public static function getPopularTypes($userId, $limit = 5)
    {
        return self::where('user_id', $userId)
                  ->groupBy('type')
                  ->selectRaw('type, COUNT(*) as count')
                  ->orderBy('count', 'desc')
                  ->limit($limit)
                  ->get();
    }
}