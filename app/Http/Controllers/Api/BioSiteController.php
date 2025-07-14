<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BioSite;
use Illuminate\Http\Request;

class BioSiteController extends Controller
{
    public function index(Request $request)
    {
        $bioSites = BioSite::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bioSites,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:bio_sites',
            'description' => 'nullable|string',
            'template_id' => 'nullable|integer',
        ]);

        $bioSite = BioSite::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'slug' => $request->slug,
            'description' => $request->description,
            'template_id' => $request->template_id,
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bio site created successfully',
            'data' => $bioSite,
        ], 201);
    }

    public function show(BioSite $bioSite)
    {
        // Check if user owns the bio site
        if ($bioSite->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to bio site',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $bioSite,
        ]);
    }

    public function update(Request $request, BioSite $bioSite)
    {
        // Check if user owns the bio site
        if ($bioSite->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to bio site',
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|array',
            'status' => 'in:draft,published',
        ]);

        $bioSite->update($request->only(['title', 'description', 'content', 'status']));

        return response()->json([
            'success' => true,
            'message' => 'Bio site updated successfully',
            'data' => $bioSite,
        ]);
    }

    public function destroy(BioSite $bioSite)
    {
        // Check if user owns the bio site
        if ($bioSite->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to bio site',
            ], 403);
        }

        $bioSite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bio site deleted successfully',
        ]);
    }

    public function getAnalytics(BioSite $bioSite)
    {
        // Check if user owns the bio site
        if ($bioSite->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to bio site',
            ], 403);
        }

        // TODO: Get analytics for the bio site
        $analytics = [
            'total_visits' => 0,
            'link_clicks' => 0,
            'conversion_rate' => '0%',
            'top_source' => 'Direct',
            'daily_visits' => [],
            'top_links' => [],
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    public function getTemplates()
    {
        // TODO: Get available bio site templates
        $templates = [
            [
                'id' => 1,
                'name' => 'Personal',
                'description' => 'Perfect for personal branding',
                'preview_image' => null,
                'is_premium' => false,
            ],
            [
                'id' => 2,
                'name' => 'Business',
                'description' => 'Professional business template',
                'preview_image' => null,
                'is_premium' => false,
            ],
            [
                'id' => 3,
                'name' => 'Creator',
                'description' => 'For content creators',
                'preview_image' => null,
                'is_premium' => true,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }
}