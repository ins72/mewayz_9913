@extends('layouts.app')

@section('title', 'Privacy Policy - Mewayz')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">Privacy Policy</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                    Your privacy is important to us. Learn how we collect, use, and protect your information.
                </p>
                @if($document)
                <p class="text-lg opacity-90">
                    Last updated: {{ $document->updated_at->format('F j, Y') }}
                </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Privacy Content -->
    <div class="py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                @if($document && $document->content)
                    <div class="prose prose-lg max-w-none">
                        {!! $document->content !!}
                    </div>
                @else
                    <div class="prose prose-lg max-w-none">
                        <h2>1. Information We Collect</h2>
                        <p>We collect information you provide directly to us, such as when you create an account, use our services, or contact us for support.</p>
                        
                        <h3>Personal Information</h3>
                        <ul>
                            <li>Name and contact information</li>
                            <li>Account credentials</li>
                            <li>Payment information</li>
                            <li>Profile information</li>
                        </ul>

                        <h3>Usage Information</h3>
                        <ul>
                            <li>How you interact with our services</li>
                            <li>Features you use</li>
                            <li>Time and frequency of use</li>
                            <li>Device and browser information</li>
                        </ul>

                        <h2>2. How We Use Your Information</h2>
                        <p>We use the information we collect to:</p>
                        <ul>
                            <li>Provide, maintain, and improve our services</li>
                            <li>Process transactions and send related information</li>
                            <li>Send technical notices and support messages</li>
                            <li>Communicate with you about products, services, and events</li>
                            <li>Monitor and analyze trends and usage</li>
                            <li>Detect, investigate, and prevent fraudulent transactions</li>
                        </ul>

                        <h2>3. Information Sharing</h2>
                        <p>We do not sell, trade, or otherwise transfer your personal information to third parties except as described in this policy:</p>
                        <ul>
                            <li>With your consent</li>
                            <li>To comply with legal obligations</li>
                            <li>To protect our rights and safety</li>
                            <li>With service providers who assist us in operating our platform</li>
                        </ul>

                        <h2>4. Data Security</h2>
                        <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

                        <h2>5. Data Retention</h2>
                        <p>We retain your information for as long as your account is active or as needed to provide you services. We may retain certain information as required by law or for legitimate business purposes.</p>

                        <h2>6. Your Rights</h2>
                        <p>You have the right to:</p>
                        <ul>
                            <li>Access your personal information</li>
                            <li>Correct inaccurate information</li>
                            <li>Delete your information</li>
                            <li>Object to processing</li>
                            <li>Data portability</li>
                        </ul>

                        <h2>7. Cookies and Tracking</h2>
                        <p>We use cookies and similar technologies to enhance your experience, analyze usage, and provide personalized content. You can control cookie settings through your browser.</p>

                        <h2>8. Third-Party Services</h2>
                        <p>Our service may contain links to third-party websites or services. We are not responsible for the privacy practices of these third parties.</p>

                        <h2>9. Children's Privacy</h2>
                        <p>Our services are not intended for children under 13. We do not knowingly collect personal information from children under 13.</p>

                        <h2>10. International Data Transfers</h2>
                        <p>Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place for such transfers.</p>

                        <h2>11. Changes to This Policy</h2>
                        <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new policy on this page.</p>

                        <h2>12. Contact Us</h2>
                        <p>If you have any questions about this Privacy Policy, please contact us at:</p>
                        <p>
                            Email: privacy@mewayz.com<br>
                            Address: 123 Business St, Suite 100, City, State 12345
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection