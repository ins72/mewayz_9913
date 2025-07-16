<x-layouts.dashboard title="Analytics - Mewayz" page-title="Analytics">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Analytics Dashboard</h1>
                <p class="text-secondary-text">Track your performance and gain insights into your business</p>
            </div>
            <div class="flex gap-3">
                <button class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Export Report
                </button>
                <button class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Custom Report
                </button>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Visitors</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">45,892</div>
                <div class="text-sm text-success">+12.3% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Page Views</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">128,493</div>
                <div class="text-sm text-success">+8.7% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Conversion Rate</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">3.7%</div>
                <div class="text-sm text-warning">+0.4% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Revenue</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">$24,897</div>
                <div class="text-sm text-success">+18.2% from last month</div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Traffic Chart -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Traffic Overview</h3>
                    <select class="form-input text-sm">
                        <option>Last 30 days</option>
                        <option>Last 90 days</option>
                        <option>Last year</option>
                    </select>
                </div>
                <div class="h-64 bg-app-bg rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-secondary-text mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-secondary-text">Chart will be displayed here</p>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Revenue Trends</h3>
                    <select class="form-input text-sm">
                        <option>Last 30 days</option>
                        <option>Last 90 days</option>
                        <option>Last year</option>
                    </select>
                </div>
                <div class="h-64 bg-app-bg rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-secondary-text mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <p class="text-secondary-text">Chart will be displayed here</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Top Pages -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Top Pages</h3>
                    <button class="btn btn-secondary text-sm">View All</button>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-primary-text font-medium">/home</div>
                            <div class="text-sm text-secondary-text">Homepage</div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">8,429</div>
                            <div class="text-sm text-success">+12.3%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-primary-text font-medium">/courses</div>
                            <div class="text-sm text-secondary-text">Courses page</div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">3,247</div>
                            <div class="text-sm text-info">+8.7%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-primary-text font-medium">/about</div>
                            <div class="text-sm text-secondary-text">About page</div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">1,892</div>
                            <div class="text-sm text-warning">+4.2%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Traffic Sources -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Traffic Sources</h3>
                    <button class="btn btn-secondary text-sm">View All</button>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-success rounded-full mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Organic Search</div>
                                <div class="text-sm text-secondary-text">Google, Bing</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">42%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-info rounded-full mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Direct</div>
                                <div class="text-sm text-secondary-text">Direct visits</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">28%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-warning rounded-full mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Social Media</div>
                                <div class="text-sm text-secondary-text">Facebook, Instagram</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">18%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Device Breakdown -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Device Types</h3>
                    <button class="btn btn-secondary text-sm">View All</button>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-success rounded-full mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Desktop</div>
                                <div class="text-sm text-secondary-text">Laptop & desktop</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">52%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-info rounded-full mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Mobile</div>
                                <div class="text-sm text-secondary-text">Smartphones</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">35%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-warning rounded-full mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Tablet</div>
                                <div class="text-sm text-secondary-text">iPad, tablets</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">13%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Recent Activity</h2>
                <button class="btn btn-secondary">View All Activity</button>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-app-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-success/10 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-primary-text font-medium">New purchase completed</div>
                            <div class="text-sm text-secondary-text">Web Development Course - $299</div>
                        </div>
                    </div>
                    <div class="text-sm text-secondary-text">2 minutes ago</div>
                </div>

                <div class="flex items-center justify-between p-4 bg-app-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-info/10 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-primary-text font-medium">New subscriber joined</div>
                            <div class="text-sm text-secondary-text">sarah.johnson@email.com</div>
                        </div>
                    </div>
                    <div class="text-sm text-secondary-text">15 minutes ago</div>
                </div>

                <div class="flex items-center justify-between p-4 bg-app-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-warning/10 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-primary-text font-medium">High traffic spike detected</div>
                            <div class="text-sm text-secondary-text">Homepage - 2,347 visitors in last hour</div>
                        </div>
                    </div>
                    <div class="text-sm text-secondary-text">1 hour ago</div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>