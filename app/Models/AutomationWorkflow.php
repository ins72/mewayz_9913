<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'name',
        'description',
        'trigger_type',
        'trigger_config',
        'actions',
        'conditions',
        'is_active',
        'execution_count',
        'last_executed_at',
        'next_execution_at'
    ];

    protected $casts = [
        'trigger_config' => 'json',
        'actions' => 'json',
        'conditions' => 'json',
        'is_active' => 'boolean',
        'last_executed_at' => 'datetime',
        'next_execution_at' => 'datetime'
    ];

    /**
     * Get the user that owns the workflow
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace that owns the workflow
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Scope for active workflows
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for workflows by trigger type
     */
    public function scopeByTriggerType($query, $type)
    {
        return $query->where('trigger_type', $type);
    }

    /**
     * Scope for workflows by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for workflows ready for execution
     */
    public function scopeReadyForExecution($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->where('next_execution_at', '<=', now())
                          ->orWhereNull('next_execution_at');
                    });
    }

    /**
     * Execute the workflow
     */
    public function execute()
    {
        if (!$this->is_active) {
            return false;
        }

        // Check conditions
        if (!$this->checkConditions()) {
            return false;
        }

        // Execute actions
        $results = $this->executeActions();

        // Update execution statistics
        $this->increment('execution_count');
        $this->update([
            'last_executed_at' => now(),
            'next_execution_at' => $this->calculateNextExecution()
        ]);

        return $results;
    }

    /**
     * Check if workflow conditions are met
     */
    private function checkConditions()
    {
        $conditions = $this->conditions ?? [];
        
        foreach ($conditions as $condition) {
            if (!$this->evaluateCondition($condition)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Evaluate a single condition
     */
    private function evaluateCondition($condition)
    {
        switch ($condition['type']) {
            case 'time':
                return $this->checkTimeCondition($condition);
            case 'user_action':
                return $this->checkUserActionCondition($condition);
            case 'data_value':
                return $this->checkDataValueCondition($condition);
            default:
                return true;
        }
    }

    /**
     * Execute workflow actions
     */
    private function executeActions()
    {
        $results = [];
        
        foreach ($this->actions as $action) {
            try {
                $result = $this->executeAction($action);
                $results[] = [
                    'action' => $action['type'],
                    'status' => 'success',
                    'result' => $result
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'action' => $action['type'],
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Execute a single action
     */
    private function executeAction($action)
    {
        switch ($action['type']) {
            case 'generate_content':
                return $this->generateContentAction($action);
            case 'send_email':
                return $this->sendEmailAction($action);
            case 'post_social':
                return $this->postSocialAction($action);
            case 'create_bio_site':
                return $this->createBioSiteAction($action);
            case 'update_profile':
                return $this->updateProfileAction($action);
            default:
                throw new \Exception('Unknown action type: ' . $action['type']);
        }
    }

    /**
     * Calculate next execution time
     */
    private function calculateNextExecution()
    {
        if ($this->trigger_type !== 'schedule') {
            return null;
        }

        $schedule = $this->trigger_config['schedule'] ?? 'daily';
        
        switch ($schedule) {
            case 'hourly':
                return now()->addHour();
            case 'daily':
                return now()->addDay();
            case 'weekly':
                return now()->addWeek();
            case 'monthly':
                return now()->addMonth();
            default:
                return null;
        }
    }

    /**
     * Toggle workflow active status
     */
    public function toggleActive()
    {
        $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Pause workflow
     */
    public function pause()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Resume workflow
     */
    public function resume()
    {
        $this->update([
            'is_active' => true,
            'next_execution_at' => $this->calculateNextExecution()
        ]);
    }

    /**
     * Get workflow statistics
     */
    public function getStats()
    {
        return [
            'execution_count' => $this->execution_count,
            'last_executed_at' => $this->last_executed_at,
            'next_execution_at' => $this->next_execution_at,
            'is_active' => $this->is_active,
            'trigger_type' => $this->trigger_type,
            'actions_count' => count($this->actions),
            'conditions_count' => count($this->conditions ?? [])
        ];
    }

    /**
     * Duplicate workflow
     */
    public function duplicate()
    {
        $newWorkflow = $this->replicate();
        $newWorkflow->name = $this->name . ' (Copy)';
        $newWorkflow->is_active = false;
        $newWorkflow->execution_count = 0;
        $newWorkflow->last_executed_at = null;
        $newWorkflow->next_execution_at = null;
        $newWorkflow->save();
        
        return $newWorkflow;
    }

    /**
     * Get workflow templates
     */
    public static function getTemplates()
    {
        return [
            [
                'name' => 'Daily Content Generator',
                'description' => 'Automatically generate daily social media content',
                'trigger_type' => 'schedule',
                'trigger_config' => ['schedule' => 'daily', 'time' => '09:00'],
                'actions' => [
                    ['type' => 'generate_content', 'content_type' => 'social_post', 'platform' => 'instagram'],
                    ['type' => 'post_social', 'platform' => 'instagram', 'auto_publish' => false]
                ]
            ],
            [
                'name' => 'Welcome Email Sequence',
                'description' => 'Send welcome emails to new users',
                'trigger_type' => 'event',
                'trigger_config' => ['event' => 'user_registered'],
                'actions' => [
                    ['type' => 'send_email', 'template' => 'welcome', 'delay' => 0],
                    ['type' => 'send_email', 'template' => 'getting_started', 'delay' => 86400]
                ]
            ],
            [
                'name' => 'Bio Site Updater',
                'description' => 'Update bio site with latest content',
                'trigger_type' => 'schedule',
                'trigger_config' => ['schedule' => 'weekly', 'day' => 'monday'],
                'actions' => [
                    ['type' => 'generate_content', 'content_type' => 'bio'],
                    ['type' => 'update_profile', 'field' => 'bio']
                ]
            ]
        ];
    }

    /**
     * Action implementations
     */
    private function generateContentAction($action)
    {
        // Integration with AI content generation
        return 'Content generated: ' . $action['content_type'];
    }

    private function sendEmailAction($action)
    {
        // Integration with email service
        return 'Email sent: ' . $action['template'];
    }

    private function postSocialAction($action)
    {
        // Integration with social media posting
        return 'Social media post created on: ' . $action['platform'];
    }

    private function createBioSiteAction($action)
    {
        // Integration with bio site creation
        return 'Bio site created';
    }

    private function updateProfileAction($action)
    {
        // Integration with profile updating
        return 'Profile updated: ' . $action['field'];
    }

    /**
     * Condition check implementations
     */
    private function checkTimeCondition($condition)
    {
        $targetTime = $condition['time'] ?? '09:00';
        $currentTime = now()->format('H:i');
        
        return $currentTime === $targetTime;
    }

    private function checkUserActionCondition($condition)
    {
        // Check if user has performed specific action
        return true; // Placeholder
    }

    private function checkDataValueCondition($condition)
    {
        // Check if data value meets condition
        return true; // Placeholder
    }
}