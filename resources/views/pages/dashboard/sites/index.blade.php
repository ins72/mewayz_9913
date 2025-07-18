@extends('layouts.dashboard')

@section('title', 'Bio Sites')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Bio Sites</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Templates</button>
                <button class="btn btn-primary btn-sm">Create Bio Site</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Create beautiful link-in-bio pages to showcase your content and drive traffic to your important links.
            </p>
        </div>
    </div>

    <!-- Bio Sites Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">5</div>
            <div class="stat-label">Active Sites</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2 new this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">12,847</div>
            <div class="stat-label">Total Views</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +18% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">1,247</div>
            <div class="stat-label">Link Clicks</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +23% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">9.7%</div>
            <div class="stat-label">Click Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +1.2% from last month
            </div>
        </div>
    </div>

    <!-- Bio Sites List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Your Bio Sites</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-primary">All Sites</button>
                <button class="btn btn-sm btn-secondary">Active</button>
                <button class="btn btn-sm btn-secondary">Draft</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Bio Site 1 -->
            <div class="card">
                <div class="space-y-4">
                    <div style="height: 200px; background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; text-align: center;">
                        <div style="width: 4rem; height: 4rem; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.5rem;">👤</span>
                        </div>
                        <div style="font-weight: 600; font-size: 1.125rem;">John Doe</div>
                        <div style="font-size: 0.875rem; opacity: 0.9;">Digital Marketing Expert</div>
                    </div>
                    <div>
                        <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Personal Portfolio</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">mewayz.com/johndoe</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">2,456 views • 8 links</div>
                            <div style="color: var(--accent-secondary); font-size: 0.75rem;">Active</div>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Edit</button>
                            <button class="btn btn-sm btn-primary">View</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create New Site -->
            <div class="card" style="border: 2px dashed var(--border-primary); background: var(--bg-primary);">
                <div style="height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-secondary);">➕</div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Create New Bio Site</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1.5rem;">Start building your link-in-bio page</p>
                    <button class="btn btn-primary">Get Started</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection