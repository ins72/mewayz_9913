@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Help & Support</h1>
            <p class="text-secondary-text mt-2">Get help and find answers to your questions</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Live Chat
            </button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Contact Support
            </button>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="dashboard-card hover:transform hover:scale-105 transition-all cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-primary-text">FAQ</h3>
                    <p class="text-sm text-secondary-text">Find quick answers</p>
                </div>
            </div>
        </div>

        <div class="dashboard-card hover:transform hover:scale-105 transition-all cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-primary-text">Documentation</h3>
                    <p class="text-sm text-secondary-text">Detailed guides</p>
                </div>
            </div>
        </div>

        <div class="dashboard-card hover:transform hover:scale-105 transition-all cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-primary-text">Video Tutorials</h3>
                    <p class="text-sm text-secondary-text">Step-by-step videos</p>
                </div>
            </div>
        </div>

        <div class="dashboard-card hover:transform hover:scale-105 transition-all cursor-pointer">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-primary-text">Community</h3>
                    <p class="text-sm text-secondary-text">Join discussions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-8">
        <div class="max-w-2xl mx-auto">
            <div class="relative">
                <input type="text" placeholder="Search help articles..." class="w-full form-input pl-12 pr-4 py-3 text-lg">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="space-y-6">
                <!-- Getting Started -->
                <div class="dashboard-card">
                    <h3 class="font-semibold text-primary-text mb-4">Getting Started</h3>
                    <div class="space-y-3">
                        <a href="#" class="block p-3 rounded-lg hover:bg-secondary-bg transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-primary-text">How to create your first website</div>
                                    <div class="text-sm text-secondary-text">Learn the basics of building a website with our platform</div>
                                </div>
                                <svg class="w-5 h-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                        <a href="#" class="block p-3 rounded-lg hover:bg-secondary-bg transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-primary-text">Setting up your profile</div>
                                    <div class="text-sm text-secondary-text">Complete your profile and workspace setup</div>
                                </div>
                                <svg class="w-5 h-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                        <a href="#" class="block p-3 rounded-lg hover:bg-secondary-bg transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-primary-text">Understanding the dashboard</div>
                                    <div class="text-sm text-secondary-text">Navigate and use the main dashboard effectively</div>
                                </div>
                                <svg class="w-5 h-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Features -->
                <div class="dashboard-card">
                    <h3 class="font-semibold text-primary-text mb-4">Features & Tools</h3>
                    <div class="space-y-3">
                        <a href="#" class="block p-3 rounded-lg hover:bg-secondary-bg transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-primary-text">Website builder guide</div>
                                    <div class="text-sm text-secondary-text">Advanced website building techniques and tips</div>
                                </div>
                                <svg class="w-5 h-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                        <a href="#" class="block p-3 rounded-lg hover:bg-secondary-bg transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-primary-text">Social media integration</div>
                                    <div class="text-sm text-secondary-text">Connect and manage your social media accounts</div>
                                </div>
                                <svg class="w-5 h-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                        <a href="#" class="block p-3 rounded-lg hover:bg-secondary-bg transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-primary-text">E-commerce setup</div>
                                    <div class="text-sm text-secondary-text">Start selling online with our e-commerce tools</div>
                                </div>
                                <svg class="w-5 h-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Troubleshooting -->
                <div class="dashboard-card">
                    <h3 class="font-semibold text-primary-text mb-4">Troubleshooting</h3>
                    <div class="space-y-3">
                        <a href="#" class="block p-3 rounded-lg hover:bg-secondary-bg transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-primary-text">Common issues and solutions</div>
                                    <div class="text-sm text-secondary-text">Fix the most common problems quickly</div>
                                </div>
                                <svg class="w-5 h-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                        <a href="#" class="block p-3 rounded-lg hover:bg-secondary-bg transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-primary-text">Performance optimization</div>
                                    <div class="text-sm text-secondary-text">Improve your website's speed and performance</div>
                                </div>
                                <svg class="w-5 h-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                        <a href="#" class="block p-3 rounded-lg hover:bg-secondary-bg transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-primary-text">Account and billing issues</div>
                                    <div class="text-sm text-secondary-text">Resolve account and payment problems</div>
                                </div>
                                <svg class="w-5 h-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Contact Info -->
            <div class="dashboard-card">
                <h3 class="font-semibold text-primary-text mb-4">Contact Support</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">Email Support</div>
                            <div class="text-sm text-secondary-text">support@mewayz.com</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">Live Chat</div>
                            <div class="text-sm text-secondary-text">Available 24/7</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Articles -->
            <div class="dashboard-card">
                <h3 class="font-semibold text-primary-text mb-4">Popular Articles</h3>
                <div class="space-y-3">
                    <a href="#" class="block text-sm text-primary-text hover:text-primary">How to reset your password</a>
                    <a href="#" class="block text-sm text-primary-text hover:text-primary">Connecting social media accounts</a>
                    <a href="#" class="block text-sm text-primary-text hover:text-primary">Setting up payments</a>
                    <a href="#" class="block text-sm text-primary-text hover:text-primary">Managing team members</a>
                    <a href="#" class="block text-sm text-primary-text hover:text-primary">Customizing your website</a>
                </div>
            </div>

            <!-- Status -->
            <div class="dashboard-card">
                <h3 class="font-semibold text-primary-text mb-4">System Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-primary-text">All Systems</span>
                        <span class="badge badge-success">Operational</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-primary-text">Website Builder</span>
                        <span class="badge badge-success">Operational</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-primary-text">API</span>
                        <span class="badge badge-success">Operational</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-primary-text">Database</span>
                        <span class="badge badge-success">Operational</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection