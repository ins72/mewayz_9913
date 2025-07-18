<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="demo-token">
    <title>Mewayz - All-in-One Business Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased" x-data="{ darkMode: false }">
    
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40 backdrop-blur-sm bg-white/95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Mewayz</span>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#" class="text-blue-600 font-medium">Dashboard</a>
                    <a href="#" class="text-gray-700 hover:text-gray-900 font-medium">Social Media</a>
                    <a href="#" class="text-gray-700 hover:text-gray-900 font-medium">Link in Bio</a>
                    <a href="#" class="text-gray-700 hover:text-gray-900 font-medium">E-commerce</a>
                    <a href="#" class="text-gray-700 hover:text-gray-900 font-medium">Courses</a>
                    <a href="#" class="text-gray-700 hover:text-gray-900 font-medium">Analytics</a>
                </div>
                
                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    <button class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Upgrade Plan
                    </button>
                    
                    <div class="flex items-center space-x-2">
                        <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name=John+Doe&background=007AFF&color=fff" alt="Profile">
                        <span class="text-sm font-medium text-gray-900">John Doe</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            Welcome back, John! 👋
                        </h1>
                        <p class="text-gray-600 mt-2">
                            Here's what's happening with your business today.
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <a href="#" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Upgrade Plan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Workspace Selector -->
            <div class="workspace-selector mb-8">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-gray-900">Current Workspace</h3>
                    <button class="btn-sm btn-outline-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Workspace
                    </button>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-semibold text-sm">M</span>
                    </div>
                    <div class="flex-1">
                        <select class="form-select w-full">
                            <option>My Business</option>
                            <option>Personal Brand</option>
                            <option>Agency Client</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-3 text-xs text-gray-500">
                    Digital marketing and course creation workspace
                </div>
                
                <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">12</div>
                        <div class="text-xs text-gray-500">Active Features</div>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-900">156</div>
                        <div class="text-xs text-gray-500">Total Posts</div>
                    </div>
                    <div>
                        <div class="text-lg font-semibold text-gray-900">$2,847</div>
                        <div class="text-xs text-gray-500">Monthly Revenue</div>
                    </div>
                </div>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="metric-card hover-lift">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="metric-change positive">
                                <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
                                </svg>
                                +12.5%
                            </div>
                        </div>
                    </div>
                    <div class="metric-value">$12,345</div>
                    <div class="metric-label">Total Revenue</div>
                </div>

                <div class="metric-card hover-lift">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-gradient-to-br from-green-500 to-teal-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="metric-change positive">
                                <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
                                </svg>
                                +8.2%
                            </div>
                        </div>
                    </div>
                    <div class="metric-value">2,847</div>
                    <div class="metric-label">Active Users</div>
                </div>

                <div class="metric-card hover-lift">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="metric-change positive">
                                <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
                                </svg>
                                +23.1%
                            </div>
                        </div>
                    </div>
                    <div class="metric-value">156</div>
                    <div class="metric-label">Social Posts</div>
                </div>

                <div class="metric-card hover-lift">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-gradient-to-br from-orange-500 to-amber-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="metric-change negative">
                                <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"></path>
                                </svg>
                                -3.2%
                            </div>
                        </div>
                    </div>
                    <div class="metric-value">89</div>
                    <div class="metric-label">Course Sales</div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Quick Actions -->
                <div class="lg:col-span-2">
                    <!-- Quick Actions -->
                    <div class="card mb-8">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
                                <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    Customize
                                </button>
                            </div>
                            
                            <div class="quick-actions-grid">
                                <div class="quick-action-item group">
                                    <div class="quick-action-icon bg-blue-500 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="quick-action-title">Schedule Post</div>
                                    <div class="quick-action-description">Create and schedule social media posts</div>
                                </div>

                                <div class="quick-action-item group">
                                    <div class="quick-action-icon bg-green-500 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                    <div class="quick-action-title">Add Product</div>
                                    <div class="quick-action-description">Add new product to your store</div>
                                </div>

                                <div class="quick-action-item group">
                                    <div class="quick-action-icon bg-purple-500 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                        </svg>
                                    </div>
                                    <div class="quick-action-title">Create Course</div>
                                    <div class="quick-action-description">Start building your online course</div>
                                </div>

                                <div class="quick-action-item group">
                                    <div class="quick-action-icon bg-pink-500 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="quick-action-title">Email Campaign</div>
                                    <div class="quick-action-description">Send targeted email campaigns</div>
                                </div>

                                <div class="quick-action-item group">
                                    <div class="quick-action-icon bg-orange-500 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                    </div>
                                    <div class="quick-action-title">Bio Link</div>
                                    <div class="quick-action-description">Create professional bio pages</div>
                                </div>

                                <div class="quick-action-item group">
                                    <div class="quick-action-icon bg-teal-500 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div class="quick-action-title">Analytics</div>
                                    <div class="quick-action-description">View detailed performance analytics</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Activity & Notifications -->
                <div class="space-y-6">
                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold text-gray-900">Recent Activity</h2>
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    View All
                                </a>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-start space-x-4 p-4 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">Instagram post published</p>
                                            <span class="text-xs text-gray-500 whitespace-nowrap">2 hours ago</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Your post "Summer Sale is here!" was successfully published</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 p-4 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">New course enrollment</p>
                                            <span class="text-xs text-gray-500 whitespace-nowrap">4 hours ago</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">John Doe enrolled in "Digital Marketing Basics"</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 p-4 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">Payment received</p>
                                            <span class="text-xs text-gray-500 whitespace-nowrap">6 hours ago</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">You received $99.00 for course purchase</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="card">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Events</h3>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Instagram post scheduled</p>
                                        <p class="text-xs text-gray-500">Tomorrow at 2:00 PM</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Email campaign launch</p>
                                        <p class="text-xs text-gray-500">Monday at 9:00 AM</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3 p-3 bg-purple-50 rounded-lg">
                                    <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Course webinar</p>
                                        <p class="text-xs text-gray-500">Thursday at 7:00 PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>