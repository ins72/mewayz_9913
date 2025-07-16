<x-layouts.dashboard title="Link in Bio - Mewayz" page-title="Link in Bio">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Link in Bio</h1>
                <p class="text-secondary-text">Create beautiful bio pages that convert visitors into customers</p>
            </div>
            <div class="flex gap-3">
                <button class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Preview
                </button>
                <button class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create New Bio
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Views</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">12,847</div>
                <div class="text-sm text-success">+18.2% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Link Clicks</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">3,421</div>
                <div class="text-sm text-success">+24.7% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Conversions</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">289</div>
                <div class="text-sm text-warning">+12.3% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Revenue</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">$8,492</div>
                <div class="text-sm text-success">+31.4% from last month</div>
            </div>
        </div>

        <!-- Bio Pages Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Active Bio Page -->
            <div class="card">
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Active</span>
                        <button class="text-secondary-text hover:text-primary-text">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="w-full h-32 bg-gradient-to-br from-info/20 to-success/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Personal Brand</h3>
                    <p class="text-secondary-text text-sm mb-4">mewayz.com/johndoe</p>
                    <div class="flex items-center justify-between text-sm mb-4">
                        <span class="text-secondary-text">8,421 views</span>
                        <span class="text-secondary-text">234 clicks</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-secondary text-sm flex-1">Edit</button>
                    <button class="btn btn-primary text-sm flex-1">Share</button>
                </div>
            </div>

            <!-- Draft Bio Page -->
            <div class="card">
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-1 text-xs font-medium bg-warning/10 text-warning rounded">Draft</span>
                        <button class="text-secondary-text hover:text-primary-text">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="w-full h-32 bg-gradient-to-br from-warning/20 to-error/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Business Page</h3>
                    <p class="text-secondary-text text-sm mb-4">Not published yet</p>
                    <div class="flex items-center justify-between text-sm mb-4">
                        <span class="text-secondary-text">0 views</span>
                        <span class="text-secondary-text">0 clicks</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-secondary text-sm flex-1">Edit</button>
                    <button class="btn btn-primary text-sm flex-1">Publish</button>
                </div>
            </div>

            <!-- Create New Bio Card -->
            <div class="card border-dashed border-2 border-border-color hover:border-info/50 transition-colors">
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-16 h-16 bg-info/10 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Create New Bio</h3>
                    <p class="text-secondary-text text-sm mb-4">Start building your bio page</p>
                    <button class="btn btn-primary">Get Started</button>
                </div>
            </div>
        </div>

        <!-- Templates Section -->
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Bio Templates</h2>
                <a href="{{ route('dashboard-templates-index') }}" class="btn btn-secondary">View All</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card">
                    <div class="w-full h-40 bg-gradient-to-br from-info/20 to-success/20 rounded-lg flex items-center justify-center mb-4">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-white rounded-full mx-auto mb-2"></div>
                            <div class="h-2 bg-white rounded w-20 mx-auto mb-1"></div>
                            <div class="h-2 bg-white rounded w-16 mx-auto mb-3"></div>
                            <div class="space-y-1">
                                <div class="h-6 bg-white rounded w-full"></div>
                                <div class="h-6 bg-white rounded w-full"></div>
                                <div class="h-6 bg-white rounded w-full"></div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Creator</h3>
                    <p class="text-secondary-text text-sm mb-4">Perfect for content creators and influencers</p>
                    <button class="btn btn-primary w-full">Use Template</button>
                </div>

                <div class="card">
                    <div class="w-full h-40 bg-gradient-to-br from-warning/20 to-error/20 rounded-lg flex items-center justify-center mb-4">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-white rounded-full mx-auto mb-2"></div>
                            <div class="h-2 bg-white rounded w-20 mx-auto mb-1"></div>
                            <div class="h-2 bg-white rounded w-16 mx-auto mb-3"></div>
                            <div class="grid grid-cols-2 gap-1">
                                <div class="h-6 bg-white rounded"></div>
                                <div class="h-6 bg-white rounded"></div>
                                <div class="h-6 bg-white rounded"></div>
                                <div class="h-6 bg-white rounded"></div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Business</h3>
                    <p class="text-secondary-text text-sm mb-4">Great for businesses and professionals</p>
                    <button class="btn btn-primary w-full">Use Template</button>
                </div>

                <div class="card">
                    <div class="w-full h-40 bg-gradient-to-br from-success/20 to-info/20 rounded-lg flex items-center justify-center mb-4">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-white rounded-full mx-auto mb-2"></div>
                            <div class="h-2 bg-white rounded w-20 mx-auto mb-1"></div>
                            <div class="h-2 bg-white rounded w-16 mx-auto mb-3"></div>
                            <div class="space-y-1">
                                <div class="h-4 bg-white rounded w-full"></div>
                                <div class="h-4 bg-white rounded w-full"></div>
                                <div class="h-8 bg-white rounded w-full"></div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Portfolio</h3>
                    <p class="text-secondary-text text-sm mb-4">Showcase your work and achievements</p>
                    <button class="btn btn-primary w-full">Use Template</button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>