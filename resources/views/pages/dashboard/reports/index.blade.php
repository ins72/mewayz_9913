@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Reports & Analytics</h1>
            <p class="text-secondary-text mt-2">Comprehensive insights and analytics</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Report
            </button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Report
            </button>
        </div>
    </div>

    <!-- Time Period Selector -->
    <div class="mb-8">
        <div class="flex items-center space-x-2">
            <span class="text-sm text-secondary-text">Time Period:</span>
            <div class="flex rounded-lg border border-secondary-bg">
                <button class="px-4 py-2 bg-primary text-white rounded-l-lg text-sm">Last 7 days</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text text-sm">Last 30 days</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text text-sm">Last 90 days</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text rounded-r-lg text-sm">Custom</button>
            </div>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="dashboard-grid mb-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Total Revenue</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <div class="dashboard-card-value">$47,892</div>
            <div class="dashboard-card-change positive">+12.5% from last period</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Total Orders</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6l-1-12z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">1,247</div>
            <div class="dashboard-card-change positive">+8.3% from last period</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">New Customers</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">389</div>
            <div class="dashboard-card-change positive">+15.7% from last period</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Conversion Rate</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="dashboard-card-value">3.2%</div>
            <div class="dashboard-card-change positive">+0.4% from last period</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="dashboard-card">
            <h3 class="font-semibold text-primary-text mb-4">Revenue Over Time</h3>
            <div class="h-64 bg-secondary-bg rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-16 h-16 text-secondary-text mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-secondary-text">Revenue chart would be displayed here</p>
                </div>
            </div>
        </div>

        <!-- Traffic Sources -->
        <div class="dashboard-card">
            <h3 class="font-semibold text-primary-text mb-4">Traffic Sources</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-primary-text">Organic Search</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-primary-text font-medium">45%</span>
                        <div class="w-20 bg-secondary-bg rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-primary-text">Direct</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-primary-text font-medium">30%</span>
                        <div class="w-20 bg-secondary-bg rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 30%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                        <span class="text-primary-text">Social Media</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-primary-text font-medium">15%</span>
                        <div class="w-20 bg-secondary-bg rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: 15%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-primary-text">Email</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-primary-text font-medium">10%</span>
                        <div class="w-20 bg-secondary-bg rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: 10%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="dashboard-table">
        <div class="dashboard-table-header">
            <h3 class="dashboard-table-title">Detailed Reports</h3>
            <div class="flex items-center space-x-2">
                <input type="text" placeholder="Search reports..." class="form-input w-64">
                <button class="btn btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Report Name</th>
                        <th>Category</th>
                        <th>Period</th>
                        <th>Generated</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= 8; $i++)
                    <tr>
                        <td>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-{{ $i % 3 == 0 ? 'blue' : ($i % 2 == 0 ? 'green' : 'purple') }}-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-primary-text">{{ $i % 3 == 0 ? 'Monthly Sales Report' : ($i % 2 == 0 ? 'Customer Analytics' : 'Product Performance') }}</div>
                                    <div class="text-sm text-secondary-text">{{ $i % 3 == 0 ? 'Comprehensive sales analysis' : ($i % 2 == 0 ? 'Customer behavior insights' : 'Product sales metrics') }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $i % 3 == 0 ? 'primary' : ($i % 2 == 0 ? 'success' : 'warning') }}">
                                {{ $i % 3 == 0 ? 'Sales' : ($i % 2 == 0 ? 'Analytics' : 'Products') }}
                            </span>
                        </td>
                        <td>{{ $i % 3 == 0 ? 'Last 30 days' : ($i % 2 == 0 ? 'Last 90 days' : 'Last 7 days') }}</td>
                        <td>{{ date('M j, Y', strtotime('-' . rand(1, 30) . ' days')) }}</td>
                        <td>
                            <span class="badge badge-{{ $i % 4 == 0 ? 'warning' : 'success' }}">
                                {{ $i % 4 == 0 ? 'Generating' : 'Ready' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <button class="btn btn-sm btn-secondary">View</button>
                                <button class="btn btn-sm btn-secondary">Download</button>
                                <button class="btn btn-sm btn-secondary">Schedule</button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection