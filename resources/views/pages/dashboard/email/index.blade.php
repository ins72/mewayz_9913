@extends('layouts.dashboard')

@section('title', 'Email Marketing')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Email Marketing</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Templates</button>
                <button class="btn btn-primary btn-sm">Create Campaign</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Create, send, and track email campaigns to engage your audience and drive conversions.
            </p>
        </div>
    </div>

    <!-- Email Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">12,847</div>
            <div class="stat-label">Subscribers</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +247 new this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">18.5%</div>
            <div class="stat-label">Open Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2.3% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">4.2%</div>
            <div class="stat-label">Click Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +0.8% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">$8,450</div>
            <div class="stat-label">Revenue</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +15% from last month
            </div>
        </div>
    </div>

    <!-- Campaign Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quick Actions</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">✉️</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Send Newsletter</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📊</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">View Analytics</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">👥</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Manage Lists</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🎨</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Design Template</div>
            </button>
        </div>
    </div>

    <!-- Recent Campaigns -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Campaigns</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-primary">All Campaigns</button>
                <button class="btn btn-sm btn-secondary">Sent</button>
                <button class="btn btn-sm btn-secondary">Drafts</button>
            </div>
        </div>
        <div class="space-y-4">
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-size: 1rem;">📧</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Summer Sale Newsletter</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Sent to 8,247 subscribers • 2 hours ago</div>
                </div>
                <div style="text-align: right; min-width: 8rem;">
                    <div class="flex gap-4 text-sm">
                        <div style="text-align: center;">
                            <div style="font-weight: 600; color: var(--text-primary);">21.2%</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Open Rate</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-weight: 600; color: var(--text-primary);">5.8%</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Click Rate</div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">View</button>
                    <button class="btn btn-sm btn-primary">Clone</button>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-secondary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-size: 1rem;">🎯</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Product Launch Announcement</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Sent to 5,632 subscribers • 1 day ago</div>
                </div>
                <div style="text-align: right; min-width: 8rem;">
                    <div class="flex gap-4 text-sm">
                        <div style="text-align: center;">
                            <div style="font-weight: 600; color: var(--text-primary);">18.7%</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Open Rate</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-weight: 600; color: var(--text-primary);">4.2%</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Click Rate</div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">View</button>
                    <button class="btn btn-sm btn-primary">Clone</button>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-warning); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-size: 1rem;">📝</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Weekly Tips & Tricks</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Draft • Created 3 days ago</div>
                </div>
                <div style="text-align: right; min-width: 8rem;">
                    <div class="flex gap-4 text-sm">
                        <div style="text-align: center;">
                            <div style="font-weight: 600; color: var(--text-secondary);">--</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Open Rate</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-weight: 600; color: var(--text-secondary);">--</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Click Rate</div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">Edit</button>
                    <button class="btn btn-sm btn-primary">Send</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Lists & Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Email Lists -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Email Lists</h3>
                <button class="btn btn-primary btn-sm">Create List</button>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📧</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Newsletter Subscribers</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">8,247 subscribers • 32 new this week</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Edit</button>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🛍️</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Customers</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">3,456 subscribers • 12 new this week</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Edit</button>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🎯</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Prospects</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">1,144 subscribers • 8 new this week</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Edit</button>
                    </div>
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
                    <div style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.5rem;">Email Performance Chart</div>
                    <div style="font-size: 0.875rem;">Track open rates, click rates, and conversions over time</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Automation -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Email Automation</h3>
            <button class="btn btn-primary btn-sm">Create Automation</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div style="padding: 1rem; background: var(--bg-primary); border-radius: 8px; text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">🤖</div>
                <div style="font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">Welcome Series</div>
                <div style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;">Automatically welcome new subscribers</div>
                <div style="color: var(--accent-secondary); font-size: 0.75rem;">Active • 247 subscribers</div>
            </div>
            
            <div style="padding: 1rem; background: var(--bg-primary); border-radius: 8px; text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">🛒</div>
                <div style="font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">Abandoned Cart</div>
                <div style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;">Recover lost sales automatically</div>
                <div style="color: var(--accent-secondary); font-size: 0.75rem;">Active • 89 recovered</div>
            </div>
            
            <div style="padding: 1rem; background: var(--bg-primary); border-radius: 8px; text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">🎂</div>
                <div style="font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">Birthday Wishes</div>
                <div style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;">Send birthday discounts</div>
                <div style="color: var(--accent-warning); font-size: 0.75rem;">Paused • 0 sent</div>
            </div>
        </div>
    </div>
</div>
@endsection