<?php
/**
 * Instagram Management Console Component
 * Professional Instagram marketing automation interface
 */

use function Livewire\Volt\{mount, state, computed, on, layout};
use function Livewire\Volt\{rules, with, usesPagination};
use App\Models\InstagramAccount;
use App\Models\InstagramPost;
use App\Models\InstagramHashtag;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

layout('components.layouts.app');

state([
    'accounts' => [],
    'posts' => [],
    'hashtags' => [],
    'analytics' => null,
    'activeTab' => 'overview',
    'loading' => false,
    'showAccountModal' => false,
    'showPostModal' => false,
    'selectedAccount' => null,
    'selectedPost' => null,
    'searchTerm' => '',
    'dateRange' => '7days',
    'newPost' => [
        'caption' => '',
        'image_url' => '',
        'scheduled_time' => '',
        'hashtags' => []
    ]
]);

mount(function () {
    $this->loadAccounts();
    $this->loadPosts();
    $this->loadHashtags();
    $this->loadAnalytics();
});

$loadAccounts = function () {
    $this->accounts = InstagramAccount::where('user_id', auth()->id())->get();
};

$loadPosts = function () {
    $this->posts = InstagramPost::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->take(20)
        ->get();
};

$loadHashtags = function () {
    $this->hashtags = InstagramHashtag::where('user_id', auth()->id())
        ->orderBy('performance_score', 'desc')
        ->take(50)
        ->get();
};

$loadAnalytics = function () {
    $this->analytics = [
        'followers' => 12543,
        'engagement' => 4.2,
        'reach' => 45678,
        'impressions' => 89012,
        'posts_this_month' => 15,
        'growth_rate' => 12.5
    ];
};

$setActiveTab = function ($tab) {
    $this->activeTab = $tab;
};

$connectAccount = function () {
    $this->showAccountModal = true;
};

$createPost = function () {
    $this->showPostModal = true;
};

$schedulePost = function () {
    $this->loading = true;
    
    // Validate and schedule post
    $this->validate([
        'newPost.caption' => 'required|string|max:2200',
        'newPost.image_url' => 'required|url',
        'newPost.scheduled_time' => 'required|date|after:now'
    ]);
    
    InstagramPost::create([
        'user_id' => auth()->id(),
        'caption' => $this->newPost['caption'],
        'image_url' => $this->newPost['image_url'],
        'scheduled_time' => $this->newPost['scheduled_time'],
        'hashtags' => json_encode($this->newPost['hashtags']),
        'status' => 'scheduled'
    ]);
    
    $this->showPostModal = false;
    $this->newPost = [
        'caption' => '',
        'image_url' => '',
        'scheduled_time' => '',
        'hashtags' => []
    ];
    
    $this->loadPosts();
    $this->loading = false;
    
    session()->flash('success', 'Post scheduled successfully!');
};

?>

<div class="console-page">
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Instagram Management</h1>
                <p class="text-gray-600 dark:text-gray-400">Advanced Instagram marketing automation and analytics</p>
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
                wire:click="setActiveTab('posts')"
                class="tab-button {{ $activeTab === 'posts' ? 'active' : '' }}">
                <i class="fi fi-rr-picture mr-2"></i>
                Posts
            </button>
            <button 
                wire:click="setActiveTab('accounts')"
                class="tab-button {{ $activeTab === 'accounts' ? 'active' : '' }}">
                <i class="fi fi-rr-user mr-2"></i>
                Accounts
            </button>
            <button 
                wire:click="setActiveTab('hashtags')"
                class="tab-button {{ $activeTab === 'hashtags' ? 'active' : '' }}">
                <i class="fi fi-rr-hashtag mr-2"></i>
                Hashtags
            </button>
            <button 
                wire:click="setActiveTab('analytics')"
                class="tab-button {{ $activeTab === 'analytics' ? 'active' : '' }}">
                <i class="fi fi-rr-stats mr-2"></i>
                Analytics
            </button>
            <button 
                wire:click="setActiveTab('competitor')"
                class="tab-button {{ $activeTab === 'competitor' ? 'active' : '' }}">
                <i class="fi fi-rr-search mr-2"></i>
                Competitor Analysis
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        @if($activeTab === 'overview')
            <div class="overview-tab">
                <!-- Analytics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Followers</h3>
                                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ number_format($analytics['followers'] ?? 0) }}
                                    </p>
                                    <p class="text-sm text-green-600 dark:text-green-400">
                                        +{{ $analytics['growth_rate'] ?? 0 }}% this month
                                    </p>
                                </div>
                                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                                    <i class="fi fi-rr-users text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Engagement Rate</h3>
                                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                        {{ $analytics['engagement'] ?? 0 }}%
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Average engagement
                                    </p>
                                </div>
                                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                                    <i class="fi fi-rr-heart text-purple-600 dark:text-purple-400 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Posts This Month</h3>
                                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                        {{ $analytics['posts_this_month'] ?? 0 }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Content published
                                    </p>
                                </div>
                                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                                    <i class="fi fi-rr-picture text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Posts -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Posts</h3>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($posts as $post)
                                <div class="post-card">
                                    <div class="post-image">
                                        @if($post->image_url)
                                            <img src="{{ $post->image_url }}" alt="Post image" class="w-full h-48 object-cover rounded-lg">
                                        @else
                                            <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                <i class="fi fi-rr-picture text-gray-400 text-3xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="post-content mt-3">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                            {{ $post->caption }}
                                        </p>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="badge badge-{{ $post->status === 'published' ? 'success' : 'warning' }}">
                                                {{ ucfirst($post->status) }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $post->created_at->format('M j, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <i class="fi fi-rr-picture text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-600 dark:text-gray-400">No posts yet. Create your first post!</p>
                                </div>
                            @endforelse
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
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Post Management</h3>
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
                                        <th>Image</th>
                                        <th>Caption</th>
                                        <th>Status</th>
                                        <th>Scheduled</th>
                                        <th>Engagement</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($posts as $post)
                                        <tr>
                                            <td>
                                                @if($post->image_url)
                                                    <img src="{{ $post->image_url }}" alt="Post" class="w-12 h-12 object-cover rounded">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                        <i class="fi fi-rr-picture text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-900 dark:text-white line-clamp-2">
                                                    {{ Str::limit($post->caption, 50) }}
                                                </p>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $post->status === 'published' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($post->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $post->scheduled_time ? $post->scheduled_time->format('M j, Y H:i') : 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $post->engagement_rate ?? 0 }}%
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex items-center space-x-2">
                                                    <button class="btn btn-sm btn-secondary">
                                                        <i class="fi fi-rr-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-8">
                                                <i class="fi fi-rr-picture text-gray-400 text-3xl mb-4"></i>
                                                <p class="text-gray-600 dark:text-gray-400">No posts found. Create your first post!</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Connected Accounts</h3>
                            <button wire:click="connectAccount" class="btn btn-primary">
                                <i class="fi fi-rr-plus mr-2"></i>
                                Connect Account
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($accounts as $account)
                                <div class="account-card">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                                            <i class="fi fi-brands-instagram text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $account->username }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $account->followers_count }} followers</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="badge badge-success">Connected</span>
                                        <button class="btn btn-sm btn-secondary">
                                            <i class="fi fi-rr-settings"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <i class="fi fi-brands-instagram text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">No accounts connected yet</p>
                                    <button wire:click="connectAccount" class="btn btn-primary">
                                        Connect Your First Account
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'hashtags')
            <div class="hashtags-tab">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Hashtag Research & Performance</h3>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($hashtags as $hashtag)
                                <div class="hashtag-card">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="hashtag-name">#{{ $hashtag->name }}</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $hashtag->post_count }} posts
                                        </span>
                                    </div>
                                    <div class="hashtag-metrics">
                                        <div class="metric">
                                            <span class="label">Performance</span>
                                            <span class="value">{{ $hashtag->performance_score }}/100</span>
                                        </div>
                                        <div class="metric">
                                            <span class="label">Difficulty</span>
                                            <span class="value">{{ $hashtag->difficulty }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <i class="fi fi-rr-hashtag text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-600 dark:text-gray-400">No hashtag data available</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'analytics')
            <div class="analytics-tab">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Performance Overview</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                <div class="metric-item">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Total Reach</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($analytics['reach'] ?? 0) }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                                    </div>
                                </div>
                                <div class="metric-item">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Impressions</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($analytics['impressions'] ?? 0) }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: 85%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Growth Metrics</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-green-600 dark:text-green-400 mb-2">
                                    +{{ $analytics['growth_rate'] ?? 0 }}%
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">Monthly Growth Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'competitor')
            <div class="competitor-tab">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Competitor Analysis</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-12">
                            <i class="fi fi-rr-search text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Competitor analysis coming soon</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500">Track your competitors' performance and strategies</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modals -->
    @if($showPostModal)
        <div class="modal-backdrop" wire:click="$set('showPostModal', false)">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">Create New Post</h3>
                    <button wire:click="$set('showPostModal', false)" class="modal-close">
                        <i class="fi fi-rr-cross"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="schedulePost">
                        <div class="form-group">
                            <label class="form-label">Caption</label>
                            <textarea wire:model="newPost.caption" class="form-input" rows="4" placeholder="Write your caption..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Image URL</label>
                            <input type="url" wire:model="newPost.image_url" class="form-input" placeholder="https://example.com/image.jpg">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Schedule Time</label>
                            <input type="datetime-local" wire:model="newPost.scheduled_time" class="form-input">
                        </div>
                        <div class="flex items-center justify-end space-x-4">
                            <button type="button" wire:click="$set('showPostModal', false)" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>Schedule Post</span>
                                <span wire:loading>Scheduling...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.console-page {
    @apply p-6 max-w-7xl mx-auto;
}

.page-header {
    @apply mb-8;
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

.tab-content {
    @apply mt-6;
}

.post-card {
    @apply bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow;
}

.account-card {
    @apply bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700;
}

.hashtag-card {
    @apply bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700;
}

.hashtag-name {
    @apply text-blue-600 dark:text-blue-400 font-semibold;
}

.hashtag-metrics {
    @apply flex items-center justify-between mt-3;
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

.metric-item {
    @apply space-y-2;
}

.modal-backdrop {
    @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
}

.modal-content {
    @apply bg-white dark:bg-gray-800 rounded-lg max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto;
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
</style>