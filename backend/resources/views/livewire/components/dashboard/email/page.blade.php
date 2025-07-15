<?php
/**
 * Email Marketing Console Component
 * Professional email campaign management interface
 */

use function Livewire\Volt\{mount, state, computed, on, layout};
use App\Models\User;
use Illuminate\Support\Collection;

layout('components.layouts.app');

state([
    'campaigns' => [],
    'subscribers' => [],
    'templates' => [],
    'analytics' => [],
    'activeTab' => 'campaigns',
    'selectedCampaign' => null,
    'showCampaignModal' => false,
    'showTemplateModal' => false,
    'emailStats' => [],
    'searchTerm' => '',
    'dateRange' => '30days',
    'newCampaign' => [
        'name' => '',
        'subject' => '',
        'template_id' => '',
        'recipients' => 'all',
        'schedule_type' => 'now',
        'schedule_time' => '',
        'content' => ''
    ]
]);

mount(function () {
    $this->loadCampaigns();
    $this->loadSubscribers();
    $this->loadTemplates();
    $this->loadEmailStats();
});

$loadCampaigns = function () {
    $this->campaigns = [
        ['id' => 1, 'name' => 'Welcome Series', 'subject' => 'Welcome to Mewayz!', 'status' => 'active', 'sent' => 1250, 'opened' => 832, 'clicked' => 156, 'created_at' => now()->subDays(5)],
        ['id' => 2, 'name' => 'Product Launch', 'subject' => 'New Features Released', 'status' => 'draft', 'sent' => 0, 'opened' => 0, 'clicked' => 0, 'created_at' => now()->subDays(2)],
        ['id' => 3, 'name' => 'Monthly Newsletter', 'subject' => 'Your Monthly Update', 'status' => 'scheduled', 'sent' => 0, 'opened' => 0, 'clicked' => 0, 'created_at' => now()->subDays(1)]
    ];
};

$loadSubscribers = function () {
    $this->subscribers = User::where('role', '!=', 'admin')
        ->where('email_verified_at', '!=', null)
        ->orderBy('created_at', 'desc')
        ->take(100)
        ->get();
};

$loadTemplates = function () {
    $this->templates = [
        ['id' => 1, 'name' => 'Welcome Template', 'type' => 'welcome', 'created_at' => now()->subDays(10)],
        ['id' => 2, 'name' => 'Newsletter Template', 'type' => 'newsletter', 'created_at' => now()->subDays(8)],
        ['id' => 3, 'name' => 'Product Update', 'type' => 'product', 'created_at' => now()->subDays(6)]
    ];
};

$loadEmailStats = function () {
    $this->emailStats = [
        'total_campaigns' => 15,
        'total_subscribers' => 5420,
        'emails_sent' => 23450,
        'open_rate' => 24.5,
        'click_rate' => 3.2,
        'unsubscribe_rate' => 0.8
    ];
};

$setActiveTab = function ($tab) {
    $this->activeTab = $tab;
};

$createCampaign = function () {
    $this->showCampaignModal = true;
};

$saveCampaign = function () {
    $this->validate([
        'newCampaign.name' => 'required|string|max:255',
        'newCampaign.subject' => 'required|string|max:255',
        'newCampaign.content' => 'required|string'
    ]);

    // Save campaign logic here
    $this->showCampaignModal = false;
    $this->newCampaign = [
        'name' => '',
        'subject' => '',
        'template_id' => '',
        'recipients' => 'all',
        'schedule_type' => 'now',
        'schedule_time' => '',
        'content' => ''
    ];

    session()->flash('success', 'Campaign created successfully!');
    $this->loadCampaigns();
};

?>

<div class="console-page">
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Email Marketing</h1>
                <p class="text-gray-600 dark:text-gray-400">Advanced email marketing campaigns and automation</p>
            </div>
            <div class="flex items-center space-x-4">
                <button class="btn btn-secondary">
                    <i class="fi fi-rr-stats mr-2"></i>
                    Analytics
                </button>
                <button wire:click="createCampaign" class="btn btn-primary">
                    <i class="fi fi-rr-plus mr-2"></i>
                    Create Campaign
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <div class="metric-card">
            <div class="metric-icon bg-blue-100 dark:bg-blue-900">
                <i class="fi fi-rr-envelope text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Total Campaigns</h3>
                <p class="metric-value">{{ number_format($emailStats['total_campaigns'] ?? 0) }}</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-green-100 dark:bg-green-900">
                <i class="fi fi-rr-users text-green-600 dark:text-green-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Subscribers</h3>
                <p class="metric-value">{{ number_format($emailStats['total_subscribers'] ?? 0) }}</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-purple-100 dark:bg-purple-900">
                <i class="fi fi-rr-paper-plane text-purple-600 dark:text-purple-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Emails Sent</h3>
                <p class="metric-value">{{ number_format($emailStats['emails_sent'] ?? 0) }}</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-yellow-100 dark:bg-yellow-900">
                <i class="fi fi-rr-eye text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Open Rate</h3>
                <p class="metric-value">{{ $emailStats['open_rate'] ?? 0 }}%</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-indigo-100 dark:bg-indigo-900">
                <i class="fi fi-rr-cursor-finger text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Click Rate</h3>
                <p class="metric-value">{{ $emailStats['click_rate'] ?? 0 }}%</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-red-100 dark:bg-red-900">
                <i class="fi fi-rr-user-remove text-red-600 dark:text-red-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Unsubscribe Rate</h3>
                <p class="metric-value">{{ $emailStats['unsubscribe_rate'] ?? 0 }}%</p>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tabs-container">
        <nav class="flex space-x-8 border-b border-gray-200 dark:border-gray-700">
            <button 
                wire:click="setActiveTab('campaigns')"
                class="tab-button {{ $activeTab === 'campaigns' ? 'active' : '' }}">
                <i class="fi fi-rr-envelope mr-2"></i>
                Campaigns
            </button>
            <button 
                wire:click="setActiveTab('subscribers')"
                class="tab-button {{ $activeTab === 'subscribers' ? 'active' : '' }}">
                <i class="fi fi-rr-users mr-2"></i>
                Subscribers
            </button>
            <button 
                wire:click="setActiveTab('templates')"
                class="tab-button {{ $activeTab === 'templates' ? 'active' : '' }}">
                <i class="fi fi-rr-document mr-2"></i>
                Templates
            </button>
            <button 
                wire:click="setActiveTab('automation')"
                class="tab-button {{ $activeTab === 'automation' ? 'active' : '' }}">
                <i class="fi fi-rr-refresh mr-2"></i>
                Automation
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
        @if($activeTab === 'campaigns')
            <div class="campaigns-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Email Campaigns</h3>
                            <button wire:click="createCampaign" class="btn btn-primary">
                                <i class="fi fi-rr-plus mr-2"></i>
                                Create Campaign
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Campaign Name</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Sent</th>
                                        <th>Opened</th>
                                        <th>Clicked</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($campaigns as $campaign)
                                        <tr>
                                            <td>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $campaign['name'] }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $campaign['subject'] }}</p>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $campaign['status'] === 'active' ? 'success' : ($campaign['status'] === 'draft' ? 'secondary' : 'warning') }}">
                                                    {{ ucfirst($campaign['status']) }}
                                                </span>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-900 dark:text-white">{{ number_format($campaign['sent']) }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-900 dark:text-white">
                                                    {{ number_format($campaign['opened']) }}
                                                    @if($campaign['sent'] > 0)
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            ({{ number_format(($campaign['opened'] / $campaign['sent']) * 100, 1) }}%)
                                                        </span>
                                                    @endif
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-900 dark:text-white">
                                                    {{ number_format($campaign['clicked']) }}
                                                    @if($campaign['sent'] > 0)
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            ({{ number_format(($campaign['clicked'] / $campaign['sent']) * 100, 1) }}%)
                                                        </span>
                                                    @endif
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($campaign['created_at'])->format('M j, Y') }}
                                                </p>
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
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-8">
                                                <i class="fi fi-rr-envelope text-gray-400 text-3xl mb-4"></i>
                                                <p class="text-gray-600 dark:text-gray-400">No campaigns found</p>
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

        @if($activeTab === 'subscribers')
            <div class="subscribers-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Subscriber Management</h3>
                            <div class="flex items-center space-x-4">
                                <button class="btn btn-secondary">
                                    <i class="fi fi-rr-download mr-2"></i>
                                    Export
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fi fi-rr-plus mr-2"></i>
                                    Import
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Subscriber</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Subscribed</th>
                                        <th>Last Activity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subscribers as $subscriber)
                                        <tr>
                                            <td>
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-semibold">{{ substr($subscriber->name, 0, 1) }}</span>
                                                    </div>
                                                    <p class="font-medium text-gray-900 dark:text-white">{{ $subscriber->name }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $subscriber->email }}</p>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">Active</span>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $subscriber->created_at->format('M j, Y') }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $subscriber->last_login_at ? $subscriber->last_login_at->diffForHumans() : 'Never' }}
                                                </p>
                                            </td>
                                            <td>
                                                <div class="flex items-center space-x-2">
                                                    <button class="btn btn-sm btn-secondary">
                                                        <i class="fi fi-rr-envelope"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fi fi-rr-user-remove"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-8">
                                                <i class="fi fi-rr-users text-gray-400 text-3xl mb-4"></i>
                                                <p class="text-gray-600 dark:text-gray-400">No subscribers found</p>
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

        @if($activeTab === 'templates')
            <div class="templates-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Email Templates</h3>
                            <button wire:click="$set('showTemplateModal', true)" class="btn btn-primary">
                                <i class="fi fi-rr-plus mr-2"></i>
                                Create Template
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($templates as $template)
                                <div class="template-card">
                                    <div class="template-preview">
                                        <div class="w-full h-32 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <i class="fi fi-rr-document text-gray-400 text-2xl"></i>
                                        </div>
                                    </div>
                                    <div class="template-info">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $template['name'] }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($template['type']) }} Template</p>
                                        <div class="template-actions">
                                            <button class="btn btn-sm btn-primary">Use Template</button>
                                            <button class="btn btn-sm btn-secondary">Edit</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <i class="fi fi-rr-document text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-600 dark:text-gray-400">No templates found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Create Campaign Modal -->
    @if($showCampaignModal)
        <div class="modal-backdrop" wire:click="$set('showCampaignModal', false)">
            <div class="modal-content modal-large" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">Create New Campaign</h3>
                    <button wire:click="$set('showCampaignModal', false)" class="modal-close">
                        <i class="fi fi-rr-cross"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveCampaign">
                        <div class="form-group">
                            <label class="form-label">Campaign Name</label>
                            <input type="text" wire:model="newCampaign.name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subject Line</label>
                            <input type="text" wire:model="newCampaign.subject" class="form-input" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Recipients</label>
                                <select wire:model="newCampaign.recipients" class="form-select">
                                    <option value="all">All Subscribers</option>
                                    <option value="active">Active Subscribers</option>
                                    <option value="segment">Specific Segment</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Schedule</label>
                                <select wire:model="newCampaign.schedule_type" class="form-select">
                                    <option value="now">Send Now</option>
                                    <option value="later">Schedule for Later</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Content</label>
                            <textarea wire:model="newCampaign.content" class="form-input" rows="8" required></textarea>
                        </div>
                        <div class="flex items-center justify-end space-x-4">
                            <button type="button" wire:click="$set('showCampaignModal', false)" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Campaign</button>
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

.metric-card {
    @apply bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700;
}

.metric-icon {
    @apply w-10 h-10 rounded-full flex items-center justify-center mb-3;
}

.metric-title {
    @apply text-sm font-medium text-gray-600 dark:text-gray-400;
}

.metric-value {
    @apply text-2xl font-bold text-gray-900 dark:text-white;
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

.template-card {
    @apply bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700;
}

.template-preview {
    @apply mb-4;
}

.template-info {
    @apply space-y-3;
}

.template-actions {
    @apply flex items-center space-x-2;
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

.form-select {
    @apply form-input;
}
</style>