<x-layouts.dashboard title="Sites - Mewayz" page-title="Sites">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Your Sites</h1>
                <p class="text-secondary-text">Manage and create beautiful sites with AI assistance</p>
            </div>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New Site
            </button>
        </div>

        <!-- Sites Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Site Card 1 -->
            <div class="card">
                <div class="mb-4">
                    <div class="w-full h-32 bg-gradient-to-br from-info/20 to-success/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Personal Portfolio</h3>
                    <p class="text-secondary-text text-sm mb-4">Showcase your work and skills with a professional portfolio site.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-success">Active</span>
                        <span class="text-secondary-text">2,341 views</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-secondary text-sm flex-1">Edit</button>
                    <button class="btn btn-primary text-sm flex-1">View</button>
                </div>
            </div>

            <!-- Site Card 2 -->
            <div class="card">
                <div class="mb-4">
                    <div class="w-full h-32 bg-gradient-to-br from-warning/20 to-error/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">E-commerce Store</h3>
                    <p class="text-secondary-text text-sm mb-4">Sell your products online with integrated payment processing.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-success">Active</span>
                        <span class="text-secondary-text">5,892 views</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-secondary text-sm flex-1">Edit</button>
                    <button class="btn btn-primary text-sm flex-1">View</button>
                </div>
            </div>

            <!-- Site Card 3 -->
            <div class="card">
                <div class="mb-4">
                    <div class="w-full h-32 bg-gradient-to-br from-success/20 to-info/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Course Landing</h3>
                    <p class="text-secondary-text text-sm mb-4">Promote and sell your online courses with a dedicated landing page.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-warning">Draft</span>
                        <span class="text-secondary-text">0 views</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-secondary text-sm flex-1">Edit</button>
                    <button class="btn btn-primary text-sm flex-1">Preview</button>
                </div>
            </div>

            <!-- Create New Site Card -->
            <div class="card border-dashed border-2 border-border-color hover:border-info/50 transition-colors">
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-16 h-16 bg-info/10 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Create New Site</h3>
                    <p class="text-secondary-text text-sm mb-4">Start building your next site with AI assistance</p>
                    <button class="btn btn-primary">Get Started</button>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-12">
            <h2 class="text-xl font-semibold text-primary-text mb-6">Recent Activity</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-card-bg rounded-lg border border-border-color">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-success/10 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-primary-text font-medium">Site "Personal Portfolio" updated</p>
                            <p class="text-secondary-text text-sm">Added new project showcase section</p>
                        </div>
                    </div>
                    <span class="text-secondary-text text-sm">2 hours ago</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-card-bg rounded-lg border border-border-color">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-info/10 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-primary-text font-medium">New site "Course Landing" created</p>
                            <p class="text-secondary-text text-sm">Created from AI-generated template</p>
                        </div>
                    </div>
                    <span class="text-secondary-text text-sm">1 day ago</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-card-bg rounded-lg border border-border-color">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-warning/10 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-primary-text font-medium">Site performance report</p>
                            <p class="text-secondary-text text-sm">E-commerce Store reached 5,000 monthly views</p>
                        </div>
                    </div>
                    <span class="text-secondary-text text-sm">3 days ago</span>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>