<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_primary',
        'settings',
        'selected_goals',
        'selected_features',
        'team_setup',
        'subscription_plan_id',
        'branding_config',
        'setup_step',
        'setup_completed',
        'setup_completed_at',
    ];
    
    protected $casts = [
        'settings' => 'array',
        'is_primary' => 'boolean',
        'selected_goals' => 'array',
        'selected_features' => 'array',
        'team_setup' => 'array',
        'branding_config' => 'array',
        'setup_completed' => 'boolean',
        'setup_completed_at' => 'datetime',
    ];
    
    /**
     * Get the user that owns the workspace
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan for this workspace
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get features enabled in this workspace
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'workspace_features')
                    ->withPivot('is_enabled', 'configuration', 'enabled_at', 'disabled_at')
                    ->withTimestamps();
    }

    /**
     * Get workspace features pivot records
     */
    public function workspaceFeatures(): HasMany
    {
        return $this->hasMany(WorkspaceFeature::class);
    }

    /**
     * Get enabled features
     */
    public function enabledFeatures(): BelongsToMany
    {
        return $this->features()->wherePivot('is_enabled', true);
    }

    /**
     * Get team invitations
     */
    public function teamInvitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    /**
     * Get pending team invitations
     */
    public function pendingInvitations(): HasMany
    {
        return $this->teamInvitations()->pending();
    }

    /**
     * Get selected goals as models
     */
    public function getSelectedGoalsModels()
    {
        if (empty($this->selected_goals)) {
            return collect([]);
        }

        return WorkspaceGoal::whereIn('slug', $this->selected_goals)->ordered()->get();
    }

    /**
     * Get selected features as models
     */
    public function getSelectedFeaturesModels()
    {
        if (empty($this->selected_features)) {
            return collect([]);
        }

        return Feature::whereIn('id', $this->selected_features)->orderBy('sort_order')->get();
    }

    /**
     * Check if workspace has specific goal
     */
    public function hasGoal(string $goalSlug): bool
    {
        return in_array($goalSlug, $this->selected_goals ?? []);
    }

    /**
     * Check if workspace has specific feature
     */
    public function hasFeature(int $featureId): bool
    {
        return in_array($featureId, $this->selected_features ?? []);
    }

    /**
     * Check if workspace has feature enabled
     */
    public function hasFeatureEnabled(int $featureId): bool
    {
        return $this->workspaceFeatures()
                    ->where('feature_id', $featureId)
                    ->where('is_enabled', true)
                    ->exists();
    }

    /**
     * Enable a feature
     */
    public function enableFeature(int $featureId, array $configuration = [])
    {
        $workspaceFeature = $this->workspaceFeatures()
                                ->where('feature_id', $featureId)
                                ->first();

        if ($workspaceFeature) {
            $workspaceFeature->enable();
            if (!empty($configuration)) {
                $workspaceFeature->updateConfiguration($configuration);
            }
        } else {
            $this->workspaceFeatures()->create([
                'feature_id' => $featureId,
                'is_enabled' => true,
                'configuration' => $configuration,
                'enabled_at' => now(),
            ]);
        }
    }

    /**
     * Disable a feature
     */
    public function disableFeature(int $featureId)
    {
        $workspaceFeature = $this->workspaceFeatures()
                                ->where('feature_id', $featureId)
                                ->first();

        if ($workspaceFeature) {
            $workspaceFeature->disable();
        }
    }

    /**
     * Get workspace feature configuration
     */
    public function getFeatureConfiguration(int $featureId, string $key = null, $default = null)
    {
        $workspaceFeature = $this->workspaceFeatures()
                                ->where('feature_id', $featureId)
                                ->first();

        if (!$workspaceFeature) {
            return $default;
        }

        if ($key === null) {
            return $workspaceFeature->configuration;
        }

        return $workspaceFeature->getConfiguration($key, $default);
    }

    /**
     * Set workspace feature configuration
     */
    public function setFeatureConfiguration(int $featureId, string $key, $value)
    {
        $workspaceFeature = $this->workspaceFeatures()
                                ->where('feature_id', $featureId)
                                ->first();

        if ($workspaceFeature) {
            $workspaceFeature->setConfiguration($key, $value);
        }
    }

    /**
     * Check if setup is completed
     */
    public function isSetupCompleted(): bool
    {
        return $this->setup_completed;
    }

    /**
     * Mark setup as completed
     */
    public function markSetupCompleted()
    {
        $this->update([
            'setup_step' => 'complete',
            'setup_completed' => true,
            'setup_completed_at' => now(),
        ]);
    }

    /**
     * Get current setup step
     */
    public function getCurrentSetupStep(): string
    {
        return $this->setup_step;
    }

    /**
     * Move to next setup step
     */
    public function moveToNextSetupStep()
    {
        $steps = ['goals', 'features', 'team', 'subscription', 'branding', 'complete'];
        $currentIndex = array_search($this->setup_step, $steps);
        
        if ($currentIndex !== false && $currentIndex < count($steps) - 1) {
            $nextStep = $steps[$currentIndex + 1];
            $this->update(['setup_step' => $nextStep]);
            
            if ($nextStep === 'complete') {
                $this->markSetupCompleted();
            }
        }
    }

    /**
     * Get setup progress percentage
     */
    public function getSetupProgress(): int
    {
        $steps = ['goals', 'features', 'team', 'subscription', 'branding', 'complete'];
        $currentIndex = array_search($this->setup_step, $steps);
        
        if ($currentIndex === false) {
            return 0;
        }
        
        return (int) (($currentIndex + 1) / count($steps) * 100);
    }

    /**
     * Calculate monthly cost
     */
    public function calculateMonthlyCost(): float
    {
        if (!$this->subscriptionPlan) {
            return 0.0;
        }

        return $this->subscriptionPlan->calculateMonthlyPrice($this->selected_features ?? []);
    }

    /**
     * Calculate yearly cost
     */
    public function calculateYearlyCost(): float
    {
        if (!$this->subscriptionPlan) {
            return 0.0;
        }

        return $this->subscriptionPlan->calculateYearlyPrice($this->selected_features ?? []);
    }

    /**
     * Get branding configuration
     */
    public function getBrandingConfig(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->branding_config;
        }

        return data_get($this->branding_config, $key, $default);
    }

    /**
     * Set branding configuration
     */
    public function setBrandingConfig(string $key, $value)
    {
        $config = $this->branding_config ?? [];
        data_set($config, $key, $value);
        $this->update(['branding_config' => $config]);
    }

    /**
     * Get team setup configuration
     */
    public function getTeamSetup(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->team_setup;
        }

        return data_get($this->team_setup, $key, $default);
    }

    /**
     * Set team setup configuration
     */
    public function setTeamSetup(string $key, $value)
    {
        $config = $this->team_setup ?? [];
        data_set($config, $key, $value);
        $this->update(['team_setup' => $config]);
    }

    /**
     * Scope to get workspaces with completed setup
     */
    public function scopeSetupCompleted($query)
    {
        return $query->where('setup_completed', true);
    }

    /**
     * Scope to get workspaces with incomplete setup
     */
    public function scopeSetupIncomplete($query)
    {
        return $query->where('setup_completed', false);
    }

    /**
     * Scope to get primary workspaces
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
