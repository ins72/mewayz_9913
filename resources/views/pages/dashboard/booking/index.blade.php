<x-layouts.dashboard title="Booking & Appointments - Mewayz" page-title="Booking & Appointments">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Booking & Appointments</h1>
                <p class="text-secondary-text">Manage your calendar, appointments, and booking settings</p>
            </div>
            <div class="flex gap-3">
                <button class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </button>
                <button class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Service
                </button>
            </div>
        </div>

        <!-- Booking Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Bookings</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">147</div>
                <div class="text-sm text-success">+12.3% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">This Week</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">28</div>
                <div class="text-sm text-success">+8.7% from last week</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Revenue</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">$8,492</div>
                <div class="text-sm text-warning">+15.4% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Cancellation Rate</h3>
                    <svg class="w-5 h-5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">3.2%</div>
                <div class="text-sm text-success">-1.1% from last month</div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Today's Schedule</h3>
                    <button class="btn btn-secondary text-sm">View Calendar</button>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-success rounded-full mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Business Consultation</div>
                                <div class="text-sm text-secondary-text">10:00 AM - 11:00 AM</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">Sarah Johnson</div>
                            <div class="text-sm text-secondary-text">$150</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-warning rounded-full mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Website Review</div>
                                <div class="text-sm text-secondary-text">2:00 PM - 3:00 PM</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">Mike Chen</div>
                            <div class="text-sm text-secondary-text">$100</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-info rounded-full mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Strategy Session</div>
                                <div class="text-sm text-secondary-text">4:00 PM - 5:00 PM</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-text font-medium">Emma Wilson</div>
                            <div class="text-sm text-secondary-text">$200</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Services</h3>
                    <button class="btn btn-secondary text-sm">Manage Services</button>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div>
                            <div class="text-primary-text font-medium">Business Consultation</div>
                            <div class="text-sm text-secondary-text">60 minutes • $150</div>
                        </div>
                        <div class="text-right">
                            <div class="text-success text-sm">28 bookings</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div>
                            <div class="text-primary-text font-medium">Website Review</div>
                            <div class="text-sm text-secondary-text">45 minutes • $100</div>
                        </div>
                        <div class="text-right">
                            <div class="text-success text-sm">15 bookings</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div>
                            <div class="text-primary-text font-medium">Strategy Session</div>
                            <div class="text-sm text-secondary-text">90 minutes • $200</div>
                        </div>
                        <div class="text-right">
                            <div class="text-success text-sm">12 bookings</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Recent Bookings</h2>
                <div class="flex items-center gap-3">
                    <select class="form-input">
                        <option>All Bookings</option>
                        <option>Confirmed</option>
                        <option>Pending</option>
                        <option>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border-color">
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Client</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Service</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Date & Time</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Duration</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Price</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Status</th>
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
                            <td class="py-4 px-4 text-secondary-text">Business Consultation</td>
                            <td class="py-4 px-4 text-secondary-text">Jan 15, 2024 at 10:00 AM</td>
                            <td class="py-4 px-4 text-secondary-text">60 minutes</td>
                            <td class="py-4 px-4 text-primary-text font-medium">$150</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Confirmed</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-info hover:text-blue-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button class="text-error hover:text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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