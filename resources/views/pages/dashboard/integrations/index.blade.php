@extends('layouts.dashboard')

@section('title', 'Integrations')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Integrations</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Browse All</button>
                <button class="btn btn-primary btn-sm">Custom Integration</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Connect with your favorite tools and automate your workflow seamlessly.
            </p>
        </div>
    </div>

    <!-- Integration Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">12</div>
            <div class="stat-label">Active Integrations</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +3 new this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">4,247</div>
            <div class="stat-label">API Calls</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +18% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">99.8%</div>
            <div class="stat-label">Uptime</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +0.2% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">2.3s</div>
            <div class="stat-label">Avg Response</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↘ -0.5s from last month
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Categories</h3>
        </div>
        <div class="flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-primary">All</button>
            <button class="btn btn-sm btn-secondary">Marketing</button>
            <button class="btn btn-sm btn-secondary">Analytics</button>
            <button class="btn btn-sm btn-secondary">Payment</button>
            <button class="btn btn-sm btn-secondary">Communication</button>
            <button class="btn btn-sm btn-secondary">CRM</button>
            <button class="btn btn-sm btn-secondary">Social Media</button>
        </div>
    </div>

    <!-- Active Integrations -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Active Integrations</h3>
            <a href="#" class="btn btn-secondary btn-sm">Manage All</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Integration 1 -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">📊</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Google Analytics</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Analytics • Active</div>
                        </div>
                        <div style="background: var(--accent-secondary); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Track website performance and user behavior with comprehensive analytics.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Last sync: 2 hours ago</div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Settings</button>
                            <button class="btn btn-sm btn-error">Disconnect</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integration 2 -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-secondary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">💳</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Stripe</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Payment • Active</div>
                        </div>
                        <div style="background: var(--accent-secondary); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Accept payments, manage subscriptions, and handle billing seamlessly.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Last sync: 1 hour ago</div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Settings</button>
                            <button class="btn btn-sm btn-error">Disconnect</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integration 3 -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-warning); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">✉️</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Mailchimp</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Email Marketing • Active</div>
                        </div>
                        <div style="background: var(--accent-secondary); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Sync contacts and create automated email marketing campaigns.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Last sync: 30 minutes ago</div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Settings</button>
                            <button class="btn btn-sm btn-error">Disconnect</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integration 4 -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">📱</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Facebook</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Social Media • Active</div>
                        </div>
                        <div style="background: var(--accent-warning); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Manage posts, track engagement, and run ad campaigns across Facebook.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Last sync: 5 hours ago</div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Settings</button>
                            <button class="btn btn-sm btn-error">Disconnect</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integration 5 -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-secondary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">🔗</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Zapier</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Automation • Active</div>
                        </div>
                        <div style="background: var(--accent-secondary); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Connect 5000+ apps and automate workflows between different services.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Last sync: 15 minutes ago</div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Settings</button>
                            <button class="btn btn-sm btn-error">Disconnect</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integration 6 -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-error); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">💬</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Slack</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Communication • Error</div>
                        </div>
                        <div style="background: var(--accent-error); width: 0.5rem; height: 0.5rem; border-radius: 50%;"></div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Get notifications and updates directly in your Slack workspace.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div style="color: var(--accent-error); font-size: 0.75rem;">Connection failed</div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-warning">Reconnect</button>
                            <button class="btn btn-sm btn-error">Disconnect</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Integrations -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Available Integrations</h3>
            <a href="#" class="btn btn-secondary btn-sm">Browse All</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Available Integration 1 -->
            <div class="card">
                <div class="space-y-3">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1rem;">📈</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">HubSpot</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">CRM</div>
                        </div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.75rem;">Sync contacts and track customer interactions.</p>
                    </div>
                    <button class="btn btn-sm btn-primary w-full">Connect</button>
                </div>
            </div>

            <!-- Available Integration 2 -->
            <div class="card">
                <div class="space-y-3">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1rem;">🎬</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">YouTube</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Video</div>
                        </div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.75rem;">Embed videos and track performance metrics.</p>
                    </div>
                    <button class="btn btn-sm btn-primary w-full">Connect</button>
                </div>
            </div>

            <!-- Available Integration 3 -->
            <div class="card">
                <div class="space-y-3">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1rem;">🛒</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">Shopify</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">E-commerce</div>
                        </div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.75rem;">Sync products and manage online store.</p>
                    </div>
                    <button class="btn btn-sm btn-primary w-full">Connect</button>
                </div>
            </div>

            <!-- Available Integration 4 -->
            <div class="card">
                <div class="space-y-3">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1rem;">💼</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">LinkedIn</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Professional</div>
                        </div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.75rem;">Share content and manage professional network.</p>
                    </div>
                    <button class="btn btn-sm btn-primary w-full">Connect</button>
                </div>
            </div>
        </div>
    </div>

    <!-- API Keys Management -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">API Keys</h3>
            <button class="btn btn-primary btn-sm">Generate New Key</button>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Name</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Key</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Created</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Last Used</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Production API</td>
                        <td style="padding: 1rem; color: var(--text-secondary); font-family: monospace;">sk_live_****...****</td>
                        <td style="padding: 1rem; color: var(--text-primary);">Jan 15, 2024</td>
                        <td style="padding: 1rem; color: var(--text-primary);">2 hours ago</td>
                        <td style="padding: 1rem;">
                            <div class="flex gap-2">
                                <button class="btn btn-sm btn-secondary">Edit</button>
                                <button class="btn btn-sm btn-error">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Development API</td>
                        <td style="padding: 1rem; color: var(--text-secondary); font-family: monospace;">sk_test_****...****</td>
                        <td style="padding: 1rem; color: var(--text-primary);">Jan 10, 2024</td>
                        <td style="padding: 1rem; color: var(--text-primary);">1 day ago</td>
                        <td style="padding: 1rem;">
                            <div class="flex gap-2">
                                <button class="btn btn-sm btn-secondary">Edit</button>
                                <button class="btn btn-sm btn-error">Delete</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection