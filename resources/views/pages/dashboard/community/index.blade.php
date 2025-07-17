@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Community</h1>
            <p class="text-secondary-text mt-2">Build and manage your community</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4a2 2 0 011-3.464M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4a2 2 0 011-3.464"/>
                </svg>
                Settings
            </button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Event
            </button>
        </div>
    </div>

    <!-- Community Stats -->
    <div class="dashboard-grid mb-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Total Members</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">2,847</div>
            <div class="dashboard-card-change positive">+15.2% from last month</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Active Members</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">1,924</div>
            <div class="dashboard-card-change positive">67.6% active</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">New Posts</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">156</div>
            <div class="dashboard-card-change positive">+23 today</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Engagement Rate</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">78.3%</div>
            <div class="dashboard-card-change positive">+5.2% from last week</div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="dashboard-table">
                <div class="dashboard-table-header">
                    <h3 class="dashboard-table-title">Recent Posts</h3>
                    <button class="btn btn-sm btn-secondary">View All</button>
                </div>
                <div class="space-y-4">
                    @for ($i = 1; $i <= 6; $i++)
                    <div class="dashboard-card">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-info rounded-full flex items-center justify-center">
                                <span class="text-white font-medium">{{ substr('User ' . $i, 0, 1) }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium text-primary-text">User {{ $i }}</h4>
                                    <span class="text-sm text-secondary-text">{{ rand(1, 24) }}h ago</span>
                                </div>
                                <p class="text-secondary-text mt-1">
                                    {{ $i % 3 == 0 ? 'Just shared an amazing resource about content creation. Check it out!' : ($i % 2 == 0 ? 'Looking for feedback on my latest project. What do you think?' : 'Excited to be part of this community! Learning so much every day.') }}
                                </p>
                                <div class="flex items-center space-x-4 mt-3">
                                    <button class="flex items-center space-x-1 text-secondary-text hover:text-primary-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                        <span>{{ rand(5, 50) }}</span>
                                    </button>
                                    <button class="flex items-center space-x-1 text-secondary-text hover:text-primary-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span>{{ rand(1, 20) }}</span>
                                    </button>
                                    <button class="flex items-center space-x-1 text-secondary-text hover:text-primary-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                        </svg>
                                        <span>Share</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Top Contributors -->
            <div class="dashboard-card">
                <h3 class="font-semibold text-primary-text mb-4">Top Contributors</h3>
                <div class="space-y-3">
                    @for ($i = 1; $i <= 5; $i++)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-info rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ $i }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-primary-text">Contributor {{ $i }}</div>
                                <div class="text-sm text-secondary-text">{{ rand(50, 500) }} posts</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span class="text-sm text-primary-text">{{ rand(4, 5) }}.{{ rand(0, 9) }}</span>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="dashboard-card">
                <h3 class="font-semibold text-primary-text mb-4">Upcoming Events</h3>
                <div class="space-y-3">
                    @for ($i = 1; $i <= 3; $i++)
                    <div class="border-l-4 border-primary pl-4">
                        <div class="font-medium text-primary-text">Community Workshop {{ $i }}</div>
                        <div class="text-sm text-secondary-text">{{ date('M j, Y', strtotime('+' . $i . ' days')) }}</div>
                        <div class="text-sm text-secondary-text mt-1">{{ rand(50, 200) }} attending</div>
                    </div>
                    @endfor
                </div>
            </div>

            <!-- Community Guidelines -->
            <div class="dashboard-card">
                <h3 class="font-semibold text-primary-text mb-4">Community Guidelines</h3>
                <div class="space-y-2 text-sm text-secondary-text">
                    <div>• Be respectful and kind to all members</div>
                    <div>• Share valuable and relevant content</div>
                    <div>• No spam or self-promotion</div>
                    <div>• Follow community rules and guidelines</div>
                    <div>• Report inappropriate content</div>
                </div>
                <a href="#" class="text-primary text-sm hover:underline mt-3 block">View full guidelines</a>
            </div>
        </div>
    </div>
</div>
@endsection