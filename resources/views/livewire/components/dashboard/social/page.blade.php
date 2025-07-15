<?php
/**
 * Social Media Management Console Component
 * Professional social media management and publishing interface
 */

use function Livewire\Volt\{mount, state, computed, on, layout};
use App\Models\User;
use Illuminate\Support\Collection;

layout('components.layouts.app');

state([
    'connectedAccounts' => [],
    'scheduledPosts' => [],
    'publishedPosts' => [],
    'analytics' => [],
    'activeTab' => 'overview',
    'selectedPlatform' => 'all',
    'showPostModal' => false,
    'showAccountModal' => false,
    'socialStats' => [],
    'contentCalendar' => [],
    'engagementData' => [],
    'newPost' => [
        'content' => '',
        'platforms' => [],
        'scheduled_time' => '',
        'media_url' => '',
        'hashtags' => []
    ]
]);

mount(function () {
    $this->loadConnectedAccounts();
    $this->loadScheduledPosts();
    $this->loadPublishedPosts();
    $this->loadSocialStats();
    $this->loadContentCalendar();
    $this->loadEngagementData();
});

$loadConnectedAccounts = function () {
    $this->connectedAccounts = [
        ['platform' => 'facebook', 'username' => '@mewayz', 'followers' => 15420, 'status' => 'active'],
        ['platform' => 'twitter', 'username' => '@mewayz', 'followers' => 8750, 'status' => 'active'],
        ['platform' => 'instagram', 'username' => '@mewayz', 'followers' => 12300, 'status' => 'active'],
        ['platform' => 'linkedin', 'username' => 'Mewayz', 'followers' => 5680, 'status' => 'active'],
        ['platform' => 'youtube', 'username' => 'Mewayz', 'followers' => 3450, 'status' => 'inactive']
    ];
};

$loadScheduledPosts = function () {
    $this->scheduledPosts = [
        ['id' => 1, 'content' => 'Excited to announce our new features!', 'platforms' => ['facebook', 'twitter'], 'scheduled_time' => now()->addHours(2), 'status' => 'scheduled'],
        ['id' => 2, 'content' => 'Check out our latest blog post about social media marketing', 'platforms' => ['linkedin'], 'scheduled_time' => now()->addDays(1), 'status' => 'scheduled'],
        ['id' => 3, 'content' => 'Behind the scenes at Mewayz HQ', 'platforms' => ['instagram'], 'scheduled_time' => now()->addDays(2), 'status' => 'scheduled']
    ];
};

$loadPublishedPosts = function () {
    $this->publishedPosts = [
        ['id' => 1, 'content' => 'Welcome to the future of social media management!', 'platforms' => ['facebook', 'twitter'], 'published_time' => now()->subHours(3), 'engagement' => ['likes' => 145, 'comments' => 23, 'shares' => 12]],
        ['id' => 2, 'content' => 'Our team is growing! We\'re hiring talented individuals.', 'platforms' => ['linkedin'], 'published_time' => now()->subDays(1), 'engagement' => ['likes' => 89, 'comments' => 15, 'shares' => 7]],
        ['id' => 3, 'content' => 'New product demo video is live!', 'platforms' => ['youtube'], 'published_time' => now()->subDays(2), 'engagement' => ['likes' => 234, 'comments' => 45, 'shares' => 67]]
    ];
};

$loadSocialStats = function () {
    $this->socialStats = [
        'total_followers' => 45800,
        'total_posts' => 156,
        'engagement_rate' => 4.8,
        'reach' => 125000,
        'impressions' => 450000,
        'clicks' => 12400,
        'growth_rate' => 15.2
    ];
};

$loadContentCalendar = function () {
    $this->contentCalendar = [
        ['date' => now()->format('Y-m-d'), 'posts' => 3, 'platforms' => ['facebook', 'twitter', 'instagram']],
        ['date' => now()->addDay()->format('Y-m-d'), 'posts' => 2, 'platforms' => ['linkedin', 'youtube']],
        ['date' => now()->addDays(2)->format('Y-m-d'), 'posts' => 1, 'platforms' => ['instagram']]
    ];
};

$loadEngagementData = function () {
    $this->engagementData = [
        'facebook' => ['likes' => 2450, 'comments' => 345, 'shares' => 123],
        'twitter' => ['likes' => 1890, 'comments' => 234, 'shares' => 89],
        'instagram' => ['likes' => 3250, 'comments' => 567, 'shares' => 234],
        'linkedin' => ['likes' => 987, 'comments' => 123, 'shares' => 45],
        'youtube' => ['likes' => 1234, 'comments' => 234, 'shares' => 156]
    ];
};

$setActiveTab = function ($tab) {
    $this->activeTab = $tab;
};

$createPost = function () {
    $this->showPostModal = true;
};

$connectAccount = function () {
    $this->showAccountModal = true;
};

$publishPost = function () {
    $this->validate([
        'newPost.content' => 'required|string|max:2000',
        'newPost.platforms' => 'required|array|min:1'
    ]);

    // Publish post logic here
    $this->showPostModal = false;
    $this->newPost = [
        'content' => '',
        'platforms' => [],
        'scheduled_time' => '',
        'media_url' => '',
        'hashtags' => []
    ];

    session()->flash('success', 'Post published successfully!');
    $this->loadPublishedPosts();
};

$getPlatformIcon = function ($platform) {
    $icons = [
        'facebook' => 'fi-brands-facebook',
        'twitter' => 'fi-brands-twitter',
        'instagram' => 'fi-brands-instagram',
        'linkedin' => 'fi-brands-linkedin',
        'youtube' => 'fi-brands-youtube'
    ];
    return $icons[$platform] ?? 'fi-rr-globe';
};

$getPlatformColor = function ($platform) {
    $colors = [
        'facebook' => 'text-blue-600',
        'twitter' => 'text-blue-400',
        'instagram' => 'text-pink-600',
        'linkedin' => 'text-blue-700',
        'youtube' => 'text-red-600'
    ];
    return $colors[$platform] ?? 'text-gray-600';
};

?>

