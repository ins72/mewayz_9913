@extends('layouts.dashboard')

@section('title', 'Automation')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Automation</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Templates</button>
                <button class="btn btn-primary btn-sm">Create Automation</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Automate your workflows, social media posts, and marketing campaigns to save time and increase efficiency.
            </p>
        </div>
    </div>

    <!-- Automation Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">8</div>
            <div class="stat-label">Active Automations</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2 new this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">1,247</div>
            <div class="stat-label">Tasks Executed</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +127 this week
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">24h</div>
            <div class="stat-label">Time Saved</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +6h this week
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">98.5%</div>
            <div class="stat-label">Success Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +1.2% this month
            </div>
        </div>
    </div>

    <!-- Active Automations -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Active Automations</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-primary">All</button>
                <button class="btn btn-sm btn-secondary">Active</button>
                <button class="btn btn-sm btn-secondary">Paused</button>
            </div>
        </div>
        <div class="space-y-4">
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-size: 1.25rem;">📧</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Welcome Email Sequence</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Automatically send welcome emails to new subscribers</div>
                </div>
                <div style="text-align: right;">
                    <div style="background: var(--accent-secondary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Active</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.5rem;">47 executed</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">Edit</button>
                    <button class="btn btn-sm btn-secondary">Pause</button>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-secondary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-size: 1.25rem;">📱</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Social Media Posts</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Daily posts to Facebook, Instagram, and Twitter</div>
                </div>
                <div style="text-align: right;">
                    <div style="background: var(--accent-secondary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Active</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.5rem;">156 executed</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">Edit</button>
                    <button class="btn btn-sm btn-secondary">Pause</button>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-warning); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-size: 1.25rem;">🎯</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Lead Nurturing</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Follow up with leads based on their behavior</div>
                </div>
                <div style="text-align: right;">
                    <div style="background: var(--accent-warning); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Paused</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.5rem;">23 executed</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">Edit</button>
                    <button class="btn btn-sm btn-primary">Resume</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Automation Templates -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Automation Templates</h3>
            <a href="#" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="card">
                <div class="space-y-3">
                    <div style="height: 120px; background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 2rem;">🤖</span>
                    </div>
                    <div>
                        <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Welcome Series</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Automatically welcome new subscribers with a series of engaging emails.</p>
                    </div>
                    <button class="btn btn-sm btn-primary w-full">Use Template</button>
                </div>
            </div>

            <div class="card">
                <div class="space-y-3">
                    <div style="height: 120px; background: linear-gradient(135deg, var(--accent-warning) 0%, var(--accent-error) 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 2rem;">🛒</span>
                    </div>
                    <div>
                        <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Abandoned Cart</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Recover lost sales with automated abandoned cart email sequences.</p>
                    </div>
                    <button class="btn btn-sm btn-primary w-full">Use Template</button>
                </div>
            </div>

            <div class="card">
                <div class="space-y-3">
                    <div style="height: 120px; background: linear-gradient(135deg, var(--accent-secondary) 0%, var(--accent-primary) 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 2rem;">📅</span>
                    </div>
                    <div>
                        <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Content Scheduler</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Schedule and publish content across multiple social media platforms.</p>
                    </div>
                    <button class="btn btn-sm btn-primary w-full">Use Template</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection