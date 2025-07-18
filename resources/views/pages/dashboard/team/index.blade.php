@extends('layouts.dashboard')

@section('title', 'Team Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Team Management</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Import Members</button>
                <button class="btn btn-primary btn-sm">Invite Member</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Manage your team members, roles, permissions, and collaboration settings.
            </p>
        </div>
    </div>

    <!-- Team Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">8</div>
            <div class="stat-label">Team Members</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2 new this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">6</div>
            <div class="stat-label">Active Today</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +1 from yesterday
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">12</div>
            <div class="stat-label">Projects</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +3 active projects
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">94.5%</div>
            <div class="stat-label">Team Satisfaction</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2.1% from last survey
            </div>
        </div>
    </div>

    <!-- Team Roles -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Team Roles</h3>
            <button class="btn btn-secondary btn-sm">Manage Roles</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">👑</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Owner</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">1 member</div>
            </div>
            
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🛡️</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Admin</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">2 members</div>
            </div>
            
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">👥</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Member</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">4 members</div>
            </div>
            
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">👀</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Viewer</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">1 member</div>
            </div>
        </div>
    </div>

    <!-- Team Members -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Team Members</h3>
            <div class="flex gap-2">
                <input type="text" placeholder="Search members..." class="form-input" style="padding: 0.5rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
                <button class="btn btn-secondary btn-sm">Filter</button>
            </div>
        </div>
        <div class="space-y-4">
            <!-- Team Member 1 -->
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-weight: 600;">JD</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">John Doe</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">john@example.com</div>
                </div>
                <div style="text-align: center; min-width: 6rem;">
                    <div style="background: var(--accent-primary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Owner</div>
                </div>
                <div style="text-align: center; min-width: 6rem;">
                    <div style="color: var(--accent-secondary); font-size: 0.75rem;">🟢 Online</div>
                </div>
                <div style="text-align: center; min-width: 6rem;">
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Joined 2 years ago</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">View Profile</button>
                    <button class="btn btn-sm btn-secondary">Message</button>
                </div>
            </div>
            
            <!-- Team Member 2 -->
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-weight: 600;">SJ</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Sarah Johnson</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">sarah@example.com</div>
                </div>
                <div style="text-align: center; min-width: 6rem;">
                    <div style="background: var(--accent-secondary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Admin</div>
                </div>
                <div style="text-align: center; min-width: 6rem;">
                    <div style="color: var(--accent-secondary); font-size: 0.75rem;">🟢 Online</div>
                </div>
                <div style="text-align: center; min-width: 6rem;">
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Joined 1 year ago</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">View Profile</button>
                    <button class="btn btn-sm btn-secondary">Message</button>
                </div>
            </div>
            
            <!-- Team Member 3 -->
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-weight: 600;">MC</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Mike Chen</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">mike@example.com</div>
                </div>
                <div style="text-align: center; min-width: 6rem;">
                    <div style="background: var(--accent-warning); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Member</div>
                </div>
                <div style="text-align: center; min-width: 6rem;">
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">⚫ Offline</div>
                </div>
                <div style="text-align: center; min-width: 6rem;">
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Joined 8 months ago</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">View Profile</button>
                    <button class="btn btn-sm btn-secondary">Message</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Invitations & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Invitations -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pending Invitations</h3>
                <a href="#" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">✉️</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">alex@example.com</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Invited as Member • 2 days ago</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Resend</button>
                        <button class="btn btn-sm btn-error">Cancel</button>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">✉️</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">lisa@example.com</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Invited as Viewer • 5 days ago</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Resend</button>
                        <button class="btn btn-sm btn-error">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Activity</h3>
                <a href="#" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">👤</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">New member joined</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Emma Wilson accepted invitation • 2 hours ago</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🔄</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Role updated</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Mike Chen promoted to Admin • 1 day ago</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📝</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Project created</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Sarah Johnson created "Website Redesign" • 2 days ago</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Permissions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Role Permissions</h3>
            <button class="btn btn-secondary btn-sm">Manage Permissions</button>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Permission</th>
                        <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--text-primary);">Owner</th>
                        <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--text-primary);">Admin</th>
                        <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--text-primary);">Member</th>
                        <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--text-primary);">Viewer</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Manage Team</td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--text-secondary);">❌</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--text-secondary);">❌</span></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Create Projects</td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--text-secondary);">❌</span></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Edit Content</td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--text-secondary);">❌</span></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">View Analytics</td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection