<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BiometricCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BiometricAuthController extends Controller
{
    /**
     * Register a new biometric credential
     */
    public function register(Request $request)
    {
        $request->validate([
            'credential_id' => 'required|string',
            'public_key' => 'required|string',
            'authenticator_data' => 'required|string',
            'client_data_json' => 'required|string',
            'attestation_object' => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'device_type' => 'nullable|string|max:255',
        ]);

        try {
            $user = $request->user();

            // Check if credential already exists
            $existingCredential = BiometricCredential::where('credential_id', $request->credential_id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingCredential) {
                return response()->json([
                    'success' => false,
                    'message' => 'Biometric credential already registered'
                ], 400);
            }

            // Create new biometric credential
            $credential = BiometricCredential::create([
                'user_id' => $user->id,
                'credential_id' => $request->credential_id,
                'public_key' => $request->public_key,
                'authenticator_data' => $request->authenticator_data,
                'client_data_json' => $request->client_data_json,
                'attestation_object' => $request->attestation_object,
                'device_name' => $request->device_name ?? 'Unknown Device',
                'device_type' => $request->device_type ?? 'unknown',
                'counter' => 0,
                'is_active' => true,
                'last_used_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Biometric credential registered successfully',
                'data' => [
                    'credential_id' => $credential->credential_id,
                    'device_name' => $credential->device_name,
                    'created_at' => $credential->created_at
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to register biometric credential: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to register biometric credential'
            ], 500);
        }
    }

    /**
     * Authenticate using biometric credential
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'credential_id' => 'required|string',
            'authenticator_data' => 'required|string',
            'client_data_json' => 'required|string',
            'signature' => 'required|string',
            'user_handle' => 'nullable|string',
        ]);

        try {
            // Find the credential
            $credential = BiometricCredential::where('credential_id', $request->credential_id)
                ->where('is_active', true)
                ->first();

            if (!$credential) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid biometric credential'
                ], 401);
            }

            // Verify the signature (in production, implement proper WebAuthn verification)
            // This is a simplified verification for demo purposes
            $isValid = $this->verifySignature(
                $credential->public_key,
                $request->authenticator_data,
                $request->client_data_json,
                $request->signature
            );

            if (!$isValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid biometric signature'
                ], 401);
            }

            // Update credential usage
            $credential->update([
                'counter' => $credential->counter + 1,
                'last_used_at' => now(),
            ]);

            // Generate authentication token
            $user = $credential->user;
            $token = $user->createToken('biometric-auth')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Biometric authentication successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'credential_id' => $credential->credential_id,
                    'device_name' => $credential->device_name
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to authenticate with biometric: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Biometric authentication failed'
            ], 500);
        }
    }

    /**
     * Get user's biometric credentials
     */
    public function getUserCredentials(Request $request)
    {
        try {
            $user = $request->user();
            
            $credentials = BiometricCredential::where('user_id', $user->id)
                ->where('is_active', true)
                ->select([
                    'id',
                    'credential_id',
                    'device_name',
                    'device_type',
                    'counter',
                    'last_used_at',
                    'created_at'
                ])
                ->orderBy('last_used_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $credentials,
                'message' => 'Biometric credentials retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve biometric credentials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve biometric credentials'
            ], 500);
        }
    }

    /**
     * Revoke a biometric credential
     */
    public function revoke(Request $request, $credentialId)
    {
        try {
            $user = $request->user();
            
            $credential = BiometricCredential::where('credential_id', $credentialId)
                ->where('user_id', $user->id)
                ->first();

            if (!$credential) {
                return response()->json([
                    'success' => false,
                    'message' => 'Biometric credential not found'
                ], 404);
            }

            $credential->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Biometric credential revoked successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to revoke biometric credential: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke biometric credential'
            ], 500);
        }
    }

    /**
     * Get authentication options for WebAuthn
     */
    public function getAuthenticationOptions(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Get user's credentials
            $credentials = BiometricCredential::where('user_id', $user->id)
                ->where('is_active', true)
                ->pluck('credential_id')
                ->toArray();

            $challenge = base64_encode(random_bytes(32));
            
            // Store challenge temporarily (in production, use Redis or similar)
            cache()->put("webauthn_challenge_{$user->id}", $challenge, now()->addMinutes(5));

            $options = [
                'challenge' => $challenge,
                'timeout' => 60000,
                'rpId' => parse_url(config('app.url'), PHP_URL_HOST),
                'allowCredentials' => array_map(function ($credentialId) {
                    return [
                        'id' => $credentialId,
                        'type' => 'public-key',
                        'transports' => ['internal', 'usb', 'nfc', 'ble']
                    ];
                }, $credentials),
                'userVerification' => 'preferred'
            ];

            return response()->json([
                'success' => true,
                'data' => $options,
                'message' => 'Authentication options generated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate authentication options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate authentication options'
            ], 500);
        }
    }

    /**
     * Get registration options for WebAuthn
     */
    public function getRegistrationOptions(Request $request)
    {
        try {
            $user = $request->user();
            
            $challenge = base64_encode(random_bytes(32));
            
            // Store challenge temporarily
            cache()->put("webauthn_challenge_{$user->id}", $challenge, now()->addMinutes(5));

            $options = [
                'challenge' => $challenge,
                'rp' => [
                    'name' => config('app.name'),
                    'id' => parse_url(config('app.url'), PHP_URL_HOST),
                ],
                'user' => [
                    'id' => base64_encode($user->id),
                    'name' => $user->email,
                    'displayName' => $user->name,
                ],
                'pubKeyCredParams' => [
                    [
                        'type' => 'public-key',
                        'alg' => -7, // ES256
                    ],
                    [
                        'type' => 'public-key',
                        'alg' => -257, // RS256
                    ],
                ],
                'timeout' => 60000,
                'attestation' => 'direct',
                'excludeCredentials' => [],
                'authenticatorSelection' => [
                    'authenticatorAttachment' => 'platform',
                    'userVerification' => 'required',
                    'requireResidentKey' => false,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $options,
                'message' => 'Registration options generated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate registration options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate registration options'
            ], 500);
        }
    }

    /**
     * Verify signature (simplified for demo)
     * In production, implement proper WebAuthn signature verification
     */
    private function verifySignature($publicKey, $authenticatorData, $clientDataJson, $signature)
    {
        // This is a simplified verification for demo purposes
        // In production, implement proper WebAuthn signature verification
        // using libraries like web-auth/webauthn-lib
        
        // For demo, we'll return true if all required fields are present
        return !empty($publicKey) && !empty($authenticatorData) && 
               !empty($clientDataJson) && !empty($signature);
    }
}