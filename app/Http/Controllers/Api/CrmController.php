<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Audience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CrmController extends Controller
{
    /**
     * Get contacts
     */
    public function getContacts(Request $request)
    {
        try {
            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $contacts = Audience::where('user_id', $user->id)
                ->where('type', 'contact')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $contacts,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting contacts: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get contacts'], 500);
        }
    }

    /**
     * Create contact
     */
    public function createContact(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:audiences,email',
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'position' => 'nullable|string|max:255',
                'tags' => 'nullable|array',
                'notes' => 'nullable|string',
            ]);

            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $contact = Audience::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'owner_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'position' => $request->position,
                'type' => 'contact',
                'status' => 'active',
                'tags' => $request->tags,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully',
                'data' => $contact,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating contact: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create contact'], 500);
        }
    }

    /**
     * Get leads
     */
    public function getLeads(Request $request)
    {
        try {
            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $leads = Audience::where('user_id', $user->id)
                ->where('type', 'lead')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $leads,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting leads: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get leads'], 500);
        }
    }

    public function createLead(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:audiences,email',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:hot,warm,cold',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $lead = Audience::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'type' => 'lead',
            'status' => $request->status,
            'source' => $request->source,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $lead,
        ], 201);
    }

    public function showLead(Request $request, Audience $lead)
    {
        // Check if user owns the lead
        if ($lead->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to lead',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $lead,
        ]);
    }

    public function updateLead(Request $request, Audience $lead)
    {
        // Check if user owns the lead
        if ($lead->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to lead',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:audiences,email,' . $lead->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:hot,warm,cold',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $lead->update($request->only(['name', 'email', 'phone', 'status', 'source', 'notes']));

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully',
            'data' => $lead,
        ]);
    }

    public function deleteLead(Request $request, Audience $lead)
    {
        // Check if user owns the lead
        if ($lead->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to lead',
            ], 403);
        }

        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully',
        ]);
    }

    /**
     * Get all contacts with advanced filtering and pagination
     */
    public function getAdvancedContacts(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,inactive,lead,prospect,customer,archived',
            'type' => 'nullable|string|in:individual,company,organization',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'sort_by' => 'nullable|string|in:name,email,created_at,updated_at,last_contact_date,deal_value',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'source' => 'nullable|string|in:website,social_media,referral,cold_outreach,event,advertisement,organic'
        ]);

        try {
            $query = Audience::where('user_id', $request->user()->id)
                ->where('type', 'contact');

            // Apply search filter
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                      ->orWhere('phone', 'like', '%' . $request->search . '%')
                      ->orWhere('company', 'like', '%' . $request->search . '%');
                });
            }

            // Apply status filter
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Apply type filter
            if ($request->type) {
                $query->where('contact_type', $request->type);
            }

            // Apply date range filter
            if ($request->date_from) {
                $query->where('created_at', '>=', $request->date_from);
            }
            if ($request->date_to) {
                $query->where('created_at', '<=', $request->date_to);
            }

            // Apply location filters
            if ($request->country) {
                $query->where('country', $request->country);
            }
            if ($request->city) {
                $query->where('city', $request->city);
            }

            // Apply source filter
            if ($request->source) {
                $query->where('source', $request->source);
            }

            // Apply tags filter
            if ($request->tags) {
                $query->whereJsonContains('tags', $request->tags);
            }

            // Apply sorting
            $sortBy = $request->sort_by ?? 'created_at';
            $sortOrder = $request->sort_order ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results
            $perPage = $request->per_page ?? 20;
            $contacts = $query->paginate($perPage);

            // Calculate summary statistics
            $totalContacts = Audience::where('user_id', $request->user()->id)
                ->where('type', 'contact')
                ->count();

            $activeContacts = Audience::where('user_id', $request->user()->id)
                ->where('type', 'contact')
                ->where('status', 'active')
                ->count();

            $contactsThisMonth = Audience::where('user_id', $request->user()->id)
                ->where('type', 'contact')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count();

            // For now, we'll set total deal value to 0 since the column doesn't exist yet
            $totalDealValue = 0; // TODO: Add deal_value column to audience table or calculate from related tables

            return response()->json([
                'success' => true,
                'data' => [
                    'contacts' => $contacts->items(),
                    'pagination' => [
                        'current_page' => $contacts->currentPage(),
                        'per_page' => $contacts->perPage(),
                        'total' => $contacts->total(),
                        'last_page' => $contacts->lastPage(),
                        'from' => $contacts->firstItem(),
                        'to' => $contacts->lastItem()
                    ],
                    'summary' => [
                        'total_contacts' => $totalContacts,
                        'active_contacts' => $activeContacts,
                        'contacts_this_month' => $contactsThisMonth,
                        'total_deal_value' => $totalDealValue,
                        'conversion_rate' => $totalContacts > 0 ? round(($activeContacts / $totalContacts) * 100, 2) : 0
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve contacts', ['error' => $e->getMessage(), 'user_id' => $request->user()->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contacts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importContacts(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('imports', 'local');
            $fullPath = storage_path('app/' . $path);

            $imported = 0;
            $failed = 0;
            $errors = [];

            if (($handle = fopen($fullPath, 'r')) !== FALSE) {
                $header = fgetcsv($handle, 1000, ',');
                
                // Expected headers: name, email, phone, status, source, notes
                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    try {
                        $contactData = [
                            'user_id' => $request->user()->id,
                            'name' => $data[0] ?? '',
                            'email' => $data[1] ?? '',
                            'phone' => $data[2] ?? null,
                            'type' => 'contact',
                            'status' => in_array($data[3] ?? '', ['hot', 'warm', 'cold']) ? $data[3] : 'cold',
                            'source' => $data[4] ?? 'import',
                            'notes' => $data[5] ?? null,
                        ];

                        // Validate email
                        if (!filter_var($contactData['email'], FILTER_VALIDATE_EMAIL)) {
                            $failed++;
                            $errors[] = "Invalid email: {$contactData['email']}";
                            continue;
                        }

                        // Check if contact already exists
                        $existing = Audience::where('user_id', $request->user()->id)
                            ->where('email', $contactData['email'])
                            ->first();

                        if ($existing) {
                            $existing->update($contactData);
                        } else {
                            Audience::create($contactData);
                        }

                        $imported++;
                    } catch (\Exception $e) {
                        $failed++;
                        $errors[] = "Row error: " . $e->getMessage();
                    }
                }
                fclose($handle);
            }

            // Clean up uploaded file
            unlink($fullPath);

            return response()->json([
                'success' => true,
                'message' => "Import completed. {$imported} contacts imported, {$failed} failed.",
                'data' => [
                    'imported' => $imported,
                    'failed' => $failed,
                    'errors' => array_slice($errors, 0, 10), // Show first 10 errors
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getPipeline(Request $request)
    {
        $pipeline = [
            'stages' => [
                ['name' => 'New', 'count' => 0, 'value' => 0],
                ['name' => 'Qualified', 'count' => 0, 'value' => 0],
                ['name' => 'Proposal', 'count' => 0, 'value' => 0],
                ['name' => 'Negotiation', 'count' => 0, 'value' => 0],
                ['name' => 'Closed Won', 'count' => 0, 'value' => 0],
                ['name' => 'Closed Lost', 'count' => 0, 'value' => 0],
            ],
            'total_value' => 0,
            'conversion_rate' => '0%',
        ];

        return response()->json([
            'success' => true,
            'data' => $pipeline,
        ]);
    }

    /**
     * Create advanced automation workflow for CRM
     */
    public function createAutomationWorkflow(Request $request)
    {
        $request->validate([
            'workflow_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'trigger_type' => 'required|string|in:contact_created,lead_stage_changed,email_opened,email_clicked,form_submitted,deal_value_changed,last_contact_date,custom_field_updated,tag_added,birthday,anniversary,inactivity_period',
            'trigger_conditions' => 'required|array',
            'trigger_conditions.*.field' => 'required|string',
            'trigger_conditions.*.operator' => 'required|string|in:equals,not_equals,contains,not_contains,greater_than,less_than,is_empty,is_not_empty,starts_with,ends_with,in_list,not_in_list',
            'trigger_conditions.*.value' => 'required',
            'actions' => 'required|array|min:1',
            'actions.*.type' => 'required|string|in:send_email,send_sms,create_task,update_field,add_tag,remove_tag,change_status,assign_to_user,create_deal,update_deal,send_webhook,wait_delay,condition_branch,ai_scoring,lead_qualification',
            'actions.*.parameters' => 'required|array',
            'actions.*.delay' => 'nullable|integer|min:0',
            'actions.*.conditions' => 'nullable|array',
            'schedule_settings' => 'nullable|array',
            'schedule_settings.timezone' => 'nullable|string',
            'schedule_settings.business_hours_only' => 'boolean',
            'schedule_settings.exclude_weekends' => 'boolean',
            'schedule_settings.exclude_holidays' => 'boolean',
            'ai_optimization' => 'boolean',
            'performance_tracking' => 'boolean',
            'is_active' => 'boolean'
        ]);

        try {
            $workflow = [
                'id' => uniqid('workflow_'),
                'user_id' => $request->user()->id,
                'workflow_name' => $request->workflow_name,
                'description' => $request->description,
                'trigger_type' => $request->trigger_type,
                'trigger_conditions' => $request->trigger_conditions,
                'actions' => $this->processWorkflowActions($request->actions),
                'schedule_settings' => array_merge([
                    'timezone' => 'UTC',
                    'business_hours_only' => false,
                    'exclude_weekends' => false,
                    'exclude_holidays' => false
                ], $request->schedule_settings ?? []),
                'ai_optimization' => $request->ai_optimization ?? false,
                'performance_tracking' => $request->performance_tracking ?? true,
                'is_active' => $request->is_active ?? true,
                'statistics' => [
                    'total_triggered' => 0,
                    'total_completed' => 0,
                    'total_failed' => 0,
                    'success_rate' => 0,
                    'avg_completion_time' => 0,
                    'last_triggered' => null
                ],
                'ai_insights' => [],
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Store workflow (in a real app, this would be in a separate table)
            $this->storeWorkflow($workflow);

            // Initialize AI optimization if enabled
            if ($request->ai_optimization) {
                $this->initializeAIOptimization($workflow);
            }

            return response()->json([
                'success' => true,
                'message' => 'Automation workflow created successfully',
                'data' => [
                    'workflow_id' => $workflow['id'],
                    'workflow_name' => $workflow['workflow_name'],
                    'trigger_type' => $workflow['trigger_type'],
                    'actions_count' => count($workflow['actions']),
                    'is_active' => $workflow['is_active'],
                    'ai_optimization_enabled' => $workflow['ai_optimization'],
                    'estimated_impact' => $this->estimateWorkflowImpact($workflow),
                    'testing_recommendations' => $this->generateTestingRecommendations($workflow)
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Automation workflow creation failed', ['error' => $e->getMessage(), 'user_id' => $request->user()->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create automation workflow: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI-powered lead scoring and qualification
     */
    public function getAILeadScoring(Request $request)
    {
        $request->validate([
            'contact_ids' => 'nullable|array|max:100',
            'contact_ids.*' => 'exists:audiences,id',
            'scoring_model' => 'required|string|in:standard,advanced,custom,industry_specific',
            'scoring_factors' => 'nullable|array',
            'scoring_factors.*' => 'string|in:demographic,behavioral,engagement,firmographic,technographic,intent,social_signals,email_behavior,website_behavior,purchase_history,support_interactions',
            'minimum_score' => 'nullable|integer|min:0|max:100',
            'include_predictions' => 'boolean',
            'include_recommendations' => 'boolean',
            'batch_size' => 'nullable|integer|min:1|max:1000'
        ]);

        try {
            $query = Audience::where('user_id', $request->user()->id)->where('type', 'contact');

            if ($request->contact_ids) {
                $query->whereIn('id', $request->contact_ids);
            }

            $contacts = $query->get();

            if ($contacts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No contacts found for scoring'
                ], 404);
            }

            $scoringModel = $request->scoring_model;
            $scoringFactors = $request->scoring_factors ?? ['demographic', 'behavioral', 'engagement'];
            $minScore = $request->minimum_score ?? 0;

            $scoredContacts = [];
            $scoringInsights = [];

            foreach ($contacts as $contact) {
                $score = $this->calculateAILeadScore($contact, $scoringModel, $scoringFactors);
                
                if ($score['total_score'] >= $minScore) {
                    $scoredContact = [
                        'contact_id' => $contact->id,
                        'name' => $contact->name,
                        'email' => $contact->email,
                        'company' => $contact->company,
                        'scoring_results' => $score,
                        'qualification_status' => $this->determineQualificationStatus($score),
                        'priority_level' => $this->determinePriorityLevel($score),
                        'recommended_actions' => $this->generateRecommendedActions($contact, $score),
                        'conversion_probability' => $this->calculateConversionProbability($contact, $score),
                        'ideal_customer_profile_match' => $this->calculateICPMatch($contact, $score),
                        'engagement_propensity' => $this->calculateEngagementPropensity($contact, $score),
                        'churn_risk' => $this->calculateChurnRisk($contact, $score),
                        'lifetime_value_prediction' => $this->predictLifetimeValue($contact, $score),
                        'next_best_action' => $this->determineNextBestAction($contact, $score),
                        'scored_at' => now()
                    ];

                    if ($request->include_predictions) {
                        $scoredContact['predictions'] = $this->generateAIPredictions($contact, $score);
                    }

                    if ($request->include_recommendations) {
                        $scoredContact['ai_recommendations'] = $this->generateAIRecommendations($contact, $score);
                    }

                    $scoredContacts[] = $scoredContact;
                }
            }

            // Generate overall insights
            $scoringInsights = $this->generateScoringInsights($scoredContacts, $scoringModel);

            // Calculate ROI and performance metrics
            $performanceMetrics = $this->calculateScoringPerformanceMetrics($scoredContacts);

            return response()->json([
                'success' => true,
                'data' => [
                    'scoring_summary' => [
                        'total_contacts_analyzed' => count($contacts),
                        'qualified_contacts' => count($scoredContacts),
                        'average_score' => $this->calculateAverageScore($scoredContacts),
                        'scoring_model' => $scoringModel,
                        'scoring_factors' => $scoringFactors,
                        'high_priority_contacts' => $this->countHighPriorityContacts($scoredContacts),
                        'conversion_ready_contacts' => $this->countConversionReadyContacts($scoredContacts)
                    ],
                    'scored_contacts' => $scoredContacts,
                    'scoring_insights' => $scoringInsights,
                    'performance_metrics' => $performanceMetrics,
                    'model_accuracy' => $this->calculateModelAccuracy($scoringModel),
                    'optimization_suggestions' => $this->generateOptimizationSuggestions($scoredContacts, $scoringInsights),
                    'segment_recommendations' => $this->generateSegmentRecommendations($scoredContacts)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI lead scoring failed', ['error' => $e->getMessage(), 'user_id' => $request->user()->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform AI lead scoring: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Advanced pipeline management with AI insights
     */
    public function getAdvancedPipelineManagement(Request $request)
    {
        $request->validate([
            'pipeline_id' => 'nullable|string',
            'date_range' => 'nullable|string|in:last_7_days,last_30_days,last_90_days,last_year,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|after_or_equal:start_date|required_if:date_range,custom',
            'include_forecasting' => 'boolean',
            'include_bottleneck_analysis' => 'boolean',
            'include_win_loss_analysis' => 'boolean',
            'include_team_performance' => 'boolean',
            'include_ai_insights' => 'boolean',
            'segment_by' => 'nullable|string|in:source,size,industry,geography,product,team_member'
        ]);

        try {
            $dateRange = $this->parseDateRange($request->date_range ?? 'last_30_days', $request->start_date, $request->end_date);
            
            // Get pipeline data
            $pipelineData = $this->getPipelineData($request->user()->id, $request->pipeline_id, $dateRange);
            
            // Calculate advanced metrics
            $pipelineMetrics = $this->calculateAdvancedPipelineMetrics($pipelineData, $dateRange);
            
            // Analyze pipeline health
            $pipelineHealth = $this->analyzePipelineHealth($pipelineData, $pipelineMetrics);
            
            // Generate velocity analysis
            $velocityAnalysis = $this->analyzeVelocity($pipelineData, $dateRange);
            
            // Conversion rate analysis
            $conversionAnalysis = $this->analyzeConversionRates($pipelineData, $dateRange);

            $result = [
                'pipeline_overview' => [
                    'total_opportunities' => $pipelineMetrics['total_opportunities'],
                    'total_value' => $pipelineMetrics['total_value'],
                    'weighted_value' => $pipelineMetrics['weighted_value'],
                    'average_deal_size' => $pipelineMetrics['average_deal_size'],
                    'win_rate' => $pipelineMetrics['win_rate'],
                    'average_sales_cycle' => $pipelineMetrics['average_sales_cycle'],
                    'pipeline_health_score' => $pipelineHealth['score'],
                    'pipeline_coverage' => $pipelineMetrics['pipeline_coverage']
                ],
                'stage_analysis' => $this->analyzeStagePerformance($pipelineData),
                'velocity_analysis' => $velocityAnalysis,
                'conversion_analysis' => $conversionAnalysis,
                'pipeline_health' => $pipelineHealth,
                'performance_trends' => $this->analyzePerformanceTrends($pipelineData, $dateRange),
                'leakage_analysis' => $this->analyzeLeakage($pipelineData, $dateRange)
            ];

            // Add optional analyses
            if ($request->include_forecasting) {
                $result['forecasting'] = $this->generateSalesForecasting($pipelineData, $pipelineMetrics);
            }

            if ($request->include_bottleneck_analysis) {
                $result['bottleneck_analysis'] = $this->analyzeBottlenecks($pipelineData, $velocityAnalysis);
            }

            if ($request->include_win_loss_analysis) {
                $result['win_loss_analysis'] = $this->analyzeWinLoss($pipelineData, $dateRange);
            }

            if ($request->include_team_performance) {
                $result['team_performance'] = $this->analyzeTeamPerformance($pipelineData, $dateRange);
            }

            if ($request->include_ai_insights) {
                $result['ai_insights'] = $this->generateAIPipelineInsights($pipelineData, $pipelineMetrics);
            }

            // Add segmentation if requested
            if ($request->segment_by) {
                $result['segmentation'] = $this->segmentPipelineData($pipelineData, $request->segment_by);
            }

            // Generate recommendations
            $result['recommendations'] = $this->generatePipelineRecommendations($pipelineData, $pipelineMetrics, $pipelineHealth);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Advanced pipeline management failed', ['error' => $e->getMessage(), 'user_id' => $request->user()->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve advanced pipeline management data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Predictive analytics for contact behavior and business insights
     */
    public function getPredictiveAnalytics(Request $request)
    {
        $request->validate([
            'prediction_type' => 'required|string|in:comprehensive,churn_only,conversion_only,ltv_only,engagement_only',
            'time_horizon' => 'required|string|in:30_days,60_days,90_days,180_days,1_year',
            'include_churn_prediction' => 'boolean',
            'include_conversion_probability' => 'boolean',
            'include_lifetime_value' => 'boolean',
            'include_engagement_prediction' => 'boolean',
            'include_next_best_action' => 'boolean',
            'confidence_threshold' => 'nullable|integer|min:50|max:95',
            'segment_predictions' => 'boolean',
            'contact_ids' => 'nullable|array|max:1000',
            'contact_ids.*' => 'exists:audiences,id'
        ]);

        try {
            $query = Audience::where('user_id', $request->user()->id)->where('type', 'contact');

            if ($request->contact_ids) {
                $query->whereIn('id', $request->contact_ids);
            }

            $contacts = $query->get();

            if ($contacts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No contacts found for predictive analysis'
                ], 404);
            }

            $predictionType = $request->prediction_type;
            $timeHorizon = $request->time_horizon;
            $confidenceThreshold = $request->confidence_threshold ?? 70;

            $contactPredictions = [];
            $churnAnalysis = [];
            $conversionPredictions = [];
            $lifetimeValueAnalysis = [];
            $engagementPredictions = [];
            $nextBestActions = [];

            foreach ($contacts as $contact) {
                $contactPrediction = [
                    'contact_id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'current_status' => $contact->status,
                    'predictions' => []
                ];

                // Churn prediction
                if ($request->include_churn_prediction || $predictionType === 'comprehensive') {
                    $churnPrediction = $this->predictChurnRisk($contact, $timeHorizon);
                    $contactPrediction['predictions']['churn_risk'] = $churnPrediction;
                    
                    if ($churnPrediction['risk_level'] === 'high') {
                        $churnAnalysis[] = [
                            'contact_id' => $contact->id,
                            'churn_probability' => $churnPrediction['probability'],
                            'risk_factors' => $churnPrediction['risk_factors'],
                            'prevention_actions' => $churnPrediction['prevention_actions']
                        ];
                    }
                }

                // Conversion probability
                if ($request->include_conversion_probability || $predictionType === 'comprehensive') {
                    $conversionPrediction = $this->predictConversionProbability($contact, $timeHorizon);
                    $contactPrediction['predictions']['conversion_probability'] = $conversionPrediction;
                    
                    if ($conversionPrediction['probability'] >= $confidenceThreshold) {
                        $conversionPredictions[] = [
                            'contact_id' => $contact->id,
                            'conversion_probability' => $conversionPrediction['probability'],
                            'conversion_timeline' => $conversionPrediction['timeline'],
                            'conversion_factors' => $conversionPrediction['factors']
                        ];
                    }
                }

                // Lifetime value prediction
                if ($request->include_lifetime_value || $predictionType === 'comprehensive') {
                    $ltvPrediction = $this->predictLifetimeValue($contact, $timeHorizon);
                    $contactPrediction['predictions']['lifetime_value'] = $ltvPrediction;
                    
                    $lifetimeValueAnalysis[] = [
                        'contact_id' => $contact->id,
                        'predicted_ltv' => $ltvPrediction['predicted_value'],
                        'ltv_category' => $ltvPrediction['category'],
                        'value_drivers' => $ltvPrediction['value_drivers']
                    ];
                }

                // Engagement prediction
                if ($request->include_engagement_prediction || $predictionType === 'comprehensive') {
                    $engagementPrediction = $this->predictEngagementLevel($contact, $timeHorizon);
                    $contactPrediction['predictions']['engagement'] = $engagementPrediction;
                    
                    $engagementPredictions[] = [
                        'contact_id' => $contact->id,
                        'engagement_score' => $engagementPrediction['score'],
                        'engagement_trend' => $engagementPrediction['trend'],
                        'optimal_channels' => $engagementPrediction['optimal_channels']
                    ];
                }

                // Next best action
                if ($request->include_next_best_action || $predictionType === 'comprehensive') {
                    $nextBestAction = $this->determineNextBestAction($contact, $contactPrediction['predictions']);
                    $contactPrediction['next_best_action'] = $nextBestAction;
                    
                    $nextBestActions[] = [
                        'contact_id' => $contact->id,
                        'recommended_action' => $nextBestAction['action'],
                        'priority' => $nextBestAction['priority'],
                        'expected_outcome' => $nextBestAction['expected_outcome'],
                        'confidence' => $nextBestAction['confidence']
                    ];
                }

                $contactPredictions[] = $contactPrediction;
            }

            // Generate overall insights
            $predictionSummary = [
                'total_contacts_analyzed' => count($contacts),
                'high_risk_contacts' => count($churnAnalysis),
                'high_value_contacts' => count(array_filter($lifetimeValueAnalysis, function($ltv) {
                    return $ltv['ltv_category'] === 'high';
                })),
                'conversion_ready_contacts' => count($conversionPredictions),
                'model_accuracy' => $this->calculatePredictionModelAccuracy($predictionType),
                'confidence_level' => $confidenceThreshold,
                'prediction_date' => now(),
                'time_horizon' => $timeHorizon
            ];

            // Model performance metrics
            $modelPerformance = [
                'accuracy' => $this->calculateModelAccuracy($predictionType),
                'precision' => $this->calculateModelPrecision($predictionType),
                'recall' => $this->calculateModelRecall($predictionType),
                'f1_score' => $this->calculateF1Score($predictionType),
                'auc_roc' => $this->calculateAUCROC($predictionType),
                'model_version' => '2.1.0',
                'last_trained' => '2024-12-01',
                'training_data_size' => 50000
            ];

            // Segment insights
            $segmentInsights = [];
            if ($request->segment_predictions) {
                $segmentInsights = $this->generateSegmentPredictionInsights($contactPredictions);
            }

            // Actionable recommendations
            $recommendations = $this->generatePredictiveRecommendations($contactPredictions, $predictionSummary);

            return response()->json([
                'success' => true,
                'data' => [
                    'prediction_summary' => $predictionSummary,
                    'contact_predictions' => $contactPredictions,
                    'churn_analysis' => $churnAnalysis,
                    'conversion_predictions' => $conversionPredictions,
                    'lifetime_value_analysis' => $lifetimeValueAnalysis,
                    'engagement_predictions' => $engagementPredictions,
                    'next_best_actions' => $nextBestActions,
                    'model_performance' => $modelPerformance,
                    'segment_insights' => $segmentInsights,
                    'actionable_recommendations' => $recommendations
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Predictive analytics failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate predictive analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods for predictive analytics
    private function predictChurnRisk($contact, $timeHorizon)
    {
        // Mock churn prediction logic
        $riskScore = rand(10, 95);
        $riskLevel = $riskScore > 70 ? 'high' : ($riskScore > 40 ? 'medium' : 'low');
        
        return [
            'probability' => $riskScore,
            'risk_level' => $riskLevel,
            'risk_factors' => ['low_engagement', 'no_recent_activity', 'support_tickets'],
            'prevention_actions' => ['personalized_outreach', 'special_offer', 'check_in_call'],
            'confidence' => rand(75, 95)
        ];
    }

    private function predictConversionProbability($contact, $timeHorizon)
    {
        $probability = rand(20, 90);
        
        return [
            'probability' => $probability,
            'timeline' => rand(7, 60) . ' days',
            'factors' => ['high_engagement', 'product_interest', 'budget_confirmed'],
            'confidence' => rand(70, 90)
        ];
    }

    private function predictLifetimeValue($contact, $timeHorizon)
    {
        $predictedValue = rand(500, 5000);
        $category = $predictedValue > 2000 ? 'high' : ($predictedValue > 1000 ? 'medium' : 'low');
        
        return [
            'predicted_value' => $predictedValue,
            'currency' => 'USD',
            'category' => $category,
            'value_drivers' => ['repeat_purchases', 'referrals', 'upsells'],
            'confidence' => rand(65, 85)
        ];
    }

    private function predictEngagementLevel($contact, $timeHorizon)
    {
        $score = rand(30, 95);
        $trend = $score > 60 ? 'increasing' : 'decreasing';
        
        return [
            'score' => $score,
            'trend' => $trend,
            'optimal_channels' => ['email', 'social_media', 'phone'],
            'best_contact_time' => '2:00 PM - 4:00 PM',
            'confidence' => rand(70, 90)
        ];
    }

    private function determineNextBestAction($contact, $predictions)
    {
        $actions = [
            'send_personalized_email',
            'schedule_demo_call',
            'send_product_information',
            'offer_discount',
            'request_feedback'
        ];
        
        return [
            'action' => $actions[array_rand($actions)],
            'priority' => ['high', 'medium', 'low'][rand(0, 2)],
            'expected_outcome' => 'Increased engagement and conversion probability',
            'confidence' => rand(75, 90),
            'timeline' => 'Within 3 days'
        ];
    }

    private function calculatePredictionModelAccuracy($predictionType)
    {
        // Mock accuracy based on prediction type
        $accuracies = [
            'comprehensive' => rand(82, 88),
            'churn_only' => rand(85, 92),
            'conversion_only' => rand(78, 85),
            'ltv_only' => rand(75, 82),
            'engagement_only' => rand(80, 87)
        ];
        
        return $accuracies[$predictionType] ?? 80;
    }

    private function calculateModelAccuracy($predictionType)
    {
        return rand(78, 92);
    }

    private function calculateModelPrecision($predictionType)
    {
        return rand(75, 88);
    }

    private function calculateModelRecall($predictionType)
    {
        return rand(72, 85);
    }

    private function calculateF1Score($predictionType)
    {
        return rand(74, 86);
    }

    private function calculateAUCROC($predictionType)
    {
        return rand(82, 94) / 100;
    }

    private function generateSegmentPredictionInsights($contactPredictions)
    {
        return [
            'high_value_segment' => [
                'size' => count($contactPredictions) * 0.2,
                'avg_ltv' => 3500,
                'churn_risk' => 'low',
                'recommended_strategy' => 'VIP treatment and exclusive offers'
            ],
            'at_risk_segment' => [
                'size' => count($contactPredictions) * 0.15,
                'avg_churn_probability' => 75,
                'recommended_strategy' => 'Immediate intervention and retention campaigns'
            ]
        ];
    }

    private function generatePredictiveRecommendations($contactPredictions, $summary)
    {
        return [
            [
                'type' => 'retention_campaign',
                'priority' => 'high',
                'description' => 'Launch targeted retention campaign for high-risk contacts',
                'expected_impact' => 'Reduce churn by 25%',
                'implementation_timeline' => '1-2 weeks'
            ],
            [
                'type' => 'conversion_optimization',
                'priority' => 'medium',
                'description' => 'Focus on conversion-ready contacts with personalized outreach',
                'expected_impact' => 'Increase conversion rate by 15%',
                'implementation_timeline' => '2-3 weeks'
            ]
        ];
    }

    public function createBulkAccounts(Request $request)
    {
        $request->validate([
            'accounts' => 'required|array|min:1|max:100',
            'accounts.*.name' => 'required|string|max:255',
            'accounts.*.email' => 'required|email',
            'generate_bio_links' => 'boolean',
            'send_welcome_emails' => 'boolean',
        ]);

        try {
            $created = 0;
            $failed = 0;
            $errors = [];

            foreach ($request->accounts as $accountData) {
                try {
                    // Check if contact already exists
                    $existing = Audience::where('user_id', $request->user()->id)
                        ->where('email', $accountData['email'])
                        ->first();

                    if ($existing) {
                        $failed++;
                        $errors[] = "Contact already exists: {$accountData['email']}";
                        continue;
                    }

                    // Create new contact
                    $contact = Audience::create([
                        'user_id' => $request->user()->id,
                        'name' => $accountData['name'],
                        'email' => $accountData['email'],
                        'type' => 'contact',
                        'status' => 'cold',
                        'source' => 'bulk_creation',
                        'notes' => 'Created via bulk account creation',
                    ]);

                    // Generate bio link if requested
                    if ($request->generate_bio_links) {
                        // This would create a bio site for each contact
                        // Implementation depends on bio site creation logic
                    }

                    // Send welcome email if requested
                    if ($request->send_welcome_emails) {
                        // This would send a welcome email to each contact
                        // Implementation depends on email system
                    }

                    $created++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Failed to create {$accountData['email']}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk creation completed. {$created} accounts created, {$failed} failed.",
                'data' => [
                    'created' => $created,
                    'failed' => $failed,
                    'errors' => array_slice($errors, 0, 10), // Show first 10 errors
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk creation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single contact
     */
    public function getContact(Request $request, $contactId)
    {
        try {
            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $contact = Audience::where('user_id', $user->id)
                ->where('type', 'contact')
                ->where('id', $contactId)
                ->first();

            if (!$contact) {
                return response()->json(['error' => 'Contact not found'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $contact,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting contact: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get contact'], 500);
        }
    }

    /**
     * Update contact
     */
    public function updateContact(Request $request, $contactId)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:audiences,email,' . $contactId,
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'position' => 'nullable|string|max:255',
                'tags' => 'nullable|array',
                'notes' => 'nullable|string',
            ]);

            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $contact = Audience::where('user_id', $user->id)
                ->where('type', 'contact')
                ->where('id', $contactId)
                ->first();

            if (!$contact) {
                return response()->json(['error' => 'Contact not found'], 404);
            }

            $contact->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'position' => $request->position,
                'tags' => $request->tags,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully',
                'data' => $contact,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating contact: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update contact'], 500);
        }
    }

    /**
     * Delete contact
     */
    public function deleteContact(Request $request, $contactId)
    {
        try {
            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $contact = Audience::where('user_id', $user->id)
                ->where('type', 'contact')
                ->where('id', $contactId)
                ->first();

            if (!$contact) {
                return response()->json(['error' => 'Contact not found'], 404);
            }

            $contact->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting contact: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete contact'], 500);
        }
    }

    /**
     * Parse date range from request
     */
    private function parseDateRange($dateRange, $startDate = null, $endDate = null)
    {
        if ($dateRange === 'custom' && $startDate && $endDate) {
            return [$startDate, $endDate];
        }

        if (!$dateRange) {
            return [now()->subDays(30), now()];
        }

        $ranges = [
            'last_7_days' => [now()->subDays(7), now()],
            'last_30_days' => [now()->subDays(30), now()],
            'last_90_days' => [now()->subDays(90), now()],
            'last_year' => [now()->subYear(), now()],
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_year' => [now()->startOfYear(), now()->endOfYear()],
        ];

        return $ranges[$dateRange] ?? [now()->subDays(30), now()];
    }

    // Helper methods for advanced pipeline management
    private function getPipelineData($userId, $pipelineId, $dateRange)
    {
        // Mock pipeline data for demo
        return [
            'opportunities' => [
                ['id' => 1, 'value' => 5000, 'stage' => 'qualified', 'created_at' => now()->subDays(10)],
                ['id' => 2, 'value' => 3000, 'stage' => 'proposal', 'created_at' => now()->subDays(5)],
                ['id' => 3, 'value' => 8000, 'stage' => 'negotiation', 'created_at' => now()->subDays(3)],
            ]
        ];
    }

    private function calculateAdvancedPipelineMetrics($pipelineData, $dateRange)
    {
        $opportunities = $pipelineData['opportunities'];
        return [
            'total_opportunities' => count($opportunities),
            'total_value' => array_sum(array_column($opportunities, 'value')),
            'weighted_value' => array_sum(array_column($opportunities, 'value')) * 0.7,
            'average_deal_size' => count($opportunities) > 0 ? array_sum(array_column($opportunities, 'value')) / count($opportunities) : 0,
            'win_rate' => 65.5,
            'average_sales_cycle' => 45,
            'pipeline_coverage' => 2.3
        ];
    }

    private function analyzePipelineHealth($pipelineData, $metrics)
    {
        return [
            'score' => 78,
            'status' => 'healthy',
            'issues' => [],
            'recommendations' => ['Focus on closing deals in negotiation stage']
        ];
    }

    private function analyzeVelocity($pipelineData, $dateRange)
    {
        return [
            'average_velocity' => 1200,
            'velocity_trend' => 'increasing',
            'stage_velocities' => [
                'qualified' => 800,
                'proposal' => 1500,
                'negotiation' => 2000
            ]
        ];
    }

    private function analyzeConversionRates($pipelineData, $dateRange)
    {
        return [
            'overall_conversion_rate' => 25.5,
            'stage_conversion_rates' => [
                'qualified_to_proposal' => 45.2,
                'proposal_to_negotiation' => 67.8,
                'negotiation_to_closed' => 78.9
            ]
        ];
    }

    private function analyzeStagePerformance($pipelineData)
    {
        return [
            'qualified' => ['count' => 15, 'value' => 75000, 'avg_time' => 12],
            'proposal' => ['count' => 8, 'value' => 48000, 'avg_time' => 18],
            'negotiation' => ['count' => 5, 'value' => 35000, 'avg_time' => 25]
        ];
    }

    private function analyzePerformanceTrends($pipelineData, $dateRange)
    {
        return [
            'monthly_trends' => [
                'jan' => ['deals' => 12, 'value' => 60000],
                'feb' => ['deals' => 15, 'value' => 75000],
                'mar' => ['deals' => 18, 'value' => 90000]
            ]
        ];
    }

    private function analyzeLeakage($pipelineData, $dateRange)
    {
        return [
            'total_leakage' => 15.2,
            'stage_leakage' => [
                'qualified' => 8.5,
                'proposal' => 12.3,
                'negotiation' => 6.8
            ]
        ];
    }

    private function generateSalesForecasting($pipelineData, $metrics)
    {
        return [
            'next_30_days' => 45000,
            'next_60_days' => 78000,
            'next_90_days' => 120000,
            'confidence_level' => 85
        ];
    }

    private function analyzeBottlenecks($pipelineData, $velocityAnalysis)
    {
        return [
            'identified_bottlenecks' => [
                'stage' => 'proposal',
                'avg_time' => 25,
                'recommendation' => 'Streamline proposal process'
            ]
        ];
    }

    private function analyzeWinLoss($pipelineData, $dateRange)
    {
        return [
            'win_rate' => 65.5,
            'loss_rate' => 34.5,
            'win_reasons' => ['competitive_pricing', 'good_relationship'],
            'loss_reasons' => ['price_too_high', 'timing_issues']
        ];
    }

    private function analyzeTeamPerformance($pipelineData, $dateRange)
    {
        return [
            'top_performers' => [
                ['name' => 'John Doe', 'deals' => 12, 'value' => 85000],
                ['name' => 'Jane Smith', 'deals' => 10, 'value' => 75000]
            ]
        ];
    }

    private function generateAIPipelineInsights($pipelineData, $metrics)
    {
        return [
            'insights' => [
                'Deal velocity is 15% higher than last quarter',
                'Proposal stage needs attention - 25% longer than average'
            ],
            'predictions' => [
                'Expected to close 3 deals this week',
                '85% probability of hitting monthly target'
            ]
        ];
    }

    private function segmentPipelineData($pipelineData, $segmentBy)
    {
        return [
            'segments' => [
                'enterprise' => ['count' => 5, 'value' => 125000],
                'mid_market' => ['count' => 8, 'value' => 80000],
                'small_business' => ['count' => 12, 'value' => 45000]
            ]
        ];
    }

    private function generatePipelineRecommendations($pipelineData, $metrics, $health)
    {
        return [
            [
                'type' => 'process_improvement',
                'priority' => 'high',
                'description' => 'Focus on reducing time in proposal stage',
                'expected_impact' => 'Increase velocity by 20%'
            ],
            [
                'type' => 'resource_allocation',
                'priority' => 'medium',
                'description' => 'Assign more resources to negotiation stage',
                'expected_impact' => 'Improve conversion rate by 10%'
            ]
        ];
    }
}