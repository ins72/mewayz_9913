@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Admin Dashboard</h1>
            <p class="text-secondary-text">Manage users, plans, and system settings</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Data
            </button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create User
            </button>
        </div>
    </div>

    <!-- System Overview -->
    <div class="dashboard-grid mb-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Total Users</h3>
                <svg class="dashboard-card-icon text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">12,847</div>
            <div class="dashboard-card-change positive">+234 this month</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Active Workspaces</h3>
                <svg class="dashboard-card-icon text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="dashboard-card-value">8,456</div>
            <div class="dashboard-card-change positive">+156 this month</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Monthly Revenue</h3>
                <svg class="dashboard-card-icon text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <div class="dashboard-card-value">$234,567</div>
            <div class="dashboard-card-change positive">+18.2% this month</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">System Load</h3>
                <svg class="dashboard-card-icon text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">76%</div>
            <div class="dashboard-card-change positive">Normal</div>
        </div>
    </div>

    <!-- Management Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- User Management -->
        <div class="dashboard-card">
            <h3 class="text-lg font-semibold text-primary-text mb-4">User Management</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                            AB
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">Active Users</div>
                            <div class="text-sm text-secondary-text">Currently online</div>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-primary-text">2,847</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                            NU
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">New Users</div>
                            <div class="text-sm text-secondary-text">This month</div>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-primary-text">234</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                            SU
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">Suspended</div>
                            <div class="text-sm text-secondary-text">Violations</div>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-primary-text">12</span>
                </div>
            </div>
            <button class="btn btn-primary w-full mt-4">Manage Users</button>
        </div>

        <!-- Subscription Plans -->
        <div class="dashboard-card">
            <h3 class="text-lg font-semibold text-primary-text mb-4">Subscription Plans</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div>
                        <div class="font-medium text-primary-text">Free Plan</div>
                        <div class="text-sm text-secondary-text">Basic features</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-primary-text">8,456</div>
                        <div class="text-sm text-secondary-text">users</div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div>
                        <div class="font-medium text-primary-text">Professional</div>
                        <div class="text-sm text-secondary-text">$1/feature/month</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-primary-text">3,234</div>
                        <div class="text-sm text-secondary-text">users</div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div>
                        <div class="font-medium text-primary-text">Enterprise</div>
                        <div class="text-sm text-secondary-text">$1.5/feature/month</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-primary-text">1,157</div>
                        <div class="text-sm text-secondary-text">users</div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary w-full mt-4">Manage Plans</button>
        </div>

        <!-- System Status -->
        <div class="dashboard-card">
            <h3 class="text-lg font-semibold text-primary-text mb-4">System Status</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <div>
                            <div class="font-medium text-primary-text">Database</div>
                            <div class="text-sm text-secondary-text">MySQL 8.0</div>
                        </div>
                    </div>
                    <span class="text-sm text-success">Healthy</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <div>
                            <div class="font-medium text-primary-text">Redis Cache</div>
                            <div class="text-sm text-secondary-text">Session storage</div>
                        </div>
                    </div>
                    <span class="text-sm text-success">Healthy</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                        <div>
                            <div class="font-medium text-primary-text">API Gateway</div>
                            <div class="text-sm text-secondary-text">Rate limiting</div>
                        </div>
                    </div>
                    <span class="text-sm text-warning">Slow</span>
                </div>
            </div>
            <button class="btn btn-primary w-full mt-4">View Details</button>
        </div>
    </div>

    <!-- Feature Usage Analytics -->
    <div class="dashboard-card mb-8">
        <h3 class="text-lg font-semibold text-primary-text mb-6">Feature Usage Analytics</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="text-center p-4 bg-secondary-bg rounded-lg">
                <div class="w-12 h-12 bg-pink-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z"/>
                    </svg>
                </div>
                <div class="text-lg font-bold text-primary-text">78%</div>
                <div class="text-sm text-secondary-text">Instagram</div>
            </div>
            
            <div class="text-center p-4 bg-secondary-bg rounded-lg">
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div class="text-lg font-bold text-primary-text">65%</div>
                <div class="text-sm text-secondary-text">Link in Bio</div>
            </div>
            
            <div class="text-center p-4 bg-secondary-bg rounded-lg">
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="text-lg font-bold text-primary-text">45%</div>
                <div class="text-sm text-secondary-text">Courses</div>
            </div>
            
            <div class="text-center p-4 bg-secondary-bg rounded-lg">
                <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div class="text-lg font-bold text-primary-text">58%</div>
                <div class="text-sm text-secondary-text">E-commerce</div>
            </div>
            
            <div class="text-center p-4 bg-secondary-bg rounded-lg">
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="text-lg font-bold text-primary-text">72%</div>
                <div class="text-sm text-secondary-text">CRM</div>
            </div>
            
            <div class="text-center p-4 bg-secondary-bg rounded-lg">
                <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-lg font-bold text-primary-text">39%</div>
                <div class="text-sm text-secondary-text">Email Marketing</div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & System Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div class="dashboard-table">
            <div class="dashboard-table-header">
                <h3 class="dashboard-table-title">Recent Admin Activity</h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Admin</th>
                            <th>Action</th>
                            <th>Target</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                        JS
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">John Smith</div>
                                        <div class="text-sm text-secondary-text">Super Admin</div>
                                    </div>
                                </div>
                            </td>
                            <td>User Suspended</td>
                            <td>@spammer123</td>
                            <td>2 hours ago</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                        SA
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">Sarah Adams</div>
                                        <div class="text-sm text-secondary-text">Admin</div>
                                    </div>
                                </div>
                            </td>
                            <td>Plan Updated</td>
                            <td>Professional Plan</td>
                            <td>4 hours ago</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                        MJ
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">Mike Johnson</div>
                                        <div class="text-sm text-secondary-text">Admin</div>
                                    </div>
                                </div>
                            </td>
                            <td>Feature Toggle</td>
                            <td>Template Marketplace</td>
                            <td>6 hours ago</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Logs -->
        <div class="dashboard-table">
            <div class="dashboard-table-header">
                <h3 class="dashboard-table-title">System Logs</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center p-3 bg-secondary-bg rounded-lg">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-primary-text">System backup completed</div>
                            <div class="text-xs text-secondary-text">2 hours ago</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center p-3 bg-secondary-bg rounded-lg">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-primary-text">High CPU usage detected</div>
                            <div class="text-xs text-secondary-text">3 hours ago</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center p-3 bg-secondary-bg rounded-lg">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-primary-text">Database maintenance completed</div>
                            <div class="text-xs text-secondary-text">5 hours ago</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center p-3 bg-secondary-bg rounded-lg">
                        <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-primary-text">API rate limit exceeded</div>
                            <div class="text-xs text-secondary-text">8 hours ago</div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-secondary w-full mt-4">View All Logs</button>
            </div>
        </div>
    </div>
</div>
@endsection