@extends('layouts.dashboard')

@section('title', 'Social Media')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Social Media Management</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Connect Account</button>
                <button class="btn btn-primary btn-sm">Create Post</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Manage all your social media accounts, schedule posts, and track engagement from one dashboard.
            </p>
        </div>
    </div>

    <!-- Social Media Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">47,238</div>
            <div class="stat-label">Total Followers</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +1,247 this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">156</div>
            <div class="stat-label">Posts This Month</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +12 from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">8.7%</div>
            <div class="stat-label">Engagement Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +1.2% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">$2,847</div>
            <div class="stat-label">Ad Spend</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +$347 from last month
            </div>
        </div>
    </div>

    <!-- Connected Accounts -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Connected Accounts</h3>
            <button class="btn btn-secondary btn-sm">Manage Connections</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Facebook -->
            <div class="card">
                <div class="space-y-3">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: #1877F2; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1rem;">f</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Facebook</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">@mybusiness</div>
                        </div>
                        <div style="background: var(--accent-secondary); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-weight: 600; color: var(--text-primary);">24,580</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Followers</div>
                    </div>
                </div>
            </div>

            <!-- Instagram -->
            <div class="card">
                <div class="space-y-3">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: linear-gradient(45deg, #E4405F, #FCAF45); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1rem;">📷</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Instagram</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">@mybusiness</div>
                        </div>
                        <div style="background: var(--accent-secondary); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-weight: 600; color: var(--text-primary);">15,247</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Followers</div>
                    </div>
                </div>
            </div>

            <!-- Twitter -->
            <div class="card">
                <div class="space-y-3">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: #1DA1F2; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1rem;">🐦</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Twitter</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">@mybusiness</div>
                        </div>
                        <div style="background: var(--accent-secondary); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-weight: 600; color: var(--text-primary);">5,847</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Followers</div>
                    </div>
                </div>
            </div>

            <!-- LinkedIn -->
            <div class="card">
                <div class="space-y-3">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: #0A66C2; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1rem;">in</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">LinkedIn</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">@mybusiness</div>
                        </div>
                        <div style="background: var(--accent-secondary); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-weight: 600; color: var(--text-primary);">1,564</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Connections</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Posts -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Posts</h3>
            <a href="#" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="space-y-4">
            <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 4rem; height: 4rem; background: var(--accent-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span style="color: white; font-size: 1.25rem;">📱</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem; margin-bottom: 0.5rem;">Marketing Tips for Small Business</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem; margin-bottom: 0.5rem;">Facebook, Instagram • 2 hours ago</div>
                    <div style="display: flex; gap: 1rem; font-size: 0.75rem; color: var(--text-secondary);">
                        <span>👍 247 likes</span>
                        <span>💬 23 comments</span>
                        <span>🔄 12 shares</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection