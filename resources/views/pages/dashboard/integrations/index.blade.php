@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Integrations</h1>
            <p class="text-secondary-text mt-2">Connect with your favorite tools and services</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Browse All
            </button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Custom Integration
            </button>
        </div>
    </div>

    <!-- Integration Stats -->
    <div class="dashboard-grid mb-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Active Integrations</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </div>
            <div class="dashboard-card-value">8</div>
            <div class="dashboard-card-change positive">+2 this month</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Data Synced</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div class="dashboard-card-value">12.7K</div>
            <div class="dashboard-card-change positive">+847 today</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">API Calls</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">45.2K</div>
            <div class="dashboard-card-change positive">+1.2K today</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Success Rate</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">99.2%</div>
            <div class="dashboard-card-change positive">+0.3% from last week</div>
        </div>
    </div>

    <!-- Integration Categories -->
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <span class="text-sm text-secondary-text">Categories:</span>
            <div class="flex rounded-lg border border-secondary-bg">
                <button class="px-4 py-2 bg-primary text-white rounded-l-lg text-sm">All</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text text-sm">Analytics</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text text-sm">Marketing</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text text-sm">Payment</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text text-sm">Social</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text rounded-r-lg text-sm">Storage</button>
            </div>
        </div>
    </div>

    <!-- Popular Integrations -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-primary-text mb-4">Popular Integrations</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @php
                $integrations = [
                    ['name' => 'Google Analytics', 'logo' => 'google', 'color' => 'red', 'connected' => true],
                    ['name' => 'Stripe', 'logo' => 'stripe', 'color' => 'blue', 'connected' => true],
                    ['name' => 'Mailchimp', 'logo' => 'mailchimp', 'color' => 'yellow', 'connected' => true],
                    ['name' => 'Zapier', 'logo' => 'zapier', 'color' => 'orange', 'connected' => false],
                    ['name' => 'Slack', 'logo' => 'slack', 'color' => 'purple', 'connected' => true],
                    ['name' => 'Dropbox', 'logo' => 'dropbox', 'color' => 'blue', 'connected' => false],
                    ['name' => 'HubSpot', 'logo' => 'hubspot', 'color' => 'orange', 'connected' => true],
                    ['name' => 'Zoom', 'logo' => 'zoom', 'color' => 'blue', 'connected' => false],
                ]
            @endphp
            @foreach ($integrations as $integration)
            <div class="dashboard-card hover:transform hover:scale-105 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-{{ $integration['color'] }}-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    @if ($integration['connected'])
                    <span class="badge badge-success">Connected</span>
                    @else
                    <span class="badge badge-secondary">Available</span>
                    @endif
                </div>
                <h3 class="font-semibold text-primary-text mb-2">{{ $integration['name'] }}</h3>
                <p class="text-sm text-secondary-text mb-4">
                    {{ $integration['name'] == 'Google Analytics' ? 'Track website performance and user behavior' : 
                       ($integration['name'] == 'Stripe' ? 'Accept payments and manage subscriptions' : 
                       ($integration['name'] == 'Mailchimp' ? 'Email marketing and automation' : 
                       ($integration['name'] == 'Zapier' ? 'Automate workflows between apps' : 
                       ($integration['name'] == 'Slack' ? 'Team communication and notifications' : 
                       ($integration['name'] == 'Dropbox' ? 'Cloud storage and file sharing' : 
                       ($integration['name'] == 'HubSpot' ? 'CRM and marketing automation' : 
                       'Video conferencing and webinars')))))) }}
                </p>
                <div class="flex space-x-2">
                    @if ($integration['connected'])
                    <button class="btn btn-sm btn-secondary flex-1">Configure</button>
                    <button class="btn btn-sm btn-error">Disconnect</button>
                    @else
                    <button class="btn btn-sm btn-primary flex-1">Connect</button>
                    <button class="btn btn-sm btn-secondary">Learn More</button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Active Integrations -->
    <div class="dashboard-table">
        <div class="dashboard-table-header">
            <h3 class="dashboard-table-title">Active Integrations</h3>
            <div class="flex items-center space-x-2">
                <input type="text" placeholder="Search integrations..." class="form-input w-64">
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
                        <th>Integration</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Last Sync</th>
                        <th>Data Points</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($integrations as $i => $integration)
                    @if ($integration['connected'])
                    <tr>
                        <td>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-{{ $integration['color'] }}-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-primary-text">{{ $integration['name'] }}</div>
                                    <div class="text-sm text-secondary-text">Connected {{ rand(1, 30) }} days ago</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $integration['color'] == 'red' ? 'primary' : ($integration['color'] == 'blue' ? 'info' : 'warning') }}">
                                {{ $integration['name'] == 'Google Analytics' ? 'Analytics' : 
                                   ($integration['name'] == 'Stripe' ? 'Payment' : 
                                   ($integration['name'] == 'Mailchimp' ? 'Marketing' : 
                                   ($integration['name'] == 'Slack' ? 'Communication' : 
                                   ($integration['name'] == 'HubSpot' ? 'CRM' : 'Automation')))) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ rand(0, 10) > 2 ? 'success' : 'warning' }}">
                                {{ rand(0, 10) > 2 ? 'Active' : 'Warning' }}
                            </span>
                        </td>
                        <td>{{ rand(1, 60) }} minutes ago</td>
                        <td>{{ number_format(rand(1000, 50000)) }}</td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <button class="btn btn-sm btn-secondary">Configure</button>
                                <button class="btn btn-sm btn-secondary">Logs</button>
                                <button class="btn btn-sm btn-error">Disconnect</button>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection