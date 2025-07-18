@extends('layouts.dashboard')

@section('title', 'CRM & Leads')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">CRM & Lead Management</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Import Contacts</button>
                <button class="btn btn-primary btn-sm">Add Lead</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Manage your customer relationships, track leads, and optimize your sales funnel.
            </p>
        </div>
    </div>

    <!-- CRM Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">1,247</div>
            <div class="stat-label">Total Contacts</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +32 new this week
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">156</div>
            <div class="stat-label">Active Leads</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +12 from last week
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">$45,230</div>
            <div class="stat-label">Pipeline Value</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +18% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">23.5%</div>
            <div class="stat-label">Conversion Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2.3% from last month
            </div>
        </div>
    </div>

    <!-- Pipeline Overview -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sales Pipeline</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-primary">This Month</button>
                <button class="btn btn-sm btn-secondary">Last Month</button>
                <button class="btn btn-sm btn-secondary">Custom Range</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-primary rounded-lg" style="background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">🎯</div>
                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Prospects</div>
                <div style="font-size: 1.5rem; color: var(--accent-primary); margin-bottom: 0.5rem;">42</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">$12,460 value</div>
            </div>
            
            <div class="text-center p-4 bg-primary rounded-lg" style="background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">📞</div>
                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Contacted</div>
                <div style="font-size: 1.5rem; color: var(--accent-warning); margin-bottom: 0.5rem;">28</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">$15,890 value</div>
            </div>
            
            <div class="text-center p-4 bg-primary rounded-lg" style="background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">💬</div>
                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Negotiating</div>
                <div style="font-size: 1.5rem; color: var(--accent-secondary); margin-bottom: 0.5rem;">15</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">$8,740 value</div>
            </div>
            
            <div class="text-center p-4 bg-primary rounded-lg" style="background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">✅</div>
                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Closed</div>
                <div style="font-size: 1.5rem; color: var(--accent-secondary); margin-bottom: 0.5rem;">8</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">$8,140 value</div>
            </div>
        </div>
    </div>

    <!-- Recent Leads -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Hot Leads -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Hot Leads</h3>
                <a href="#" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px; border-left: 4px solid var(--accent-error);">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-error); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🔥</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Acme Corp</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">john@acme.com • $5,000 potential</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Last contact</div>
                        <div style="color: var(--text-primary); font-size: 0.75rem;">2 hours ago</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px; border-left: 4px solid var(--accent-warning);">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">⚡</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">TechStart Inc</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">sarah@techstart.com • $3,200 potential</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Last contact</div>
                        <div style="color: var(--text-primary); font-size: 0.75rem;">1 day ago</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px; border-left: 4px solid var(--accent-primary);">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">⭐</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Digital Solutions</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">mike@digitalsol.com • $2,800 potential</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Last contact</div>
                        <div style="color: var(--text-primary); font-size: 0.75rem;">3 days ago</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Activity</h3>
                <a href="#" class="btn btn-secondary btn-sm">View Timeline</a>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📞</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Call scheduled</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Follow-up call with Acme Corp for tomorrow at 2:00 PM</div>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">30 min ago</div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">✉️</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Email sent</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Proposal sent to TechStart Inc for web development project</div>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">2 hours ago</div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🤝</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Deal closed</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Successfully closed deal with Digital Solutions for $2,800</div>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">4 hours ago</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Contacts</h3>
            <div class="flex gap-2">
                <input type="text" placeholder="Search contacts..." class="form-input" style="padding: 0.5rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
                <button class="btn btn-secondary btn-sm">Filter</button>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Name</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Company</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Email</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Status</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Value</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">John Smith</td>
                        <td style="padding: 1rem; color: var(--text-primary);">Acme Corp</td>
                        <td style="padding: 1rem; color: var(--text-primary);">john@acme.com</td>
                        <td style="padding: 1rem;">
                            <span style="background: var(--accent-error); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Hot</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">$5,000</td>
                        <td style="padding: 1rem;">
                            <div class="flex gap-2">
                                <button class="btn btn-sm btn-secondary">View</button>
                                <button class="btn btn-sm btn-primary">Contact</button>
                            </div>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Sarah Johnson</td>
                        <td style="padding: 1rem; color: var(--text-primary);">TechStart Inc</td>
                        <td style="padding: 1rem; color: var(--text-primary);">sarah@techstart.com</td>
                        <td style="padding: 1rem;">
                            <span style="background: var(--accent-warning); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Warm</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">$3,200</td>
                        <td style="padding: 1rem;">
                            <div class="flex gap-2">
                                <button class="btn btn-sm btn-secondary">View</button>
                                <button class="btn btn-sm btn-primary">Contact</button>
                            </div>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Mike Chen</td>
                        <td style="padding: 1rem; color: var(--text-primary);">Digital Solutions</td>
                        <td style="padding: 1rem; color: var(--text-primary);">mike@digitalsol.com</td>
                        <td style="padding: 1rem;">
                            <span style="background: var(--accent-secondary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Closed</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">$2,800</td>
                        <td style="padding: 1rem;">
                            <div class="flex gap-2">
                                <button class="btn btn-sm btn-secondary">View</button>
                                <button class="btn btn-sm btn-primary">Contact</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection