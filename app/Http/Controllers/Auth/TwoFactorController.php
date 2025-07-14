<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generate 2FA secret for user
     */
    public function generate(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is already enabled'
            ], 400);
        }

        $secret = $this->google2fa->generateSecretKey();
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Store the secret temporarily (not yet enabled)
        $user->update([
            'two_factor_secret' => Crypt::encrypt($secret)
        ]);

        return response()->json([
            'success' => true,
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'manual_entry_key' => $secret,
            'message' => 'Scan the QR code with your authenticator app, then verify with a code to enable 2FA'
        ]);
    }

    /**
     * Enable 2FA after verification
     */
    public function enable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json([
                'success' => false,
                'message' => 'No 2FA secret found. Please generate a secret first.'
            ], 400);
        }

        if ($user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is already enabled'
            ], 400);
        }

        $secret = Crypt::decrypt($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code'
            ], 400);
        }

        // Generate recovery codes
        $recoveryCodes = $this->createRecoveryCodes();

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_recovery_codes' => $recoveryCodes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Two-factor authentication enabled successfully',
            'recovery_codes' => $recoveryCodes
        ]);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not enabled'
            ], 400);
        }

        $secret = Crypt::decrypt($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code'
            ], 400);
        }

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Two-factor authentication disabled successfully'
        ]);
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|min:6|max:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not enabled'
            ], 400);
        }

        $secret = Crypt::decrypt($user->two_factor_secret);
        $code = $request->code;

        // Check if it's a recovery code
        if (strlen($code) > 6) {
            $valid = $this->verifyRecoveryCode($user, $code);
        } else {
            $valid = $this->google2fa->verifyKey($secret, $code);
        }

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Two-factor authentication verified successfully'
        ]);
    }

    /**
     * Generate new recovery codes
     */
    public function generateRecoveryCodes(Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not enabled'
            ], 400);
        }

        $recoveryCodes = $this->createRecoveryCodes();

        $user->update([
            'two_factor_recovery_codes' => $recoveryCodes
        ]);

        return response()->json([
            'success' => true,
            'recovery_codes' => $recoveryCodes,
            'message' => 'New recovery codes generated successfully'
        ]);
    }

    /**
     * Get 2FA status
     */
    public function status(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'two_factor_enabled' => $user->two_factor_enabled,
            'has_recovery_codes' => $user->two_factor_recovery_codes ? true : false,
            'recovery_codes_count' => $user->two_factor_recovery_codes ? count($user->two_factor_recovery_codes) : 0
        ]);
    }

    /**
     * Generate recovery codes
     */
    private function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = Str::random(10);
        }
        return $codes;
    }

    /**
     * Verify recovery code
     */
    private function verifyRecoveryCode($user, $code)
    {
        $recoveryCodes = $user->two_factor_recovery_codes;

        if (!$recoveryCodes || !in_array($code, $recoveryCodes)) {
            return false;
        }

        // Remove used recovery code
        $updatedCodes = array_filter($recoveryCodes, function($recoveryCode) use ($code) {
            return $recoveryCode !== $code;
        });

        $user->update([
            'two_factor_recovery_codes' => array_values($updatedCodes)
        ]);

        return true;
    }
}
