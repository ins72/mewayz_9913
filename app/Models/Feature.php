<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'goal_key',
        'category',
        'type',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the goal that this feature belongs to.
     */
    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class, 'goal_key', 'key');
    }

    /**
     * Get the subscription plans that include this feature.
     */
    public function subscriptionPlans(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'plan_features', 'feature_key', 'plan_id', 'key')
            ->withPivot(['is_included', 'quota_limit', 'overage_price'])
            ->withTimestamps();
    }

    /**
     * Get the workspaces that have this feature enabled.
     */
    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_features', 'feature_key', 'workspace_id', 'key')
            ->withPivot(['is_enabled', 'quota_limit', 'usage_count', 'last_used_at'])
            ->withTimestamps();
    }

    /**
     * Get the usage records for this feature.
     */
    public function usageRecords(): HasMany
    {
        return $this->hasMany(FeatureUsage::class, 'feature_key', 'key');
    }

    /**
     * Scope to get only active features.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by goal.
     */
    public function scopeForGoal($query, string $goalKey)
    {
        return $query->where('goal_key', $goalKey);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the default features for the system.
     */
    public static function getDefaultFeatures(): array
    {
        return [
            // Instagram Features
            [
                'key' => 'instagram_account_connect',
                'name' => 'Instagram Account Connection',
                'description' => 'Connect Instagram business accounts',
                'goal_key' => 'instagram',
                'category' => 'social_media',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'instagram_post_scheduling',
                'name' => 'Instagram Post Scheduling',
                'description' => 'Schedule Instagram posts in advance',
                'goal_key' => 'instagram',
                'category' => 'social_media',
                'type' => 'quota',
                'is_active' => true,
            ],
            [
                'key' => 'instagram_analytics',
                'name' => 'Instagram Analytics',
                'description' => 'Detailed Instagram performance analytics',
                'goal_key' => 'instagram',
                'category' => 'social_media',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'instagram_auto_hashtags',
                'name' => 'Auto Hashtag Suggestions',
                'description' => 'AI-powered hashtag recommendations',
                'goal_key' => 'instagram',
                'category' => 'social_media',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'instagram_story_scheduling',
                'name' => 'Instagram Story Scheduling',
                'description' => 'Schedule Instagram stories',
                'goal_key' => 'instagram',
                'category' => 'social_media',
                'type' => 'quota',
                'is_active' => true,
            ],

            // Link in Bio Features
            [
                'key' => 'link_bio_pages',
                'name' => 'Bio Pages',
                'description' => 'Create link in bio pages',
                'goal_key' => 'link_bio',
                'category' => 'websites',
                'type' => 'quota',
                'is_active' => true,
            ],
            [
                'key' => 'link_bio_custom_domain',
                'name' => 'Custom Domain',
                'description' => 'Use custom domain for bio pages',
                'goal_key' => 'link_bio',
                'category' => 'websites',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'link_bio_analytics',
                'name' => 'Link Analytics',
                'description' => 'Track link clicks and page views',
                'goal_key' => 'link_bio',
                'category' => 'websites',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'link_bio_custom_themes',
                'name' => 'Custom Themes',
                'description' => 'Advanced theme customization',
                'goal_key' => 'link_bio',
                'category' => 'websites',
                'type' => 'binary',
                'is_active' => true,
            ],

            // Course Features
            [
                'key' => 'course_creation',
                'name' => 'Course Creation',
                'description' => 'Create online courses',
                'goal_key' => 'courses',
                'category' => 'education',
                'type' => 'quota',
                'is_active' => true,
            ],
            [
                'key' => 'course_video_hosting',
                'name' => 'Video Hosting',
                'description' => 'Host course videos',
                'goal_key' => 'courses',
                'category' => 'education',
                'type' => 'quota',
                'is_active' => true,
            ],
            [
                'key' => 'course_student_management',
                'name' => 'Student Management',
                'description' => 'Manage course students',
                'goal_key' => 'courses',
                'category' => 'education',
                'type' => 'quota',
                'is_active' => true,
            ],
            [
                'key' => 'course_certificates',
                'name' => 'Course Certificates',
                'description' => 'Generate completion certificates',
                'goal_key' => 'courses',
                'category' => 'education',
                'type' => 'binary',
                'is_active' => true,
            ],

            // E-commerce Features
            [
                'key' => 'ecommerce_products',
                'name' => 'Product Management',
                'description' => 'Manage products and inventory',
                'goal_key' => 'ecommerce',
                'category' => 'commerce',
                'type' => 'quota',
                'is_active' => true,
            ],
            [
                'key' => 'ecommerce_payment_processing',
                'name' => 'Payment Processing',
                'description' => 'Accept payments via Stripe/PayPal',
                'goal_key' => 'ecommerce',
                'category' => 'commerce',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'ecommerce_inventory',
                'name' => 'Inventory Management',
                'description' => 'Track inventory levels',
                'goal_key' => 'ecommerce',
                'category' => 'commerce',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'ecommerce_multi_currency',
                'name' => 'Multi-Currency Support',
                'description' => 'Accept payments in multiple currencies',
                'goal_key' => 'ecommerce',
                'category' => 'commerce',
                'type' => 'binary',
                'is_active' => true,
            ],

            // CRM Features
            [
                'key' => 'crm_contacts',
                'name' => 'Contact Management',
                'description' => 'Manage customer contacts',
                'goal_key' => 'crm',
                'category' => 'marketing',
                'type' => 'quota',
                'is_active' => true,
            ],
            [
                'key' => 'crm_email_campaigns',
                'name' => 'Email Campaigns',
                'description' => 'Create and send email campaigns',
                'goal_key' => 'crm',
                'category' => 'marketing',
                'type' => 'quota',
                'is_active' => true,
            ],
            [
                'key' => 'crm_automation',
                'name' => 'Email Automation',
                'description' => 'Automated email sequences',
                'goal_key' => 'crm',
                'category' => 'marketing',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'crm_lead_scoring',
                'name' => 'Lead Scoring',
                'description' => 'Automated lead scoring',
                'goal_key' => 'crm',
                'category' => 'marketing',
                'type' => 'binary',
                'is_active' => true,
            ],

            // Website Features
            [
                'key' => 'website_builder',
                'name' => 'Website Builder',
                'description' => 'Drag-and-drop website builder',
                'goal_key' => 'website',
                'category' => 'websites',
                'type' => 'quota',
                'is_active' => true,
            ],
            [
                'key' => 'website_custom_domain',
                'name' => 'Custom Domain',
                'description' => 'Use custom domain for websites',
                'goal_key' => 'website',
                'category' => 'websites',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'website_seo_tools',
                'name' => 'SEO Tools',
                'description' => 'SEO optimization tools',
                'goal_key' => 'website',
                'category' => 'websites',
                'type' => 'binary',
                'is_active' => true,
            ],

            // Analytics Features
            [
                'key' => 'analytics_dashboard',
                'name' => 'Analytics Dashboard',
                'description' => 'Comprehensive analytics dashboard',
                'goal_key' => 'analytics',
                'category' => 'analytics',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'analytics_custom_reports',
                'name' => 'Custom Reports',
                'description' => 'Create custom analytics reports',
                'goal_key' => 'analytics',
                'category' => 'analytics',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'analytics_data_export',
                'name' => 'Data Export',
                'description' => 'Export analytics data',
                'goal_key' => 'analytics',
                'category' => 'analytics',
                'type' => 'binary',
                'is_active' => true,
            ],

            // AI Tools Features
            [
                'key' => 'ai_content_generation',
                'name' => 'AI Content Generation',
                'description' => 'Generate content with AI',
                'goal_key' => 'ai_tools',
                'category' => 'ai',
                'type' => 'quota',
                'is_active' => true,
            ],
            [
                'key' => 'ai_seo_optimization',
                'name' => 'AI SEO Optimization',
                'description' => 'AI-powered SEO suggestions',
                'goal_key' => 'ai_tools',
                'category' => 'ai',
                'type' => 'binary',
                'is_active' => true,
            ],
            [
                'key' => 'ai_image_generation',
                'name' => 'AI Image Generation',
                'description' => 'Generate images with AI',
                'goal_key' => 'ai_tools',
                'category' => 'ai',
                'type' => 'quota',
                'is_active' => true,
            ],
        ];
    }

    /**
     * Check if this is a quota-based feature.
     */
    public function isQuotaBased(): bool
    {
        return $this->type === 'quota';
    }

    /**
     * Check if this is a binary feature.
     */
    public function isBinary(): bool
    {
        return $this->type === 'binary';
    }

    /**
     * Check if this is a tiered feature.
     */
    public function isTiered(): bool
    {
        return $this->type === 'tiered';
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'key';
    }
}