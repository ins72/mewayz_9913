<x-layouts.dashboard title="Dashboard - Mewayz" page-title="Dashboard">
    <div class="fade-in">
        <!-- Dashboard Stats -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">Total Revenue</h3>
                    <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="dashboard-card-value">$12,345</div>
                <div class="dashboard-card-change positive">+12.5% from last month</div>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">Active Sites</h3>
                    <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                    </svg>
                </div>
                <div class="dashboard-card-value">24</div>
                <div class="dashboard-card-change positive">+3 new this week</div>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">Total Audience</h3>
                    <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="dashboard-card-value">8,429</div>
                <div class="dashboard-card-change positive">+15.2% growth</div>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">Course Sales</h3>
                    <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="dashboard-card-value">156</div>
                <div class="dashboard-card-change positive">+8.3% this month</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <a href="{{ route('dashboard-sites-index') }}" class="card hover:bg-hover-bg transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-primary-text mb-2">Create New Site</h3>
                        <p class="text-secondary-text">Build beautiful sites with AI assistance</p>
                    </div>
                    <div class="text-info">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('dashboard-store-index') }}" class="card hover:bg-hover-bg transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-primary-text mb-2">Manage Store</h3>
                        <p class="text-secondary-text">Add products and manage inventory</p>
                    </div>
                    <div class="text-info">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('dashboard-audience-index') }}" class="card hover:bg-hover-bg transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-primary-text mb-2">Audience Insights</h3>
                        <p class="text-secondary-text">Track and engage your audience</p>
                    </div>
                    <div class="text-info">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="dashboard-table">
            <div class="dashboard-table-header">
                <h3 class="dashboard-table-title">Recent Activity</h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>New site created: "Portfolio 2024"</td>
                            <td>Site</td>
                            <td>2 hours ago</td>
                            <td><span class="text-success">Active</span></td>
                        </tr>
                        <tr>
                            <td>Course purchased: "Web Development Basics"</td>
                            <td>Course</td>
                            <td>5 hours ago</td>
                            <td><span class="text-success">Completed</span></td>
                        </tr>
                        <tr>
                            <td>Product added: "Digital Marketing Guide"</td>
                            <td>Product</td>
                            <td>1 day ago</td>
                            <td><span class="text-info">Published</span></td>
                        </tr>
                        <tr>
                            <td>Audience milestone: 1000 subscribers</td>
                            <td>Audience</td>
                            <td>2 days ago</td>
                            <td><span class="text-success">Achieved</span></td>
                        </tr>
                        <tr>
                            <td>Payment received: $299.00</td>
                            <td>Payment</td>
                            <td>3 days ago</td>
                            <td><span class="text-success">Processed</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.dashboard>