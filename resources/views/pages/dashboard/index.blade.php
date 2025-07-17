@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-primary-text">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="text-secondary-text mt-2">Here's what's happening with your business today</p>
            </div>
            <div class="flex items-center space-x-4">
                <button class="btn btn-secondary">
                    <x-icon name="download" size="sm" class="mr-2" alt="Export data" />
                    Export Data
                </button>
                <button class="btn btn-primary">
                    <x-icon name="plus" size="sm" class="mr-2" alt="Create new" />
                    Create New
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="dashboard-grid mb-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Total Revenue</h3>
                <x-icon name="dollar" size="md" class="dashboard-card-icon" alt="Revenue" />
            </div>
            <div class="dashboard-card-value">$45,320</div>
            <div class="dashboard-card-change positive">+12.5% from last month</div>
            <div class="mt-4">
                <svg class="w-full h-8" viewBox="0 0 100 20">
                    <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,15 20,12 40,8 60,10 80,5 100,7" opacity="0.5"/>
                </svg>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Active Users</h3>
                <x-icon name="users" size="md" class="dashboard-card-icon" alt="Users" />
            </div>
            <div class="dashboard-card-value">2,847</div>
            <div class="dashboard-card-change positive">+8.2% from last month</div>
            <div class="mt-4">
                <svg class="w-full h-8" viewBox="0 0 100 20">
                    <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,18 20,15 40,12 60,9 80,7 100,5" opacity="0.5"/>
                </svg>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Conversion Rate</h3>
                <x-icon name="trending" size="md" class="dashboard-card-icon" alt="Trending" />
            </div>
            <div class="dashboard-card-value">18.4%</div>
            <div class="dashboard-card-change positive">+2.1% from last month</div>
            <div class="mt-4">
                <svg class="w-full h-8" viewBox="0 0 100 20">
                    <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,12 20,14 40,11 60,8 80,6 100,4" opacity="0.5"/>
                </svg>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Orders</h3>
                <x-icon name="shopping-bag" size="md" class="dashboard-card-icon" alt="Orders" />
            </div>
            <div class="dashboard-card-value">1,234</div>
            <div class="dashboard-card-change positive">+15.3% from last month</div>
            <div class="mt-4">
                <svg class="w-full h-8" viewBox="0 0 100 20">
                    <polyline fill="none" stroke="currentColor" stroke-width="2" points="0,16 20,13 40,10 60,7 80,9 100,6" opacity="0.5"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-primary-text mb-6">Quick Actions</h2>
        <div class="grid grid-cols-3 md:grid-cols-6 lg:grid-cols-9 gap-4">
            <!-- Instagram Management -->
            <a href="{{ route('dashboard-instagram-index') }}" class="dashboard-card text-center hover:transform hover:scale-105 transition-all">
                <div class="w-12 h-12 bg-pink-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </div>
                <div class="text-sm font-medium text-primary-text">Instagram Search</div>
            </a>

            <!-- Post Scheduler -->
            <a href="{{ route('dashboard-social-index') }}" class="dashboard-card text-center hover:transform hover:scale-105 transition-all">
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-sm font-medium text-primary-text">Post Scheduler</div>
            </a>

            <!-- Link Builder -->
            <a href="{{ route('dashboard-linkinbio-index') }}" class="dashboard-card text-center hover:transform hover:scale-105 transition-all">
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div class="text-sm font-medium text-primary-text">Link Builder</div>
            </a>

            <!-- Course Creator -->
            <a href="{{ route('dashboard-courses-index') }}" class="dashboard-card text-center hover:transform hover:scale-105 transition-all">
                <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="text-sm font-medium text-primary-text">Course Creator</div>
            </a>

            <!-- Store Manager -->
            <a href="{{ route('dashboard-store-index') }}" class="dashboard-card text-center hover:transform hover:scale-105 transition-all">
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div class="text-sm font-medium text-primary-text">Store Manager</div>
            </a>

            <!-- CRM Hub -->
            <a href="{{ route('dashboard-crm-index') }}" class="dashboard-card text-center hover:transform hover:scale-105 transition-all">
                <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="text-sm font-medium text-primary-text">CRM Hub</div>
            </a>

            <!-- Email Marketing -->
            <a href="{{ route('dashboard-email-index') }}" class="dashboard-card text-center hover:transform hover:scale-105 transition-all">
                <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-sm font-medium text-primary-text">Email Marketing</div>
            </a>

            <!-- Content Calendar -->
            <a href="{{ route('dashboard-calendar-index') }}" class="dashboard-card text-center hover:transform hover:scale-105 transition-all">
                <div class="w-12 h-12 bg-pink-600/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div class="text-sm font-medium text-primary-text">Content Calendar</div>
            </a>

            <!-- QR Generator -->
            <a href="{{ route('dashboard-qr-index') }}" class="dashboard-card text-center hover:transform hover:scale-105 transition-all">
                <div class="w-12 h-12 bg-gray-500/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div class="text-sm font-medium text-primary-text">QR Generator</div>
            </a>
        </div>
    </div>

    <!-- Recent Activity and Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div class="dashboard-table">
            <div class="dashboard-table-header">
                <h3 class="dashboard-table-title">Recent Activity</h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Type</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">Posted to Instagram</div>
                                        <div class="text-sm text-secondary-text">New product launch</div>
                                    </div>
                                </div>
                            </td>
                            <td>Social Media</td>
                            <td>2 hours ago</td>
                            <td><span class="text-success">Published</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">New order received</div>
                                        <div class="text-sm text-secondary-text">Order #12345</div>
                                    </div>
                                </div>
                            </td>
                            <td>E-commerce</td>
                            <td>3 hours ago</td>
                            <td><span class="text-warning">Processing</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-500/20 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">New lead captured</div>
                                        <div class="text-sm text-secondary-text">From contact form</div>
                                    </div>
                                </div>
                            </td>
                            <td>CRM</td>
                            <td>5 hours ago</td>
                            <td><span class="text-info">New</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-orange-500/20 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">Course completed</div>
                                        <div class="text-sm text-secondary-text">Marketing Fundamentals</div>
                                    </div>
                                </div>
                            </td>
                            <td>Education</td>
                            <td>1 day ago</td>
                            <td><span class="text-success">Completed</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="dashboard-table">
            <div class="dashboard-table-header">
                <h3 class="dashboard-table-title">Performance Overview</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex space-x-4">
                        <button class="btn btn-sm btn-primary">7 Days</button>
                        <button class="btn btn-sm btn-secondary">30 Days</button>
                        <button class="btn btn-sm btn-secondary">3 Months</button>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-text">892</div>
                        <div class="text-sm text-secondary-text">Page Views</div>
                        <div class="text-sm text-success">+12%</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-text">143</div>
                        <div class="text-sm text-secondary-text">Conversions</div>
                        <div class="text-sm text-success">+8%</div>
                    </div>
                </div>
                
                <div class="h-32 bg-secondary-bg rounded-lg flex items-center justify-center">
                    <div class="text-secondary-text">Chart visualization would go here</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection