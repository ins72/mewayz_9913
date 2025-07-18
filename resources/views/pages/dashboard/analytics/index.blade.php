@extends('layouts.dashboard')

@section('title', 'Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Analytics Overview</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Export Data</button>
                <button class="btn btn-primary btn-sm">Generate Report</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Track your performance across all channels and campaigns.
            </p>
        </div>
    </div>

    <!-- Time Period Filter -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Time Period</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-secondary">Today</button>
                <button class="btn btn-sm btn-primary">7 Days</button>
                <button class="btn btn-sm btn-secondary">30 Days</button>
                <button class="btn btn-sm btn-secondary">90 Days</button>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">12,847</div>
            <div class="stat-label">Total Visitors</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +18% from last period
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">4.2%</div>
            <div class="stat-label">Conversion Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2.1% from last period
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">2m 45s</div>
            <div class="stat-label">Avg. Session Duration</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +12s from last period
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">67.8%</div>
            <div class="stat-label">Bounce Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-error); margin-top: 0.5rem;">
                ↘ -5.2% from last period
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Traffic Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Traffic Overview</h3>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">Views</button>
                    <button class="btn btn-sm btn-primary">Visitors</button>
                </div>
            </div>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center; background: var(--bg-primary); border-radius: 8px;">
                <div style="text-align: center; color: var(--text-secondary);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📊</div>
                    <div style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.5rem;">Traffic Chart</div>
                    <div style="font-size: 0.875rem;">Real-time traffic analytics will be displayed here</div>
                </div>
            </div>
        </div>

        <!-- Top Sources -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top Traffic Sources</h3>
                <a href="#" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🔍</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Google Search</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">5,248 visits • 42.8%</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🔗</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Direct Traffic</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">3,124 visits • 25.4%</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📱</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Social Media</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">2,847 visits • 23.2%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detailed Analytics</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-primary">Pages</button>
                <button class="btn btn-sm btn-secondary">Events</button>
                <button class="btn btn-sm btn-secondary">Goals</button>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Page</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Views</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Unique Views</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Bounce Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">/dashboard</td>
                        <td style="padding: 1rem; color: var(--text-primary);">3,245</td>
                        <td style="padding: 1rem; color: var(--text-primary);">2,847</td>
                        <td style="padding: 1rem; color: var(--accent-secondary);">45.2%</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">/bio-site</td>
                        <td style="padding: 1rem; color: var(--text-primary);">2,156</td>
                        <td style="padding: 1rem; color: var(--text-primary);">1,923</td>
                        <td style="padding: 1rem; color: var(--accent-secondary);">38.7%</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">/courses</td>
                        <td style="padding: 1rem; color: var(--text-primary);">1,847</td>
                        <td style="padding: 1rem; color: var(--text-primary);">1,654</td>
                        <td style="padding: 1rem; color: var(--accent-secondary);">52.3%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection