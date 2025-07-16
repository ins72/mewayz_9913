<x-layouts.dashboard title="Instagram Management - Mewayz" page-title="Instagram Management">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Instagram Management</h1>
                <p class="text-secondary-text">Manage your Instagram accounts, schedule posts, and track analytics</p>
            </div>
            <div class="flex gap-3">
                <button class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Connect Account
                </button>
                <button class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    Create Post
                </button>
            </div>
        </div>

        <!-- Instagram Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Followers</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">24,593</div>
                <div class="text-sm text-success">+12.3% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Engagement Rate</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">4.7%</div>
                <div class="text-sm text-info">+0.8% from last month</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Posts This Month</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">28</div>
                <div class="text-sm text-warning">3 scheduled</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Reach</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text">156K</div>
                <div class="text-sm text-success">+18.4% from last month</div>
            </div>
        </div>

        <!-- Content Planning -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Content Calendar</h3>
                    <button class="btn btn-secondary text-sm">View Calendar</button>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-info/20 to-success/20 rounded-lg mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Product Launch Post</div>
                                <div class="text-sm text-secondary-text">Today, 2:00 PM</div>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium bg-success/10 text-success rounded">Scheduled</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-warning/20 to-error/20 rounded-lg mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Behind the Scenes</div>
                                <div class="text-sm text-secondary-text">Tomorrow, 10:00 AM</div>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium bg-warning/10 text-warning rounded">Draft</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-success/20 to-info/20 rounded-lg mr-3"></div>
                            <div>
                                <div class="text-primary-text font-medium">Weekly Tips</div>
                                <div class="text-sm text-secondary-text">Friday, 3:00 PM</div>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium bg-info/10 text-info rounded">Planned</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Top Hashtags</h3>
                    <button class="btn btn-secondary text-sm">Manage Tags</button>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-info">#entrepreneur</span>
                        <span class="text-secondary-text">2.3M posts</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-info">#businessowner</span>
                        <span class="text-secondary-text">1.8M posts</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-info">#startup</span>
                        <span class="text-secondary-text">1.2M posts</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-info">#marketing</span>
                        <span class="text-secondary-text">980K posts</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-info">#socialmedia</span>
                        <span class="text-secondary-text">756K posts</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Recent Posts</h2>
                <div class="flex items-center gap-3">
                    <select class="form-input">
                        <option>All Posts</option>
                        <option>Published</option>
                        <option>Scheduled</option>
                        <option>Draft</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <div class="bg-app-bg rounded-lg p-4">
                    <div class="w-full h-32 bg-gradient-to-br from-info/20 to-success/20 rounded-lg mb-3"></div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-secondary-text">2 hours ago</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-error" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span class="text-sm text-secondary-text">234</span>
                        </div>
                    </div>
                    <p class="text-sm text-primary-text">New product launch is here! 🚀</p>
                </div>

                <div class="bg-app-bg rounded-lg p-4">
                    <div class="w-full h-32 bg-gradient-to-br from-warning/20 to-error/20 rounded-lg mb-3"></div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-secondary-text">5 hours ago</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-error" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span class="text-sm text-secondary-text">189</span>
                        </div>
                    </div>
                    <p class="text-sm text-primary-text">Behind the scenes content creation 📸</p>
                </div>

                <div class="bg-app-bg rounded-lg p-4">
                    <div class="w-full h-32 bg-gradient-to-br from-success/20 to-info/20 rounded-lg mb-3"></div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-secondary-text">1 day ago</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-error" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span class="text-sm text-secondary-text">312</span>
                        </div>
                    </div>
                    <p class="text-sm text-primary-text">Weekly business tips for entrepreneurs 💼</p>
                </div>

                <div class="bg-app-bg rounded-lg p-4">
                    <div class="w-full h-32 bg-gradient-to-br from-error/20 to-warning/20 rounded-lg mb-3"></div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-secondary-text">2 days ago</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-error" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span class="text-sm text-secondary-text">445</span>
                        </div>
                    </div>
                    <p class="text-sm text-primary-text">Team collaboration made easy ✨</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>