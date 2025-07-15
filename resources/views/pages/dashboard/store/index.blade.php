<x-layouts.dashboard title="Store - Mewayz" page-title="Store">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Your Store</h1>
                <p class="text-secondary-text">Manage your products and track sales performance</p>
            </div>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Product
            </button>
        </div>

        <!-- Store Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Sales</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">$8,456</div>
                <div class="text-sm text-success">+12.3% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Orders</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">142</div>
                <div class="text-sm text-info">+8.1% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Products</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">28</div>
                <div class="text-sm text-warning">3 out of stock</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Conversion</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">3.2%</div>
                <div class="text-sm text-success">+0.4% from last month</div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Products</h2>
                <div class="flex items-center gap-3">
                    <input type="text" placeholder="Search products..." class="form-input w-64">
                    <select class="form-input">
                        <option>All Categories</option>
                        <option>Digital Products</option>
                        <option>Physical Products</option>
                        <option>Services</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border-color">
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Product</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Category</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Price</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Stock</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Sales</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Status</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-info/20 to-success/20 rounded-lg mr-3"></div>
                                    <div>
                                        <div class="font-medium text-primary-text">Web Development Course</div>
                                        <div class="text-sm text-secondary-text">Complete beginner's guide</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-secondary-text">Digital</td>
                            <td class="py-4 px-4 text-primary-text font-medium">$299</td>
                            <td class="py-4 px-4 text-secondary-text">Unlimited</td>
                            <td class="py-4 px-4 text-primary-text">47</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Active</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-info hover:text-blue-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-secondary-text hover:text-primary-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-error hover:text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-warning/20 to-error/20 rounded-lg mr-3"></div>
                                    <div>
                                        <div class="font-medium text-primary-text">UI/UX Design Kit</div>
                                        <div class="text-sm text-secondary-text">Premium design resources</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-secondary-text">Digital</td>
                            <td class="py-4 px-4 text-primary-text font-medium">$89</td>
                            <td class="py-4 px-4 text-secondary-text">Unlimited</td>
                            <td class="py-4 px-4 text-primary-text">23</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Active</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-info hover:text-blue-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-secondary-text hover:text-primary-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-error hover:text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-success/20 to-info/20 rounded-lg mr-3"></div>
                                    <div>
                                        <div class="font-medium text-primary-text">Marketing Templates</div>
                                        <div class="text-sm text-secondary-text">Social media templates</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-secondary-text">Digital</td>
                            <td class="py-4 px-4 text-primary-text font-medium">$49</td>
                            <td class="py-4 px-4 text-secondary-text">Unlimited</td>
                            <td class="py-4 px-4 text-primary-text">72</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-warning/10 text-warning rounded">Draft</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-info hover:text-blue-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-secondary-text hover:text-primary-text">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-error hover:text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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