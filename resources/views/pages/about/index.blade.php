@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary-bg">
    <!-- Hero Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto text-center mb-16">
            <h1 class="text-5xl font-bold text-primary-text mb-6">About Mewayz</h1>
            <p class="text-xl text-secondary-text leading-relaxed">
                Empowering creators and businesses with the ultimate all-in-one platform for building, managing, and growing their digital presence.
            </p>
        </div>
    </div>

    <!-- Mission Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-primary-text mb-6">Our Mission</h2>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        At Mewayz, we believe that every creator and business deserves access to powerful, professional-grade tools without the complexity and cost typically associated with enterprise solutions.
                    </p>
                    <p class="text-secondary-text leading-relaxed mb-4">
                        Our mission is to democratize digital business tools by providing an integrated platform that combines website building, social media management, e-commerce, CRM, and marketing automation in one seamless experience.
                    </p>
                    <p class="text-secondary-text leading-relaxed">
                        We're committed to helping our users build authentic connections with their audiences, grow their businesses, and achieve their goals through innovative technology and exceptional user experience.
                    </p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-8">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary mb-2">10K+</div>
                            <div class="text-secondary-text">Active Users</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary mb-2">50K+</div>
                            <div class="text-secondary-text">Websites Created</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary mb-2">99.9%</div>
                            <div class="text-secondary-text">Uptime</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary mb-2">24/7</div>
                            <div class="text-secondary-text">Support</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Values Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-primary-text text-center mb-12">Our Values</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-secondary-bg rounded-lg p-8 text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-3">Innovation</h3>
                    <p class="text-secondary-text">
                        We continuously push the boundaries of what's possible, delivering cutting-edge features that help our users stay ahead of the curve.
                    </p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-8 text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-3">User-Centric</h3>
                    <p class="text-secondary-text">
                        Every decision we make is guided by our users' needs. We listen, learn, and adapt to ensure our platform serves you best.
                    </p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-8 text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-3">Security</h3>
                    <p class="text-secondary-text">
                        We prioritize the security and privacy of your data with enterprise-grade security measures and transparent practices.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Overview -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-primary-text text-center mb-12">What Makes Mewayz Different</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-secondary-bg rounded-lg p-6">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">All-in-One Platform</h3>
                    <p class="text-secondary-text">
                        Everything you need in one place - website builder, social media management, e-commerce, CRM, and more.
                    </p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-6">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">No-Code Solutions</h3>
                    <p class="text-secondary-text">
                        Build professional websites and manage your business without any technical knowledge required.
                    </p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-6">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Advanced Analytics</h3>
                    <p class="text-secondary-text">
                        Gain deep insights into your audience and performance with comprehensive analytics and reporting.
                    </p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-6">
                    <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Powerful Integrations</h3>
                    <p class="text-secondary-text">
                        Connect with your favorite tools and services through our extensive integration ecosystem.
                    </p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-6">
                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Mobile-First Design</h3>
                    <p class="text-secondary-text">
                        PWA technology ensures perfect mobile experience and native app-like performance.
                    </p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-6">
                    <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">24/7 Support</h3>
                    <p class="text-secondary-text">
                        Get help when you need it with our dedicated support team available around the clock.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-primary-text text-center mb-12">Meet Our Team</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-secondary-bg rounded-lg p-8 text-center">
                    <div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-2">Sarah Johnson</h3>
                    <p class="text-primary mb-2">CEO & Founder</p>
                    <p class="text-secondary-text">
                        Former tech executive with 15+ years of experience in digital platforms and user experience design.
                    </p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-8 text-center">
                    <div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-2">Michael Chen</h3>
                    <p class="text-primary mb-2">CTO</p>
                    <p class="text-secondary-text">
                        Software architect and engineering leader with expertise in scalable cloud platforms and AI integration.
                    </p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-8 text-center">
                    <div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-2">Emily Rodriguez</h3>
                    <p class="text-primary mb-2">Head of Design</p>
                    <p class="text-secondary-text">
                        Creative director with a passion for user-centered design and creating beautiful, functional interfaces.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-primary-text mb-6">Get in Touch</h2>
            <p class="text-secondary-text mb-8">
                Have questions about Mewayz? We'd love to hear from you.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-secondary-bg rounded-lg p-6">
                    <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Email</h3>
                    <p class="text-secondary-text">hello@mewayz.com</p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-6">
                    <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Phone</h3>
                    <p class="text-secondary-text">(555) 123-4567</p>
                </div>
                <div class="bg-secondary-bg rounded-lg p-6">
                    <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Address</h3>
                    <p class="text-secondary-text">123 Business Ave<br>San Francisco, CA 94105</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection