@extends('layouts.dashboard')

@section('title', 'E-commerce Store')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">E-commerce Store</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Store Settings</button>
                <button class="btn btn-primary btn-sm">Add Product</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Manage your online store, products, inventory, and orders all in one place.
            </p>
        </div>
    </div>

    <!-- Store Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">$18,247</div>
            <div class="stat-label">Total Revenue</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +22% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">156</div>
            <div class="stat-label">Orders</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +8 from yesterday
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">24</div>
            <div class="stat-label">Products</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2 added this week
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">94.2%</div>
            <div class="stat-label">Fulfillment Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +1.2% from last month
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quick Actions</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📦</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">View Orders</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📊</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Analytics</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🏪</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Store Design</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💳</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Payment Settings</div>
            </button>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Your Products</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-primary">All Products</button>
                <button class="btn btn-sm btn-secondary">In Stock</button>
                <button class="btn btn-sm btn-secondary">Low Stock</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Product 1 -->
            <div class="card">
                <div style="height: 200px; background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                    <div style="color: white; font-size: 3rem;">📱</div>
                </div>
                <div class="space-y-3">
                    <div>
                        <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Premium Mobile App Template</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Complete mobile app UI kit with 50+ screens</p>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Digital Product</span>
                        <span style="color: var(--accent-secondary);">In Stock</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary);">$49.99</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">23 sold</div>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Edit</button>
                            <button class="btn btn-sm btn-primary">View</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="card">
                <div style="height: 200px; background: linear-gradient(135deg, var(--accent-warning) 0%, var(--accent-error) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                    <div style="color: white; font-size: 3rem;">📚</div>
                </div>
                <div class="space-y-3">
                    <div>
                        <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Marketing Strategy E-book</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Comprehensive guide to digital marketing strategies</p>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Digital Product</span>
                        <span style="color: var(--accent-secondary);">In Stock</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary);">$29.99</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">45 sold</div>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Edit</button>
                            <button class="btn btn-sm btn-primary">View</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="card">
                <div style="height: 200px; background: linear-gradient(135deg, var(--accent-secondary) 0%, var(--accent-primary) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                    <div style="color: white; font-size: 3rem;">🎨</div>
                </div>
                <div class="space-y-3">
                    <div>
                        <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Brand Identity Package</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Complete branding package with logo and guidelines</p>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Service</span>
                        <span style="color: var(--accent-warning);">Limited</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <div style="font-weight: 600; color: var(--text-primary);">$199.99</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">8 sold</div>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary">Edit</button>
                            <button class="btn btn-sm btn-primary">View</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Orders</h3>
            <a href="#" class="btn btn-secondary btn-sm">View All Orders</a>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Order ID</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Customer</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Product</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Amount</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Status</th>
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">#ORD-001</td>
                        <td style="padding: 1rem; color: var(--text-primary);">John Smith</td>
                        <td style="padding: 1rem; color: var(--text-primary);">Mobile App Template</td>
                        <td style="padding: 1rem; color: var(--text-primary);">$49.99</td>
                        <td style="padding: 1rem;">
                            <span style="background: var(--accent-secondary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Completed</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary);">2 hours ago</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">#ORD-002</td>
                        <td style="padding: 1rem; color: var(--text-primary);">Sarah Johnson</td>
                        <td style="padding: 1rem; color: var(--text-primary);">Marketing E-book</td>
                        <td style="padding: 1rem; color: var(--text-primary);">$29.99</td>
                        <td style="padding: 1rem;">
                            <span style="background: var(--accent-warning); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Processing</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary);">4 hours ago</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">#ORD-003</td>
                        <td style="padding: 1rem; color: var(--text-primary);">Mike Chen</td>
                        <td style="padding: 1rem; color: var(--text-primary);">Brand Identity Package</td>
                        <td style="padding: 1rem; color: var(--text-primary);">$199.99</td>
                        <td style="padding: 1rem;">
                            <span style="background: var(--accent-primary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">In Progress</span>
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary);">6 hours ago</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection