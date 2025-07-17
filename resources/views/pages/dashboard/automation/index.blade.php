@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Automation</h1>
            <p class="text-secondary-text mt-2">Automate your workflows and save time</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Templates
            </button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Automation
            </button>
        </div>
    </div>

    <!-- Automation Stats -->
    <div class="dashboard-grid mb-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Active Automations</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">12</div>
            <div class="dashboard-card-change positive">+3 this month</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Tasks Automated</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">2,847</div>
            <div class="dashboard-card-change positive">+156 today</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Time Saved</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">127h</div>
            <div class="dashboard-card-change positive">+18h this week</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Success Rate</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="dashboard-card-value">94.7%</div>
            <div class="dashboard-card-change positive">+2.3% from last month</div>
        </div>
    </div>

    <!-- Automation Categories -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-primary-text mb-4">Automation Categories</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="dashboard-card hover:transform hover:scale-105 transition-all">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-primary-text">Email Marketing</h3>
                        <p class="text-sm text-secondary-text">5 active automations</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card hover:transform hover:scale-105 transition-all">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6l-1-12z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-primary-text">E-commerce</h3>
                        <p class="text-sm text-secondary-text">3 active automations</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card hover:transform hover:scale-105 transition-all">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-primary-text">Social Media</h3>
                        <p class="text-sm text-secondary-text">2 active automations</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card hover:transform hover:scale-105 transition-all">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-primary-text">CRM</h3>
                        <p class="text-sm text-secondary-text">2 active automations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Automations -->
    <div class="dashboard-table">
        <div class="dashboard-table-header">
            <h3 class="dashboard-table-title">Active Automations</h3>
            <div class="flex items-center space-x-2">
                <button class="btn btn-sm btn-secondary">Filter</button>
                <button class="btn btn-sm btn-secondary">Export</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Automation Name</th>
                        <th>Category</th>
                        <th>Trigger</th>
                        <th>Status</th>
                        <th>Last Run</th>
                        <th>Success Rate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= 10; $i++)
                    <tr>
                        <td>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-{{ $i % 3 == 0 ? 'blue' : ($i % 2 == 0 ? 'green' : 'purple') }}-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-primary-text">{{ $i % 3 == 0 ? 'Welcome Email Sequence' : ($i % 2 == 0 ? 'Abandoned Cart Recovery' : 'Social Media Posting') }}</div>
                                    <div class="text-sm text-secondary-text">{{ $i % 3 == 0 ? 'Send welcome emails to new subscribers' : ($i % 2 == 0 ? 'Recover abandoned shopping carts' : 'Auto-post to social media') }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $i % 3 == 0 ? 'primary' : ($i % 2 == 0 ? 'success' : 'warning') }}">
                                {{ $i % 3 == 0 ? 'Email' : ($i % 2 == 0 ? 'E-commerce' : 'Social') }}
                            </span>
                        </td>
                        <td>{{ $i % 3 == 0 ? 'New subscriber' : ($i % 2 == 0 ? 'Cart abandoned' : 'New content') }}</td>
                        <td>
                            <span class="badge badge-{{ $i % 4 == 0 ? 'warning' : 'success' }}">
                                {{ $i % 4 == 0 ? 'Paused' : 'Active' }}
                            </span>
                        </td>
                        <td>{{ rand(1, 60) }} min ago</td>
                        <td>
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-primary-text">{{ rand(85, 99) }}%</div>
                                <div class="w-16 bg-secondary-bg rounded-full h-2 ml-2">
                                    <div class="bg-success h-2 rounded-full" style="width: {{ rand(85, 99) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <button class="btn btn-sm btn-secondary">Edit</button>
                                <button class="btn btn-sm btn-secondary">Analytics</button>
                                <button class="btn btn-sm btn-{{ $i % 4 == 0 ? 'success' : 'warning' }}">
                                    {{ $i % 4 == 0 ? 'Resume' : 'Pause' }}
                                </button>
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