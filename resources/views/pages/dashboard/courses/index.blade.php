<x-layouts.dashboard title="Courses - Mewayz" page-title="Courses">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Course Management</h1>
                <p class="text-secondary-text">Create and manage your online courses</p>
            </div>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Course
            </button>
        </div>

        <!-- Course Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Courses</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">12</div>
                <div class="text-sm text-success">2 published this month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Students</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">1,429</div>
                <div class="text-sm text-success">+23% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Revenue</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">$24,156</div>
                <div class="text-sm text-warning">+15.7% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Completion Rate</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">68%</div>
                <div class="text-sm text-success">+4.2% from last month</div>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Course Card 1 -->
            <div class="card">
                <div class="mb-4">
                    <div class="w-full h-32 bg-gradient-to-br from-info/20 to-success/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Web Development Fundamentals</h3>
                    <p class="text-secondary-text text-sm mb-4">Learn the basics of HTML, CSS, and JavaScript from scratch.</p>
                    <div class="flex items-center justify-between text-sm mb-4">
                        <span class="text-success">Published</span>
                        <span class="text-secondary-text">234 students</span>
                    </div>
                    <div class="text-lg font-bold text-primary-text mb-2">$299</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-secondary text-sm flex-1">Edit</button>
                    <button class="btn btn-primary text-sm flex-1">View</button>
                </div>
            </div>

            <!-- Course Card 2 -->
            <div class="card">
                <div class="mb-4">
                    <div class="w-full h-32 bg-gradient-to-br from-warning/20 to-error/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v3M7 4H5a1 1 0 00-1 1v14a1 1 0 001 1h14a1 1 0 001-1V5a1 1 0 00-1-1h-2M7 4h10M9 9h6m-6 4h6m-6 4h6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Advanced React Patterns</h3>
                    <p class="text-secondary-text text-sm mb-4">Master advanced React concepts and design patterns.</p>
                    <div class="flex items-center justify-between text-sm mb-4">
                        <span class="text-success">Published</span>
                        <span class="text-secondary-text">89 students</span>
                    </div>
                    <div class="text-lg font-bold text-primary-text mb-2">$499</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-secondary text-sm flex-1">Edit</button>
                    <button class="btn btn-primary text-sm flex-1">View</button>
                </div>
            </div>

            <!-- Course Card 3 -->
            <div class="card">
                <div class="mb-4">
                    <div class="w-full h-32 bg-gradient-to-br from-success/20 to-info/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Digital Marketing Mastery</h3>
                    <p class="text-secondary-text text-sm mb-4">Complete guide to digital marketing strategies and tools.</p>
                    <div class="flex items-center justify-between text-sm mb-4">
                        <span class="text-warning">Draft</span>
                        <span class="text-secondary-text">0 students</span>
                    </div>
                    <div class="text-lg font-bold text-primary-text mb-2">$399</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-secondary text-sm flex-1">Edit</button>
                    <button class="btn btn-primary text-sm flex-1">Preview</button>
                </div>
            </div>

            <!-- Create New Course Card -->
            <div class="card border-dashed border-2 border-border-color hover:border-info/50 transition-colors">
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-16 h-16 bg-info/10 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Create New Course</h3>
                    <p class="text-secondary-text text-sm mb-4">Start building your next course</p>
                    <button class="btn btn-primary">Get Started</button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>