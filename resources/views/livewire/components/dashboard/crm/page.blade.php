<?php
/**
 * CRM Management Console Component
 * Professional Customer Relationship Management interface
 */

use function Livewire\Volt\{mount, state, computed, on, layout};
use App\Models\User;
use App\Models\PaymentTransaction;
use Illuminate\Support\Collection;

layout('components.layouts.app');

state([
    'customers' => [],
    'leads' => [],
    'deals' => [],
    'activities' => [],
    'activeTab' => 'customers',
    'selectedCustomer' => null,
    'showCustomerModal' => false,
    'customerStats' => [],
    'recentActivities' => [],
    'searchTerm' => '',
    'dateRange' => '30days',
    'newCustomer' => [
        'name' => '',
        'email' => '',
        'phone' => '',
        'company' => '',
        'status' => 'lead',
        'source' => 'website',
        'notes' => ''
    ]
]);

mount(function () {
    $this->loadCustomers();
    $this->loadLeads();
    $this->loadStats();
    $this->loadRecentActivities();
});

$loadCustomers = function () {
    $this->customers = User::with(['transactions', 'sites'])
        ->where('role', '!=', 'admin')
        ->orderBy('created_at', 'desc')
        ->take(50)
        ->get();
};

$loadLeads = function () {
    $this->leads = User::where('email_verified_at', null)
        ->orWhere('created_at', '>=', now()->subDays(7))
        ->orderBy('created_at', 'desc')
        ->take(20)
        ->get();
};

$loadStats = function () {
    $this->customerStats = [
        'total_customers' => User::where('role', '!=', 'admin')->count(),
        'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
        'active_customers' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
        'total_revenue' => PaymentTransaction::where('status', 'completed')->sum('amount'),
        'conversion_rate' => 12.5,
        'churn_rate' => 2.3
    ];
};

$loadRecentActivities = function () {
    $this->recentActivities = [
        ['type' => 'signup', 'user' => 'John Doe', 'time' => '2 hours ago'],
        ['type' => 'purchase', 'user' => 'Jane Smith', 'time' => '4 hours ago'],
        ['type' => 'site_created', 'user' => 'Bob Johnson', 'time' => '6 hours ago']
    ];
};

$setActiveTab = function ($tab) {
    $this->activeTab = $tab;
};

$addCustomer = function () {
    $this->showCustomerModal = true;
};

$saveCustomer = function () {
    $this->validate([
        'newCustomer.name' => 'required|string|max:255',
        'newCustomer.email' => 'required|email|unique:users,email',
        'newCustomer.phone' => 'nullable|string|max:20',
        'newCustomer.company' => 'nullable|string|max:255'
    ]);

    User::create([
        'name' => $this->newCustomer['name'],
        'email' => $this->newCustomer['email'],
        'phone' => $this->newCustomer['phone'],
        'company' => $this->newCustomer['company'],
        'password' => bcrypt('password'),
        'role' => 'customer',
        'status' => $this->newCustomer['status']
    ]);

    $this->showCustomerModal = false;
    $this->newCustomer = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'company' => '',
        'status' => 'lead',
        'source' => 'website',
        'notes' => ''
    ];

    $this->loadCustomers();
    session()->flash('success', 'Customer added successfully!');
};

?>

<div class="console-page">
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">CRM Management</h1>
                <p class="text-gray-600 dark:text-gray-400">Comprehensive customer relationship management and lead tracking</p>
            </div>
            <div class="flex items-center space-x-4">
                <button class="btn btn-secondary">
                    <i class="fi fi-rr-download mr-2"></i>
                    Export Data
                </button>
                <button wire:click="addCustomer" class="btn btn-primary">
                    <i class="fi fi-rr-plus mr-2"></i>
                    Add Customer
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <div class="metric-card">
            <div class="metric-icon bg-blue-100 dark:bg-blue-900">
                <i class="fi fi-rr-users text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Total Customers</h3>
                <p class="metric-value">{{ number_format($customerStats['total_customers'] ?? 0) }}</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-green-100 dark:bg-green-900">
                <i class="fi fi-rr-user-add text-green-600 dark:text-green-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">New This Month</h3>
                <p class="metric-value">{{ number_format($customerStats['new_this_month'] ?? 0) }}</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-purple-100 dark:bg-purple-900">
                <i class="fi fi-rr-time-quarter-past text-purple-600 dark:text-purple-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Active Customers</h3>
                <p class="metric-value">{{ number_format($customerStats['active_customers'] ?? 0) }}</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-yellow-100 dark:bg-yellow-900">
                <i class="fi fi-rr-usd-circle text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Total Revenue</h3>
                <p class="metric-value">${{ number_format($customerStats['total_revenue'] ?? 0) }}</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-indigo-100 dark:bg-indigo-900">
                <i class="fi fi-rr-chart-line-up text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Conversion Rate</h3>
                <p class="metric-value">{{ $customerStats['conversion_rate'] ?? 0 }}%</p>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon bg-red-100 dark:bg-red-900">
                <i class="fi fi-rr-chart-line-down text-red-600 dark:text-red-400"></i>
            </div>
            <div class="metric-content">
                <h3 class="metric-title">Churn Rate</h3>
                <p class="metric-value">{{ $customerStats['churn_rate'] ?? 0 }}%</p>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tabs-container">
        <nav class="flex space-x-8 border-b border-gray-200 dark:border-gray-700">
            <button 
                wire:click="setActiveTab('customers')"
                class="tab-button {{ $activeTab === 'customers' ? 'active' : '' }}">
                <i class="fi fi-rr-users mr-2"></i>
                Customers
            </button>
            <button 
                wire:click="setActiveTab('leads')"
                class="tab-button {{ $activeTab === 'leads' ? 'active' : '' }}">
                <i class="fi fi-rr-user-add mr-2"></i>
                Leads
            </button>
            <button 
                wire:click="setActiveTab('deals')"
                class="tab-button {{ $activeTab === 'deals' ? 'active' : '' }}">
                <i class="fi fi-rr-handshake mr-2"></i>
                Deals
            </button>
            <button 
                wire:click="setActiveTab('activities')"
                class="tab-button {{ $activeTab === 'activities' ? 'active' : '' }}">
                <i class="fi fi-rr-time-quarter-past mr-2"></i>
                Activities
            </button>
            <button 
                wire:click="setActiveTab('reports')"
                class="tab-button {{ $activeTab === 'reports' ? 'active' : '' }}">
                <i class="fi fi-rr-stats mr-2"></i>
                Reports
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        @if($activeTab === 'customers')
            <div class="customers-tab">
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Customer Database</h3>
                            <div class="flex items-center space-x-4">
                                <input type="text" wire:model="searchTerm" placeholder="Search customers..." class="form-input">
                                <select wire:model="dateRange" class="form-select">
                                    <option value="7days">Last 7 days</option>
                                    <option value="30days">Last 30 days</option>
                                    <option value="90days">Last 90 days</option>
                                    <option value="all">All time</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Revenue</th>
                                        <th>Last Activity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                        <tr>
                                            <td>
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-semibold">{{ substr($customer->name, 0, 1) }}</span>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-white">{{ $customer->name }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">ID: {{ $customer->id }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-900 dark:text-white">{{ $customer->email }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-900 dark:text-white">{{ $customer->company ?? 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $customer->email_verified_at ? 'success' : 'warning' }}">
                                                    {{ $customer->email_verified_at ? 'Active' : 'Pending' }}
                                                </span>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-900 dark:text-white">
                                                    ${{ number_format($customer->transactions->sum('amount') ?? 0) }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $customer->last_login_at ? $customer->last_login_at->diffForHumans() : 'Never' }}
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
                                            <td colspan="7" class="text-center py-8">
                                                <i class="fi fi-rr-users text-gray-400 text-3xl mb-4"></i>
                                                <p class="text-gray-600 dark:text-gray-400">No customers found</p>
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

        @if($activeTab === 'leads')
            <div class="leads-tab">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lead Management</h3>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($leads as $lead)
                                <div class="lead-card">
                                    <div class="lead-header">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-semibold">{{ substr($lead->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $lead->name }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $lead->email }}</p>
                                            </div>
                                        </div>
                                        <span class="badge badge-warning">New Lead</span>
                                    </div>
                                    <div class="lead-content">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Signed up {{ $lead->created_at->diffForHumans() }}
                                        </p>
                                        <div class="lead-actions">
                                            <button class="btn btn-sm btn-primary">Contact</button>
                                            <button class="btn btn-sm btn-secondary">Convert</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12">
                                    <i class="fi fi-rr-user-add text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-600 dark:text-gray-400">No leads available</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'activities')
            <div class="activities-tab">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activities</h3>
                    </div>
                    <div class="card-body">
                        <div class="activity-timeline">
                            @forelse($recentActivities as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fi fi-rr-{{ $activity['type'] === 'signup' ? 'user-add' : ($activity['type'] === 'purchase' ? 'shopping-cart' : 'globe') }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            <strong>{{ $activity['user'] }}</strong> 
                                            {{ $activity['type'] === 'signup' ? 'signed up' : ($activity['type'] === 'purchase' ? 'made a purchase' : 'created a new site') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['time'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fi fi-rr-time-quarter-past text-gray-400 text-3xl mb-4"></i>
                                    <p class="text-gray-600 dark:text-gray-400">No recent activities</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Add Customer Modal -->
    @if($showCustomerModal)
        <div class="modal-backdrop" wire:click="$set('showCustomerModal', false)">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">Add New Customer</h3>
                    <button wire:click="$set('showCustomerModal', false)" class="modal-close">
                        <i class="fi fi-rr-cross"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveCustomer">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <input type="text" wire:model="newCustomer.name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input type="email" wire:model="newCustomer.email" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" wire:model="newCustomer.phone" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Company</label>
                                <input type="text" wire:model="newCustomer.company" class="form-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select wire:model="newCustomer.status" class="form-select">
                                <option value="lead">Lead</option>
                                <option value="prospect">Prospect</option>
                                <option value="customer">Customer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Notes</label>
                            <textarea wire:model="newCustomer.notes" class="form-input" rows="3"></textarea>
                        </div>
                        <div class="flex items-center justify-end space-x-4">
                            <button type="button" wire:click="$set('showCustomerModal', false)" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Customer</button>
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

.lead-card {
    @apply bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700;
}

.lead-header {
    @apply flex items-center justify-between mb-4;
}

.lead-content {
    @apply space-y-3;
}

.lead-actions {
    @apply flex items-center space-x-2;
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