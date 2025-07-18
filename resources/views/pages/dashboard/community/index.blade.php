@extends('layouts.dashboard')

@section('title', 'Community')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Community Hub</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Member Settings</button>
                <button class="btn btn-primary btn-sm">Create Group</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Build and manage your community, engage with members, and foster meaningful connections.
            </p>
        </div>
    </div>

    <!-- Community Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">1,247</div>
            <div class="stat-label">Total Members</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +42 new this week
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">84</div>
            <div class="stat-label">Active Today</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +12% from yesterday
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">156</div>
            <div class="stat-label">New Posts</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +8 from yesterday
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">92.3%</div>
            <div class="stat-label">Engagement Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +3.2% from last week
            </div>
        </div>
    </div>

    <!-- Community Groups -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Community Groups</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-primary">All Groups</button>
                <button class="btn btn-sm btn-secondary">My Groups</button>
                <button class="btn btn-sm btn-secondary">Moderated</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Group 1 -->
            <div class="card">
                <div style="height: 120px; background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                    <div style="color: white; font-size: 2rem;">🚀</div>
                </div>
                <div class="space-y-2">
                    <h4 style="font-weight: 600; color: var(--text-primary);">Entrepreneurs Hub</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Connect with fellow entrepreneurs and share business insights.</p>
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">423 members</span>
                        <span style="color: var(--text-secondary);">24 posts today</span>
                    </div>
                    <button class="btn btn-sm btn-primary w-full">Join Group</button>
                </div>
            </div>

            <!-- Group 2 -->
            <div class="card">
                <div style="height: 120px; background: linear-gradient(135deg, var(--accent-warning) 0%, var(--accent-error) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                    <div style="color: white; font-size: 2rem;">🎨</div>
                </div>
                <div class="space-y-2">
                    <h4 style="font-weight: 600; color: var(--text-primary);">Creative Designers</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Share your creative work and get feedback from designers.</p>
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">287 members</span>
                        <span style="color: var(--text-secondary);">18 posts today</span>
                    </div>
                    <button class="btn btn-sm btn-secondary w-full">Joined</button>
                </div>
            </div>

            <!-- Group 3 -->
            <div class="card">
                <div style="height: 120px; background: linear-gradient(135deg, var(--accent-secondary) 0%, var(--accent-primary) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                    <div style="color: white; font-size: 2rem;">💻</div>
                </div>
                <div class="space-y-2">
                    <h4 style="font-weight: 600; color: var(--text-primary);">Tech Innovators</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Discuss the latest tech trends and innovations.</p>
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">156 members</span>
                        <span style="color: var(--text-secondary);">12 posts today</span>
                    </div>
                    <button class="btn btn-sm btn-primary w-full">Join Group</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Posts -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Posts</h3>
                <a href="#" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="space-y-4">
                <div style="padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 0.875rem;">JS</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">John Smith</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">2 hours ago • Entrepreneurs Hub</div>
                        </div>
                    </div>
                    <div style="color: var(--text-primary); font-size: 0.875rem; margin-bottom: 1rem;">
                        Just launched my new SaaS product! Looking for feedback from fellow entrepreneurs. What features do you think are most important for user onboarding?
                    </div>
                    <div class="flex gap-4 text-sm">
                        <button style="color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                            <span>👍</span> 12 likes
                        </button>
                        <button style="color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                            <span>💬</span> 5 comments
                        </button>
                        <button style="color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                            <span>📤</span> Share
                        </button>
                    </div>
                </div>
                
                <div style="padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 0.875rem;">SJ</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Sarah Johnson</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">4 hours ago • Creative Designers</div>
                        </div>
                    </div>
                    <div style="color: var(--text-primary); font-size: 0.875rem; margin-bottom: 1rem;">
                        Excited to share my latest UI design project! This dashboard redesign took 3 weeks but I'm really happy with the results. What do you think?
                    </div>
                    <div class="flex gap-4 text-sm">
                        <button style="color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                            <span>👍</span> 24 likes
                        </button>
                        <button style="color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                            <span>💬</span> 8 comments
                        </button>
                        <button style="color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                            <span>📤</span> Share
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Contributors -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top Contributors</h3>
                <a href="#" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">AW</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Alex Wilson</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">247 posts • 1.2k likes received</div>
                    </div>
                    <div style="background: var(--accent-primary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                        #1
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">MK</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Maria Kim</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">189 posts • 987 likes received</div>
                    </div>
                    <div style="background: var(--accent-secondary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                        #2
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">DL</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">David Lee</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">156 posts • 743 likes received</div>
                    </div>
                    <div style="background: var(--accent-warning); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                        #3
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Moderation Tools -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Moderation Tools</h3>
            <button class="btn btn-secondary btn-sm">Settings</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🛡️</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Auto-moderation</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">3 actions today</div>
            </div>
            
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📝</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Pending Reviews</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">2 posts waiting</div>
            </div>
            
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🚫</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Blocked Users</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">5 total</div>
            </div>
            
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">⚠️</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Reports</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">1 open report</div>
            </div>
        </div>
    </div>
</div>
@endsection