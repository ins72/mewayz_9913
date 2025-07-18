@extends('layouts.app')

@section('title', 'Features')

@section('content')
<div class="min-h-screen bg-secondary">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-primary to-secondary text-white py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">Powerful Features</h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8">
                    Everything you need to build, manage, and grow your online presence in one platform.
                </p>
            </div>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                <!-- Core Features -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-center mb-12 text-primary">Core Features</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="card">
                            <div class="w-16 h-16 bg-accent rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-primary">Link-in-Bio Pages</h3>
                            <p class="text-secondary">Create beautiful, customizable bio pages that showcase all your important links in one place.</p>
                        </div>

                        <div class="card">
                            <div class="w-16 h-16 bg-accent rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-primary">Advanced Analytics</h3>
                            <p class="text-secondary">Track clicks, views, and engagement with detailed analytics and insights.</p>
                        </div>

                        <div class="card">
                            <div class="w-16 h-16 bg-accent rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-primary">Email Marketing</h3>
                            <p class="text-secondary">Build your email list and create automated campaigns to nurture your audience.</p>
                        </div>

                        <div class="card">
                            <div class="w-16 h-16 bg-accent rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-primary">Social Media Management</h3>
                            <p class="text-secondary">Schedule posts, manage multiple accounts, and track performance across platforms.</p>
                        </div>

                        <div class="card">
                            <div class="w-16 h-16 bg-accent rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-primary">Course Creation</h3>
                            <p class="text-secondary">Create and sell online courses with video lessons, quizzes, and certificates.</p>
                        </div>

                        <div class="card">
                            <div class="w-16 h-16 bg-accent rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-primary">E-commerce</h3>
                            <p class="text-secondary">Sell products and services directly from your bio page with integrated payments.</p>
                        </div>
                    </div>
                </div>

                <!-- Advanced Features -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-center mb-12 text-primary">Advanced Features</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="card">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold mb-3 text-primary">AI-Powered Content</h3>
                                    <p class="text-secondary">Generate compelling copy, social media posts, and email campaigns using advanced AI technology.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold mb-3 text-primary">Team Collaboration</h3>
                                    <p class="text-secondary">Invite team members, assign roles, and collaborate on projects with built-in workspace management.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold mb-3 text-primary">Booking System</h3>
                                    <p class="text-secondary">Accept appointments and bookings directly through your bio page with calendar integration.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold mb-3 text-primary">Automation</h3>
                                    <p class="text-secondary">Automate repetitive tasks, social media posting, and email campaigns to save time.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Integration Features -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-center mb-12 text-primary">Integrations</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-3 mx-auto">
                                <span class="text-2xl">📊</span>
                            </div>
                            <p class="text-sm text-secondary">Google Analytics</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-3 mx-auto">
                                <span class="text-2xl">💳</span>
                            </div>
                            <p class="text-sm text-secondary">Stripe</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-3 mx-auto">
                                <span class="text-2xl">📧</span>
                            </div>
                            <p class="text-sm text-secondary">Mailchimp</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-3 mx-auto">
                                <span class="text-2xl">🔗</span>
                            </div>
                            <p class="text-sm text-secondary">Zapier</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-3 mx-auto">
                                <span class="text-2xl">📱</span>
                            </div>
                            <p class="text-sm text-secondary">Social Media</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-3 mx-auto">
                                <span class="text-2xl">🎥</span>
                            </div>
                            <p class="text-sm text-secondary">YouTube</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-20 bg-primary text-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-6">Ready to Get Started?</h2>
                <p class="text-xl text-gray-300 mb-8">
                    Join thousands of creators and businesses who trust Mewayz to grow their online presence.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/register" class="btn btn-accent btn-lg">Start Free Trial</a>
                    <a href="/contact" class="btn btn-outline btn-lg">Schedule Demo</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection