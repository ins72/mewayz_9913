<x-layouts.dashboard title="Audience - Mewayz" page-title="Audience">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Audience Management</h1>
                <p class="text-secondary-text">Connect with your audience and grow your community</p>
            </div>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Contact
            </button>
        </div>

        <!-- Audience Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Subscribers</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">8,429</div>
                <div class="text-sm text-success">+15.2% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Active Users</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">2,341</div>
                <div class="text-sm text-info">+7.8% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Email Opens</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">24.3%</div>
                <div class="text-sm text-warning">-1.2% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Engagement</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">18.7%</div>
                <div class="text-sm text-success">+3.4% from last month</div>
            </div>
        </div>

        <!-- Audience Segments -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <h3 class="text-lg font-semibold text-primary-text mb-4">Audience Segments</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-success rounded-full mr-3"></div>
                            <span class="text-primary-text">Active Subscribers</span>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">6,234</div>
                            <div class="text-sm text-secondary-text">74%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-warning rounded-full mr-3"></div>
                            <span class="text-primary-text">Inactive Subscribers</span>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">1,892</div>
                            <div class="text-sm text-secondary-text">22%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-error rounded-full mr-3"></div>
                            <span class="text-primary-text">Unsubscribed</span>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">303</div>
                            <div class="text-sm text-secondary-text">4%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="text-lg font-semibold text-primary-text mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-success/10 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm text-primary-text">25 new subscribers</div>
                                <div class="text-xs text-secondary-text">Today</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-info/10 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm text-primary-text">Email campaign sent</div>
                                <div class="text-xs text-secondary-text">2 hours ago</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-warning/10 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm text-primary-text">Analytics report ready</div>
                                <div class="text-xs text-secondary-text">5 hours ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscribers Table -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Recent Subscribers</h2>
                <div class="flex items-center gap-3">
                    <input type="text" placeholder="Search subscribers..." class="form-input w-64">
                    <select class="form-input">
                        <option>All Segments</option>
                        <option>Active</option>
                        <option>Inactive</option>
                        <option>VIP</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border-color">
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Subscriber</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Email</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Segment</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Joined</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Status</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-info/20 to-success/20 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-primary-text">JD</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">John Doe</div>
                                        <div class="text-sm text-secondary-text">Web Developer</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-secondary-text">john.doe@example.com</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Active</span>
                            </td>
                            <td class="py-4 px-4 text-secondary-text">2 days ago</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Subscribed</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-info hover:text-blue-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-secondary-text hover:text-primary-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-warning/20 to-error/20 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-primary-text">SM</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">Sarah Miller</div>
                                        <div class="text-sm text-secondary-text">Designer</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-secondary-text">sarah.miller@example.com</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-warning/10 text-warning rounded">VIP</span>
                            </td>
                            <td class="py-4 px-4 text-secondary-text">1 week ago</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Subscribed</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-info hover:text-blue-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-secondary-text hover:text-primary-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-success/20 to-info/20 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-primary-text">MB</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">Mike Brown</div>
                                        <div class="text-sm text-secondary-text">Entrepreneur</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-secondary-text">mike.brown@example.com</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Active</span>
                            </td>
                            <td class="py-4 px-4 text-secondary-text">2 weeks ago</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-error/10 text-error rounded">Unsubscribed</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-info hover:text-blue-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-secondary-text hover:text-primary-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.dashboard>