<div class="dashboard-page">
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Social Media Management</h1>
                <p class="text-gray-600 dark:text-gray-400">Comprehensive social media management and publishing platform</p>
            </div>
            <div class="flex items-center space-x-4">
                <button wire:click="connectAccount" class="btn btn-secondary">
                    <i class="fi fi-rr-plus mr-2"></i>
                    Connect Account
                </button>
                <button wire:click="createPost" class="btn btn-primary">
                    <i class="fi fi-rr-edit mr-2"></i>
                    Create Post
                </button>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4 mb-8">
        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Total Followers</h3>
                <div class="kpi-icon bg-blue-100 dark:bg-blue-900">
                    <i class="fi fi-rr-users text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ number_format($socialStats['total_followers'] ?? 0) }}</p>
                <p class="kpi-change positive">+{{ $socialStats['growth_rate'] ?? 0 }}%</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Total Posts</h3>
                <div class="kpi-icon bg-green-100 dark:bg-green-900">
                    <i class="fi fi-rr-document text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ number_format($socialStats['total_posts'] ?? 0) }}</p>
                <p class="kpi-change positive">+12</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Engagement Rate</h3>
                <div class="kpi-icon bg-purple-100 dark:bg-purple-900">
                    <i class="fi fi-rr-heart text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ $socialStats['engagement_rate'] ?? 0 }}%</p>
                <p class="kpi-change positive">+0.8%</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Reach</h3>
                <div class="kpi-icon bg-yellow-100 dark:bg-yellow-900">
                    <i class="fi fi-rr-eye text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ number_format($socialStats['reach'] ?? 0) }}</p>
                <p class="kpi-change positive">+18%</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Impressions</h3>
                <div class="kpi-icon bg-indigo-100 dark:bg-indigo-900">
                    <i class="fi fi-rr-stats text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ number_format($socialStats['impressions'] ?? 0) }}</p>
                <p class="kpi-change positive">+25%</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Clicks</h3>
                <div class="kpi-icon bg-red-100 dark:bg-red-900">
                    <i class="fi fi-rr-cursor-finger text-red-600 dark:text-red-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ number_format($socialStats['clicks'] ?? 0) }}</p>
                <p class="kpi-change positive">+32%</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Connected</h3>
                <div class="kpi-icon bg-pink-100 dark:bg-pink-900">
                    <i class="fi fi-rr-link text-pink-600 dark:text-pink-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ count($connectedAccounts) }}</p>
                <p class="kpi-change neutral">platforms</p>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tabs-container">
        <nav class="flex space-x-8 border-b border-gray-200 dark:border-gray-700">
            <button 
                wire:click="setActiveTab('overview')"
                class="tab-button {{ $activeTab === 'overview' ? 'active' : '' }}">
                <i class="fi fi-rr-dashboard mr-2"></i>
                Overview
            </button>
            <button 
                wire:click="setActiveTab('accounts')"
                class="tab-button {{ $activeTab === 'accounts' ? 'active' : '' }}">
                <i class="fi fi-rr-user mr-2"></i>
                Accounts
            </button>
            <button 
                wire:click="setActiveTab('posts')"
                class="tab-button {{ $activeTab === 'posts' ? 'active' : '' }}">
                <i class="fi fi-rr-document mr-2"></i>
                Posts
            </button>
            <button 
                wire:click="setActiveTab('scheduled')"
                class="tab-button {{ $activeTab === 'scheduled' ? 'active' : '' }}">
                <i class="fi fi-rr-time-quarter-past mr-2"></i>
                Scheduled
            </button>
            <button 
                wire:click="setActiveTab('calendar')"
                class="tab-button {{ $activeTab === 'calendar' ? 'active' : '' }}">
                <i class="fi fi-rr-calendar mr-2"></i>
                Calendar
            </button>
            <button 
                wire:click="setActiveTab('analytics')"
                class="tab-button {{ $activeTab === 'analytics' ? 'active' : '' }}">
                <i class="fi fi-rr-stats mr-2"></i>
                Analytics
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        @if($activeTab === 'overview')
            <div class="overview-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Connected Accounts Overview -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Connected Accounts</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                @foreach($connectedAccounts as $account)
                                    <div class="account-overview-item">
                                        <div class="flex items-center space-x-4">
                                            <div class="platform-icon {{ $this->getPlatformColor($account['platform']) }}">
                                                <i class="fi {{ $this->getPlatformIcon($account['platform']) }} text-2xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $account['username'] }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($account['followers']) }} followers</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-{{ $account['status'] === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($account['status']) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Recent Posts -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Posts</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                @foreach($publishedPosts as $post)
                                    <div class="post-overview-item">
                                        <div class="post-content">
                                            <p class="text-sm text-gray-900 dark:text-white line-clamp-2">{{ $post['content'] }}</p>
                                            <div class="post-platforms">
                                                @foreach($post['platforms'] as $platform)
                                                    <i class="fi {{ $this->getPlatformIcon($platform) }} {{ $this->getPlatformColor($platform) }} text-sm"></i>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="post-stats">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $post['published_time']->diffForHumans() }}</span>
                                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-400">
                                                <span>{{ $post['engagement']['likes'] }} likes</span>
                                                <span>{{ $post['engagement']['comments'] }} comments</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'accounts')
            <div class="accounts-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Social Media Accounts</h3>
                            <button wire:click="connectAccount" class="btn btn-primary">
                                <i class="fi fi-rr-plus mr-2"></i>
                                Connect Account
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($connectedAccounts as $account)
                                <div class="account-card">
                                    <div class="account-header">
                                        <div class="flex items-center space-x-3">
                                            <div class="platform-icon {{ $this->getPlatformColor($account['platform']) }}">
                                                <i class="fi {{ $this->getPlatformIcon($account['platform']) }} text-3xl"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($account['platform']) }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $account['username'] }}</p>
                                            </div>
                                        </div>
                                        <span class="badge badge-{{ $account['status'] === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($account['status']) }}
                                        </span>
                                    </div>
                                    <div class="account-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Followers</span>
                                            <span class="stat-value">{{ number_format($account['followers']) }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Posts</span>
                                            <span class="stat-value">{{ rand(20, 100) }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Engagement</span>
                                            <span class="stat-value">{{ rand(3, 8) }}%</span>
                                        </div>
                                    </div>
                                    <div class="account-actions">
                                        <button class="btn btn-sm btn-secondary">Settings</button>
                                        <button class="btn btn-sm btn-primary">View Analytics</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'posts')
            <div class="posts-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Published Posts</h3>
                            <button wire:click="createPost" class="btn btn-primary">
                                <i class="fi fi-rr-plus mr-2"></i>
                                Create Post
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Content</th>
                                        <th>Platforms</th>
                                        <th>Published</th>
                                        <th>Engagement</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($publishedPosts as $post)
                                        <tr>
                                            <td>
                                                <p class="text-sm text-gray-900 dark:text-white line-clamp-2">{{ $post['content'] }}</p>
                                            </td>
                                            <td>
                                                <div class="flex items-center space-x-2">
                                                    @foreach($post['platforms'] as $platform)
                                                        <i class="fi {{ $this->getPlatformIcon($platform) }} {{ $this->getPlatformColor($platform) }}"></i>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $post['published_time']->format('M j, Y H:i') }}</span>
                                            </td>
                                            <td>
                                                <div class="engagement-stats">
                                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $post['engagement']['likes'] }} likes</span>
                                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $post['engagement']['comments'] }} comments</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex items-center space-x-2">
                                                    <button class="btn btn-sm btn-secondary">
                                                        <i class="fi fi-rr-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-primary">
                                                        <i class="fi fi-rr-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'scheduled')
            <div class="scheduled-tab">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Scheduled Posts</h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-4">
                            @foreach($scheduledPosts as $post)
                                <div class="scheduled-post-item">
                                    <div class="post-content">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $post['content'] }}</p>
                                        <div class="post-platforms">
                                            @foreach($post['platforms'] as $platform)
                                                <i class="fi {{ $this->getPlatformIcon($platform) }} {{ $this->getPlatformColor($platform) }}"></i>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="post-schedule">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $post['scheduled_time']->format('M j, Y H:i') }}</span>
                                        <span class="badge badge-warning">{{ ucfirst($post['status']) }}</span>
                                    </div>
                                    <div class="post-actions">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-danger">Cancel</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'calendar')
            <div class="calendar-tab">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Content Calendar</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-12">
                            <i class="fi fi-rr-calendar text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Advanced content calendar coming soon</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500">Plan and schedule your content across all platforms</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'analytics')
            <div class="analytics-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Platform Performance</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                @foreach($engagementData as $platform => $data)
                                    <div class="platform-performance">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <i class="fi {{ $this->getPlatformIcon($platform) }} {{ $this->getPlatformColor($platform) }}"></i>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($platform) }}</span>
                                            </div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $data['likes'] + $data['comments'] + $data['shares'] }} total</span>
                                        </div>
                                        <div class="performance-metrics">
                                            <div class="metric">
                                                <span class="label">Likes</span>
                                                <span class="value">{{ number_format($data['likes']) }}</span>
                                            </div>
                                            <div class="metric">
                                                <span class="label">Comments</span>
                                                <span class="value">{{ number_format($data['comments']) }}</span>
                                            </div>
                                            <div class="metric">
                                                <span class="label">Shares</span>
                                                <span class="value">{{ number_format($data['shares']) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Engagement Trends</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-12">
                                <i class="fi fi-rr-chart-line-up text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-600 dark:text-gray-400">Engagement trends visualization coming soon</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Create Post Modal -->
    @if($showPostModal)
        <div class="modal-backdrop" wire:click="$set('showPostModal', false)">
            <div class="modal-content modal-large" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">Create New Post</h3>
                    <button wire:click="$set('showPostModal', false)" class="modal-close">
                        <i class="fi fi-rr-cross"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="publishPost">
                        <div class="form-group">
                            <label class="form-label">Content</label>
                            <textarea wire:model="newPost.content" class="form-input" rows="4" placeholder="What's happening?"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Platforms</label>
                            <div class="platform-checkboxes">
                                @foreach($connectedAccounts as $account)
                                    @if($account['status'] === 'active')
                                        <label class="platform-checkbox">
                                            <input type="checkbox" wire:model="newPost.platforms" value="{{ $account['platform'] }}">
                                            <i class="fi {{ $this->getPlatformIcon($account['platform']) }} {{ $this->getPlatformColor($account['platform']) }}"></i>
                                            <span>{{ ucfirst($account['platform']) }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Media URL (Optional)</label>
                            <input type="url" wire:model="newPost.media_url" class="form-input" placeholder="https://example.com/image.jpg">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Schedule Time (Optional)</label>
                            <input type="datetime-local" wire:model="newPost.scheduled_time" class="form-input">
                        </div>
                        <div class="flex items-center justify-end space-x-4">
                            <button type="button" wire:click="$set('showPostModal', false)" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Publish Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.dashboard-page {
    @apply p-6 max-w-7xl mx-auto;
}

.page-header {
    @apply mb-8;
}

.kpi-card {
    @apply bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700;
}

.kpi-header {
    @apply flex items-center justify-between mb-3;
}

.kpi-title {
    @apply text-xs font-medium text-gray-600 dark:text-gray-400;
}

.kpi-icon {
    @apply w-8 h-8 rounded-full flex items-center justify-center;
}

.kpi-content {
    @apply space-y-1;
}

.kpi-value {
    @apply text-xl font-bold text-gray-900 dark:text-white;
}

.kpi-change {
    @apply text-xs font-medium;
}

.kpi-change.positive {
    @apply text-green-600 dark:text-green-400;
}

.kpi-change.negative {
    @apply text-red-600 dark:text-red-400;
}

.kpi-change.neutral {
    @apply text-gray-600 dark:text-gray-400;
}

.tabs-container {
    @apply mb-6;
}

.tab-button {
    @apply px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-600 transition-colors;
}

.tab-button.active {
    @apply text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400;
}

.account-overview-item {
    @apply p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.post-overview-item {
    @apply p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.post-content {
    @apply mb-2;
}

.post-platforms {
    @apply flex items-center space-x-2 mt-2;
}

.post-stats {
    @apply text-right space-y-1;
}

.account-card {
    @apply bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700;
}

.account-header {
    @apply flex items-center justify-between mb-4;
}

.account-stats {
    @apply grid grid-cols-3 gap-4 mb-4;
}

.stat-item {
    @apply text-center;
}

.stat-label {
    @apply block text-xs text-gray-500 dark:text-gray-400;
}

.stat-value {
    @apply block text-lg font-semibold text-gray-900 dark:text-white;
}

.account-actions {
    @apply flex items-center space-x-2;
}

.engagement-stats {
    @apply space-y-1;
}

.scheduled-post-item {
    @apply flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.post-schedule {
    @apply text-center space-y-2;
}

.post-actions {
    @apply flex items-center space-x-2;
}

.platform-performance {
    @apply p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.performance-metrics {
    @apply flex items-center justify-between;
}

.metric {
    @apply text-center;
}

.metric .label {
    @apply block text-xs text-gray-500 dark:text-gray-400;
}

.metric .value {
    @apply block text-sm font-semibold text-gray-900 dark:text-white;
}

.modal-backdrop {
    @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
}

.modal-content {
    @apply bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto;
}

.modal-large {
    @apply max-w-4xl;
}

.modal-header {
    @apply flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700;
}

.modal-title {
    @apply text-lg font-semibold text-gray-900 dark:text-white;
}

.modal-close {
    @apply text-gray-400 hover:text-gray-600 dark:hover:text-gray-300;
}

.modal-body {
    @apply p-6;
}

.form-group {
    @apply mb-4;
}

.form-label {
    @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2;
}

.platform-checkboxes {
    @apply flex flex-wrap gap-4;
}

.platform-checkbox {
    @apply flex items-center space-x-2 p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700;
}

.platform-checkbox input {
    @apply sr-only;
}

.platform-checkbox:has(input:checked) {
    @apply bg-blue-50 dark:bg-blue-900 border-blue-300 dark:border-blue-600;
}
</style>