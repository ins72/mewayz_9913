<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OnboardingProgress extends Model
{
    protected $table = 'onboarding_progress';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'current_step',
        'completed_steps',
        'total_steps',
        'progress_percentage',
        'started_at',
        'completed_at',
        'completion_time',
        'metadata'
    ];

    protected $casts = [
        'completed_steps' => 'array',
        'progress_percentage' => 'float',
        'completion_time' => 'integer',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Get the user that owns the onboarding progress
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if onboarding is completed
     */
    public function isCompleted()
    {
        return $this->completed_at !== null;
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentage()
    {
        return round($this->progress_percentage, 2);
    }

    /**
     * Get next step
     */
    public function getNextStep()
    {
        if ($this->current_step > $this->total_steps) {
            return null;
        }
        
        return $this->current_step;
    }

    /**
     * Get time spent on onboarding
     */
    public function getTimeSpent()
    {
        if ($this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        
        return $this->started_at->diffInMinutes(now());
    }
}