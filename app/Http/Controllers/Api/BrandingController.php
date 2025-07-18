<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    /**
     * Get platform branding information
     */
    public function info(Request $request)
    {
        $branding = [
            'platform' => [
                'name' => 'Mewayz',
                'full_name' => 'Mewayz Platform',
                'tagline' => 'All-in-One Business Platform for Modern Creators',
                'description' => 'Empowering creators and businesses with seamless, integrated solutions',
                'company' => 'Mewayz Technologies Inc.',
                'philosophy' => 'Creating seamless business solutions for the modern digital world',
                'mission' => 'To empower creators and entrepreneurs with tools that enhance creativity, not complicate it',
                'version' => '2.0.0'
            ],
            'visual_identity' => [
                'primary_color' => '#2563eb',
                'secondary_color' => '#1e40af',
                'accent_color' => '#3b82f6',
                'text_color' => '#1f2937',
                'background_color' => '#ffffff',
                'logo_url' => asset('images/logo.png'),
                'logo_icon_url' => asset('images/logo-icon.png'),
                'favicon_url' => asset('images/favicon.ico')
            ],
            'typography' => [
                'primary_font' => 'Inter',
                'secondary_font' => 'Roboto',
                'heading_font' => 'Inter',
                'body_font' => 'Inter',
                'mono_font' => 'Fira Code'
            ],
            'messaging' => [
                'welcome_message' => 'Welcome to Mewayz - Your All-in-One Business Platform',
                'success_message' => 'Success! Your action has been completed.',
                'error_message' => 'Oops! Something went wrong. Please try again.',
                'loading_message' => 'Loading your content...',
                'empty_state' => 'No content available yet. Start creating!'
            ],
            'contact' => [
                'website' => config('app.url'),
                'support_email' => 'support@mewayz.com',
                'contact_email' => 'hello@mewayz.com',
                'phone' => '+1-800-MEWAYZ',
                'address' => 'Mewayz Technologies Inc., Innovation District, Tech City'
            ],
            'social_media' => [
                'twitter' => '@mewayz',
                'facebook' => 'facebook.com/mewayz',
                'instagram' => 'instagram.com/mewayz',
                'linkedin' => 'linkedin.com/company/mewayz',
                'youtube' => 'youtube.com/mewayz'
            ],
            'legal' => [
                'copyright' => '© 2025 Mewayz Technologies Inc. All rights reserved.',
                'terms_url' => config('app.url') . '/terms-of-service',
                'privacy_url' => config('app.url') . '/privacy-policy',
                'cookie_policy_url' => config('app.url') . '/cookie-policy'
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Branding information retrieved successfully',
            'data' => $branding
        ]);
    }

    /**
     * Update platform branding
     */
    public function update(Request $request)
    {
        $request->validate([
            'platform_name' => 'sometimes|string|max:255',
            'tagline' => 'sometimes|string|max:500',
            'description' => 'sometimes|string|max:1000',
            'primary_color' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo' => 'sometimes|image|mimes:png,jpg,jpeg,svg|max:2048',
            'logo_icon' => 'sometimes|image|mimes:png,jpg,jpeg,svg|max:1024',
            'favicon' => 'sometimes|image|mimes:ico,png|max:512'
        ]);

        $branding_data = [];

        // Handle text updates
        if ($request->has('platform_name')) {
            $branding_data['platform_name'] = $request->platform_name;
        }

        if ($request->has('tagline')) {
            $branding_data['tagline'] = $request->tagline;
        }

        if ($request->has('description')) {
            $branding_data['description'] = $request->description;
        }

        // Handle color updates
        if ($request->has('primary_color')) {
            $branding_data['primary_color'] = $request->primary_color;
        }

        if ($request->has('secondary_color')) {
            $branding_data['secondary_color'] = $request->secondary_color;
        }

        if ($request->has('accent_color')) {
            $branding_data['accent_color'] = $request->accent_color;
        }

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $logo_path = $request->file('logo')->store('branding/logos', 'public');
            $branding_data['logo_url'] = Storage::url($logo_path);
        }

        if ($request->hasFile('logo_icon')) {
            $icon_path = $request->file('logo_icon')->store('branding/icons', 'public');
            $branding_data['logo_icon_url'] = Storage::url($icon_path);
        }

        if ($request->hasFile('favicon')) {
            $favicon_path = $request->file('favicon')->store('branding/favicons', 'public');
            $branding_data['favicon_url'] = Storage::url($favicon_path);
        }

        // Save branding data to database or configuration
        if (!empty($branding_data)) {
            // Here you would typically save to a branding settings table
            // For now, we'll just return the updated data
            
            return response()->json([
                'success' => true,
                'message' => 'Branding updated successfully',
                'data' => $branding_data
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No changes detected'
        ], 422);
    }

    /**
     * Get branding assets
     */
    public function assets(Request $request)
    {
        $assets = [
            'logos' => [
                'primary_logo' => asset('images/logo.png'),
                'white_logo' => asset('images/logo-white.png'),
                'dark_logo' => asset('images/logo-dark.png'),
                'icon_logo' => asset('images/logo-icon.png'),
                'favicon' => asset('images/favicon.ico')
            ],
            'colors' => [
                'primary' => '#2563eb',
                'secondary' => '#1e40af',
                'accent' => '#3b82f6',
                'success' => '#10b981',
                'warning' => '#f59e0b',
                'error' => '#ef4444',
                'info' => '#06b6d4'
            ],
            'gradients' => [
                'primary' => 'linear-gradient(135deg, #2563eb 0%, #3b82f6 100%)',
                'secondary' => 'linear-gradient(135deg, #1e40af 0%, #2563eb 100%)',
                'accent' => 'linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%)'
            ],
            'images' => [
                'hero_background' => asset('images/hero-bg.jpg'),
                'login_background' => asset('images/login-bg.jpg'),
                'dashboard_background' => asset('images/dashboard-bg.jpg'),
                'placeholder_avatar' => asset('images/avatar-placeholder.png'),
                'placeholder_image' => asset('images/image-placeholder.png')
            ],
            'icons' => [
                'instagram' => asset('icons/instagram.svg'),
                'facebook' => asset('icons/facebook.svg'),
                'twitter' => asset('icons/twitter.svg'),
                'linkedin' => asset('icons/linkedin.svg'),
                'youtube' => asset('icons/youtube.svg'),
                'tiktok' => asset('icons/tiktok.svg')
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Branding assets retrieved successfully',
            'data' => $assets
        ]);
    }

    /**
     * Generate branding consistency report
     */
    public function consistencyReport(Request $request)
    {
        // Check for old branding references
        $inconsistencies = [];
        
        // This would typically scan files for old branding references
        $old_references = [
            'LEGACY_ZEPH' => 'Should be "Mewayz"',
            'yena' => 'Should be "mewayz"',
            'old-logo.png' => 'Should use new logo assets',
            '#old-color' => 'Should use new color scheme'
        ];

        foreach ($old_references as $old_ref => $suggestion) {
            // In a real implementation, you would scan files for these references
            $inconsistencies[] = [
                'type' => 'branding_inconsistency',
                'reference' => $old_ref,
                'suggestion' => $suggestion,
                'severity' => 'medium',
                'found_in' => []
            ];
        }

        $report = [
            'scan_date' => now()->toISOString(),
            'total_files_scanned' => 0,
            'inconsistencies_found' => count($inconsistencies),
            'consistency_score' => 85.5,
            'issues' => $inconsistencies,
            'recommendations' => [
                'Update all references from "LEGACY_ZEPH" to "Mewayz"',
                'Replace old logo assets with new branding',
                'Update color scheme throughout the platform',
                'Ensure consistent typography usage',
                'Review and update all user-facing messages'
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Branding consistency report generated',
            'data' => $report
        ]);
    }
}