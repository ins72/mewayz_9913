@extends('layouts.app')

@section('title', 'Terms of Service - Mewayz')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">Terms of Service</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                    Please read these terms carefully before using our services.
                </p>
                @if($document)
                <p class="text-lg opacity-90">
                    Last updated: {{ $document->updated_at->format('F j, Y') }}
                </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Terms Content -->
    <div class="py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                @if($document && $document->content)
                    <div class="prose prose-lg max-w-none">
                        {!! $document->content !!}
                    </div>
                @else
                    <div class="prose prose-lg max-w-none">
                        <h2>1. Acceptance of Terms</h2>
                        <p>By accessing and using Mewayz ("the Service"), you accept and agree to be bound by the terms and provision of this agreement.</p>

                        <h2>2. Description of Service</h2>
                        <p>Mewayz is a comprehensive digital presence management platform that allows users to create, manage, and optimize their online presence across multiple channels.</p>

                        <h2>3. User Accounts</h2>
                        <p>To access certain features of the Service, you must register for an account. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>

                        <h2>4. Acceptable Use</h2>
                        <p>You agree to use the Service only for lawful purposes and in accordance with these Terms. You agree not to:</p>
                        <ul>
                            <li>Use the Service for any illegal or unauthorized purpose</li>
                            <li>Violate any applicable laws or regulations</li>
                            <li>Infringe upon the rights of others</li>
                            <li>Upload or transmit malicious code or content</li>
                            <li>Attempt to gain unauthorized access to the Service</li>
                        </ul>

                        <h2>5. Content and Intellectual Property</h2>
                        <p>You retain ownership of any content you submit to the Service. However, by submitting content, you grant us a worldwide, non-exclusive, royalty-free license to use, reproduce, and distribute your content in connection with the Service.</p>

                        <h2>6. Privacy</h2>
                        <p>Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the Service, to understand our practices.</p>

                        <h2>7. Payment Terms</h2>
                        <p>Certain features of the Service may require payment. You agree to pay all fees associated with your use of paid features. All fees are non-refundable unless otherwise specified.</p>

                        <h2>8. Termination</h2>
                        <p>We may terminate or suspend your account and access to the Service at our sole discretion, without prior notice, for conduct that we believe violates these Terms or is harmful to other users, us, or third parties.</p>

                        <h2>9. Disclaimers</h2>
                        <p>The Service is provided "as is" without any warranties, express or implied. We do not warrant that the Service will be uninterrupted, error-free, or completely secure.</p>

                        <h2>10. Limitation of Liability</h2>
                        <p>In no event shall Mewayz be liable for any indirect, incidental, special, consequential, or punitive damages arising out of or relating to your use of the Service.</p>

                        <h2>11. Changes to Terms</h2>
                        <p>We reserve the right to modify these Terms at any time. We will notify users of any material changes via email or through the Service.</p>

                        <h2>12. Contact Information</h2>
                        <p>If you have any questions about these Terms, please contact us at:</p>
                        <p>
                            Email: legal@mewayz.com<br>
                            Address: 123 Business St, Suite 100, City, State 12345
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection