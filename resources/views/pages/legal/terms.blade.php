@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary-bg">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-primary-text mb-4">Terms of Service</h1>
                <p class="text-secondary-text">Last updated: {{ date('F j, Y') }}</p>
            </div>

            <div class="bg-secondary-bg rounded-lg p-8 space-y-8">
                <!-- Introduction -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">1. Introduction</h2>
                    <p class="text-secondary-text leading-relaxed">
                        Welcome to Mewayz ("we," "our," or "us"). These Terms of Service ("Terms") govern your use of our website builder, social media management, e-commerce, and digital marketing platform located at mewayz.com (the "Service") operated by Mewayz Inc.
                    </p>
                    <p class="text-secondary-text leading-relaxed mt-4">
                        By accessing or using our Service, you agree to be bound by these Terms. If you disagree with any part of these terms, then you may not access the Service.
                    </p>
                </section>

                <!-- Account Registration -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">2. Account Registration</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        To use certain features of our Service, you must register for an account. You agree to:
                    </p>
                    <ul class="list-disc list-inside text-secondary-text space-y-2">
                        <li>Provide accurate, current, and complete information during registration</li>
                        <li>Maintain and update your account information</li>
                        <li>Maintain the security of your password and account</li>
                        <li>Accept responsibility for all activities under your account</li>
                        <li>Notify us immediately of any unauthorized use of your account</li>
                    </ul>
                </section>

                <!-- User Conduct -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">3. User Conduct</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        You agree not to use the Service to:
                    </p>
                    <ul class="list-disc list-inside text-secondary-text space-y-2">
                        <li>Violate any laws or regulations</li>
                        <li>Infringe on intellectual property rights</li>
                        <li>Distribute spam, viruses, or malicious content</li>
                        <li>Engage in fraudulent or deceptive practices</li>
                        <li>Harass, abuse, or harm other users</li>
                        <li>Interfere with the Service's operation</li>
                    </ul>
                </section>

                <!-- Subscription and Billing -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">4. Subscription and Billing</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        Our Service offers various subscription plans:
                    </p>
                    <ul class="list-disc list-inside text-secondary-text space-y-2">
                        <li><strong>Free Plan:</strong> Basic features with usage limitations</li>
                        <li><strong>Professional Plan:</strong> Advanced features and increased limits</li>
                        <li><strong>Enterprise Plan:</strong> Full feature access and priority support</li>
                    </ul>
                    <p class="text-secondary-text leading-relaxed mt-4">
                        Subscription fees are billed in advance on a monthly or annual basis. You may cancel your subscription at any time through your account settings.
                    </p>
                </section>

                <!-- Content Ownership -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">5. Content Ownership</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        You retain ownership of all content you create, upload, or publish through our Service. By using our Service, you grant us a limited license to:
                    </p>
                    <ul class="list-disc list-inside text-secondary-text space-y-2">
                        <li>Store and display your content</li>
                        <li>Provide backup and security services</li>
                        <li>Improve our Service functionality</li>
                        <li>Comply with legal requirements</li>
                    </ul>
                </section>

                <!-- Privacy -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">6. Privacy</h2>
                    <p class="text-secondary-text leading-relaxed">
                        Your privacy is important to us. Our Privacy Policy explains how we collect, use, and protect your information when you use our Service. By using our Service, you agree to the collection and use of information in accordance with our Privacy Policy.
                    </p>
                </section>

                <!-- Service Availability -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">7. Service Availability</h2>
                    <p class="text-secondary-text leading-relaxed">
                        We strive to provide reliable service but cannot guarantee 100% uptime. We reserve the right to modify, suspend, or discontinue any aspect of the Service at any time. We will provide reasonable notice of significant changes when possible.
                    </p>
                </section>

                <!-- Intellectual Property -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">8. Intellectual Property</h2>
                    <p class="text-secondary-text leading-relaxed">
                        The Service and its original content, features, and functionality are owned by Mewayz Inc. and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.
                    </p>
                </section>

                <!-- Limitation of Liability -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">9. Limitation of Liability</h2>
                    <p class="text-secondary-text leading-relaxed">
                        In no event shall Mewayz Inc., its directors, employees, partners, agents, suppliers, or affiliates be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your use of the Service.
                    </p>
                </section>

                <!-- Termination -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">10. Termination</h2>
                    <p class="text-secondary-text leading-relaxed">
                        We may terminate or suspend your account and bar access to the Service immediately, without prior notice or liability, under our sole discretion, for any reason whatsoever, including without limitation if you breach the Terms.
                    </p>
                </section>

                <!-- Governing Law -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">11. Governing Law</h2>
                    <p class="text-secondary-text leading-relaxed">
                        These Terms shall be governed and construed in accordance with the laws of Delaware, United States, without regard to its conflict of law provisions. Any disputes arising from these Terms will be resolved in the courts of Delaware.
                    </p>
                </section>

                <!-- Changes to Terms -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">12. Changes to Terms</h2>
                    <p class="text-secondary-text leading-relaxed">
                        We reserve the right to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days notice prior to any new terms taking effect. Your continued use of the Service after any changes constitutes acceptance of the new Terms.
                    </p>
                </section>

                <!-- Contact Information -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">13. Contact Information</h2>
                    <p class="text-secondary-text leading-relaxed">
                        If you have any questions about these Terms of Service, please contact us at:
                    </p>
                    <div class="mt-4 p-4 bg-primary-bg rounded-lg">
                        <p class="text-secondary-text">
                            <strong>Mewayz Inc.</strong><br>
                            Email: legal@mewayz.com<br>
                            Address: 123 Business Ave, Suite 100, San Francisco, CA 94105<br>
                            Phone: (555) 123-4567
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection