<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LegalDocument;

class LegalDocumentsSeeder extends Seeder
{
    public function run()
    {
        $documents = [
            [
                'type' => 'terms_of_service',
                'title' => 'Terms of Service',
                'content' => $this->getTermsOfServiceContent(),
                'version' => '1.0',
                'is_active' => true,
                'effective_date' => now(),
                'metadata' => [
                    'last_reviewed' => now()->format('Y-m-d'),
                    'reviewer' => 'Legal Team',
                    'jurisdiction' => 'United States'
                ]
            ],
            [
                'type' => 'privacy_policy',
                'title' => 'Privacy Policy',
                'content' => $this->getPrivacyPolicyContent(),
                'version' => '1.0',
                'is_active' => true,
                'effective_date' => now(),
                'metadata' => [
                    'last_reviewed' => now()->format('Y-m-d'),
                    'reviewer' => 'Legal Team',
                    'compliance' => ['GDPR', 'CCPA', 'PIPEDA']
                ]
            ],
            [
                'type' => 'cookie_policy',
                'title' => 'Cookie Policy',
                'content' => $this->getCookiePolicyContent(),
                'version' => '1.0',
                'is_active' => true,
                'effective_date' => now(),
                'metadata' => [
                    'last_reviewed' => now()->format('Y-m-d'),
                    'reviewer' => 'Legal Team',
                    'compliance' => ['GDPR', 'ePrivacy Directive']
                ]
            ],
            [
                'type' => 'refund_policy',
                'title' => 'Refund Policy',
                'content' => $this->getRefundPolicyContent(),
                'version' => '1.0',
                'is_active' => true,
                'effective_date' => now(),
                'metadata' => [
                    'last_reviewed' => now()->format('Y-m-d'),
                    'reviewer' => 'Legal Team',
                    'billing_cycles' => ['monthly', 'annual']
                ]
            ],
            [
                'type' => 'accessibility_statement',
                'title' => 'Accessibility Statement',
                'content' => $this->getAccessibilityStatementContent(),
                'version' => '1.0',
                'is_active' => true,
                'effective_date' => now(),
                'metadata' => [
                    'last_reviewed' => now()->format('Y-m-d'),
                    'reviewer' => 'Legal Team',
                    'standards' => ['WCAG 2.1 AA', 'Section 508', 'ADA']
                ]
            ]
        ];

        foreach ($documents as $document) {
            LegalDocument::updateOrCreate(
                ['type' => $document['type']],
                $document
            );
        }
    }

    private function getTermsOfServiceContent()
    {
        return "1. ACCEPTANCE OF TERMS

By accessing and using the Mewayz platform, you accept and agree to be bound by the terms and provisions of this agreement.

2. USE LICENSE

Permission is granted to temporarily download one copy of the materials on Mewayz's platform for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:
- modify or copy the materials;
- use the materials for any commercial purpose or for any public display;
- attempt to decompile or reverse engineer any software contained on Mewayz's platform;
- remove any copyright or other proprietary notations from the materials.

3. DISCLAIMER

The materials on Mewayz's platform are provided on an 'as is' basis. Mewayz makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.

4. LIMITATIONS

In no event shall Mewayz or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Mewayz's platform.

5. ACCURACY OF MATERIALS

The materials appearing on Mewayz's platform could include technical, typographical, or photographic errors. Mewayz does not warrant that any of the materials on its platform are accurate, complete, or current.

6. MODIFICATIONS

Mewayz may revise these terms of service for its platform at any time without notice. By using this platform, you are agreeing to be bound by the then current version of these terms of service.

7. GOVERNING LAW

These terms and conditions are governed by and construed in accordance with the laws of the United States and you irrevocably submit to the exclusive jurisdiction of the courts in that state or location.";
    }

    private function getPrivacyPolicyContent()
    {
        return "1. INFORMATION WE COLLECT

We collect information you provide directly to us, such as when you create an account, use our services, contact us for support, or subscribe to our newsletter.

Personal Information:
- Name, email address, and phone number
- Payment information (processed securely by third-party providers)
- Profile information and preferences
- Communications with us

Usage Information:
- Device information (IP address, browser type, operating system)
- Usage patterns and preferences
- Log data and analytics
- Cookies and similar technologies

2. HOW WE USE YOUR INFORMATION

We use the information we collect to:
- Provide and improve our services
- Process transactions and send confirmations
- Communicate with you about your account and our services
- Personalize your experience
- Detect and prevent fraud
- Comply with legal obligations

3. INFORMATION SHARING

We do not sell, trade, or otherwise transfer your personal information to third parties except with your consent, to service providers, to comply with legal obligations, or to protect our rights and safety.

4. DATA SECURITY

We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.

5. YOUR RIGHTS

You have the right to access, correct, request deletion, object to processing, data portability, and withdraw consent regarding your personal information.

6. CONTACT US

If you have any questions about this Privacy Policy, please contact us at privacy@mewayz.com.";
    }

