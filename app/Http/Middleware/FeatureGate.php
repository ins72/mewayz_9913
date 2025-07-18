<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FeatureGate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $featureKey
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $featureKey)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $workspace = $user->currentWorkspace ?? $user->workspaces()->first();

        if (!$workspace) {
            return redirect()->route('workspace.setup');
        }

        // Check if workspace has the feature enabled
        if (!$workspace->hasFeature($featureKey)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Feature not available',
                    'error' => 'FEATURE_NOT_AVAILABLE',
                    'feature_key' => $featureKey,
                    'upgrade_required' => true,
                ], 403);
            }

            return redirect()->route('subscription.plans')
                ->with('error', 'This feature is not available in your current plan. Please upgrade to access it.');
        }

        // Check quota limits for quota-based features
        if ($workspace->hasReachedQuotaLimit($featureKey)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Feature quota exceeded',
                    'error' => 'QUOTA_EXCEEDED',
                    'feature_key' => $featureKey,
                    'upgrade_required' => true,
                ], 403);
            }

            return redirect()->route('subscription.plans')
                ->with('error', 'You have reached the usage limit for this feature. Please upgrade to continue.');
        }

        // Store the workspace in the request for later use
        $request->attributes->set('workspace', $workspace);
        $request->attributes->set('feature_key', $featureKey);

        return $next($request);
    }
}