@extends('layouts.app')

@section('title', 'Privacy Policy')
@section('meta_description', 'Learn how Mewayz collects, uses, and protects your personal information.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-900 mb-4">Privacy Policy</h1>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                    Your privacy is important to us. This policy outlines how we collect, use, and protect your information.
                </p>
                @if($document && $document->effective_date)
                    <p class="text-sm text-slate-500 mt-4">
                        Last updated: {{ $document->effective_date->format('F j, Y') }}
                        | Version: {{ $document->version }}
                    </p>
                @endif
            </div>

            <!-- Privacy Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
                @if($document && $document->content)
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($document->content)) !!}
                    </div>
                @else
                    <div class="prose prose-lg max-w-none">
                        <h2>1. Information We Collect</h2>
                        <p>We collect information you provide directly to us, such as when you:</p>
                        <ul>
                            <li>Create an account</li>
                            <li>Use our services</li>
                            <li>Contact us for support</li>
                            <li>Subscribe to our newsletter</li>
                            <li>Participate in surveys or promotions</li>
                        </ul>
                        
                        <h3>Personal Information</h3>
                        <p>This may include:</p>
                        <ul>
                            <li>Name, email address, and phone number</li>
                            <li>Payment information (processed securely by third-party providers)</li>
                            <li>Profile information and preferences</li>
                            <li>Communications with us</li>
                        </ul>
                        
                        <h3>Usage Information</h3>
                        <p>We automatically collect certain information about your use of our platform:</p>
                        <ul>
                            <li>Device information (IP address, browser type, operating system)</li>
                            <li>Usage patterns and preferences</li>
                            <li>Log data and analytics</li>
                            <li>Cookies and similar technologies</li>
                        </ul>
                        
                        <h2>2. How We Use Your Information</h2>
                        <p>We use the information we collect to:</p>
                        <ul>
                            <li>Provide and improve our services</li>
                            <li>Process transactions and send confirmations</li>
                            <li>Communicate with you about your account and our services</li>
                            <li>Personalize your experience</li>
                            <li>Detect and prevent fraud</li>
                            <li>Comply with legal obligations</li>
                        </ul>
                        
                        <h2>3. Information Sharing</h2>
                        <p>We do not sell, trade, or otherwise transfer your personal information to third parties except:</p>
                        <ul>
                            <li>With your consent</li>
                            <li>To service providers who assist us in operating our platform</li>
                            <li>To comply with legal obligations</li>
                            <li>To protect our rights and safety</li>
                        </ul>
                        
                        <h2>4. Data Security</h2>
                        <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                        
                        <h2>5. Data Retention</h2>
                        <p>We retain your personal information only for as long as necessary to fulfill the purposes outlined in this privacy policy, unless a longer retention period is required by law.</p>
                        
                        <h2>6. Your Rights</h2>
                        <p>You have the right to:</p>
                        <ul>
                            <li>Access your personal information</li>
                            <li>Correct inaccurate information</li>
                            <li>Request deletion of your information</li>
                            <li>Object to processing</li>
                            <li>Data portability</li>
                            <li>Withdraw consent</li>
                        </ul>
                        
                        <h2>7. Cookies</h2>
                        <p>We use cookies and similar technologies to enhance your experience. You can control cookies through your browser settings.</p>
                        
                        <h2>8. Third-Party Links</h2>
                        <p>Our platform may contain links to third-party websites. We are not responsible for the privacy practices of these external sites.</p>
                        
                        <h2>9. International Data Transfers</h2>
                        <p>Your information may be transferred to and processed in countries other than your country of residence. We ensure appropriate safeguards are in place.</p>
                        
                        <h2>10. Updates to This Policy</h2>
                        <p>We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page.</p>
                        
                        <h2>11. Contact Us</h2>
                        <p>If you have any questions about this Privacy Policy, please contact us at:</p>
                        <p><strong>Email:</strong> privacy@mewayz.com<br>
                        <strong>Address:</strong> [Your Company Address]</p>
                    </div>
                @endif
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-8">
                <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection