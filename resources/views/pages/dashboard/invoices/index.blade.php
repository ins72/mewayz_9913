@extends('layouts.dashboard')

@section('title', 'Invoices')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Invoice Management</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Settings</button>
                <button class="btn btn-primary btn-sm">Create Invoice</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Create, send, and manage invoices for your clients and customers.
            </p>
        </div>
    </div>

    <!-- Invoice Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">$24,580</div>
            <div class="stat-label">Total Invoiced</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +18% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">$18,240</div>
            <div class="stat-label">Paid</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +15% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">$6,340</div>
            <div class="stat-label">Pending</div>
            <div style="font-size: 0.75rem; color: var(--accent-warning); margin-top: 0.5rem;">
                3 invoices overdue
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">74.2%</div>
            <div class="stat-label">Payment Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2.1% from last month
            </div>
        </div>
    </div>

    <!-- Invoice Filters -->
    <div class="card">
        <div class="flex justify-between items-center">
            <div class="flex gap-2">
                <button class="btn btn-sm btn-primary">All Invoices</button>
                <button class="btn btn-sm btn-secondary">Paid</button>
                <button class="btn btn-sm btn-secondary">Pending</button>
                <button class="btn btn-sm btn-secondary">Overdue</button>
                <button class="btn btn-sm btn-secondary">Draft</button>
            </div>
            <div class="flex gap-2">
                <input type="text" placeholder="Search invoices..." class="form-input" style="padding: 0.5rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
                <button class="btn btn-secondary btn-sm">Export</button>
            </div>
        </div>
    </div>

    <!-- Invoice List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Invoices</h3>
            <a href="#" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Invoice</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Client</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Amount</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Status</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Due Date</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">
                            <div style="font-weight: 500;">#INV-001</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Web Development</div>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">
                            <div style="font-weight: 500;">Acme Corp</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">john@acme.com</div>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">$2,500.00</td>
                        <td style="padding: 1rem;">
                            <span style="background: var(--accent-secondary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Paid</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">Jan 15, 2024</td>
                        <td style="padding: 1rem;">
                            <div class="flex gap-2">
                                <button class="btn btn-sm btn-secondary">View</button>
                                <button class="btn btn-sm btn-primary">Download</button>
                            </div>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">
                            <div style="font-weight: 500;">#INV-002</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Marketing Consultation</div>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">
                            <div style="font-weight: 500;">TechStart Inc</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">sarah@techstart.com</div>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">$1,200.00</td>
                        <td style="padding: 1rem;">
                            <span style="background: var(--accent-warning); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Pending</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">Jan 20, 2024</td>
                        <td style="padding: 1rem;">
                            <div class="flex gap-2">
                                <button class="btn btn-sm btn-secondary">View</button>
                                <button class="btn btn-sm btn-primary">Send</button>
                            </div>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">
                            <div style="font-weight: 500;">#INV-003</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Brand Identity</div>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">
                            <div style="font-weight: 500;">Digital Solutions</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">mike@digitalsol.com</div>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">$3,800.00</td>
                        <td style="padding: 1rem;">
                            <span style="background: var(--accent-error); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Overdue</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary);">Jan 10, 2024</td>
                        <td style="padding: 1rem;">
                            <div class="flex gap-2">
                                <button class="btn btn-sm btn-secondary">View</button>
                                <button class="btn btn-sm btn-error">Remind</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions & Templates -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📄</div>
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">New Invoice</div>
                </button>
                
                <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💰</div>
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Record Payment</div>
                </button>
                
                <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📊</div>
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">View Reports</div>
                </button>
                
                <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">⚙️</div>
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Settings</div>
                </button>
            </div>
        </div>

        <!-- Invoice Templates -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Invoice Templates</h3>
                <button class="btn btn-primary btn-sm">Create Template</button>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📄</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Standard Invoice</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Basic invoice template for services</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Use</button>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🎨</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Creative Services</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Template for design and creative work</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Use</button>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">💻</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Tech Consulting</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Template for hourly consulting work</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Use</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Payment Methods</h3>
            <button class="btn btn-secondary btn-sm">Manage Payment Methods</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💳</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Credit Card</div>
                <div style="color: var(--accent-secondary); font-size: 0.75rem;">Enabled</div>
            </div>
            
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🏦</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Bank Transfer</div>
                <div style="color: var(--accent-secondary); font-size: 0.75rem;">Enabled</div>
            </div>
            
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📱</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">PayPal</div>
                <div style="color: var(--accent-secondary); font-size: 0.75rem;">Enabled</div>
            </div>
            
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💰</div>
                <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Cryptocurrency</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">Disabled</div>
            </div>
        </div>
    </div>
</div>
@endsection