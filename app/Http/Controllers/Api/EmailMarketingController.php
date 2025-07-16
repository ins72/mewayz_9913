<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Models\EmailSubscriber;
use App\Models\EmailTemplate;
use App\Models\EmailList;
use App\Models\EmailCampaignAnalytics;
use App\Models\Workspace;
use App\Services\ElasticEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmailMarketingController extends Controller
{
    /**
     * Get all campaigns for the workspace
     */
    public function getCampaigns(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $campaigns = EmailCampaign::where('workspace_id', $workspace->id)
                ->with(['template', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            return response()->json([
                'success' => true,
                'campaigns' => $campaigns->items(),
                'pagination' => [
                    'current_page' => $campaigns->currentPage(),
                    'total_pages' => $campaigns->lastPage(),
                    'total_items' => $campaigns->total(),
                    'per_page' => $campaigns->perPage()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching campaigns: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch campaigns'], 500);
        }
    }

    /**
     * Create a new email campaign
     */
    public function createCampaign(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
                'template_id' => 'nullable|exists:email_templates,id',
                'recipient_lists' => 'required|array',
                'recipient_lists.*' => 'exists:email_lists,id',
                'scheduled_at' => 'nullable|date|after:now',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors()
                ], 400);
            }

            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            // Calculate total recipients
            $totalRecipients = EmailSubscriber::where('workspace_id', $workspace->id)
                ->whereHas('emailLists', function ($query) use ($request) {
                    $query->whereIn('email_lists.id', $request->recipient_lists);
                })
                ->where('status', 'subscribed')
                ->count();

            $campaign = EmailCampaign::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'name' => $request->name,
                'subject' => $request->subject,
                'content' => $request->content,
                'template_id' => $request->template_id,
                'recipient_lists' => $request->recipient_lists,
                'scheduled_at' => $request->scheduled_at,
                'status' => $request->scheduled_at ? 'scheduled' : 'draft',
                'total_recipients' => $totalRecipients,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Campaign created successfully',
                'campaign' => $campaign->load(['template', 'user'])
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating campaign: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create campaign'], 500);
        }
    }

    /**
     * Get campaign details
     */
    public function getCampaign(Request $request, $campaignId)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $campaign = EmailCampaign::where('workspace_id', $workspace->id)
                ->where('id', $campaignId)
                ->with(['template', 'user', 'analytics'])
                ->first();

            if (!$campaign) {
                return response()->json(['error' => 'Campaign not found'], 404);
            }

            return response()->json([
                'success' => true,
                'campaign' => $campaign,
                'metrics' => $campaign->getFormattedMetrics()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching campaign: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch campaign'], 500);
        }
    }

    /**
     * Update campaign
     */
    public function updateCampaign(Request $request, $campaignId)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $campaign = EmailCampaign::where('workspace_id', $workspace->id)
                ->where('id', $campaignId)
                ->first();

            if (!$campaign) {
                return response()->json(['error' => 'Campaign not found'], 404);
            }

            if (!$campaign->canBeEdited()) {
                return response()->json(['error' => 'Campaign cannot be edited'], 400);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
                'template_id' => 'nullable|exists:email_templates,id',
                'recipient_lists' => 'required|array',
                'recipient_lists.*' => 'exists:email_lists,id',
                'scheduled_at' => 'nullable|date|after:now',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors()
                ], 400);
            }

            $campaign->update([
                'name' => $request->name,
                'subject' => $request->subject,
                'content' => $request->content,
                'template_id' => $request->template_id,
                'recipient_lists' => $request->recipient_lists,
                'scheduled_at' => $request->scheduled_at,
                'status' => $request->scheduled_at ? 'scheduled' : 'draft',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Campaign updated successfully',
                'campaign' => $campaign->load(['template', 'user'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating campaign: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update campaign'], 500);
        }
    }

    /**
     * Delete campaign
     */
    public function deleteCampaign(Request $request, $campaignId)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $campaign = EmailCampaign::where('workspace_id', $workspace->id)
                ->where('id', $campaignId)
                ->first();

            if (!$campaign) {
                return response()->json(['error' => 'Campaign not found'], 404);
            }

            $campaign->delete();

            return response()->json([
                'success' => true,
                'message' => 'Campaign deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting campaign: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete campaign'], 500);
        }
    }

    /**
     * Send campaign
     */
    public function sendCampaign(Request $request, $campaignId)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $campaign = EmailCampaign::where('workspace_id', $workspace->id)
                ->where('id', $campaignId)
                ->first();

            if (!$campaign) {
                return response()->json(['error' => 'Campaign not found'], 404);
            }

            if (!$campaign->canBeSent()) {
                return response()->json(['error' => 'Campaign cannot be sent'], 400);
            }

            // For demo purposes, we'll simulate sending
            $campaign->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            // Simulate analytics events
            $subscribers = EmailSubscriber::where('workspace_id', $workspace->id)
                ->whereHas('emailLists', function ($query) use ($campaign) {
                    $query->whereIn('email_lists.id', $campaign->recipient_lists);
                })
                ->where('status', 'subscribed')
                ->take(100) // Limit for demo
                ->get();

            foreach ($subscribers as $subscriber) {
                // Simulate delivery
                EmailCampaignAnalytics::recordEvent(
                    $campaign->id,
                    $subscriber->id,
                    'delivered'
                );

                // Simulate some opens (60% open rate)
                if (rand(1, 100) <= 60) {
                    EmailCampaignAnalytics::recordEvent(
                        $campaign->id,
                        $subscriber->id,
                        'opened'
                    );
                }

                // Simulate some clicks (15% click rate)
                if (rand(1, 100) <= 15) {
                    EmailCampaignAnalytics::recordEvent(
                        $campaign->id,
                        $subscriber->id,
                        'clicked'
                    );
                }
            }

            // Update campaign metrics
            $campaign->updateMetrics();

            return response()->json([
                'success' => true,
                'message' => 'Campaign sent successfully',
                'campaign' => $campaign->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending campaign: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send campaign'], 500);
        }
    }

    /**
     * Get email templates
     */
    public function getTemplates(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $templates = EmailTemplate::where('workspace_id', $workspace->id)
                ->orWhere('is_default', true)
                ->where('is_active', true)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'templates' => $templates->map(function ($template) {
                    return [
                        'id' => $template->id,
                        'name' => $template->name,
                        'description' => $template->description,
                        'category' => $template->category,
                        'formatted_category' => $template->getFormattedCategory(),
                        'category_color' => $template->getCategoryColor(),
                        'thumbnail_url' => $template->thumbnail_url,
                        'is_default' => $template->is_default,
                        'usage_count' => $template->usage_count,
                        'can_be_edited' => $template->canBeEdited(),
                        'can_be_deleted' => $template->canBeDeleted(),
                        'created_at' => $template->created_at
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching templates: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch templates'], 500);
        }
    }

    /**
     * Get email lists
     */
    public function getEmailLists(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $lists = EmailList::where('workspace_id', $workspace->id)
                ->where('is_active', true)
                ->with(['user'])
                ->get();

            return response()->json([
                'success' => true,
                'lists' => $lists->map(function ($list) {
                    return [
                        'id' => $list->id,
                        'name' => $list->name,
                        'description' => $list->description,
                        'subscriber_count' => $list->subscriber_count,
                        'tags' => $list->tags,
                        'growth_metrics' => $list->getGrowthMetrics(),
                        'performance_metrics' => $list->getPerformanceMetrics(),
                        'can_be_deleted' => $list->canBeDeleted(),
                        'created_at' => $list->created_at
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching email lists: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch email lists'], 500);
        }
    }

    /**
     * Get subscribers
     */
    public function getSubscribers(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $query = EmailSubscriber::where('workspace_id', $workspace->id);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('list_id')) {
                $query->whereHas('emailLists', function ($q) use ($request) {
                    $q->where('email_lists.id', $request->list_id);
                });
            }

            if ($request->has('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('email', 'like', '%' . $request->search . '%')
                      ->orWhere('first_name', 'like', '%' . $request->search . '%')
                      ->orWhere('last_name', 'like', '%' . $request->search . '%');
                });
            }

            $subscribers = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'subscribers' => $subscribers->items(),
                'pagination' => [
                    'current_page' => $subscribers->currentPage(),
                    'total_pages' => $subscribers->lastPage(),
                    'total_items' => $subscribers->total(),
                    'per_page' => $subscribers->perPage()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching subscribers: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch subscribers'], 500);
        }
    }

    /**
     * Get analytics overview
     */
    public function getAnalytics(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $campaigns = EmailCampaign::where('workspace_id', $workspace->id)->get();
            $subscribers = EmailSubscriber::where('workspace_id', $workspace->id)->get();
            $templates = EmailTemplate::where('workspace_id', $workspace->id)->get();

            $analytics = [
                'overview' => [
                    'total_campaigns' => $campaigns->count(),
                    'total_subscribers' => $subscribers->count(),
                    'active_subscribers' => $subscribers->where('status', 'subscribed')->count(),
                    'total_templates' => $templates->count(),
                    'campaigns_sent' => $campaigns->where('status', 'sent')->count(),
                    'avg_open_rate' => $campaigns->avg('open_rate') ?? 0,
                    'avg_click_rate' => $campaigns->avg('click_rate') ?? 0,
                ],
                'recent_campaigns' => $campaigns->sortByDesc('created_at')->take(5)->values(),
                'subscriber_growth' => $this->getSubscriberGrowthData($workspace->id),
                'campaign_performance' => $this->getCampaignPerformanceData($workspace->id),
                'top_performing_campaigns' => $campaigns->sortByDesc('open_rate')->take(5)->values(),
            ];

            return response()->json([
                'success' => true,
                'analytics' => $analytics
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching analytics: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch analytics'], 500);
        }
    }

    /**
     * Get subscriber growth data
     */
    private function getSubscriberGrowthData($workspaceId)
    {
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = EmailSubscriber::where('workspace_id', $workspaceId)
                ->whereDate('created_at', $date)
                ->count();
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count
            ];
        }
        return $data;
    }

    /**
     * Get campaign performance data
     */
    private function getCampaignPerformanceData($workspaceId)
    {
        $campaigns = EmailCampaign::where('workspace_id', $workspaceId)
            ->where('status', 'sent')
            ->orderBy('sent_at', 'desc')
            ->take(10)
            ->get();

        return $campaigns->map(function ($campaign) {
            return [
                'name' => $campaign->name,
                'sent_at' => $campaign->sent_at,
                'open_rate' => $campaign->open_rate,
                'click_rate' => $campaign->click_rate,
                'total_recipients' => $campaign->total_recipients
            ];
        });
    }

    /**
     * Test ElasticEmail integration
     */
    public function testElasticEmail()
    {
        try {
            $elasticEmailService = new ElasticEmailService();
            
            // Test connection
            $result = $elasticEmailService->testConnection();
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'ElasticEmail connection successful',
                    'account_info' => $result['account'] ?? null,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('ElasticEmail test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to test ElasticEmail connection',
            ], 500);
        }
    }

    /**
     * Send campaign using ElasticEmail
     */
    public function sendCampaignWithElasticEmail($campaignId)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $campaign = EmailCampaign::where('id', $campaignId)
                ->where('workspace_id', $workspace->id)
                ->first();

            if (!$campaign) {
                return response()->json(['error' => 'Campaign not found'], 404);
            }

            // Get subscribers
            $subscribers = EmailSubscriber::where('workspace_id', $workspace->id)
                ->where('status', 'subscribed')
                ->get();

            if ($subscribers->isEmpty()) {
                return response()->json(['error' => 'No subscribers found'], 400);
            }

            $elasticEmailService = new ElasticEmailService();
            
            // Send bulk email
            $emails = $subscribers->pluck('email')->toArray();
            $result = $elasticEmailService->sendBulkEmails(
                $emails,
                $campaign->subject,
                $campaign->content,
                $campaign->from_email,
                $campaign->from_name
            );

            if ($result['success']) {
                // Update campaign status
                $campaign->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'total_recipients' => count($emails),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Campaign sent successfully',
                    'recipients' => count($emails),
                    'message_id' => $result['message_id'],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('ElasticEmail send campaign error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to send campaign',
            ], 500);
        }
    }
}