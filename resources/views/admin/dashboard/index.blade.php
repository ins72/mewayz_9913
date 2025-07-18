@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Platform Administration</h1>
            <p class="text-secondary-text">Comprehensive platform management and analytics</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2 text-sm text-secondary-text">
                <div class="w-2 h-2 bg-success rounded-full"></div>
                <span>System Healthy</span>
            </div>
            <button class="btn btn-primary">
                <x-icon name="download" size="sm" class="mr-2" alt="Export" />
                Export Report
            </button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="dashboard-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary-text">Total Users</p>
                    <p class="text-2xl font-bold text-primary-text">{{ number_format($metrics['total_users']) }}</p>
                    <p class="text-xs {{ $metrics['user_growth'] >= 0 ? 'text-success' : 'text-error' }}">
                        {{ $metrics['user_growth'] >= 0 ? '+' : '' }}{{ number_format($metrics['user_growth'], 1) }}% this month
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <x-icon name="users" size="lg" class="text-blue-500" alt="Users" />
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary-text">Active Workspaces</p>
                    <p class="text-2xl font-bold text-primary-text">{{ number_format($metrics['total_workspaces']) }}</p>
                    <p class="text-xs text-secondary-text">{{ number_format($metrics['active_subscriptions']) }} with paid plans</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <x-icon name="dashboard" size="lg" class="text-green-500" alt="Workspaces" />
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary-text">Monthly Revenue</p>
                    <p class="text-2xl font-bold text-primary-text">${{ number_format($metrics['monthly_revenue'], 2) }}</p>
                    <p class="text-xs {{ $metrics['revenue_growth'] >= 0 ? 'text-success' : 'text-error' }}">
                        {{ $metrics['revenue_growth'] >= 0 ? '+' : '' }}{{ number_format($metrics['revenue_growth'], 1) }}% this month
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <x-icon name="dollar" size="lg" class="text-purple-500" alt="Revenue" />
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary-text">Total Revenue</p>
                    <p class="text-2xl font-bold text-primary-text">${{ number_format($metrics['total_revenue'], 2) }}</p>
                    <p class="text-xs text-secondary-text">{{ number_format($metrics['total_transactions']) }} transactions</p>
                </div>
                <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center">
                    <x-icon name="trending" size="lg" class="text-orange-500" alt="Total Revenue" />
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="dashboard-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-primary-text">Monthly Revenue</h3>
                <select class="bg-background border border-border-color rounded px-3 py-1 text-sm text-primary-text">
                    <option>Last 12 months</option>
                    <option>Last 6 months</option>
                    <option>Last 3 months</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- User Growth Chart -->
        <div class="dashboard-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-primary-text">User Growth</h3>
                <select class="bg-background border border-border-color rounded px-3 py-1 text-sm text-primary-text">
                    <option>Last 12 months</option>
                    <option>Last 6 months</option>
                    <option>Last 3 months</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="dashboard-card">
        <h3 class="text-lg font-semibold text-primary-text mb-4">System Health</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-2 {{ $systemHealth['database']['status'] === 'healthy' ? 'bg-success' : 'bg-error' }}/20 rounded-full flex items-center justify-center">
                    <x-icon name="database" size="lg" class="{{ $systemHealth['database']['status'] === 'healthy' ? 'text-success' : 'text-error' }}" alt="Database" />
                </div>
                <p class="text-sm font-medium text-primary-text">Database</p>
                <p class="text-xs text-secondary-text">{{ ucfirst($systemHealth['database']['status']) }}</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-2 {{ $systemHealth['storage']['status'] === 'healthy' ? 'bg-success' : 'bg-warning' }}/20 rounded-full flex items-center justify-center">
                    <x-icon name="storage" size="lg" class="{{ $systemHealth['storage']['status'] === 'healthy' ? 'text-success' : 'text-warning' }}" alt="Storage" />
                </div>
                <p class="text-sm font-medium text-primary-text">Storage</p>
                <p class="text-xs text-secondary-text">{{ $systemHealth['storage']['disk_usage'] }}% used</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-2 {{ $systemHealth['cache']['status'] === 'healthy' ? 'bg-success' : 'bg-error' }}/20 rounded-full flex items-center justify-center">
                    <x-icon name="cache" size="lg" class="{{ $systemHealth['cache']['status'] === 'healthy' ? 'text-success' : 'text-error' }}" alt="Cache" />
                </div>
                <p class="text-sm font-medium text-primary-text">Cache</p>
                <p class="text-xs text-secondary-text">{{ ucfirst($systemHealth['cache']['status']) }}</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-2 {{ $systemHealth['queue']['status'] === 'healthy' ? 'bg-success' : 'bg-warning' }}/20 rounded-full flex items-center justify-center">
                    <x-icon name="queue" size="lg" class="{{ $systemHealth['queue']['status'] === 'healthy' ? 'text-success' : 'text-warning' }}" alt="Queue" />
                </div>
                <p class="text-sm font-medium text-primary-text">Queue</p>
                <p class="text-xs text-secondary-text">{{ $systemHealth['queue']['failed_jobs'] }} failed</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-2 {{ $systemHealth['external_apis']['status'] === 'healthy' ? 'bg-success' : 'bg-warning' }}/20 rounded-full flex items-center justify-center">
                    <x-icon name="globe" size="lg" class="{{ $systemHealth['external_apis']['status'] === 'healthy' ? 'text-success' : 'text-warning' }}" alt="APIs" />
                </div>
                <p class="text-sm font-medium text-primary-text">External APIs</p>
                <p class="text-xs text-secondary-text">{{ $systemHealth['external_apis']['healthy_count'] }}/{{ $systemHealth['external_apis']['total_count'] }} healthy</p>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Users -->
        <div class="dashboard-card">
            <h3 class="text-lg font-semibold text-primary-text mb-4">Recent Users</h3>
            <div class="space-y-3">
                @foreach($recentActivity['users'] as $user)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-info rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-primary-text">{{ $user->name }}</p>
                            <p class="text-xs text-secondary-text">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-secondary-text">{{ $user->created_at->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Workspaces -->
        <div class="dashboard-card">
            <h3 class="text-lg font-semibold text-primary-text mb-4">Recent Workspaces</h3>
            <div class="space-y-3">
                @foreach($recentActivity['workspaces'] as $workspace)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-success rounded-full flex items-center justify-center">
                            <x-icon name="dashboard" size="xs" class="text-white" alt="Workspace" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-primary-text">{{ $workspace->name }}</p>
                            <p class="text-xs text-secondary-text">{{ $workspace->owner->name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-secondary-text">{{ $workspace->created_at->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="dashboard-card">
            <h3 class="text-lg font-semibold text-primary-text mb-4">Recent Transactions</h3>
            <div class="space-y-3">
                @foreach($recentActivity['transactions'] as $transaction)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-purple-500/20 rounded-full flex items-center justify-center">
                            <x-icon name="dollar" size="xs" class="text-purple-500" alt="Transaction" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-primary-text">${{ number_format($transaction->fee_amount, 2) }}</p>
                            <p class="text-xs text-secondary-text">{{ ucfirst($transaction->transaction_type) }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-secondary-text">{{ $transaction->created_at->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json(array_keys($revenueData['monthly'])),
            datasets: [{
                label: 'Revenue',
                data: @json(array_values($revenueData['monthly'])),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($userGrowth)),
            datasets: [{
                label: 'New Users',
                data: @json(array_values($userGrowth)),
                backgroundColor: '#10B981',
                borderColor: '#059669',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush