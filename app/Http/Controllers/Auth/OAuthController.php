<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;

class OAuthController extends Controller
{
    /**
     * Redirect to OAuth provider
     */
    public function redirectToProvider($provider)
    {
        $allowedProviders = ['google', 'facebook', 'apple', 'twitter'];
        
        if (!in_array($provider, $allowedProviders)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OAuth provider'
            ], 400);
        }

        try {
            // Handle Twitter differently as it doesn't support stateless mode
            if ($provider === 'twitter') {
                $redirectUrl = Socialite::driver($provider)->redirect()->getTargetUrl();
            } else {
                $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
            }
            
            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OAuth provider configuration error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle OAuth callback
     */
    public function handleProviderCallback($provider)
    {
        $allowedProviders = ['google', 'facebook', 'apple', 'twitter'];
        
        if (!in_array($provider, $allowedProviders)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OAuth provider'
            ], 400);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            
            // Check if user exists with this provider
            $existingUser = User::where('provider_id', $socialUser->getId())
                               ->where('provider_name', $provider)
                               ->first();

            if ($existingUser) {
                // Update existing user info
                $existingUser->update([
                    'provider_avatar' => $socialUser->getAvatar(),
                    'last_login_at' => now(),
                    'last_login_ip' => request()->ip()
                ]);
                
                $user = $existingUser;
            } else {
                // Check if user exists with same email
                $userByEmail = User::where('email', $socialUser->getEmail())->first();
                
                if ($userByEmail) {
                    // Link existing account to OAuth provider
                    $userByEmail->update([
                        'provider_id' => $socialUser->getId(),
                        'provider_name' => $provider,
                        'provider_avatar' => $socialUser->getAvatar(),
                        'last_login_at' => now(),
                        'last_login_ip' => request()->ip()
                    ]);
                    
                    $user = $userByEmail;
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $socialUser->getName(),
                        'email' => $socialUser->getEmail(),
                        'provider_id' => $socialUser->getId(),
                        'provider_name' => $provider,
                        'provider_avatar' => $socialUser->getAvatar(),
                        'email_verified_at' => now(),
                        'password' => null, // OAuth users don't need passwords
                        'last_login_at' => now(),
                        'last_login_ip' => request()->ip()
                    ]);
                }
            }

            // Generate Sanctum token
            $token = $user->createToken('OAuth-' . $provider)->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->provider_avatar ?: $user->getAvatar(),
                    'provider' => $provider,
                    'two_factor_enabled' => $user->two_factor_enabled,
                    'email_verified' => $user->email_verified_at !== null
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OAuth authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Link OAuth account to existing user
     */
    public function linkAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|in:google,facebook,apple,twitter',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        try {
            $socialUser = Socialite::driver($request->provider)->stateless()->user();
            
            // Check if this provider account is already linked to another user
            $existingLink = User::where('provider_id', $socialUser->getId())
                               ->where('provider_name', $request->provider)
                               ->where('id', '!=', $user->id)
                               ->first();

            if ($existingLink) {
                return response()->json([
                    'success' => false,
                    'message' => 'This ' . $request->provider . ' account is already linked to another user'
                ], 400);
            }

            $user->update([
                'provider_id' => $socialUser->getId(),
                'provider_name' => $request->provider,
                'provider_avatar' => $socialUser->getAvatar()
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($request->provider) . ' account linked successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'provider' => $request->provider,
                    'avatar' => $user->provider_avatar ?: $user->getAvatar()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to link account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unlink OAuth account
     */
    public function unlinkAccount(Request $request)
    {
        $user = $request->user();

        if (!$user->provider_id || !$user->provider_name) {
            return response()->json([
                'success' => false,
                'message' => 'No OAuth account linked'
            ], 400);
        }

        // Don't allow unlinking if user doesn't have a password
        if (!$user->password) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot unlink OAuth account. Please set a password first.'
            ], 400);
        }

        $user->update([
            'provider_id' => null,
            'provider_name' => null,
            'provider_avatar' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OAuth account unlinked successfully'
        ]);
    }

    /**
     * Get user's OAuth status
     */
    public function getOAuthStatus(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'oauth_status' => [
                'linked' => $user->provider_id ? true : false,
                'provider' => $user->provider_name,
                'has_password' => $user->password ? true : false,
                'can_unlink' => $user->provider_id && $user->password ? true : false
            ]
        ]);
    }
}
