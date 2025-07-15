<?php
/**
 * Workspace Management Console Component
 * Professional workspace and team collaboration interface
 */

use function Livewire\Volt\{mount, state, computed, on, layout};
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;

layout('components.layouts.app');

state([
    'workspaces' => [],
    'currentWorkspace' => null,
    'teamMembers' => [],
    'invitations' => [],
    'workspaceSettings' => [],
    'activeTab' => 'overview',
    'showWorkspaceModal' => false,
    'showInviteModal' => false,
    'showSettingsModal' => false,
    'workspaceStats' => [],
    'recentActivity' => [],
    'roles' => ['admin', 'editor', 'viewer'],
    'newWorkspace' => [
        'name' => '',
        'description' => '',
        'type' => 'business',
        'privacy' => 'private'
    ],
    'newInvitation' => [
        'email' => '',
        'role' => 'editor',
        'message' => ''
    ]
]);

mount(function () {
    $this->loadWorkspaces();
    $this->loadCurrentWorkspace();
    $this->loadTeamMembers();
    $this->loadInvitations();
    $this->loadWorkspaceStats();
    $this->loadRecentActivity();
});

$loadWorkspaces = function () {
    $this->workspaces = Workspace::where('user_id', auth()->id())->get();
};

$loadCurrentWorkspace = function () {
    $this->currentWorkspace = auth()->user()->team();
    $this->workspaceSettings = [
        'name' => $this->currentWorkspace->name ?? 'My Workspace',
        'description' => $this->currentWorkspace->description ?? 'Professional workspace for team collaboration',
        'timezone' => 'UTC',
        'language' => 'en',
        'notifications' => true,
        'public_access' => false
    ];
};

$loadTeamMembers = function () {
    $this->teamMembers = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'admin', 'status' => 'active', 'last_seen' => now()->subMinutes(5)],
        ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'editor', 'status' => 'active', 'last_seen' => now()->subHours(2)],
        ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'role' => 'viewer', 'status' => 'inactive', 'last_seen' => now()->subDays(3)]
    ];
};

$loadInvitations = function () {
    $this->invitations = [
        ['id' => 1, 'email' => 'alice@example.com', 'role' => 'editor', 'status' => 'pending', 'sent_at' => now()->subDays(2)],
        ['id' => 2, 'email' => 'charlie@example.com', 'role' => 'viewer', 'status' => 'expired', 'sent_at' => now()->subDays(8)]
    ];
};

$loadWorkspaceStats = function () {
    $this->workspaceStats = [
        'total_members' => 15,
        'active_members' => 12,
        'pending_invitations' => 3,
        'total_projects' => 8,
        'storage_used' => 2.4, // GB
        'storage_limit' => 10, // GB
        'api_calls_today' => 1247,
        'api_limit' => 10000
    ];
};

$loadRecentActivity = function () {
    $this->recentActivity = [
        ['user' => 'John Doe', 'action' => 'created a new site', 'time' => now()->subMinutes(15)],
        ['user' => 'Jane Smith', 'action' => 'updated workspace settings', 'time' => now()->subHours(1)],
        ['user' => 'Bob Johnson', 'action' => 'invited new member', 'time' => now()->subHours(3)],
        ['user' => 'System', 'action' => 'workspace backup completed', 'time' => now()->subHours(6)]
    ];
};

$setActiveTab = function ($tab) {
    $this->activeTab = $tab;
};

$createWorkspace = function () {
    $this->showWorkspaceModal = true;
};

$inviteMember = function () {
    $this->showInviteModal = true;
};

