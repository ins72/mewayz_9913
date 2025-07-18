@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Terms of Service</h1>
                    @if($document && $document->effective_date)
                        <p class="text-sm text-gray-600">Effective Date: {{ $document->effective_date->format('F j, Y') }}</p>
                        <p class="text-sm text-gray-600">Version: {{ $document->version ?? '1.0' }}</p>
                    @endif
                </div>

                <div class="prose max-w-none">
                    @if($document && $document->content)
                        {!! $document->content !!}
                    @else
                        <div class="space-y-8">
                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Acceptance of Terms</h2>
                                <p class="text-gray-700 mb-4">
                                    By accessing and using Mewayz ("the Service"), you accept and agree to be bound by these Terms of Service ("Terms"). If you do not agree to these Terms, please do not use our Service.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Description of Service</h2>
                                <p class="text-gray-700 mb-4">
                                    Mewayz is an all-in-one business platform providing social media management, course creation, e-commerce, and marketing tools. We provide these services subject to the following terms and conditions.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. User Accounts</h2>
                                <p class="text-gray-700 mb-4">
                                    You must register for an account to use certain features of our Service. You are responsible for:
                                </p>
                                <ul class="list-disc pl-6 text-gray-700 mb-4">
                                    <li>Maintaining the security of your account credentials</li>
                                    <li>All activities that occur under your account</li>
                                    <li>Providing accurate and complete information</li>
                                    <li>Notifying us immediately of any unauthorized use</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Acceptable Use</h2>
                                <p class="text-gray-700 mb-4">
                                    You agree not to use the Service to:
                                </p>
                                <ul class="list-disc pl-6 text-gray-700 mb-4">
                                    <li>Violate any applicable laws or regulations</li>
                                    <li>Infringe upon the rights of others</li>
                                    <li>Distribute spam, malware, or other harmful content</li>
                                    <li>Attempt to gain unauthorized access to our systems</li>
                                    <li>Interfere with the proper functioning of the Service</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Payment Terms</h2>
                                <p class="text-gray-700 mb-4">
                                    Certain features of our Service require payment. By subscribing to a paid plan, you agree to:
                                </p>
                                <ul class="list-disc pl-6 text-gray-700 mb-4">
                                    <li>Pay all fees as specified in your subscription plan</li>
                                    <li>Provide accurate billing information</li>
                                    <li>Authorize recurring charges for subscription plans</li>
                                    <li>Comply with our refund and cancellation policies</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Intellectual Property</h2>
                                <p class="text-gray-700 mb-4">
                                    The Service and its original content, features, and functionality are owned by Mewayz and are protected by international copyright, trademark, and other intellectual property laws.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Privacy</h2>
                                <p class="text-gray-700 mb-4">
                                    Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the Service, to understand our practices.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Termination</h2>
                                <p class="text-gray-700 mb-4">
                                    We may terminate or suspend your account and access to the Service immediately, without prior notice, for conduct that we believe violates these Terms or is harmful to other users, us, or third parties.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Disclaimers</h2>
                                <p class="text-gray-700 mb-4">
                                    The Service is provided "as is" and "as available" without warranties of any kind, whether express or implied, including but not limited to implied warranties of merchantability, fitness for a particular purpose, and non-infringement.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Limitation of Liability</h2>
                                <p class="text-gray-700 mb-4">
                                    In no event shall Mewayz be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Changes to Terms</h2>
                                <p class="text-gray-700 mb-4">
                                    We reserve the right to modify these Terms at any time. We will notify users of any material changes via email or through the Service. Your continued use of the Service after changes constitutes acceptance of the new Terms.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">12. Contact Information</h2>
                                <p class="text-gray-700 mb-4">
                                    If you have any questions about these Terms, please contact us at:
                                </p>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-gray-700">
                                        Email: legal@mewayz.com<br>
                                        Address: [Your Business Address]<br>
                                        Phone: [Your Phone Number]
                                    </p>
                                </div>
                            </section>
                        </div>
                    @endif
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('legal.privacy-policy') }}" class="text-blue-600 hover:text-blue-800">
                            Privacy Policy
                        </a>
                        <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection