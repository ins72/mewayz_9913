@extends('layouts.dashboard')

@section('title', 'Workspace Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Workspace Management</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Workspace Settings</button>
                <button class="btn btn-primary btn-sm">Create Workspace</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Manage your workspaces, collaborate with team members, and organize your projects efficiently.
            </p>
        </div>
    </div>

    <!-- Workspace Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">3</div>
            <div class="stat-label">Active Workspaces</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +1 new this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">12</div>
            <div class="stat-label">Total Projects</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +3 this week
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">8</div>
            <div class="stat-label">Team Members</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2 invited
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">94.2%</div>
            <div class="stat-label">Project Completion</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +5% this month
            </div>
        </div>
    </div>

    <!-- Current Workspace -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Current Workspace</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-secondary">Switch</button>
                <button class="btn btn-sm btn-primary">Settings</button>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
            <div style="width: 4rem; height: 4rem; background: var(--accent-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <span style="color: white; font-size: 1.5rem;">🚀</span>
            </div>
            <div style="flex: 1;">
                <div style="font-weight: 600; color: var(--text-primary); font-size: 1.125rem;">Marketing Agency Hub</div>
                <div style="color: var(--text-secondary); font-size: 0.875rem;">Primary workspace for client projects and campaigns</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.5rem;">Created on January 15, 2024 • 5 active projects</div>
            </div>
            <div style="text-align: right;">
                <div style="background: var(--accent-secondary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; margin-bottom: 0.5rem;">Owner</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">6 members</div>
            </div>
        </div>
    </div>

    <!-- All Workspaces -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Workspaces</h3>
            <div class="flex gap-2">
                <input type="text" placeholder="Search workspaces..." class="form-input" style="padding: 0.5rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
                <button class="btn btn-secondary btn-sm">Filter</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Workspace 1 -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">🚀</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Marketing Agency Hub</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Owner</div>
                        </div>
                        <div style="background: var(--accent-secondary); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Primary workspace for client projects and marketing campaigns.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">6 members • 5 projects</div>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Settings</button>
                            <button class="btn btn-sm btn-primary">Open</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workspace 2 -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-secondary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">🎨</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Creative Studio</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Admin</div>
                        </div>
                        <div style="background: var(--accent-secondary); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Design and creative projects workspace for brand development.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">3 members • 4 projects</div>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Settings</button>
                            <button class="btn btn-sm btn-primary">Open</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create New Workspace -->
            <div class="card" style="border: 2px dashed var(--border-primary); background: var(--bg-primary);">
                <div style="height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-secondary);">➕</div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Create New Workspace</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1.5rem;">Start a new project or team collaboration</p>
                    <button class="btn btn-primary">Create Workspace</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection