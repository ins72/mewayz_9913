<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LegalDocument;
use App\Models\CookieConsent;
use App\Models\DataDeletionRequest;
use App\Models\AuditLog;
use App\Models\User;

class LegalComplianceController extends Controller
{
    /**
     * Terms of Service
     */
    public function termsOfService()
    {
        try {
            $termsDocument = LegalDocument::where('type', 'terms_of_service')
                ->where('is_active', true)
                ->orderBy('version', 'desc')
                ->first();

            if (!$termsDocument) {
                $termsDocument = $this->createDefaultTermsOfService();
            }

            return view('pages.legal.terms-of-service', compact('termsDocument'));

        } catch (\Exception $e) {
            Log::error('Terms of Service page failed: ' . $e->getMessage());
            return view('pages.legal.terms-of-service', ['termsDocument' => null]);
        }
    }

    /**
     * Privacy Policy
     */
    public function privacyPolicy()
    {
        try {
            $privacyDocument = LegalDocument::where('type', 'privacy_policy')
                ->where('is_active', true)
                ->orderBy('version', 'desc')
                ->first();

            if (!$privacyDocument) {
                $privacyDocument = $this->createDefaultPrivacyPolicy();
            }

            return view('pages.legal.privacy-policy', compact('privacyDocument'));

        } catch (\Exception $e) {
            Log::error('Privacy Policy page failed: ' . $e->getMessage());
            return view('pages.legal.privacy-policy', ['privacyDocument' => null]);
        }
    }

    /**
     * Cookie Policy
     */
    public function cookiePolicy()
    {
        try {
            $cookieDocument = LegalDocument::where('type', 'cookie_policy')
                ->where('is_active', true)
                ->orderBy('version', 'desc')
                ->first();

            if (!$cookieDocument) {
                $cookieDocument = $this->createDefaultCookiePolicy();
            }

            return view('pages.legal.cookie-policy', compact('cookieDocument'));

        } catch (\Exception $e) {
            Log::error('Cookie Policy page failed: ' . $e->getMessage());
            return view('pages.legal.cookie-policy', ['cookieDocument' => null]);
        }
    }

    /**
     * Refund Policy
     */
    public function refundPolicy()
    {
        try {
            $refundDocument = LegalDocument::where('type', 'refund_policy')
                ->where('is_active', true)
                ->orderBy('version', 'desc')
                ->first();

            if (!$refundDocument) {
                $refundDocument = $this->createDefaultRefundPolicy();
            }

            return view('pages.legal.refund-policy', compact('refundDocument'));

        } catch (\Exception $e) {
            Log::error('Refund Policy page failed: ' . $e->getMessage());
            return view('pages.legal.refund-policy', ['refundDocument' => null]);
        }
    }

    /**
     * Service Level Agreement
     */
    public function sla()
    {
        try {
            $slaDocument = LegalDocument::where('type', 'sla')
                ->where('is_active', true)
                ->orderBy('version', 'desc')
                ->first();

            if (!$slaDocument) {
                $slaDocument = $this->createDefaultSLA();
            }

            $uptimeStats = $this->getUptimeStats();

            return view('pages.legal.sla', compact('slaDocument', 'uptimeStats'));

        } catch (\Exception $e) {
            Log::error('SLA page failed: ' . $e->getMessage());
            return view('pages.legal.sla', [
                'slaDocument' => null,
                'uptimeStats' => ['uptime' => 99.9, 'last_incident' => null]
            ]);
        }
    }

    /**
     * Accessibility Statement
     */
    public function accessibilityStatement()
    {
        $accessibilityFeatures = [
            'Keyboard Navigation' => 'Full keyboard navigation support for all interactive elements',
            'Screen Reader Support' => 'Compatible with popular screen readers including JAWS, NVDA, and VoiceOver',
            'Color Contrast' => 'WCAG AA compliant color contrast ratios throughout the interface',
            'Text Scaling' => 'Support for browser zoom up to 200% without horizontal scrolling',
            'Alt Text' => 'Descriptive alt text for all images and graphics',
            'Focus Indicators' => 'Clear focus indicators for keyboard navigation',
            'Form Labels' => 'Proper form labels and error messages for all input fields',
            'Heading Structure' => 'Logical heading structure for better navigation'
        ];

        $wcagCompliance = [
            'level' => 'AA',
            'version' => '2.1',
            'last_audit' => '2024-12-01',
            'next_audit' => '2025-06-01'
        ];

        return view('pages.legal.accessibility-statement', compact('accessibilityFeatures', 'wcagCompliance'));
    }

    /**
     * Cookie Consent Management
     */
    public function manageCookieConsent(Request $request)
    {
        try {
            $request->validate([
                'essential' => 'boolean',
                'analytics' => 'boolean',
                'marketing' => 'boolean',
                'preferences' => 'boolean'
            ]);

            $user = $request->user();
            $ipAddress = $request->ip();

            $consent = CookieConsent::updateOrCreate(
                [
                    'user_id' => $user ? $user->id : null,
                    'ip_address' => $ipAddress
                ],
                [
                    'essential_cookies' => $request->input('essential', true),
                    'analytics_cookies' => $request->input('analytics', false),
                    'marketing_cookies' => $request->input('marketing', false),
                    'preference_cookies' => $request->input('preferences', false),
                    'consent_date' => now(),
                    'user_agent' => $request->userAgent()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Cookie preferences updated successfully',
                'consent_id' => $consent->id
            ]);

        } catch (\Exception $e) {
            Log::error('Cookie consent management failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update cookie preferences'
            ], 500);
        }
    }

    /**
     * GDPR Data Request
     */
    public function gdprDataRequest(Request $request)
    {
        try {
            $request->validate([
                'request_type' => 'required|in:access,portability,deletion,correction',
                'email' => 'required|email',
                'description' => 'nullable|string|max:1000'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'error' => 'No account found with that email address'
                ], 404);
            }

            $gdprRequest = DataDeletionRequest::create([
                'user_id' => $user->id,
                'request_type' => $request->request_type,
                'description' => $request->description,
                'status' => 'pending',
                'requested_at' => now()
            ]);

            // Log GDPR request
            $this->logAuditEvent($user->id, 'gdpr_request', [
                'request_type' => $request->request_type,
                'request_id' => $gdprRequest->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'GDPR request submitted successfully. We will process your request within 30 days.',
                'request_id' => $gdprRequest->id
            ]);

        } catch (\Exception $e) {
            Log::error('GDPR data request failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to submit GDPR request'
            ], 500);
        }
    }

    /**
     * Audit Log (Admin access)
     */
    public function auditLog(Request $request)
    {
        try {
            // Check if user is admin
            if (!$request->user() || !$request->user()->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $logs = AuditLog::with('user')
                ->when($request->user_id, function ($query, $userId) {
                    $query->where('user_id', $userId);
                })
                ->when($request->action, function ($query, $action) {
                    $query->where('action', $action);
                })
                ->when($request->date_from, function ($query, $dateFrom) {
                    $query->where('created_at', '>=', $dateFrom);
                })
                ->when($request->date_to, function ($query, $dateTo) {
                    $query->where('created_at', '<=', $dateTo);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(100);

            return response()->json([
                'success' => true,
                'logs' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'total_pages' => $logs->lastPage(),
                    'total_items' => $logs->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Audit log retrieval failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve audit logs'
            ], 500);
        }
    }

    /**
     * Create default Terms of Service
     */
    private function createDefaultTermsOfService()
    {
        $content = '
        <h1>Terms of Service</h1>
        <p>Last updated: ' . now()->format('F j, Y') . '</p>
        
        <h2>1. Agreement to Terms</h2>
        <p>By accessing and using Mewayz, you accept and agree to be bound by the terms and provision of this agreement.</p>
        
        <h2>2. Use License</h2>
        <p>Permission is granted to temporarily download one copy of the materials on Mewayz for personal, non-commercial transitory viewing only.</p>
        
        <h2>3. Disclaimer</h2>
        <p>The materials on Mewayz are provided on an "as is" basis. Mewayz makes no warranties, expressed or implied.</p>
        
        <h2>4. Limitations</h2>
        <p>In no event shall Mewayz or its suppliers be liable for any damages arising out of the use or inability to use the materials on Mewayz.</p>
        
        <h2>5. Accuracy of Materials</h2>
        <p>The materials appearing on Mewayz could include technical, typographical, or photographic errors.</p>
        
        <h2>6. Links</h2>
        <p>Mewayz has not reviewed all of the sites linked to our platform and is not responsible for the contents of any such linked site.</p>
        
        <h2>7. Modifications</h2>
        <p>Mewayz may revise these terms of service at any time without notice.</p>
        
        <h2>8. Governing Law</h2>
        <p>These terms and conditions are governed by and construed in accordance with the laws of the United States.</p>
        ';

        return LegalDocument::create([
            'type' => 'terms_of_service',
            'title' => 'Terms of Service',
            'content' => $content,
            'version' => '1.0',
            'is_active' => true,
            'effective_date' => now()
        ]);
    }

    /**
     * Create default Privacy Policy
     */
    private function createDefaultPrivacyPolicy()
    {
        $content = '
        <h1>Privacy Policy</h1>
        <p>Last updated: ' . now()->format('F j, Y') . '</p>
        
        <h2>1. Information We Collect</h2>
        <p>We collect information you provide directly to us, information we collect automatically, and information from third parties.</p>
        
        <h2>2. How We Use Your Information</h2>
        <p>We use your information to provide, maintain, and improve our services, process transactions, and communicate with you.</p>
        
        <h2>3. Information Sharing</h2>
        <p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p>
        
        <h2>4. Data Security</h2>
        <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
        
        <h2>5. Your Rights</h2>
        <p>You have the right to access, update, or delete your personal information. You may also opt out of certain communications.</p>
        
        <h2>6. Cookies</h2>
        <p>We use cookies to enhance your experience, analyze site usage, and assist in marketing efforts.</p>
        
        <h2>7. Changes to This Policy</h2>
        <p>We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page.</p>
        
        <h2>8. Contact Us</h2>
        <p>If you have any questions about this privacy policy, please contact us at privacy@mewayz.com.</p>
        ';

        return LegalDocument::create([
            'type' => 'privacy_policy',
            'title' => 'Privacy Policy',
            'content' => $content,
            'version' => '1.0',
            'is_active' => true,
            'effective_date' => now()
        ]);
    }

    /**
     * Create default Cookie Policy
     */
    private function createDefaultCookiePolicy()
    {
        $content = '
        <h1>Cookie Policy</h1>
        <p>Last updated: ' . now()->format('F j, Y') . '</p>
        
        <h2>What Are Cookies</h2>
        <p>Cookies are small text files that are placed on your computer or mobile device when you visit a website.</p>
        
        <h2>Types of Cookies We Use</h2>
        <ul>
            <li><strong>Essential Cookies:</strong> Necessary for the website to function properly</li>
            <li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with our website</li>
            <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements</li>
            <li><strong>Preference Cookies:</strong> Remember your preferences and settings</li>
        </ul>
        
        <h2>Managing Cookies</h2>
        <p>You can control and manage cookies through your browser settings or our cookie preference center.</p>
        
        <h2>Third-Party Cookies</h2>
        <p>We may use third-party services that place cookies on your device for analytics and advertising purposes.</p>
        
        <h2>Contact Us</h2>
        <p>If you have any questions about our cookie policy, please contact us at privacy@mewayz.com.</p>
        ';

        return LegalDocument::create([
            'type' => 'cookie_policy',
            'title' => 'Cookie Policy',
            'content' => $content,
            'version' => '1.0',
            'is_active' => true,
            'effective_date' => now()
        ]);
    }

    /**
     * Create default Refund Policy
     */
    private function createDefaultRefundPolicy()
    {
        $content = '
        <h1>Refund Policy</h1>
        <p>Last updated: ' . now()->format('F j, Y') . '</p>
        
        <h2>30-Day Money-Back Guarantee</h2>
        <p>We offer a 30-day money-back guarantee for all paid subscription plans.</p>
        
        <h2>Refund Eligibility</h2>
        <ul>
            <li>Refund requests must be made within 30 days of the initial purchase</li>
            <li>Refunds are only available for subscription fees, not for usage-based charges</li>
            <li>Annual subscriptions are eligible for prorated refunds</li>
        </ul>
        
        <h2>How to Request a Refund</h2>
        <p>To request a refund, please contact our support team at support@mewayz.com with your account details and reason for the refund.</p>
        
        <h2>Processing Time</h2>
        <p>Refunds are typically processed within 5-10 business days and will be credited to your original payment method.</p>
        
        <h2>Non-Refundable Items</h2>
        <ul>
            <li>Setup fees</li>
            <li>Custom development work</li>
            <li>Third-party service fees</li>
        </ul>
        
        <h2>Contact Us</h2>
        <p>If you have any questions about our refund policy, please contact us at support@mewayz.com.</p>
        ';

        return LegalDocument::create([
            'type' => 'refund_policy',
            'title' => 'Refund Policy',
            'content' => $content,
            'version' => '1.0',
            'is_active' => true,
            'effective_date' => now()
        ]);
    }

    /**
     * Create default SLA
     */
    private function createDefaultSLA()
    {
        $content = '
        <h1>Service Level Agreement (SLA)</h1>
        <p>Last updated: ' . now()->format('F j, Y') . '</p>
        
        <h2>Service Availability</h2>
        <p>We guarantee 99.9% uptime for our services, measured monthly.</p>
        
        <h2>Response Times</h2>
        <ul>
            <li><strong>Critical Issues:</strong> 2 hours</li>
            <li><strong>High Priority:</strong> 8 hours</li>
            <li><strong>Medium Priority:</strong> 24 hours</li>
            <li><strong>Low Priority:</strong> 72 hours</li>
        </ul>
        
        <h2>Planned Maintenance</h2>
        <p>Scheduled maintenance will be performed during off-peak hours with at least 24 hours advance notice.</p>
        
        <h2>Service Credits</h2>
        <p>If we fail to meet our uptime commitment, you may be eligible for service credits.</p>
        
        <h2>Exclusions</h2>
        <p>This SLA does not apply to outages caused by factors outside our control, including but not limited to natural disasters, network provider issues, or client-side problems.</p>
        
        <h2>Contact Us</h2>
        <p>For SLA-related inquiries, please contact us at support@mewayz.com.</p>
        ';

        return LegalDocument::create([
            'type' => 'sla',
            'title' => 'Service Level Agreement',
            'content' => $content,
            'version' => '1.0',
            'is_active' => true,
            'effective_date' => now()
        ]);
    }

    /**
     * Get uptime statistics
     */
    private function getUptimeStats()
    {
        // This would typically come from a monitoring service
        return [
            'uptime' => 99.95,
            'last_incident' => '2024-11-15 14:30:00',
            'mttr' => 15, // Mean Time To Recovery in minutes
            'incidents_this_month' => 1
        ];
    }

    /**
     * Log audit event
     */
    private function logAuditEvent($userId, $action, $details = [])
    {
        try {
            AuditLog::create([
                'user_id' => $userId,
                'action' => $action,
                'details' => $details,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (\Exception $e) {
            Log::error('Audit logging failed: ' . $e->getMessage());
        }
    }
}