    private function getCookiePolicyContent()
    {
        return "1. WHAT ARE COOKIES?

Cookies are small text files that are placed on your device when you visit our platform. They help us provide you with a better experience by remembering your preferences and improving our services.

2. TYPES OF COOKIES WE USE

Essential Cookies: Necessary for the platform to function and cannot be switched off.
Performance Cookies: Help us measure and improve platform performance.
Functional Cookies: Enable enhanced functionality and personalization.
Targeting Cookies: Used to build a profile of your interests and show relevant advertisements.

3. HOW WE USE COOKIES

We use cookies for:
- Authentication and security
- User preferences and settings
- Analytics and performance monitoring
- Advertising and marketing
- Social media integration

4. MANAGING COOKIES

You can control cookies through your browser settings. Most browsers allow you to view, delete, and block cookies. However, disabling cookies may affect some features of our platform.

5. THIRD-PARTY COOKIES

We may use third-party services that set cookies:
- Google Analytics for usage analysis
- Social media platforms for sharing content
- Payment processors for secure transactions
- Customer support tools

6. CONTACT US

If you have any questions about our use of cookies, please contact us at privacy@mewayz.com.";
    }

    private function getRefundPolicyContent()
    {
        return "1. GENERAL REFUND POLICY

We want you to be completely satisfied with our services. If you are not satisfied, we offer refunds under the conditions outlined in this policy.

2. SUBSCRIPTION REFUNDS

Monthly Subscriptions:
- You may cancel at any time
- Refunds are available within 7 days of billing
- No refunds after 7 days from billing date
- Service continues until the end of the current billing period

Annual Subscriptions:
- Full refunds available within 30 days of purchase
- Prorated refunds available after 30 days (unused portion)
- Minimum usage requirements may apply

3. COURSE REFUNDS

For individual course purchases:
- Full refunds available within 14 days of purchase
- Must have completed less than 20% of the course
- No refunds after course completion
- Certificates must be returned/revoked for refunds

4. REFUND PROCESS

To request a refund:
1. Log into your account
2. Navigate to your billing/subscription page
3. Click 'Request Refund' or contact support
4. Provide reason for refund request
5. Wait for review and approval

5. REFUND TIMELINE

Approved refunds will be processed:
- Credit card refunds: 3-5 business days
- PayPal refunds: 1-2 business days
- Bank transfer refunds: 5-10 business days

6. CONTACT INFORMATION

For refund requests: billing@mewayz.com
Support: support@mewayz.com";
    }

    private function getAccessibilityStatementContent()
    {
        return "1. OUR COMMITMENT

Mewayz is committed to ensuring digital accessibility for people with disabilities. We are continually improving the user experience for everyone and applying the relevant accessibility standards.

2. STANDARDS

We aim to conform to the Web Content Accessibility Guidelines (WCAG) 2.1 Level AA standards.

3. CURRENT ACCESSIBILITY FEATURES

Keyboard Navigation:
- All interactive elements are keyboard accessible
- Logical tab order throughout the interface
- Visible focus indicators
- Skip links to main content

Screen Reader Support:
- Semantic HTML structure
- Proper heading hierarchy
- Alt text for images
- ARIA labels and descriptions

Visual Design:
- High contrast color schemes
- Scalable fonts and interface elements
- Clear visual hierarchy
- Consistent navigation patterns

4. ASSISTIVE TECHNOLOGY SUPPORT

Our platform is designed to work with:
- Screen readers (NVDA, JAWS, VoiceOver, TalkBack)
- Voice recognition software
- Keyboard navigation tools
- Switch navigation devices
- Magnification software

5. FEEDBACK AND ASSISTANCE

We welcome feedback on accessibility. If you encounter any accessibility barriers, please contact our accessibility team at accessibility@mewayz.com.

6. ONGOING EFFORTS

We are actively working to improve accessibility through:
- Regular accessibility audits
- User testing with people with disabilities
- Staff training on accessibility best practices
- Continuous monitoring and updates

This statement reflects our current accessibility status and is reviewed regularly to ensure it remains accurate and current.";
    }
}