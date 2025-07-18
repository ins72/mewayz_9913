@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
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
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Information We Collect</h2>
                                <p class="text-gray-700 mb-4">
                                    We collect information you provide directly to us, such as when you create an account, subscribe to our service, or contact us for support.
                                </p>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Personal Information</h3>
                                <ul class="list-disc pl-6 text-gray-700 mb-4">
                                    <li>Name and email address</li>
                                    <li>Phone number (if provided)</li>
                                    <li>Billing and payment information</li>
                                    <li>Profile information and preferences</li>
                                    <li>Communication preferences</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. How We Use Your Information</h2>
                                <p class="text-gray-700 mb-4">
                                    We use the information we collect to:
                                </p>
                                <ul class="list-disc pl-6 text-gray-700 mb-4">
                                    <li>Provide and maintain our Service</li>
                                    <li>Process transactions and send related information</li>
                                    <li>Send you technical notices and support messages</li>
                                    <li>Communicate with you about products, services, and events</li>
                                    <li>Monitor and analyze trends and usage</li>
                                    <li>Personalize your experience</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Information Sharing</h2>
                                <p class="text-gray-700 mb-4">
                                    We do not sell, trade, or otherwise transfer your personal information to third parties, except in the following circumstances:
                                </p>
                                <ul class="list-disc pl-6 text-gray-700 mb-4">
                                    <li>With your consent</li>
                                    <li>To comply with legal obligations</li>
                                    <li>To protect our rights and property</li>
                                    <li>With service providers who assist us in operating our Service</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Data Security</h2>
                                <p class="text-gray-700 mb-4">
                                    We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Your Rights (GDPR)</h2>
                                <p class="text-gray-700 mb-4">
                                    If you are a resident of the European Economic Area (EEA), you have certain data protection rights:
                                </p>
                                <ul class="list-disc pl-6 text-gray-700 mb-4">
                                    <li>Right to access your personal data</li>
                                    <li>Right to rectify inaccurate data</li>
                                    <li>Right to erasure of your data</li>
                                    <li>Right to restrict processing</li>
                                    <li>Right to data portability</li>
                                    <li>Right to object to processing</li>
                                </ul>
                                <div class="bg-blue-50 p-4 rounded-lg mt-4">
                                    <p class="text-blue-800">
                                        <strong>Exercise Your Rights:</strong> You can request data export, deletion, or exercise other rights through your account settings or by contacting us at privacy@mewayz.com.
                                    </p>
                                </div>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Cookies and Tracking</h2>
                                <p class="text-gray-700 mb-4">
                                    We use cookies and similar tracking technologies to enhance your experience on our Service. You can manage your cookie preferences through your browser settings or our cookie consent tool.
                                </p>
                                <a href="{{ route('legal.cookie-policy') }}" class="text-blue-600 hover:text-blue-800">
                                    Learn more about our Cookie Policy
                                </a>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Data Retention</h2>
                                <p class="text-gray-700 mb-4">
                                    We retain your personal information only for as long as necessary to fulfill the purposes outlined in this Privacy Policy, comply with legal obligations, resolve disputes, and enforce our agreements.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. International Data Transfers</h2>
                                <p class="text-gray-700 mb-4">
                                    Your information may be transferred to and maintained on computers located outside of your state, province, country, or other governmental jurisdiction where data protection laws may differ.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Children's Privacy</h2>
                                <p class="text-gray-700 mb-4">
                                    Our Service is not intended for use by children under the age of 13. We do not knowingly collect personal information from children under 13.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Changes to This Policy</h2>
                                <p class="text-gray-700 mb-4">
                                    We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Effective Date" at the top.
                                </p>
                            </section>

                            <section>
                                <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Contact Us</h2>
                                <p class="text-gray-700 mb-4">
                                    If you have any questions about this Privacy Policy, please contact us:
                                </p>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-gray-700">
                                        Email: privacy@mewayz.com<br>
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
                        <a href="{{ route('legal.terms-of-service') }}" class="text-blue-600 hover:text-blue-800">
                            Terms of Service
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