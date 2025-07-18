@extends('layouts.dashboard')

@section('title', 'Notifications')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Notifications</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Mark All Read</button>
                <button class="btn btn-primary btn-sm">Settings</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Stay updated with important notifications about your account, campaigns, and system updates.
            </p>
        </div>
    </div>

    <!-- Notification Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">12</div>
            <div class="stat-label">Unread</div>
            <div style="font-size: 0.75rem; color: var(--accent-warning); margin-top: 0.5rem;">
                3 urgent notifications
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">247</div>
            <div class="stat-label">Total Today</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +18 from yesterday
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">89%</div>
            <div class="stat-label">Read Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2% from last week
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">4.2s</div>
            <div class="stat-label">Avg Response</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↘ -0.8s from last week
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Notifications</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-secondary">Today</button>
                <button class="btn btn-sm btn-secondary">This Week</button>
                <button class="btn btn-sm btn-secondary">All Time</button>
            </div>
        </div>
        <div class="space-y-1">
            <!-- Unread Important Notification -->
            <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px; border-left: 4px solid var(--accent-error);">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-error); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span style="color: white; font-size: 0.875rem;">⚠️</span>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Payment Failed</div>
                        <div style="background: var(--accent-error); color: white; padding: 0.125rem 0.375rem; border-radius: 4px; font-size: 0.625rem;">URGENT</div>
                        <div style="width: 0.5rem; height: 0.5rem; background: var(--accent-primary); border-radius: 50%;"></div>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">
                        Your subscription payment of $149 failed. Please update your payment method to continue service.
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">2 hours ago</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-error">Update Payment</button>
                    <button class="btn btn-sm btn-secondary">Dismiss</button>
                </div>
            </div>

            <!-- Unread System Notification -->
            <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px; border-left: 4px solid var(--accent-warning);">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span style="color: white; font-size: 0.875rem;">⚡</span>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">System Maintenance</div>
                        <div style="background: var(--accent-warning); color: white; padding: 0.125rem 0.375rem; border-radius: 4px; font-size: 0.625rem;">SYSTEM</div>
                        <div style="width: 0.5rem; height: 0.5rem; background: var(--accent-primary); border-radius: 50%;"></div>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;">
                        Scheduled maintenance on January 25th, 2:00 AM - 4:00 AM EST. Some features may be temporarily unavailable.
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">4 hours ago</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">Learn More</button>
                    <button class="btn btn-sm btn-secondary">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection