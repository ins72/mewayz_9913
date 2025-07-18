@extends('layouts.app')

@section('title', 'Cookie Policy')
@section('meta_description', 'Learn about how Mewayz uses cookies and similar technologies.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-900 mb-4">Cookie Policy</h1>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                    This policy explains how we use cookies and similar technologies on our platform.
                </p>
                @if($document && $document->effective_date)
                    <p class="text-sm text-slate-500 mt-4">
                        Last updated: {{ $document->effective_date->format('F j, Y') }}
                        | Version: {{ $document->version }}
                    </p>
                @endif
            </div>

            <!-- Cookie Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
                @if($document && $document->content)
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($document->content)) !!}
                    </div>
                @else
                    <div class="prose prose-lg max-w-none">
                        <h2>1. What Are Cookies?</h2>
                        <p>Cookies are small text files that are placed on your device when you visit our platform. They help us provide you with a better experience by remembering your preferences and improving our services.</p>
                        
                        <h2>2. Types of Cookies We Use</h2>
                        
                        <h3>Essential Cookies</h3>
                        <p>These cookies are necessary for the platform to function and cannot be switched off in our systems. They are usually only set in response to actions made by you which amount to a request for services, such as setting your privacy preferences, logging in, or filling in forms.</p>
                        
                        <h3>Performance Cookies</h3>
                        <p>These cookies allow us to count visits and traffic sources so we can measure and improve the performance of our platform. They help us to know which pages are the most and least popular and see how visitors move around the platform.</p>
                        
                        <h3>Functional Cookies</h3>
                        <p>These cookies enable the platform to provide enhanced functionality and personalization. They may be set by us or by third-party providers whose services we have added to our pages.</p>
                        
                        <h3>Targeting Cookies</h3>
                        <p>These cookies may be set through our platform by our advertising partners. They may be used by those companies to build a profile of your interests and show you relevant adverts on other sites.</p>
                        
                        <h2>3. How We Use Cookies</h2>
                        <p>We use cookies for various purposes:</p>
                        <ul>
                            <li>Authentication and security</li>
                            <li>User preferences and settings</li>
                            <li>Analytics and performance monitoring</li>
                            <li>Advertising and marketing</li>
                            <li>Social media integration</li>
                        </ul>
                        
                        <h2>4. Third-Party Cookies</h2>
                        <p>We may use third-party services that set cookies on our behalf:</p>
                        <ul>
                            <li>Google Analytics for usage analysis</li>
                            <li>Social media platforms for sharing content</li>
                            <li>Payment processors for secure transactions</li>
                            <li>Customer support tools</li>
                        </ul>
                        
                        <h2>5. Managing Cookies</h2>
                        <p>You can control and manage cookies in several ways:</p>
                        
                        <h3>Browser Settings</h3>
                        <p>Most browsers allow you to:</p>
                        <ul>
                            <li>View cookies stored on your device</li>
                            <li>Delete cookies</li>
                            <li>Block cookies from specific sites</li>
                            <li>Block all cookies</li>
                        </ul>
                        
                        <h3>Cookie Consent</h3>
                        <p>When you first visit our platform, we'll ask for your consent to use cookies. You can change your preferences at any time through our cookie consent manager.</p>
                        
                        <h2>6. Impact of Disabling Cookies</h2>
                        <p>If you disable cookies, some features of our platform may not work properly:</p>
                        <ul>
                            <li>You may need to re-enter information repeatedly</li>
                            <li>Some personalization features may not work</li>
                            <li>You may have trouble logging in</li>
                            <li>Analytics and performance improvements may be affected</li>
                        </ul>
                        
                        <h2>7. Updates to This Policy</h2>
                        <p>We may update this Cookie Policy from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons.</p>
                        
                        <h2>8. Contact Us</h2>
                        <p>If you have any questions about our use of cookies, please contact us at:</p>
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