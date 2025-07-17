@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary-bg">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-primary-text mb-4">Privacy Policy</h1>
                <p class="text-secondary-text">Last updated: {{ date('F j, Y') }}</p>
            </div>

            <div class="bg-secondary-bg rounded-lg p-8 space-y-8">
                <!-- Introduction -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">1. Introduction</h2>
                    <p class="text-secondary-text leading-relaxed">
                        This Privacy Policy describes how Mewayz Inc. ("we," "us," or "our") collects, uses, and shares information about you when you use our platform and services ("Service"). We are committed to protecting your privacy and handling your data responsibly.
                    </p>
                </section>

                <!-- Information We Collect -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">2. Information We Collect</h2>
                    
                    <h3 class="text-xl font-semibold text-primary-text mb-3">2.1 Information You Provide</h3>
                    <ul class="list-disc list-inside text-secondary-text space-y-2 mb-4">
                        <li>Account information (name, email, password)</li>
                        <li>Profile information (bio, profile picture, social media links)</li>
                        <li>Content you create (websites, posts, media files)</li>
                        <li>Payment information (processed through secure third-party providers)</li>
                        <li>Communications with our support team</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-primary-text mb-3">2.2 Information We Collect Automatically</h3>
                    <ul class="list-disc list-inside text-secondary-text space-y-2 mb-4">
                        <li>Device information (IP address, browser type, operating system)</li>
                        <li>Usage data (pages visited, features used, time spent)</li>
                        <li>Cookies and similar tracking technologies</li>
                        <li>Location information (approximate location based on IP address)</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-primary-text mb-3">2.3 Information from Third Parties</h3>
                    <ul class="list-disc list-inside text-secondary-text space-y-2">
                        <li>Social media account data (when you connect accounts)</li>
                        <li>Analytics data from integrated services</li>
                        <li>Information from payment processors</li>
                    </ul>
                </section>

                <!-- How We Use Information -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">3. How We Use Your Information</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        We use the information we collect to:
                    </p>
                    <ul class="list-disc list-inside text-secondary-text space-y-2">
                        <li>Provide, maintain, and improve our Service</li>
                        <li>Process transactions and manage subscriptions</li>
                        <li>Communicate with you about your account and our services</li>
                        <li>Provide customer support</li>
                        <li>Personalize your experience</li>
                        <li>Analyze usage patterns to improve our Service</li>
                        <li>Detect and prevent fraud and abuse</li>
                        <li>Comply with legal obligations</li>
                    </ul>
                </section>

                <!-- Information Sharing -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">4. Information Sharing</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        We do not sell your personal information. We may share your information in the following circumstances:
                    </p>
                    
                    <h3 class="text-xl font-semibold text-primary-text mb-3">4.1 With Service Providers</h3>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        We share information with trusted third-party service providers who help us operate our Service:
                    </p>
                    <ul class="list-disc list-inside text-secondary-text space-y-2 mb-4">
                        <li>Payment processors (Stripe, PayPal)</li>
                        <li>Cloud storage providers (AWS, Google Cloud)</li>
                        <li>Analytics services (Google Analytics)</li>
                        <li>Email service providers</li>
                        <li>Customer support platforms</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-primary-text mb-3">4.2 Legal Requirements</h3>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        We may disclose information if required by law or in response to:
                    </p>
                    <ul class="list-disc list-inside text-secondary-text space-y-2 mb-4">
                        <li>Legal process (subpoena, court order)</li>
                        <li>Government requests</li>
                        <li>Protecting rights and safety</li>
                        <li>Preventing fraud or illegal activity</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-primary-text mb-3">4.3 Business Transfers</h3>
                    <p class="text-secondary-text leading-relaxed">
                        If we are involved in a merger, acquisition, or asset sale, your information may be transferred as part of that transaction.
                    </p>
                </section>

                <!-- Data Security -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">5. Data Security</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        We implement appropriate technical and organizational measures to protect your information:
                    </p>
                    <ul class="list-disc list-inside text-secondary-text space-y-2">
                        <li>Encryption of data in transit and at rest</li>
                        <li>Regular security audits and updates</li>
                        <li>Access controls and authentication</li>
                        <li>Secure data centers and infrastructure</li>
                        <li>Employee training on data protection</li>
                    </ul>
                </section>

                <!-- Your Rights -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">6. Your Rights and Choices</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        You have the following rights regarding your personal information:
                    </p>
                    <ul class="list-disc list-inside text-secondary-text space-y-2">
                        <li><strong>Access:</strong> Request a copy of your personal information</li>
                        <li><strong>Correction:</strong> Update or correct inaccurate information</li>
                        <li><strong>Deletion:</strong> Request deletion of your personal information</li>
                        <li><strong>Portability:</strong> Receive your data in a structured format</li>
                        <li><strong>Objection:</strong> Object to certain processing activities</li>
                        <li><strong>Restriction:</strong> Request limitation of processing</li>
                    </ul>
                </section>

                <!-- Cookies -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">7. Cookies and Tracking</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        We use cookies and similar technologies to:
                    </p>
                    <ul class="list-disc list-inside text-secondary-text space-y-2 mb-4">
                        <li>Remember your preferences and settings</li>
                        <li>Analyze site usage and performance</li>
                        <li>Provide personalized content</li>
                        <li>Improve security</li>
                    </ul>
                    <p class="text-secondary-text leading-relaxed">
                        You can control cookie settings through your browser preferences. However, disabling cookies may affect Service functionality.
                    </p>
                </section>

                <!-- Data Retention -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">8. Data Retention</h2>
                    <p class="text-secondary-text leading-relaxed">
                        We retain your information for as long as necessary to provide our Service and comply with legal obligations. When you delete your account, we will delete your personal information within 30 days, except for information we are required to retain for legal or security purposes.
                    </p>
                </section>

                <!-- International Transfers -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">9. International Data Transfers</h2>
                    <p class="text-secondary-text leading-relaxed">
                        Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place to protect your information when it is transferred internationally, including through adequacy decisions, standard contractual clauses, or other approved mechanisms.
                    </p>
                </section>

                <!-- Children's Privacy -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">10. Children's Privacy</h2>
                    <p class="text-secondary-text leading-relaxed">
                        Our Service is not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13. If you are a parent or guardian and believe your child has provided us with personal information, please contact us to have it removed.
                    </p>
                </section>

                <!-- Changes to Policy -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">11. Changes to This Privacy Policy</h2>
                    <p class="text-secondary-text leading-relaxed">
                        We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date. For material changes, we will provide additional notice through email or Service notifications.
                    </p>
                </section>

                <!-- Contact Information -->
                <section>
                    <h2 class="text-2xl font-semibold text-primary-text mb-4">12. Contact Us</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        If you have any questions about this Privacy Policy or our privacy practices, please contact us at:
                    </p>
                    <div class="p-4 bg-primary-bg rounded-lg">
                        <p class="text-secondary-text">
                            <strong>Mewayz Inc.</strong><br>
                            Email: privacy@mewayz.com<br>
                            Address: 123 Business Ave, Suite 100, San Francisco, CA 94105<br>
                            Phone: (555) 123-4567<br>
                            <br>
                            <strong>Data Protection Officer:</strong><br>
                            Email: dpo@mewayz.com
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection