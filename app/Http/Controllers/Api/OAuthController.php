<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    /**
     * OAuth provider configuration
     */
    private $providers = [
        'google' => [
            'name' => 'Google',
            'icon' => 'google',
            'enabled' => true,
            'test_mode' => false, // Disable test mode for real Google OAuth
        ],
        'apple' => [
            'name' => 'Apple',
            'icon' => 'apple',
            'enabled' => true,
            'test_mode' => true,
        ],
        'facebook' => [
            'name' => 'Facebook',
            'icon' => 'facebook',
            'enabled' => true,
            'test_mode' => true,
        ],
        'twitter' => [
            'name' => 'Twitter',
            'icon' => 'twitter',
            'enabled' => true,
            'test_mode' => true,
        ],
    ];

    /**
     * Get available OAuth providers
     */
    public function getProviders()
    {
        try {
            $enabledProviders = array_filter($this->providers, function($provider) {
                return $provider['enabled'];
            });

            return response()->json([
                'success' => true,
                'providers' => $enabledProviders,
                'test_mode' => config('app.env') !== 'production',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load OAuth providers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get OAuth status for user
     */
    public function getStatus(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'connected_accounts' => [],
                    'available_providers' => array_keys($this->providers),
                    'user_id' => $user->id,
                ],
                'message' => 'OAuth status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get OAuth status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Redirect to OAuth provider
     */
    public function redirectToProvider($provider)
    {
        try {
            if (!isset($this->providers[$provider])) {
                return response()->json([
                    'success' => false,
                    'message' => 'OAuth provider not supported',
                ], 400);
            }

            // In test mode, return a test URL
            if ($this->providers[$provider]['test_mode']) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => url("/oauth/test/{$provider}"),
                    'test_mode' => true,
                    'message' => 'Test mode - OAuth simulation enabled',
                ]);
            }

            // Production OAuth redirect
            $redirectUrl = Socialite::driver($provider)->redirect()->getTargetUrl();
            
            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl,
                'test_mode' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate OAuth flow',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle OAuth callback
     */
    public function handleProviderCallback($provider)
    {
        try {
            if (!isset($this->providers[$provider])) {
                return response()->json([
                    'success' => false,
                    'message' => 'OAuth provider not supported',
                ], 400);
            }

            // In test mode, simulate OAuth response
            if ($this->providers[$provider]['test_mode']) {
                return $this->handleTestModeCallback($provider);
            }

            // Production OAuth handling
            $socialUser = Socialite::driver($provider)->user();
            
            return $this->processOAuthUser($socialUser, $provider);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OAuth authentication failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle test mode OAuth callback
     */
    private function handleTestModeCallback($provider)
    {
        // Generate test OAuth user data
        $testUsers = [
            'google' => [
                'id' => 'google_test_123',
                'email' => 'test.user@gmail.com',
                'name' => 'Test User (Google)',
                'avatar' => 'https://via.placeholder.com/150?text=Google',
            ],
            'apple' => [
                'id' => 'apple_test_456',
                'email' => 'test.user@icloud.com',
                'name' => 'Test User (Apple)',
                'avatar' => 'https://via.placeholder.com/150?text=Apple',
            ],
            'facebook' => [
                'id' => 'facebook_test_789',
                'email' => 'test.user@facebook.com',
                'name' => 'Test User (Facebook)',
                'avatar' => 'https://via.placeholder.com/150?text=Facebook',
            ],
            'twitter' => [
                'id' => 'twitter_test_101',
                'email' => 'test.user@twitter.com',
                'name' => 'Test User (Twitter)',
                'avatar' => 'https://via.placeholder.com/150?text=Twitter',
            ],
        ];

        $testUser = $testUsers[$provider];
        
        // Create mock social user object
        $socialUser = (object) [
            'id' => $testUser['id'],
            'email' => $testUser['email'],
            'name' => $testUser['name'],
            'avatar' => $testUser['avatar'],
        ];

        return $this->processOAuthUser($socialUser, $provider);
    }

    /**
     * Process OAuth user data
     */
    private function processOAuthUser($socialUser, $provider)
    {
        try {
            // Check if user exists with this email
            $existingUser = User::where('email', $socialUser->email)->first();

            if ($existingUser) {
                // Update OAuth information
                $existingUser->update([
                    'oauth_provider' => $provider,
                    'oauth_id' => $socialUser->id,
                    'avatar' => $socialUser->avatar ?? $existingUser->avatar,
                    'email_verified_at' => $existingUser->email_verified_at ?? now(),
                ]);

                $user = $existingUser;
                $isNewUser = false;
            } else {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->name,
                    'email' => $socialUser->email,
                    'password' => Hash::make(Str::random(16)), // Random password
                    'oauth_provider' => $provider,
                    'oauth_id' => $socialUser->id,
                    'avatar' => $socialUser->avatar,
                    'email_verified_at' => now(),
                ]);

                $isNewUser = true;
            }

            // Generate authentication token
            $token = $user->createToken('oauth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => $isNewUser ? 'Account created successfully' : 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'oauth_provider' => $provider,
                    'email_verified' => !is_null($user->email_verified_at),
                    'created_at' => $user->created_at,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
                'is_new_user' => $isNewUser,
                'provider' => $provider,
                'test_mode' => $this->providers[$provider]['test_mode'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process OAuth user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Link OAuth account to existing user
     */
    public function linkAccount(Request $request, $provider)
    {
        try {
            $request->validate([
                'oauth_id' => 'required|string',
                'oauth_email' => 'required|email',
            ]);

            if (!isset($this->providers[$provider])) {
                return response()->json([
                    'success' => false,
                    'message' => 'OAuth provider not supported',
                ], 400);
            }

            $user = $request->user();
            
            // Check if OAuth account is already linked to another user
            $existingLink = User::where('oauth_provider', $provider)
                ->where('oauth_id', $request->oauth_id)
                ->where('id', '!=', $user->id)
                ->first();

            if ($existingLink) {
                return response()->json([
                    'success' => false,
                    'message' => 'This OAuth account is already linked to another user',
                ], 400);
            }

            // Link the OAuth account
            $user->update([
                'oauth_provider' => $provider,
                'oauth_id' => $request->oauth_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OAuth account linked successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'oauth_provider' => $provider,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to link OAuth account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unlink OAuth account
     */
    public function unlinkAccount($provider)
    {
        try {
            if (!isset($this->providers[$provider])) {
                return response()->json([
                    'success' => false,
                    'message' => 'OAuth provider not supported',
                ], 400);
            }

            $user = $request->user();
            
            // Make sure user has a password before unlinking OAuth
            if (!$user->password && $user->oauth_provider === $provider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot unlink OAuth account without setting a password first',
                ], 400);
            }

            // Unlink the OAuth account
            $user->update([
                'oauth_provider' => null,
                'oauth_id' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OAuth account unlinked successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlink OAuth account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get OAuth account status
     */
    public function getOAuthStatus()
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'oauth_linked' => !is_null($user->oauth_provider),
                'provider' => $user->oauth_provider,
                'oauth_id' => $user->oauth_id,
                'has_password' => !is_null($user->password),
                'available_providers' => array_keys($this->providers),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get OAuth status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}