$saveWorkspace = function () {
    $this->validate([
        'newWorkspace.name' => 'required|string|max:255',
        'newWorkspace.description' => 'required|string|max:500'
    ]);

    Workspace::create([
        'name' => $this->newWorkspace['name'],
        'description' => $this->newWorkspace['description'],
        'type' => $this->newWorkspace['type'],
        'privacy' => $this->newWorkspace['privacy'],
        'user_id' => auth()->id()
    ]);

    $this->showWorkspaceModal = false;
    $this->newWorkspace = [
        'name' => '',
        'description' => '',
        'type' => 'business',
        'privacy' => 'private'
    ];

    $this->loadWorkspaces();
    session()->flash('success', 'Workspace created successfully!');
};

$sendInvitation = function () {
    $this->validate([
        'newInvitation.email' => 'required|email',
        'newInvitation.role' => 'required|in:admin,editor,viewer'
    ]);

    // Send invitation logic here
    $this->showInviteModal = false;
    $this->newInvitation = [
        'email' => '',
        'role' => 'editor',
        'message' => ''
    ];

    session()->flash('success', 'Invitation sent successfully!');
    $this->loadInvitations();
};

$updateMemberRole = function ($memberId, $newRole) {
    // Update member role logic
    session()->flash('success', 'Member role updated successfully!');
};

$removeMember = function ($memberId) {
    // Remove member logic
    session()->flash('success', 'Member removed successfully!');
};

?>

<div class="console-page">
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Workspace Management</h1>
                <p class="text-gray-600 dark:text-gray-400">Advanced workspace and team collaboration tools</p>
            </div>
            <div class="flex items-center space-x-4">
                <button wire:click="inviteMember" class="btn btn-secondary">
                    <i class="fi fi-rr-user-add mr-2"></i>
                    Invite Member
                </button>
                <button wire:click="createWorkspace" class="btn btn-primary">
                    <i class="fi fi-rr-plus mr-2"></i>
                    Create Workspace
                </button>
            </div>
        </div>
    </div>

    <!-- Workspace Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="overview-card">
            <div class="card-icon bg-blue-100 dark:bg-blue-900">
                <i class="fi fi-rr-users text-blue-600 dark:text-blue-400 text-2xl"></i>
            </div>
            <div class="card-content">
                <h3 class="card-title">Team Members</h3>
                <p class="card-value">{{ $workspaceStats['total_members'] }}</p>
                <p class="card-subtitle">{{ $workspaceStats['active_members'] }} active</p>
            </div>
        </div>

        <div class="overview-card">
            <div class="card-icon bg-green-100 dark:bg-green-900">
                <i class="fi fi-rr-folder text-green-600 dark:text-green-400 text-2xl"></i>
            </div>
            <div class="card-content">
                <h3 class="card-title">Projects</h3>
                <p class="card-value">{{ $workspaceStats['total_projects'] }}</p>
                <p class="card-subtitle">Active projects</p>
            </div>
        </div>

        <div class="overview-card">
            <div class="card-icon bg-yellow-100 dark:bg-yellow-900">
                <i class="fi fi-rr-disk text-yellow-600 dark:text-yellow-400 text-2xl"></i>
            </div>
            <div class="card-content">
                <h3 class="card-title">Storage</h3>
                <p class="card-value">{{ $workspaceStats['storage_used'] }}GB</p>
                <p class="card-subtitle">of {{ $workspaceStats['storage_limit'] }}GB used</p>
            </div>
        </div>

        <div class="overview-card">
            <div class="card-icon bg-purple-100 dark:bg-purple-900">
                <i class="fi fi-rr-stats text-purple-600 dark:text-purple-400 text-2xl"></i>
            </div>
            <div class="card-content">
                <h3 class="card-title">API Calls</h3>
                <p class="card-value">{{ number_format($workspaceStats['api_calls_today']) }}</p>
                <p class="card-subtitle">Today</p>
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
                wire:click="setActiveTab('members')"
                class="tab-button {{ $activeTab === 'members' ? 'active' : '' }}">
                <i class="fi fi-rr-users mr-2"></i>
                Members
            </button>
            <button 
                wire:click="setActiveTab('invitations')"
                class="tab-button {{ $activeTab === 'invitations' ? 'active' : '' }}">
                <i class="fi fi-rr-envelope mr-2"></i>
                Invitations
            </button>
            <button 
                wire:click="setActiveTab('settings')"
                class="tab-button {{ $activeTab === 'settings' ? 'active' : '' }}">
                <i class="fi fi-rr-settings mr-2"></i>
                Settings
            </button>
            <button 
                wire:click="setActiveTab('billing')"
                class="tab-button {{ $activeTab === 'billing' ? 'active' : '' }}">
                <i class="fi fi-rr-credit-card mr-2"></i>
                Billing
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        @if($activeTab === 'overview')
            <div class="overview-tab">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Current Workspace Info -->
                    <div class="lg:col-span-2">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Current Workspace</h3>
                            </div>
                            <div class="card-body">
                                <div class="workspace-info">
                                    <div class="workspace-header">
                                        <div class="workspace-avatar">
                                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-2xl">{{ substr($workspaceSettings['name'], 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="workspace-details">
                                            <h4 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $workspaceSettings['name'] }}</h4>
                                            <p class="text-gray-600 dark:text-gray-400">{{ $workspaceSettings['description'] }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="workspace-stats">
                                        <div class="stat-grid">
                                            <div class="stat-item">
                                                <span class="stat-label">Members</span>
                                                <span class="stat-value">{{ $workspaceStats['total_members'] }}</span>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-label">Projects</span>
                                                <span class="stat-value">{{ $workspaceStats['total_projects'] }}</span>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-label">Storage</span>
                                                <span class="stat-value">{{ $workspaceStats['storage_used'] }}GB</span>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-label">API Calls</span>
                                                <span class="stat-value">{{ number_format($workspaceStats['api_calls_today']) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="lg:col-span-1">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
                            </div>
                            <div class="card-body">
                                <div class="activity-timeline">
                                    @foreach($recentActivity as $activity)
                                        <div class="activity-item">
                                            <div class="activity-icon">
                                                <i class="fi fi-rr-user text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div class="activity-content">
                                                <p class="text-sm text-gray-900 dark:text-white">
                                                    <strong>{{ $activity['user'] }}</strong> {{ $activity['action'] }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['time']->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'members')
            <div class="members-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Team Members</h3>
                            <button wire:click="inviteMember" class="btn btn-primary">
                                <i class="fi fi-rr-plus mr-2"></i>
                                Invite Member
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Seen</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teamMembers as $member)
                                        <tr>
                                            <td>
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-semibold text-sm">{{ substr($member['name'], 0, 1) }}</span>
                                                    </div>
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ $member['name'] }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $member['email'] }}</span>
                                            </td>
                                            <td>
                                                <select wire:change="updateMemberRole({{ $member['id'] }}, $event.target.value)" class="form-select text-sm">
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role }}" {{ $member['role'] === $role ? 'selected' : '' }}>
                                                            {{ ucfirst($role) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $member['status'] === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($member['status']) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $member['last_seen']->diffForHumans() }}</span>
                                            </td>
                                            <td>
                                                <div class="flex items-center space-x-2">
                                                    <button class="btn btn-sm btn-secondary">
                                                        <i class="fi fi-rr-edit"></i>
                                                    </button>
                                                    <button wire:click="removeMember({{ $member['id'] }})" class="btn btn-sm btn-danger">
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

        @if($activeTab === 'invitations')
            <div class="invitations-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pending Invitations</h3>
                            <button wire:click="inviteMember" class="btn btn-primary">
                                <i class="fi fi-rr-plus mr-2"></i>
                                Send Invitation
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="space-y-4">
                            @forelse($invitations as $invitation)
                                <div class="invitation-item">
                                    <div class="invitation-info">
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $invitation['email'] }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Invited as {{ ucfirst($invitation['role']) }} • {{ $invitation['sent_at']->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="invitation-actions">
                                        <span class="badge badge-{{ $invitation['status'] === 'pending' ? 'warning' : 'danger' }}">
                                            {{ ucfirst($invitation['status']) }}
                                        </span>
                                        <button class="btn btn-sm btn-secondary">Resend</button>
                                        <button class="btn btn-sm btn-danger">Cancel</button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fi fi-rr-envelope text-gray-400 text-3xl mb-4"></i>
                                    <p class="text-gray-600 dark:text-gray-400">No pending invitations</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'settings')
            <div class="settings-tab">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Workspace Settings</h3>
                    </div>
                    <div class="card-body">
                        <div class="settings-form">
                            <div class="form-group">
                                <label class="form-label">Workspace Name</label>
                                <input type="text" wire:model="workspaceSettings.name" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea wire:model="workspaceSettings.description" class="form-input" rows="3"></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label">Timezone</label>
                                    <select wire:model="workspaceSettings.timezone" class="form-select">
                                        <option value="UTC">UTC</option>
                                        <option value="America/New_York">Eastern Time</option>
                                        <option value="America/Chicago">Central Time</option>
                                        <option value="America/Denver">Mountain Time</option>
                                        <option value="America/Los_Angeles">Pacific Time</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Language</label>
                                    <select wire:model="workspaceSettings.language" class="form-select">
                                        <option value="en">English</option>
                                        <option value="es">Spanish</option>
                                        <option value="fr">French</option>
                                        <option value="de">German</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Preferences</label>
                                <div class="space-y-3">
                                    <label class="checkbox-label">
                                        <input type="checkbox" wire:model="workspaceSettings.notifications">
                                        <span>Enable email notifications</span>
                                    </label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" wire:model="workspaceSettings.public_access">
                                        <span>Allow public access to workspace</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex items-center justify-end">
                                <button class="btn btn-primary">Save Settings</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'billing')
            <div class="billing-tab">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Billing & Usage</h3>
                    </div>
                    <div class="card-body">
                        <div class="billing-overview">
                            <div class="billing-section">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Plan</h4>
                                <div class="plan-info">
                                    <div class="plan-details">
                                        <h5 class="font-semibold text-gray-900 dark:text-white">Professional Plan</h5>
                                        <p class="text-gray-600 dark:text-gray-400">$29/month • Billed monthly</p>
                                    </div>
                                    <button class="btn btn-secondary">Upgrade Plan</button>
                                </div>
                            </div>

                            <div class="billing-section">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Usage</h4>
                                <div class="usage-metrics">
                                    <div class="usage-item">
                                        <div class="usage-header">
                                            <span class="usage-label">Storage</span>
                                            <span class="usage-value">{{ $workspaceStats['storage_used'] }}GB / {{ $workspaceStats['storage_limit'] }}GB</span>
                                        </div>
                                        <div class="usage-bar">
                                            <div class="usage-fill" style="width: {{ ($workspaceStats['storage_used'] / $workspaceStats['storage_limit']) * 100 }}%"></div>
                                        </div>
                                    </div>
                                    <div class="usage-item">
                                        <div class="usage-header">
                                            <span class="usage-label">API Calls</span>
                                            <span class="usage-value">{{ number_format($workspaceStats['api_calls_today']) }} / {{ number_format($workspaceStats['api_limit']) }}</span>
                                        </div>
                                        <div class="usage-bar">
                                            <div class="usage-fill" style="width: {{ ($workspaceStats['api_calls_today'] / $workspaceStats['api_limit']) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Create Workspace Modal -->
    @if($showWorkspaceModal)
        <div class="modal-backdrop" wire:click="$set('showWorkspaceModal', false)">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">Create New Workspace</h3>
                    <button wire:click="$set('showWorkspaceModal', false)" class="modal-close">
                        <i class="fi fi-rr-cross"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveWorkspace">
                        <div class="form-group">
                            <label class="form-label">Workspace Name</label>
                            <input type="text" wire:model="newWorkspace.name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea wire:model="newWorkspace.description" class="form-input" rows="3" required></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Type</label>
                                <select wire:model="newWorkspace.type" class="form-select">
                                    <option value="business">Business</option>
                                    <option value="personal">Personal</option>
                                    <option value="education">Education</option>
                                    <option value="nonprofit">Non-profit</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Privacy</label>
                                <select wire:model="newWorkspace.privacy" class="form-select">
                                    <option value="private">Private</option>
                                    <option value="public">Public</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end space-x-4">
                            <button type="button" wire:click="$set('showWorkspaceModal', false)" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Workspace</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Invite Member Modal -->
    @if($showInviteModal)
        <div class="modal-backdrop" wire:click="$set('showInviteModal', false)">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">Invite Team Member</h3>
                    <button wire:click="$set('showInviteModal', false)" class="modal-close">
                        <i class="fi fi-rr-cross"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="sendInvitation">
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" wire:model="newInvitation.email" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <select wire:model="newInvitation.role" class="form-select">
                                @foreach($roles as $role)
                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Personal Message (Optional)</label>
                            <textarea wire:model="newInvitation.message" class="form-input" rows="3" placeholder="Add a personal message to the invitation..."></textarea>
                        </div>
                        <div class="flex items-center justify-end space-x-4">
                            <button type="button" wire:click="$set('showInviteModal', false)" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Send Invitation</button>
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

.overview-card {
    @apply bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700;
}

.card-icon {
    @apply w-12 h-12 rounded-full flex items-center justify-center mb-4;
}

.card-content {
    @apply space-y-1;
}

.card-title {
    @apply text-sm font-medium text-gray-600 dark:text-gray-400;
}

.card-value {
    @apply text-2xl font-bold text-gray-900 dark:text-white;
}

.card-subtitle {
    @apply text-sm text-gray-500 dark:text-gray-500;
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

.workspace-info {
    @apply space-y-6;
}

.workspace-header {
    @apply flex items-center space-x-4;
}

.workspace-avatar {
    @apply flex-shrink-0;
}

.workspace-details {
    @apply flex-1;
}

.workspace-stats {
    @apply mt-6;
}

.stat-grid {
    @apply grid grid-cols-2 md:grid-cols-4 gap-4;
}

.stat-item {
    @apply text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.stat-label {
    @apply block text-sm text-gray-600 dark:text-gray-400;
}

.stat-value {
    @apply block text-lg font-semibold text-gray-900 dark:text-white;
}

.activity-timeline {
    @apply space-y-4;
}

.activity-item {
    @apply flex items-start space-x-3;
}

.activity-icon {
    @apply w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400;
}

.activity-content {
    @apply flex-1;
}

.invitation-item {
    @apply flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.invitation-info {
    @apply flex-1;
}

.invitation-actions {
    @apply flex items-center space-x-2;
}

.settings-form {
    @apply space-y-6;
}

.form-group {
    @apply space-y-2;
}

.form-label {
    @apply block text-sm font-medium text-gray-700 dark:text-gray-300;
}

.form-select {
    @apply form-input;
}

.checkbox-label {
    @apply flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300;
}

.billing-overview {
    @apply space-y-8;
}

.billing-section {
    @apply space-y-4;
}

.plan-info {
    @apply flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.plan-details {
    @apply flex-1;
}

.usage-metrics {
    @apply space-y-4;
}

.usage-item {
    @apply space-y-2;
}

.usage-header {
    @apply flex items-center justify-between;
}

.usage-label {
    @apply text-sm font-medium text-gray-700 dark:text-gray-300;
}

.usage-value {
    @apply text-sm text-gray-600 dark:text-gray-400;
}

.usage-bar {
    @apply w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2;
}

.usage-fill {
    @apply bg-blue-600 h-2 rounded-full transition-all duration-300;
}

.modal-backdrop {
    @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
}

.modal-content {
    @apply bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto;
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
</style>