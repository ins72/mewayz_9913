@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Welcome Back!</h2>
            <div class="header-actions">
                <a href="{{ route('dashboard-upgrade-index') }}" class="btn btn-primary btn-sm">Upgrade Plan</a>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Here's what's happening with your business today.
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">2,847</div>
            <div class="stat-label">Total Visitors</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +12% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">$12,450</div>
            <div class="stat-label">Revenue</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +8% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">456</div>
            <div class="stat-label">Subscribers</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +15% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">89.2%</div>
            <div class="stat-label">Engagement Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +3% from last month
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Activity</h3>
                <a href="#" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">👤</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">New subscriber joined</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">john@example.com • 2 hours ago</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">💰</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Payment received</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">$99.00 • 4 hours ago</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📝</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">New course published</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Advanced Marketing • 6 hours ago</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('dashboard-sites-index') }}" class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; text-decoration: none; display: block;">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🌐</div>
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Create Site</div>
                </a>
                
                <a href="{{ route('dashboard-courses-index') }}" class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; text-decoration: none; display: block;">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📚</div>
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">New Course</div>
                </a>
                
                <a href="{{ route('dashboard-email-index') }}" class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; text-decoration: none; display: block;">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">✉️</div>
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Send Email</div>
                </a>
                
                <a href="{{ route('dashboard-analytics-index') }}" class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; text-decoration: none; display: block;">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📊</div>
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">View Analytics</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Performance Overview</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-secondary">7D</button>
                <button class="btn btn-sm btn-primary">30D</button>
                <button class="btn btn-sm btn-secondary">90D</button>
            </div>
        </div>
        <div style="height: 300px; display: flex; align-items: center; justify-content: center; background: var(--bg-primary); border-radius: 8px;">
            <div style="text-align: center; color: var(--text-secondary);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📈</div>
                <div style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.5rem;">Chart Coming Soon</div>
                <div style="font-size: 0.875rem;">Performance analytics will be displayed here</div>
            </div>
        </div>
    </div>
</div>
@endsection