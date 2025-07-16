<x-layouts.dashboard title="CRM & Leads - Mewayz" page-title="CRM & Leads">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">CRM & Lead Management</h1>
                <p class="text-secondary-text">Manage your leads, customers, and sales pipeline</p>
            </div>
            <div class="flex gap-3">
                <button class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Import Leads
                </button>
                <button class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Lead
                </button>
            </div>
        </div>

        <!-- CRM Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Leads</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">1,247</div>
                <div class="text-sm text-success">+18.2% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Active Customers</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">892</div>
                <div class="text-sm text-success">+24.7% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Conversion Rate</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">28.5%</div>
                <div class="text-sm text-warning">+4.1% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Revenue</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">$47,592</div>
                <div class="text-sm text-success">+31.4% from last month</div>
            </div>
        </div>

        <!-- Sales Pipeline -->
        <div class="card mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Sales Pipeline</h2>
                <button class="btn btn-secondary">Manage Pipeline</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-app-bg rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-medium text-primary-text">New Leads</h3>
                        <span class="text-xs bg-info/10 text-info px-2 py-1 rounded">42</span>
                    </div>
                    <div class="space-y-2">
                        <div class="bg-card-bg p-3 rounded border-l-4 border-info">
                            <div class="text-sm font-medium text-primary-text">Sarah Johnson</div>
                            <div class="text-xs text-secondary-text">Web Development Course</div>
                            <div class="text-xs text-success">$299</div>
                        </div>
                        <div class="bg-card-bg p-3 rounded border-l-4 border-info">
                            <div class="text-sm font-medium text-primary-text">Mike Chen</div>
                            <div class="text-xs text-secondary-text">UI/UX Design Kit</div>
                            <div class="text-xs text-success">$89</div>
                        </div>
                    </div>
                </div>

                <div class="bg-app-bg rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-medium text-primary-text">Qualified</h3>
                        <span class="text-xs bg-warning/10 text-warning px-2 py-1 rounded">28</span>
                    </div>
                    <div class="space-y-2">
                        <div class="bg-card-bg p-3 rounded border-l-4 border-warning">
                            <div class="text-sm font-medium text-primary-text">Alex Rodriguez</div>
                            <div class="text-xs text-secondary-text">Marketing Package</div>
                            <div class="text-xs text-success">$499</div>
                        </div>
                        <div class="bg-card-bg p-3 rounded border-l-4 border-warning">
                            <div class="text-sm font-medium text-primary-text">Emma Wilson</div>
                            <div class="text-xs text-secondary-text">Business Consulting</div>
                            <div class="text-xs text-success">$1,299</div>
                        </div>
                    </div>
                </div>

                <div class="bg-app-bg rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-medium text-primary-text">Proposal</h3>
                        <span class="text-xs bg-success/10 text-success px-2 py-1 rounded">15</span>
                    </div>
                    <div class="space-y-2">
                        <div class="bg-card-bg p-3 rounded border-l-4 border-success">
                            <div class="text-sm font-medium text-primary-text">David Lee</div>
                            <div class="text-xs text-secondary-text">Full Course Bundle</div>
                            <div class="text-xs text-success">$899</div>
                        </div>
                    </div>
                </div>

                <div class="bg-app-bg rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-medium text-primary-text">Negotiation</h3>
                        <span class="text-xs bg-error/10 text-error px-2 py-1 rounded">8</span>
                    </div>
                    <div class="space-y-2">
                        <div class="bg-card-bg p-3 rounded border-l-4 border-error">
                            <div class="text-sm font-medium text-primary-text">Lisa Parker</div>
                            <div class="text-xs text-secondary-text">Enterprise Package</div>
                            <div class="text-xs text-success">$2,499</div>
                        </div>
                    </div>
                </div>

                <div class="bg-app-bg rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-medium text-primary-text">Closed Won</h3>
                        <span class="text-xs bg-success/10 text-success px-2 py-1 rounded">23</span>
                    </div>
                    <div class="space-y-2">
                        <div class="bg-card-bg p-3 rounded border-l-4 border-success">
                            <div class="text-sm font-medium text-primary-text">John Smith</div>
                            <div class="text-xs text-secondary-text">Advanced Course</div>
                            <div class="text-xs text-success">$599</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Leads -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Recent Leads</h2>
                <div class="flex items-center gap-3">
                    <input type="text" placeholder="Search leads..." class="form-input w-64">
                    <select class="form-input">
                        <option>All Leads</option>
                        <option>New</option>
                        <option>Qualified</option>
                        <option>Customers</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border-color">
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Lead</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Source</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Status</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Value</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Date</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-info/20 to-success/20 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-primary-text">SJ</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-primary-text">Sarah Johnson</div>
                                        <div class="text-sm text-secondary-text">sarah.johnson@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-secondary-text">Instagram</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-info/10 text-info rounded">New</span>
                            </td>
                            <td class="py-4 px-4 text-primary-text font-medium">$299</td>
                            <td class="py-4 px-4 text-secondary-text">2 hours ago</td>
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
                        <!-- More rows... -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.dashboard>