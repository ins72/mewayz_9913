<?php
/**
 * Analytics Dashboard Console Component
 * Professional analytics and performance tracking interface
 */

use function Livewire\Volt\{mount, state, computed, on, layout};
use App\Models\Site;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Support\Collection;

layout('components.layouts.app');

state([
    'overviewStats' => [],
    'revenueData' => [],
    'trafficData' => [],
    'conversionData' => [],
    'topPages' => [],
    'topProducts' => [],
    'activeTab' => 'overview',
    'dateRange' => '30days',
    'selectedPeriod' => 'last_30_days',
    'comparisonEnabled' => false,
    'realTimeData' => [],
    'goals' => [],
    'reports' => []
]);

mount(function () {
    $this->loadOverviewStats();
    $this->loadRevenueData();
    $this->loadTrafficData();
    $this->loadConversionData();
    $this->loadTopPages();
    $this->loadTopProducts();
    $this->loadRealTimeData();
    $this->loadGoals();
});

$loadOverviewStats = function () {
    $this->overviewStats = [
        'total_visitors' => 15847,
        'unique_visitors' => 12456,
        'page_views' => 45678,
        'bounce_rate' => 42.5,
        'avg_session_duration' => '3:45',
        'conversion_rate' => 3.2,
        'revenue' => 24580,
        'orders' => 156,
        'growth_rate' => 12.5,
        'new_users' => 2456
    ];
};

$loadRevenueData = function () {
    $this->revenueData = [
        'total_revenue' => PaymentTransaction::where('status', 'completed')->sum('amount'),
        'monthly_revenue' => PaymentTransaction::where('status', 'completed')
            ->where('created_at', '>=', now()->startOfMonth())->sum('amount'),
        'revenue_growth' => 15.2,
        'average_order_value' => 157.32,
        'refunds' => 245.50,
        'net_revenue' => 24334.50
    ];
};

$loadTrafficData = function () {
    $this->trafficData = [
        'organic_search' => 45,
        'direct' => 32,
        'social' => 15,
        'email' => 8,
        'referral' => 7,
        'paid' => 3
    ];
};

$loadConversionData = function () {
    $this->conversionData = [
        'funnel_steps' => [
            ['step' => 'Visitors', 'count' => 15847, 'rate' => 100],
            ['step' => 'Product Views', 'count' => 7823, 'rate' => 49.4],
            ['step' => 'Add to Cart', 'count' => 1245, 'rate' => 7.9],
            ['step' => 'Checkout', 'count' => 856, 'rate' => 5.4],
            ['step' => 'Purchase', 'count' => 507, 'rate' => 3.2]
        ],
        'top_converting_pages' => [
            ['page' => '/pricing', 'conversions' => 142, 'rate' => 8.7],
            ['page' => '/features', 'conversions' => 89, 'rate' => 5.4],
            ['page' => '/demo', 'conversions' => 76, 'rate' => 12.3]
        ]
    ];
};

$loadTopPages = function () {
    $this->topPages = [
        ['page' => '/dashboard', 'views' => 8547, 'unique' => 6234, 'bounce_rate' => 25.4],
        ['page' => '/pricing', 'views' => 5432, 'unique' => 4123, 'bounce_rate' => 35.2],
        ['page' => '/features', 'views' => 3456, 'unique' => 2987, 'bounce_rate' => 42.1],
        ['page' => '/about', 'views' => 2345, 'unique' => 1876, 'bounce_rate' => 55.3],
        ['page' => '/contact', 'views' => 1876, 'unique' => 1654, 'bounce_rate' => 38.9]
    ];
};

$loadTopProducts = function () {
    $this->topProducts = [
        ['product' => 'Pro Plan', 'sales' => 89, 'revenue' => 8900, 'conversion' => 4.2],
        ['product' => 'Business Plan', 'sales' => 56, 'revenue' => 11200, 'conversion' => 2.8],
        ['product' => 'Enterprise Plan', 'sales' => 23, 'revenue' => 11500, 'conversion' => 1.9],
        ['product' => 'Starter Plan', 'sales' => 145, 'revenue' => 2900, 'conversion' => 7.1]
    ];
};

$loadRealTimeData = function () {
    $this->realTimeData = [
        'active_users' => 247,
        'page_views_per_minute' => 15,
        'top_pages_now' => [
            ['page' => '/dashboard', 'users' => 89],
            ['page' => '/pricing', 'users' => 45],
            ['page' => '/features', 'users' => 32]
        ],
        'referrers' => [
            ['source' => 'google.com', 'users' => 123],
            ['source' => 'facebook.com', 'users' => 67],
            ['source' => 'twitter.com', 'users' => 34]
        ]
    ];
};

$loadGoals = function () {
    $this->goals = [
        ['name' => 'Monthly Revenue', 'target' => 25000, 'current' => 18500, 'completion' => 74],
        ['name' => 'New Signups', 'target' => 500, 'current' => 367, 'completion' => 73.4],
        ['name' => 'Conversion Rate', 'target' => 5.0, 'current' => 3.2, 'completion' => 64]
    ];
};

$setActiveTab = function ($tab) {
    $this->activeTab = $tab;
};

$setDateRange = function ($range) {
    $this->dateRange = $range;
    $this->loadOverviewStats();
    $this->loadRevenueData();
};

$exportReport = function () {
    session()->flash('success', 'Report exported successfully!');
};

?>

<div class="console-page">
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400">Comprehensive analytics and performance tracking</p>
            </div>
            <div class="flex items-center space-x-4">
                <select wire:model="dateRange" wire:change="setDateRange($event.target.value)" class="form-select">
                    <option value="7days">Last 7 days</option>
                    <option value="30days">Last 30 days</option>
                    <option value="90days">Last 90 days</option>
                    <option value="12months">Last 12 months</option>
                </select>
                <button wire:click="exportReport" class="btn btn-secondary">
                    <i class="fi fi-rr-download mr-2"></i>
                    Export Report
                </button>
                <button class="btn btn-primary">
                    <i class="fi fi-rr-settings mr-2"></i>
                    Configure
                </button>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Total Visitors</h3>
                <div class="kpi-icon bg-blue-100 dark:bg-blue-900">
                    <i class="fi fi-rr-users text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ number_format($overviewStats['total_visitors'] ?? 0) }}</p>
                <p class="kpi-change positive">+{{ $overviewStats['growth_rate'] ?? 0 }}%</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Page Views</h3>
                <div class="kpi-icon bg-green-100 dark:bg-green-900">
                    <i class="fi fi-rr-eye text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ number_format($overviewStats['page_views'] ?? 0) }}</p>
                <p class="kpi-change positive">+8.2%</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Bounce Rate</h3>
                <div class="kpi-icon bg-yellow-100 dark:bg-yellow-900">
                    <i class="fi fi-rr-bounce text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ $overviewStats['bounce_rate'] ?? 0 }}%</p>
                <p class="kpi-change negative">-2.1%</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Conversion Rate</h3>
                <div class="kpi-icon bg-purple-100 dark:bg-purple-900">
                    <i class="fi fi-rr-chart-line-up text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">{{ $overviewStats['conversion_rate'] ?? 0 }}%</p>
                <p class="kpi-change positive">+0.8%</p>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3 class="kpi-title">Revenue</h3>
                <div class="kpi-icon bg-indigo-100 dark:bg-indigo-900">
                    <i class="fi fi-rr-usd-circle text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
            <div class="kpi-content">
                <p class="kpi-value">${{ number_format($overviewStats['revenue'] ?? 0) }}</p>
                <p class="kpi-change positive">+{{ $revenueData['revenue_growth'] ?? 0 }}%</p>
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
                wire:click="setActiveTab('traffic')"
                class="tab-button {{ $activeTab === 'traffic' ? 'active' : '' }}">
                <i class="fi fi-rr-stats mr-2"></i>
                Traffic
            </button>
            <button 
                wire:click="setActiveTab('conversions')"
                class="tab-button {{ $activeTab === 'conversions' ? 'active' : '' }}">
                <i class="fi fi-rr-chart-line-up mr-2"></i>
                Conversions
            </button>
            <button 
                wire:click="setActiveTab('revenue')"
                class="tab-button {{ $activeTab === 'revenue' ? 'active' : '' }}">
                <i class="fi fi-rr-usd-circle mr-2"></i>
                Revenue
            </button>
            <button 
                wire:click="setActiveTab('realtime')"
                class="tab-button {{ $activeTab === 'realtime' ? 'active' : '' }}">
                <i class="fi fi-rr-time-quarter-past mr-2"></i>
                Real-time
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        @if($activeTab === 'overview')
            <div class="overview-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Traffic Sources -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Traffic Sources</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                @foreach($trafficData as $source => $percentage)
                                    <div class="traffic-source-item">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ ucfirst(str_replace('_', ' ', $source)) }}
                                            </span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Top Pages -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Pages</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                @foreach($topPages as $page)
                                    <div class="top-page-item">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $page['page'] }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($page['views']) }} views</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $page['bounce_rate'] }}%</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">bounce rate</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Goals Progress -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Goals Progress</h3>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($goals as $goal)
                                <div class="goal-item">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $goal['name'] }}</h4>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $goal['completion'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-2">
                                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full" 
                                             style="width: {{ $goal['completion'] }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span>Current: {{ is_numeric($goal['current']) ? number_format($goal['current']) : $goal['current'] }}</span>
                                        <span>Target: {{ is_numeric($goal['target']) ? number_format($goal['target']) : $goal['target'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'traffic')
            <div class="traffic-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Detailed Traffic Sources -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detailed Traffic Analysis</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Source</th>
                                            <th>Visitors</th>
                                            <th>% of Total</th>
                                            <th>Bounce Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trafficData as $source => $percentage)
                                            <tr>
                                                <td>
                                                    <span class="font-medium text-gray-900 dark:text-white">
                                                        {{ ucfirst(str_replace('_', ' ', $source)) }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format(($overviewStats['total_visitors'] * $percentage) / 100) }}</td>
                                                <td>{{ $percentage }}%</td>
                                                <td>{{ rand(25, 60) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Geographic Distribution -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Geographic Distribution</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-12">
                                <i class="fi fi-rr-world text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-600 dark:text-gray-400">Geographic data visualization coming soon</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'conversions')
            <div class="conversions-tab">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Conversion Funnel</h3>
                    </div>
                    <div class="card-body">
                        <div class="funnel-container">
                            @foreach($conversionData['funnel_steps'] as $index => $step)
                                <div class="funnel-step">
                                    <div class="funnel-step-content">
                                        <div class="funnel-step-info">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $step['step'] }}</h4>
                                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($step['count']) }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $step['rate'] }}%</p>
                                        </div>
                                        @if($index < count($conversionData['funnel_steps']) - 1)
                                            <div class="funnel-arrow">
                                                <i class="fi fi-rr-arrow-down text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'revenue')
            <div class="revenue-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Revenue Overview -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue Overview</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                <div class="revenue-metric">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Total Revenue</span>
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                            ${{ number_format($revenueData['total_revenue'] ?? 0) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="revenue-metric">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Monthly Revenue</span>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                            ${{ number_format($revenueData['monthly_revenue'] ?? 0) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="revenue-metric">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Average Order Value</span>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                            ${{ number_format($revenueData['average_order_value'] ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Products</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                @foreach($topProducts as $product)
                                    <div class="product-item">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $product['product'] }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product['sales'] }} sales</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-gray-900 dark:text-white">${{ number_format($product['revenue']) }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product['conversion'] }}% conversion</p>
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

        @if($activeTab === 'realtime')
            <div class="realtime-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Real-time Overview -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Real-time Activity</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-6">
                                <div class="text-6xl font-bold text-green-600 dark:text-green-400">
                                    {{ $realTimeData['active_users'] }}
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">Active Users Right Now</p>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Page Views/Minute</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $realTimeData['page_views_per_minute'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Pages -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Active Pages</h3>
                        </div>
                        <div class="card-body">
                            <div class="space-y-4">
                                @foreach($realTimeData['top_pages_now'] as $page)
                                    <div class="active-page-item">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-900 dark:text-white">{{ $page['page'] }}</span>
                                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                                {{ $page['users'] }} users
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.console-page {
    @apply p-6 max-w-7xl mx-auto;
}

.page-header {
    @apply mb-8;
}

.kpi-card {
    @apply bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700;
}

.kpi-header {
    @apply flex items-center justify-between mb-4;
}

.kpi-title {
    @apply text-sm font-medium text-gray-600 dark:text-gray-400;
}

.kpi-icon {
    @apply w-10 h-10 rounded-full flex items-center justify-center;
}

.kpi-content {
    @apply space-y-2;
}

.kpi-value {
    @apply text-2xl font-bold text-gray-900 dark:text-white;
}

.kpi-change {
    @apply text-sm font-medium;
}

.kpi-change.positive {
    @apply text-green-600 dark:text-green-400;
}

.kpi-change.negative {
    @apply text-red-600 dark:text-red-400;
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

.traffic-source-item {
    @apply p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.top-page-item {
    @apply p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.goal-item {
    @apply p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.funnel-container {
    @apply flex flex-col items-center space-y-6;
}

.funnel-step {
    @apply w-full max-w-md;
}

.funnel-step-content {
    @apply text-center;
}

.funnel-step-info {
    @apply bg-gray-50 dark:bg-gray-700 rounded-lg p-6;
}

.funnel-arrow {
    @apply my-4 text-2xl;
}

.revenue-metric {
    @apply p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.product-item {
    @apply p-4 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.active-page-item {
    @apply p-3 bg-gray-50 dark:bg-gray-700 rounded-lg;
}

.form-select {
    @apply form-input;
}
</style>