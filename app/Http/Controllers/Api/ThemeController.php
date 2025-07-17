<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPreference;

class ThemeController extends Controller
{
    /**
     * Get user theme preferences
     */
    public function getTheme(Request $request)
    {
        try {
            $user = $request->user();
            $preferences = UserPreference::firstOrCreate(['user_id' => $user->id]);

            $themeData = [
                'current_theme' => $preferences->theme ?? 'dark',
                'available_themes' => $this->getAvailableThemes(),
                'theme_settings' => $this->getThemeSettings($preferences->theme ?? 'dark'),
                'accessibility_options' => $preferences->accessibility_options ?? [],
                'custom_branding' => $this->getCustomBranding($user)
            ];

            return response()->json([
                'success' => true,
                'data' => $themeData,
                'message' => 'Theme preferences retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve theme preferences',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user theme preferences
     */
    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:light,dark,auto',
            'accessibility_options' => 'nullable|array',
            'accessibility_options.high_contrast' => 'nullable|boolean',
            'accessibility_options.reduced_motion' => 'nullable|boolean',
            'accessibility_options.large_text' => 'nullable|boolean',
            'custom_colors' => 'nullable|array'
        ]);

        try {
            $user = $request->user();
            $preferences = UserPreference::firstOrCreate(['user_id' => $user->id]);

            $updateData = [
                'theme' => $request->theme
            ];

            if ($request->has('accessibility_options')) {
                $updateData['accessibility_options'] = array_merge(
                    $preferences->accessibility_options ?? [],
                    $request->accessibility_options
                );
            }

            // Handle custom colors for enterprise users
            if ($request->has('custom_colors') && $this->canUseCustomColors($user)) {
                $updateData['custom_colors'] = $request->custom_colors;
            }

            $preferences->update($updateData);

            return response()->json([
                'success' => true,
                'data' => [
                    'theme' => $preferences->theme,
                    'accessibility_options' => $preferences->accessibility_options,
                    'theme_settings' => $this->getThemeSettings($preferences->theme)
                ],
                'message' => 'Theme preferences updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update theme preferences',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available themes
     */
    private function getAvailableThemes()
    {
        return [
            'light' => [
                'name' => 'Light Theme',
                'description' => 'Clean light interface for daytime use',
                'preview' => '/themes/light-preview.jpg',
                'colors' => [
                    'primary' => '#007AFF',
                    'secondary' => '#FFFFFF',
                    'background' => '#F8F9FA',
                    'text' => '#212529'
                ]
            ],
            'dark' => [
                'name' => 'Dark Theme',
                'description' => 'Modern dark interface for reduced eye strain',
                'preview' => '/themes/dark-preview.jpg',
                'colors' => [
                    'primary' => '#007AFF',
                    'secondary' => '#191919',
                    'background' => '#101010',
                    'text' => '#F1F1F1'
                ]
            ],
            'auto' => [
                'name' => 'Auto Theme',
                'description' => 'Automatically switches based on system preferences',
                'preview' => '/themes/auto-preview.jpg',
                'colors' => 'system'
            ]
        ];
    }

    /**
     * Get theme settings
     */
    private function getThemeSettings($theme)
    {
        $themes = [
            'light' => [
                'app_background' => '#F8F9FA',
                'card_background' => '#FFFFFF',
                'primary_text' => '#212529',
                'secondary_text' => '#6C757D',
                'border_color' => '#E9ECEF',
                'button_primary' => '#007AFF',
                'button_secondary' => '#6C757D',
                'accent_color' => '#17A2B8',
                'success_color' => '#28A745',
                'warning_color' => '#FFC107',
                'error_color' => '#DC3545',
                'sidebar_background' => '#F8F9FA',
                'navbar_background' => '#FFFFFF',
                'modal_background' => '#FFFFFF',
                'input_background' => '#FFFFFF',
                'shadow' => '0 2px 4px rgba(0,0,0,0.1)'
            ],
            'dark' => [
                'app_background' => '#101010',
                'card_background' => '#191919',
                'primary_text' => '#F1F1F1',
                'secondary_text' => '#7B7B7B',
                'border_color' => '#282828',
                'button_primary' => '#007AFF',
                'button_secondary' => '#191919',
                'accent_color' => '#17A2B8',
                'success_color' => '#28A745',
                'warning_color' => '#FFC107',
                'error_color' => '#DC3545',
                'sidebar_background' => '#0D0D0D',
                'navbar_background' => '#191919',
                'modal_background' => '#1A1A1A',
                'input_background' => '#1A1A1A',
                'shadow' => '0 2px 4px rgba(0,0,0,0.3)'
            ],
            'auto' => [
                'light' => $this->getThemeSettings('light'),
                'dark' => $this->getThemeSettings('dark')
            ]
        ];

        return $themes[$theme] ?? $themes['dark'];
    }

    /**
     * Get custom branding for workspace
     */
    private function getCustomBranding($user)
    {
        $workspace = $user->workspaces()->first();
        
        if (!$workspace) {
            return null;
        }

        $settings = $workspace->settings ?? [];
        
        return [
            'logo' => $settings['branding']['logo'] ?? null,
            'primary_color' => $settings['branding']['colors']['primary'] ?? '#007AFF',
            'secondary_color' => $settings['branding']['colors']['secondary'] ?? '#191919',
            'font_family' => $settings['branding']['font_family'] ?? 'Inter',
            'custom_css' => $settings['branding']['custom_css'] ?? null
        ];
    }

    /**
     * Check if user can use custom colors
     */
    private function canUseCustomColors($user)
    {
        $workspace = $user->workspaces()->first();
        
        if (!$workspace) {
            return false;
        }

        // Check if workspace has enterprise subscription
        $subscription = $workspace->subscription;
        
        return $subscription && $subscription->plan->name === 'Enterprise';
    }

    /**
     * Get system theme preference
     */
    public function getSystemTheme(Request $request)
    {
        try {
            $userAgent = $request->header('User-Agent');
            $systemTheme = $this->detectSystemTheme($userAgent);

            return response()->json([
                'success' => true,
                'data' => [
                    'system_theme' => $systemTheme,
                    'supported' => $this->isSystemThemeSupported($userAgent)
                ],
                'message' => 'System theme detected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to detect system theme',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detect system theme from user agent
     */
    private function detectSystemTheme($userAgent)
    {
        // This is a simplified detection - in real implementation,
        // you would use JavaScript to detect system theme
        return 'dark'; // Default to dark theme
    }

    /**
     * Check if system theme detection is supported
     */
    private function isSystemThemeSupported($userAgent)
    {
        // Check for modern browser support
        $modernBrowsers = ['Chrome', 'Firefox', 'Safari', 'Edge'];
        
        foreach ($modernBrowsers as $browser) {
            if (strpos($userAgent, $browser) !== false) {
                return true;
            }
        }
        
        return false;
    }
}