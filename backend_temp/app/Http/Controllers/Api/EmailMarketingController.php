<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AudienceBroadcast;
use Illuminate\Http\Request;

class EmailMarketingController extends Controller
{
    public function getCampaigns(Request $request)
    {
        $campaigns = AudienceBroadcast::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $campaigns,
        ]);
    }

    public function createCampaign(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'audience_ids' => 'required|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign = AudienceBroadcast::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'audience_ids' => $request->audience_ids,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->scheduled_at ? 'scheduled' : 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campaign created successfully',
            'data' => $campaign,
        ], 201);
    }

    public function showCampaign(AudienceBroadcast $campaign)
    {
        // Check if user owns the campaign
        if ($campaign->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to campaign',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $campaign,
        ]);
    }

    public function updateCampaign(Request $request, AudienceBroadcast $campaign)
    {
        // Check if user owns the campaign
        if ($campaign->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to campaign',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'audience_ids' => 'required|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign->update($request->only(['name', 'subject', 'content', 'audience_ids', 'scheduled_at']));

        return response()->json([
            'success' => true,
            'message' => 'Campaign updated successfully',
            'data' => $campaign,
        ]);
    }

    public function deleteCampaign(AudienceBroadcast $campaign)
    {
        // Check if user owns the campaign
        if ($campaign->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to campaign',
            ], 403);
        }

        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campaign deleted successfully',
        ]);
    }

    public function sendCampaign(AudienceBroadcast $campaign)
    {
        // Check if user owns the campaign
        if ($campaign->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to campaign',
            ], 403);
        }

        // TODO: Implement campaign sending logic
        $campaign->update(['status' => 'sent', 'sent_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Campaign sent successfully',
        ]);
    }

    public function getTemplates()
    {
        // TODO: Get email templates
        $templates = [
            [
                'id' => 1,
                'name' => 'Welcome Email',
                'category' => 'welcome',
                'preview_image' => null,
                'is_premium' => false,
            ],
            [
                'id' => 2,
                'name' => 'Newsletter',
                'category' => 'newsletter',
                'preview_image' => null,
                'is_premium' => false,
            ],
            [
                'id' => 3,
                'name' => 'Promotional',
                'category' => 'promotional',
                'preview_image' => null,
                'is_premium' => true,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    public function createTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // TODO: Implement template creation logic

        return response()->json([
            'success' => true,
            'message' => 'Template created successfully',
        ]);
    }

    public function getAnalytics(Request $request)
    {
        $analytics = [
            'total_sent' => 0,
            'open_rate' => '0%',
            'click_rate' => '0%',
            'bounce_rate' => '0%',
            'unsubscribe_rate' => '0%',
            'campaigns_chart' => [],
            'top_performing_campaigns' => [],
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    public function getAudience(Request $request)
    {
        $audience = [
            'total_subscribers' => 0,
            'active_subscribers' => 0,
            'growth_rate' => '0%',
            'segments' => [],
            'recent_subscribers' => [],
        ];

        return response()->json([
            'success' => true,
            'data' => $audience,
        ]);
    }
}