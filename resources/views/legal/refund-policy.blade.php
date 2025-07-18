@extends('layouts.app')

@section('title', 'Refund Policy')
@section('meta_description', 'Learn about our refund policy and how to request refunds for Mewayz services.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-900 mb-4">Refund Policy</h1>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                    Our refund policy outlines when and how you can request refunds for our services.
                </p>
                @if($document && $document->effective_date)
                    <p class="text-sm text-slate-500 mt-4">
                        Last updated: {{ $document->effective_date->format('F j, Y') }}
                        | Version: {{ $document->version }}
                    </p>
                @endif
            </div>

            <!-- Refund Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
                @if($document && $document->content)
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($document->content)) !!}
                    </div>
                @else
                    <div class="prose prose-lg max-w-none">
                        <h2>1. General Refund Policy</h2>
                        <p>We want you to be completely satisfied with our services. If you are not satisfied, we offer refunds under the conditions outlined in this policy.</p>
                        
                        <h2>2. Subscription Refunds</h2>
                        
                        <h3>Monthly Subscriptions</h3>
                        <p>For monthly subscriptions:</p>
                        <ul>
                            <li>You may cancel at any time</li>
                            <li>Refunds are available within 7 days of billing</li>
                            <li>No refunds after 7 days from billing date</li>
                            <li>Service continues until the end of the current billing period</li>
                        </ul>
                        
                        <h3>Annual Subscriptions</h3>
                        <p>For annual subscriptions:</p>
                        <ul>
                            <li>Full refunds available within 30 days of purchase</li>
                            <li>Prorated refunds available after 30 days (unused portion)</li>
                            <li>Minimum usage requirements may apply</li>
                        </ul>
                        
                        <h2>3. Course Refunds</h2>
                        <p>For individual course purchases:</p>
                        <ul>
                            <li>Full refunds available within 14 days of purchase</li>
                            <li>Must have completed less than 20% of the course</li>
                            <li>No refunds after course completion</li>
                            <li>Certificates must be returned/revoked for refunds</li>
                        </ul>
                        
                        <h2>4. Digital Product Refunds</h2>
                        <p>For digital products and downloads:</p>
                        <ul>
                            <li>Refunds available within 48 hours of purchase</li>
                            <li>Product must not have been downloaded</li>
                            <li>Defective products eligible for full refund</li>
                        </ul>
                        
                        <h2>5. Service Refunds</h2>
                        <p>For professional services:</p>
                        <ul>
                            <li>Refunds available before service delivery begins</li>
                            <li>Partial refunds available for incomplete services</li>
                            <li>Custom work may have different refund terms</li>
                        </ul>
                        
                        <h2>6. Conditions for Refunds</h2>
                        <p>Refunds are subject to the following conditions:</p>
                        <ul>
                            <li>Request must be made within the applicable time frame</li>
                            <li>Account must be in good standing</li>
                            <li>No violation of terms of service</li>
                            <li>Proper documentation may be required</li>
                        </ul>
                        
                        <h2>7. Non-Refundable Items</h2>
                        <p>The following items are not eligible for refunds:</p>
                        <ul>
                            <li>Completed courses with certificates issued</li>
                            <li>Downloaded digital products</li>
                            <li>Custom development work after delivery</li>
                            <li>Services already rendered</li>
                            <li>Fees for violated terms of service</li>
                        </ul>
                        
                        <h2>8. Refund Process</h2>
                        <p>To request a refund:</p>
                        <ol>
                            <li>Log into your account</li>
                            <li>Navigate to your billing/subscription page</li>
                            <li>Click "Request Refund" or contact support</li>
                            <li>Provide reason for refund request</li>
                            <li>Wait for review and approval</li>
                        </ol>
                        
                        <h2>9. Refund Timeline</h2>
                        <p>Approved refunds will be processed:</p>
                        <ul>
                            <li>Credit card refunds: 3-5 business days</li>
                            <li>PayPal refunds: 1-2 business days</li>
                            <li>Bank transfer refunds: 5-10 business days</li>
                            <li>Other payment methods: varies by provider</li>
                        </ul>
                        
                        <h2>10. Disputes and Chargebacks</h2>
                        <p>Before initiating a chargeback:</p>
                        <ul>
                            <li>Contact our support team first</li>
                            <li>Allow 5-7 business days for response</li>
                            <li>Chargebacks may result in account suspension</li>
                            <li>We will work with you to resolve issues</li>
                        </ul>
                        
                        <h2>11. Contact Information</h2>
                        <p>For refund requests or questions:</p>
                        <p><strong>Email:</strong> billing@mewayz.com<br>
                        <strong>Support:</strong> support@mewayz.com<br>
                        <strong>Phone:</strong> [Your Phone Number]</p>
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