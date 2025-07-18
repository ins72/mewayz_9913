<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Workspace;
use App\Models\BioSite;
use App\Models\SocialMediaAccount;
use App\Models\Audience;
use App\Models\EmailCampaign;
use App\Models\Course;
use App\Models\Product;
use App\Models\AnalyticsEvent;
use App\Models\Activity;
use Carbon\Carbon;

class UnifiedDataController extends Controller
{
    /**
     * Get unified customer journey across all touchpoints
     */
    public function getUnifiedCustomerJourney(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:audiences,id',
            'time_range' => 'nullable|string|in:7d,30d,90d,1y,all',
            'include_touchpoints' => 'boolean',
            'include_predictions' => 'boolean',
            'include_recommendations' => 'boolean'
        ]);

        try {
            $user = $request->user();
            $customer = Audience::where('id', $request->customer_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            $timeRange = $this->parseDateRange($request->time_range ?? '30d');
            
            // Get all touchpoints across platforms
            $touchpoints = $this->getCustomerTouchpoints($customer, $timeRange);
            
            // Analyze engagement patterns
            $engagementPatterns = $this->analyzeEngagementPatterns($customer, $touchpoints);
            
            // Calculate customer lifetime value
            $lifetimeValue = $this->calculateLifetimeValue($customer, $touchpoints);
            
            // Identify conversion paths
            $conversionPaths = $this->identifyConversionPaths($customer, $touchpoints);
            
            // Generate customer insights
            $customerInsights = $this->generateCustomerInsights($customer, $touchpoints, $engagementPatterns);

            // Cross-platform attribution
            $attribution = $this->performCrossPlatformAttribution($customer, $touchpoints);

            // Customer scoring
            $customerScore = $this->calculateUnifiedCustomerScore($customer, $touchpoints, $engagementPatterns);

            $journey = [
                'customer_overview' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'acquisition_date' => $customer->created_at,
                    'last_activity' => $this->getLastActivityDate($customer, $touchpoints),
                    'customer_stage' => $this->determineCustomerStage($customer, $touchpoints),
                    'lifetime_value' => $lifetimeValue,
                    'engagement_score' => $customerScore['engagement'],
                    'loyalty_score' => $customerScore['loyalty'],
                    'influence_score' => $customerScore['influence']
                ],
                'touchpoint_timeline' => $touchpoints,
                'engagement_patterns' => $engagementPatterns,
                'conversion_paths' => $conversionPaths,
                'attribution_analysis' => $attribution,
                'customer_insights' => $customerInsights,
                'journey_visualization' => $this->generateJourneyVisualization($touchpoints),
                'cross_platform_data' => $this->getCrossPlatformData($customer)
            ];

            // Add predictions if requested
            if ($request->include_predictions) {
                $journey['predictions'] = $this->generateCustomerPredictions($customer, $touchpoints, $engagementPatterns);
            }

            // Add recommendations if requested
            if ($request->include_recommendations) {
                $journey['recommendations'] = $this->generateCustomerRecommendations($customer, $touchpoints, $customerInsights);
            }

            return response()->json([
                'success' => true,
                'data' => $journey
            ]);

        } catch (\Exception $e) {
            Log::error('Unified customer journey failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate customer journey: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cross-platform analytics dashboard
     */
    public function getCrossPlatformAnalytics(Request $request)
    {
        $request->validate([
            'time_range' => 'nullable|string|in:7d,30d,90d,1y',
            'platforms' => 'nullable|array',
            'platforms.*' => 'string|in:instagram,bio_sites,email,courses,ecommerce,crm',
            'metrics' => 'nullable|array',
            'metrics.*' => 'string|in:engagement,conversion,revenue,growth,retention',
            'include_forecasting' => 'boolean',
            'include_anomaly_detection' => 'boolean'
        ]);

        try {
            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $timeRange = $this->parseDateRange($request->time_range ?? '30d');
            $platforms = $request->platforms ?? ['instagram', 'bio_sites', 'email', 'courses', 'ecommerce', 'crm'];
            
            // Get unified metrics across all platforms
            $unifiedMetrics = $this->getUnifiedMetrics($user, $workspace, $timeRange, $platforms);
            
            // Cross-platform funnel analysis
            $funnelAnalysis = $this->analyzeCrossPlatformFunnel($user, $workspace, $timeRange);
            
            // Attribution modeling
            $attributionModeling = $this->performAdvancedAttributionModeling($user, $workspace, $timeRange);
            
            // Customer flow analysis
            $customerFlowAnalysis = $this->analyzeCustomerFlow($user, $workspace, $timeRange);
            
            // Revenue attribution
            $revenueAttribution = $this->analyzeRevenueAttribution($user, $workspace, $timeRange);
            
            // Engagement synchronization
            $engagementSync = $this->analyzeEngagementSynchronization($user, $workspace, $timeRange);

            // Platform performance comparison
            $platformComparison = $this->comparePlatformPerformance($user, $workspace, $timeRange, $platforms);

            // Unified customer segments
            $customerSegments = $this->generateUnifiedCustomerSegments($user, $workspace, $timeRange);

            $analytics = [
                'overview' => [
                    'total_touchpoints' => $unifiedMetrics['total_touchpoints'],
                    'unified_conversion_rate' => $unifiedMetrics['unified_conversion_rate'],
                    'customer_lifetime_value' => $unifiedMetrics['customer_lifetime_value'],
                    'cross_platform_engagement' => $unifiedMetrics['cross_platform_engagement'],
                    'revenue_attribution' => $revenueAttribution['total_attributed_revenue'],
                    'active_customers' => $unifiedMetrics['active_customers'],
                    'platform_synergy_score' => $this->calculatePlatformSynergyScore($unifiedMetrics)
                ],
                'platform_metrics' => $unifiedMetrics['platform_breakdown'],
                'funnel_analysis' => $funnelAnalysis,
                'attribution_modeling' => $attributionModeling,
                'customer_flow' => $customerFlowAnalysis,
                'revenue_attribution' => $revenueAttribution,
                'engagement_synchronization' => $engagementSync,
                'platform_comparison' => $platformComparison,
                'customer_segments' => $customerSegments,
                'integration_health' => $this->assessIntegrationHealth($user, $workspace),
                'optimization_opportunities' => $this->identifyOptimizationOpportunities($unifiedMetrics, $funnelAnalysis)
            ];

            // Add forecasting if requested
            if ($request->include_forecasting) {
                $analytics['forecasting'] = $this->generateCrossPlatformForecasting($unifiedMetrics, $timeRange);
            }

            // Add anomaly detection if requested
            if ($request->include_anomaly_detection) {
                $analytics['anomaly_detection'] = $this->detectAnomalies($unifiedMetrics, $timeRange);
            }

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error('Cross-platform analytics failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate cross-platform analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get intelligent automation recommendations
     */
    public function getIntelligentAutomationRecommendations(Request $request)
    {
        $request->validate([
            'automation_type' => 'required|string|in:marketing,sales,customer_service,content,engagement',
            'complexity_level' => 'nullable|string|in:basic,intermediate,advanced',
            'business_goals' => 'nullable|array',
            'business_goals.*' => 'string|in:lead_generation,customer_retention,revenue_growth,engagement,brand_awareness',
            'current_tools' => 'nullable|array',
            'current_tools.*' => 'string',
            'budget_range' => 'nullable|string|in:low,medium,high',
            'time_investment' => 'nullable|string|in:minimal,moderate,extensive'
        ]);

        try {
            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $automationType = $request->automation_type;
            $complexityLevel = $request->complexity_level ?? 'intermediate';
            $businessGoals = $request->business_goals ?? [];
            
            // Analyze current automation state
            $currentAutomationState = $this->analyzeCurrentAutomationState($user, $workspace);
            
            // Generate recommendations based on data analysis
            $recommendations = $this->generateAutomationRecommendations($user, $workspace, $automationType, $complexityLevel, $businessGoals);
            
            // Create automation workflows
            $workflowSuggestions = $this->createAutomationWorkflows($user, $workspace, $recommendations);
            
            // Calculate ROI projections
            $roiProjections = $this->calculateAutomationROI($user, $workspace, $recommendations);
            
            // Implementation roadmap
            $implementationRoadmap = $this->createImplementationRoadmap($recommendations, $complexityLevel);
            
            // Integration opportunities
            $integrationOpportunities = $this->identifyIntegrationOpportunities($user, $workspace, $automationType);

            $automationInsights = [
                'automation_readiness' => [
                    'current_automation_score' => $currentAutomationState['score'],
                    'automation_gaps' => $currentAutomationState['gaps'],
                    'data_quality_score' => $currentAutomationState['data_quality'],
                    'integration_readiness' => $currentAutomationState['integration_readiness'],
                    'team_readiness' => $currentAutomationState['team_readiness']
                ],
                'recommendations' => $recommendations,
                'workflow_suggestions' => $workflowSuggestions,
                'roi_projections' => $roiProjections,
                'implementation_roadmap' => $implementationRoadmap,
                'integration_opportunities' => $integrationOpportunities,
                'automation_templates' => $this->getAutomationTemplates($automationType, $complexityLevel),
                'success_metrics' => $this->defineSuccessMetrics($automationType, $businessGoals),
                'monitoring_dashboard' => $this->createMonitoringDashboard($recommendations)
            ];

            return response()->json([
                'success' => true,
                'data' => $automationInsights
            ]);

        } catch (\Exception $e) {
            Log::error('Intelligent automation recommendations failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate automation recommendations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute cross-platform campaign
     */
    public function executeCrossPlatformCampaign(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_type' => 'required|string|in:product_launch,brand_awareness,lead_generation,customer_retention,event_promotion',
            'target_audience' => 'required|array',
            'target_audience.demographics' => 'required|array',
            'target_audience.interests' => 'required|array',
            'target_audience.behaviors' => 'required|array',
            'platforms' => 'required|array|min:2',
            'platforms.*' => 'string|in:instagram,bio_sites,email,courses,ecommerce,crm',
            'budget' => 'required|numeric|min:0',
            'duration' => 'required|array',
            'duration.start_date' => 'required|date|after:today',
            'duration.end_date' => 'required|date|after:duration.start_date',
            'objectives' => 'required|array',
            'objectives.*' => 'string|in:awareness,engagement,conversion,retention,revenue',
            'content_strategy' => 'required|array',
            'automation_rules' => 'nullable|array',
            'success_metrics' => 'required|array'
        ]);

        try {
            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            // Create campaign record
            $campaign = $this->createCampaignRecord($user, $workspace, $request->all());
            
            // Generate platform-specific content
            $contentStrategy = $this->generatePlatformContent($campaign, $request->platforms, $request->content_strategy);
            
            // Set up automation workflows
            $automationWorkflows = $this->setupCampaignAutomation($campaign, $request->automation_rules ?? []);
            
            // Configure tracking and analytics
            $trackingSetup = $this->setupCampaignTracking($campaign, $request->success_metrics);
            
            // Initialize campaign execution
            $executionPlan = $this->initializeCampaignExecution($campaign, $contentStrategy, $automationWorkflows);
            
            // Set up monitoring and alerts
            $monitoringSetup = $this->setupCampaignMonitoring($campaign, $request->success_metrics);

            // Cross-platform synchronization
            $synchronization = $this->synchronizeCampaignAcrossPlatforms($campaign, $request->platforms);

            $campaignResult = [
                'campaign_id' => $campaign['id'],
                'campaign_name' => $campaign['name'],
                'status' => 'initialized',
                'execution_plan' => $executionPlan,
                'content_strategy' => $contentStrategy,
                'automation_workflows' => $automationWorkflows,
                'tracking_setup' => $trackingSetup,
                'monitoring_setup' => $monitoringSetup,
                'synchronization' => $synchronization,
                'predicted_outcomes' => $this->predictCampaignOutcomes($campaign, $request->platforms),
                'optimization_suggestions' => $this->generateCampaignOptimizations($campaign),
                'next_steps' => $this->generateNextSteps($campaign, $executionPlan)
            ];

            return response()->json([
                'success' => true,
                'message' => 'Cross-platform campaign initialized successfully',
                'data' => $campaignResult
            ]);

        } catch (\Exception $e) {
            Log::error('Cross-platform campaign execution failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute cross-platform campaign: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods for unified data processing

    private function getCustomerTouchpoints($customer, $timeRange)
    {
        $touchpoints = [];
        
        // Instagram interactions
        $instagramData = $this->getInstagramTouchpoints($customer, $timeRange);
        $touchpoints = array_merge($touchpoints, $instagramData);
        
        // Bio site visits
        $bioSiteData = $this->getBioSiteTouchpoints($customer, $timeRange);
        $touchpoints = array_merge($touchpoints, $bioSiteData);
        
        // Email interactions
        $emailData = $this->getEmailTouchpoints($customer, $timeRange);
        $touchpoints = array_merge($touchpoints, $emailData);
        
        // Course activities
        $courseData = $this->getCourseTouchpoints($customer, $timeRange);
        $touchpoints = array_merge($touchpoints, $courseData);
        
        // E-commerce interactions
        $ecommerceData = $this->getEcommerceTouchpoints($customer, $timeRange);
        $touchpoints = array_merge($touchpoints, $ecommerceData);
        
        // CRM activities
        $crmData = $this->getCRMTouchpoints($customer, $timeRange);
        $touchpoints = array_merge($touchpoints, $crmData);
        
        // Sort by timestamp
        usort($touchpoints, function($a, $b) {
            return strtotime($a['timestamp']) - strtotime($b['timestamp']);
        });
        
        return $touchpoints;
    }

    private function getInstagramTouchpoints($customer, $timeRange)
    {
        // Mock Instagram touchpoints
        return [
            [
                'platform' => 'instagram',
                'type' => 'profile_visit',
                'timestamp' => now()->subDays(5)->toISOString(),
                'data' => ['source' => 'hashtag_search', 'duration' => 45],
                'engagement_score' => 3.2
            ],
            [
                'platform' => 'instagram',
                'type' => 'post_engagement',
                'timestamp' => now()->subDays(3)->toISOString(),
                'data' => ['action' => 'like', 'post_id' => 'post_123'],
                'engagement_score' => 4.1
            ]
        ];
    }

    private function getBioSiteTouchpoints($customer, $timeRange)
    {
        // Mock bio site touchpoints
        return [
            [
                'platform' => 'bio_sites',
                'type' => 'page_visit',
                'timestamp' => now()->subDays(4)->toISOString(),
                'data' => ['page' => 'main_bio', 'duration' => 120, 'links_clicked' => 2],
                'engagement_score' => 5.8
            ],
            [
                'platform' => 'bio_sites',
                'type' => 'link_click',
                'timestamp' => now()->subDays(2)->toISOString(),
                'data' => ['link' => 'contact_form', 'conversion' => true],
                'engagement_score' => 8.5
            ]
        ];
    }

    private function getEmailTouchpoints($customer, $timeRange)
    {
        // Mock email touchpoints
        return [
            [
                'platform' => 'email',
                'type' => 'email_open',
                'timestamp' => now()->subDays(6)->toISOString(),
                'data' => ['campaign' => 'welcome_series', 'subject' => 'Welcome to Mewayz'],
                'engagement_score' => 4.7
            ],
            [
                'platform' => 'email',
                'type' => 'email_click',
                'timestamp' => now()->subDays(1)->toISOString(),
                'data' => ['campaign' => 'product_launch', 'link' => 'learn_more'],
                'engagement_score' => 7.2
            ]
        ];
    }

    private function getCourseTouchpoints($customer, $timeRange)
    {
        // Mock course touchpoints
        return [
            [
                'platform' => 'courses',
                'type' => 'course_enrollment',
                'timestamp' => now()->subDays(7)->toISOString(),
                'data' => ['course' => 'Digital Marketing Mastery', 'payment' => 99.99],
                'engagement_score' => 9.5
            ],
            [
                'platform' => 'courses',
                'type' => 'lesson_completion',
                'timestamp' => now()->subDays(1)->toISOString(),
                'data' => ['course' => 'Digital Marketing Mastery', 'lesson' => 'Social Media Strategy'],
                'engagement_score' => 8.8
            ]
        ];
    }

    private function getEcommerceTouchpoints($customer, $timeRange)
    {
        // Mock e-commerce touchpoints
        return [
            [
                'platform' => 'ecommerce',
                'type' => 'product_view',
                'timestamp' => now()->subDays(8)->toISOString(),
                'data' => ['product' => 'Professional Plan', 'time_spent' => 180],
                'engagement_score' => 6.3
            ],
            [
                'platform' => 'ecommerce',
                'type' => 'purchase',
                'timestamp' => now()->subDays(1)->toISOString(),
                'data' => ['product' => 'Professional Plan', 'amount' => 49.99],
                'engagement_score' => 10.0
            ]
        ];
    }

    private function getCRMTouchpoints($customer, $timeRange)
    {
        // Mock CRM touchpoints
        return [
            [
                'platform' => 'crm',
                'type' => 'lead_score_update',
                'timestamp' => now()->subDays(3)->toISOString(),
                'data' => ['old_score' => 65, 'new_score' => 78, 'reason' => 'increased_engagement'],
                'engagement_score' => 7.8
            ],
            [
                'platform' => 'crm',
                'type' => 'sales_call',
                'timestamp' => now()->subDays(2)->toISOString(),
                'data' => ['duration' => 30, 'outcome' => 'interested', 'next_action' => 'demo_scheduled'],
                'engagement_score' => 9.2
            ]
        ];
    }

    private function parseDateRange($range)
    {
        $ranges = [
            '7d' => [now()->subDays(7), now()],
            '30d' => [now()->subDays(30), now()],
            '90d' => [now()->subDays(90), now()],
            '1y' => [now()->subYear(), now()],
            'all' => [now()->subYears(5), now()],
        ];

        return $ranges[$range] ?? [now()->subDays(30), now()];
    }

    private function analyzeEngagementPatterns($customer, $touchpoints)
    {
        $patterns = [];
        
        // Platform preference analysis
        $platformCounts = [];
        foreach ($touchpoints as $touchpoint) {
            $platform = $touchpoint['platform'];
            $platformCounts[$platform] = ($platformCounts[$platform] ?? 0) + 1;
        }
        
        $patterns['platform_preference'] = $platformCounts;
        
        // Time-based patterns
        $hourlyEngagement = [];
        $dailyEngagement = [];
        
        foreach ($touchpoints as $touchpoint) {
            $timestamp = Carbon::parse($touchpoint['timestamp']);
            $hour = $timestamp->hour;
            $day = $timestamp->dayOfWeek;
            
            $hourlyEngagement[$hour] = ($hourlyEngagement[$hour] ?? 0) + $touchpoint['engagement_score'];
            $dailyEngagement[$day] = ($dailyEngagement[$day] ?? 0) + $touchpoint['engagement_score'];
        }
        
        $patterns['hourly_engagement'] = $hourlyEngagement;
        $patterns['daily_engagement'] = $dailyEngagement;
        
        // Engagement trend
        $engagementTrend = [];
        foreach ($touchpoints as $touchpoint) {
            $date = Carbon::parse($touchpoint['timestamp'])->toDateString();
            $engagementTrend[$date] = ($engagementTrend[$date] ?? 0) + $touchpoint['engagement_score'];
        }
        
        $patterns['engagement_trend'] = $engagementTrend;
        
        return $patterns;
    }

    private function calculateLifetimeValue($customer, $touchpoints)
    {
        $totalValue = 0;
        
        foreach ($touchpoints as $touchpoint) {
            if (isset($touchpoint['data']['amount'])) {
                $totalValue += $touchpoint['data']['amount'];
            } elseif (isset($touchpoint['data']['payment'])) {
                $totalValue += $touchpoint['data']['payment'];
            }
        }
        
        return [
            'current_value' => $totalValue,
            'predicted_value' => $totalValue * 1.5, // Simple prediction
            'value_tier' => $totalValue > 500 ? 'high' : ($totalValue > 100 ? 'medium' : 'low')
        ];
    }

    private function identifyConversionPaths($customer, $touchpoints)
    {
        $paths = [];
        $currentPath = [];
        
        foreach ($touchpoints as $touchpoint) {
            $currentPath[] = [
                'platform' => $touchpoint['platform'],
                'type' => $touchpoint['type'],
                'timestamp' => $touchpoint['timestamp']
            ];
            
            // Check if this is a conversion event
            if (in_array($touchpoint['type'], ['purchase', 'course_enrollment', 'lead_conversion'])) {
                $paths[] = [
                    'path' => $currentPath,
                    'conversion_value' => $touchpoint['data']['amount'] ?? $touchpoint['data']['payment'] ?? 0,
                    'conversion_type' => $touchpoint['type']
                ];
                $currentPath = []; // Reset for next path
            }
        }
        
        return $paths;
    }

    private function generateCustomerInsights($customer, $touchpoints, $engagementPatterns)
    {
        $insights = [];
        
        // Most active platform
        $mostActivePlatform = array_keys($engagementPatterns['platform_preference'], max($engagementPatterns['platform_preference']))[0];
        $insights[] = "Most active on {$mostActivePlatform} with high engagement";
        
        // Best engagement time
        $bestHour = array_keys($engagementPatterns['hourly_engagement'], max($engagementPatterns['hourly_engagement']))[0];
        $insights[] = "Best engagement time is {$bestHour}:00";
        
        // Engagement trend
        $engagementTrend = array_values($engagementPatterns['engagement_trend']);
        if (count($engagementTrend) > 1) {
            $lastValue = end($engagementTrend);
            $previousValue = $engagementTrend[count($engagementTrend) - 2];
            $trend = $lastValue > $previousValue ? 'increasing' : 'decreasing';
            $insights[] = "Engagement trend is {$trend}";
        }
        
        // Purchase behavior
        $hasPurchases = false;
        foreach ($touchpoints as $touchpoint) {
            if (in_array($touchpoint['type'], ['purchase', 'course_enrollment'])) {
                $hasPurchases = true;
                break;
            }
        }
        
        if ($hasPurchases) {
            $insights[] = "High-value customer with purchase history";
        } else {
            $insights[] = "Engaged prospect ready for conversion";
        }
        
        return $insights;
    }

    private function performCrossPlatformAttribution($customer, $touchpoints)
    {
        $attribution = [
            'first_touch' => null,
            'last_touch' => null,
            'multi_touch' => [],
            'conversion_assists' => []
        ];
        
        if (!empty($touchpoints)) {
            $attribution['first_touch'] = $touchpoints[0]['platform'];
            $attribution['last_touch'] = end($touchpoints)['platform'];
            
            // Calculate multi-touch attribution
            $touchpointCount = count($touchpoints);
            foreach ($touchpoints as $index => $touchpoint) {
                $weight = 1 / $touchpointCount; // Equal weight for simplicity
                $attribution['multi_touch'][$touchpoint['platform']] = 
                    ($attribution['multi_touch'][$touchpoint['platform']] ?? 0) + $weight;
            }
            
            // Identify conversion assists
            $conversionIndices = [];
            foreach ($touchpoints as $index => $touchpoint) {
                if (in_array($touchpoint['type'], ['purchase', 'course_enrollment', 'lead_conversion'])) {
                    $conversionIndices[] = $index;
                }
            }
            
            foreach ($conversionIndices as $conversionIndex) {
                $assists = [];
                for ($i = 0; $i < $conversionIndex; $i++) {
                    $assists[] = $touchpoints[$i]['platform'];
                }
                $attribution['conversion_assists'][] = $assists;
            }
        }
        
        return $attribution;
    }

    private function calculateUnifiedCustomerScore($customer, $touchpoints, $engagementPatterns)
    {
        $engagementScore = 0;
        $loyaltyScore = 0;
        $influenceScore = 0;
        
        // Calculate engagement score
        foreach ($touchpoints as $touchpoint) {
            $engagementScore += $touchpoint['engagement_score'];
        }
        $engagementScore = min(100, $engagementScore / count($touchpoints) * 10);
        
        // Calculate loyalty score based on repeat interactions
        $platformCounts = $engagementPatterns['platform_preference'];
        $loyaltyScore = min(100, max($platformCounts) * 5);
        
        // Calculate influence score (simplified)
        $influenceScore = rand(40, 90); // Mock influence score
        
        return [
            'engagement' => round($engagementScore, 1),
            'loyalty' => round($loyaltyScore, 1),
            'influence' => $influenceScore,
            'overall' => round(($engagementScore + $loyaltyScore + $influenceScore) / 3, 1)
        ];
    }

    private function getLastActivityDate($customer, $touchpoints)
    {
        if (empty($touchpoints)) {
            return null;
        }
        
        $lastTouchpoint = end($touchpoints);
        return $lastTouchpoint['timestamp'];
    }

    private function determineCustomerStage($customer, $touchpoints)
    {
        $stages = ['awareness', 'consideration', 'purchase', 'retention', 'advocacy'];
        
        // Check for purchase activity
        $hasPurchased = false;
        foreach ($touchpoints as $touchpoint) {
            if (in_array($touchpoint['type'], ['purchase', 'course_enrollment'])) {
                $hasPurchased = true;
                break;
            }
        }
        
        if ($hasPurchased) {
            // Check for recent activity to determine retention vs advocacy
            $recentActivity = false;
            foreach ($touchpoints as $touchpoint) {
                if (Carbon::parse($touchpoint['timestamp'])->diffInDays(now()) < 30) {
                    $recentActivity = true;
                    break;
                }
            }
            
            return $recentActivity ? 'retention' : 'advocacy';
        }
        
        // Check engagement level to determine awareness vs consideration
        $totalEngagement = array_sum(array_column($touchpoints, 'engagement_score'));
        return $totalEngagement > 50 ? 'consideration' : 'awareness';
    }

    private function generateJourneyVisualization($touchpoints)
    {
        $visualization = [];
        
        foreach ($touchpoints as $touchpoint) {
            $visualization[] = [
                'platform' => $touchpoint['platform'],
                'type' => $touchpoint['type'],
                'timestamp' => $touchpoint['timestamp'],
                'engagement_score' => $touchpoint['engagement_score'],
                'icon' => $this->getPlatformIcon($touchpoint['platform']),
                'color' => $this->getPlatformColor($touchpoint['platform'])
            ];
        }
        
        return $visualization;
    }

    private function getCrossPlatformData($customer)
    {
        return [
            'instagram' => [
                'connected' => true,
                'followers' => 1250,
                'engagement_rate' => 4.2,
                'last_post' => '2 days ago'
            ],
            'bio_sites' => [
                'sites_count' => 2,
                'total_visits' => 1890,
                'conversion_rate' => 12.5,
                'most_popular' => 'Main Business Profile'
            ],
            'email' => [
                'subscriber' => true,
                'open_rate' => 35.6,
                'click_rate' => 8.9,
                'last_campaign' => 'Product Launch'
            ],
            'courses' => [
                'enrolled_courses' => 2,
                'completion_rate' => 78.5,
                'certificates_earned' => 1,
                'total_spent' => 199.98
            ],
            'ecommerce' => [
                'orders_count' => 3,
                'total_spent' => 149.97,
                'average_order_value' => 49.99,
                'last_purchase' => '1 week ago'
            ],
            'crm' => [
                'lead_score' => 78,
                'contact_frequency' => 'bi-weekly',
                'stage' => 'qualified_lead',
                'last_interaction' => '3 days ago'
            ]
        ];
    }

    private function getPlatformIcon($platform)
    {
        $icons = [
            'instagram' => 'instagram',
            'bio_sites' => 'link',
            'email' => 'mail',
            'courses' => 'book',
            'ecommerce' => 'shopping-bag',
            'crm' => 'users'
        ];
        
        return $icons[$platform] ?? 'activity';
    }

    private function getPlatformColor($platform)
    {
        $colors = [
            'instagram' => '#E1306C',
            'bio_sites' => '#10B981',
            'email' => '#3B82F6',
            'courses' => '#F59E0B',
            'ecommerce' => '#8B5CF6',
            'crm' => '#EF4444'
        ];
        
        return $colors[$platform] ?? '#6B7280';
    }

    // Additional helper methods would continue here...
    // For brevity, I'll add placeholder methods that would be fully implemented
    
    private function generateCustomerPredictions($customer, $touchpoints, $engagementPatterns)
    {
        // Implementation for customer predictions
        return [
            'next_action_probability' => 0.78,
            'churn_risk' => 0.23,
            'upsell_opportunity' => 0.65,
            'advocacy_potential' => 0.54
        ];
    }

    private function generateCustomerRecommendations($customer, $touchpoints, $customerInsights)
    {
        // Implementation for customer recommendations
        return [
            'next_best_action' => 'Send personalized product recommendation',
            'optimal_contact_time' => '2:00 PM - 4:00 PM',
            'preferred_channel' => 'email',
            'content_preferences' => ['educational', 'product_demos']
        ];
    }

    // Additional methods would be implemented here for full functionality...
}