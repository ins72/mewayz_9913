<x-layouts.dashboard title="Wallet - Mewayz" page-title="Wallet">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Wallet & Earnings</h1>
                <p class="text-secondary-text">Manage your earnings and payment methods</p>
            </div>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Withdraw Funds
            </button>
        </div>

        <!-- Wallet Balance -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Total Balance</h3>
                    <div class="text-4xl font-bold text-success mb-2">$12,456.78</div>
                    <p class="text-secondary-text">Available for withdrawal</p>
                </div>
            </div>

            <div class="card">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-primary-text mb-2">This Month</h3>
                    <div class="text-4xl font-bold text-info mb-2">$3,421.50</div>
                    <p class="text-secondary-text">+15.2% from last month</p>
                </div>
            </div>

            <div class="card">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Pending</h3>
                    <div class="text-4xl font-bold text-warning mb-2">$892.34</div>
                    <p class="text-secondary-text">Processing payments</p>
                </div>
            </div>
        </div>

        <!-- Earnings Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <h3 class="text-lg font-semibold text-primary-text mb-4">Revenue Sources</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-success/10 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-primary-text font-medium">Course Sales</div>
                                <div class="text-sm text-secondary-text">Online course revenue</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">$8,234.56</div>
                            <div class="text-sm text-success">+12.3%</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-info/10 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-primary-text font-medium">Product Sales</div>
                                <div class="text-sm text-secondary-text">Digital products</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">$2,891.34</div>
                            <div class="text-sm text-info">+8.7%</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-warning/10 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-primary-text font-medium">Donations</div>
                                <div class="text-sm text-secondary-text">Community support</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">$1,330.88</div>
                            <div class="text-sm text-warning">+24.1%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="text-lg font-semibold text-primary-text mb-4">Payment Methods</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-success/10 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-primary-text font-medium">Bank Account</div>
                                <div class="text-sm text-secondary-text">••••••••••••4567</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="text-success text-sm mr-2">Primary</span>
                            <button class="text-secondary-text hover:text-primary-text">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-info/10 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-primary-text font-medium">PayPal</div>
                                <div class="text-sm text-secondary-text">john.doe@example.com</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="text-secondary-text text-sm mr-2">Secondary</span>
                            <button class="text-secondary-text hover:text-primary-text">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button class="w-full flex items-center justify-center p-3 border-2 border-dashed border-border-color rounded-lg hover:border-info/50 transition-colors">
                        <svg class="w-5 h-5 text-info mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="text-info">Add Payment Method</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Recent Transactions</h2>
                <div class="flex items-center gap-3">
                    <select class="form-input">
                        <option>All Types</option>
                        <option>Deposits</option>
                        <option>Withdrawals</option>
                        <option>Refunds</option>
                    </select>
                    <select class="form-input">
                        <option>Last 30 days</option>
                        <option>Last 90 days</option>
                        <option>This Year</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border-color">
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Date</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Description</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Type</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Amount</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4 text-secondary-text">Jan 15, 2024</td>
                            <td class="py-4 px-4">
                                <div class="text-primary-text font-medium">Course Sale: Web Development</div>
                                <div class="text-sm text-secondary-text">Customer: Sarah Johnson</div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Sale</span>
                            </td>
                            <td class="py-4 px-4 text-primary-text font-medium">+$299.00</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Completed</span>
                            </td>
                        </tr>

                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4 text-secondary-text">Jan 14, 2024</td>
                            <td class="py-4 px-4">
                                <div class="text-primary-text font-medium">Withdrawal to Bank Account</div>
                                <div class="text-sm text-secondary-text">••••••••••••4567</div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-warning/10 text-warning rounded">Withdrawal</span>
                            </td>
                            <td class="py-4 px-4 text-primary-text font-medium">-$2,000.00</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-warning/10 text-warning rounded">Processing</span>
                            </td>
                        </tr>

                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4 text-secondary-text">Jan 13, 2024</td>
                            <td class="py-4 px-4">
                                <div class="text-primary-text font-medium">Product Sale: UI Kit</div>
                                <div class="text-sm text-secondary-text">Customer: Mike Brown</div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Sale</span>
                            </td>
                            <td class="py-4 px-4 text-primary-text font-medium">+$89.00</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Completed</span>
                            </td>
                        </tr>

                        <tr class="border-b border-border-color hover:bg-hover-bg">
                            <td class="py-4 px-4 text-secondary-text">Jan 12, 2024</td>
                            <td class="py-4 px-4">
                                <div class="text-primary-text font-medium">Donation</div>
                                <div class="text-sm text-secondary-text">Anonymous supporter</div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-info/10 text-info rounded">Donation</span>
                            </td>
                            <td class="py-4 px-4 text-primary-text font-medium">+$50.00</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Completed</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.dashboard>