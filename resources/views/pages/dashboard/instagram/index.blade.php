@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Instagram Management</h1>
            <p class="text-secondary-text">Search, analyze, and manage Instagram accounts</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Results
            </button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Search
            </button>
        </div>
    </div>

    <!-- Search Section -->
    <div class="dashboard-card mb-8">
        <h2 class="text-xl font-semibold text-primary-text mb-6">Instagram Account Search</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Basic Filters -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-primary-text">Basic Filters</h3>
                
                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Username / Keywords</label>
                    <input type="text" class="form-input w-full" placeholder="Enter username or keywords">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Location</label>
                    <input type="text" class="form-input w-full" placeholder="City, Country">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Account Type</label>
                    <select class="form-input w-full">
                        <option value="">All Types</option>
                        <option value="personal">Personal</option>
                        <option value="business">Business</option>
                        <option value="creator">Creator</option>
                    </select>
                </div>
            </div>

            <!-- Follower Filters -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-primary-text">Followers</h3>
                
                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Min Followers</label>
                    <input type="number" class="form-input w-full" placeholder="1000">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Max Followers</label>
                    <input type="number" class="form-input w-full" placeholder="100000">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Engagement Rate</label>
                    <select class="form-input w-full">
                        <option value="">Any</option>
                        <option value="low">Low (0-2%)</option>
                        <option value="medium">Medium (2-5%)</option>
                        <option value="high">High (5%+)</option>
                    </select>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-primary-text">Advanced</h3>
                
                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Hashtags</label>
                    <input type="text" class="form-input w-full" placeholder="#fashion #lifestyle">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Bio Keywords</label>
                    <input type="text" class="form-input w-full" placeholder="entrepreneur, blogger">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Language</label>
                    <select class="form-input w-full">
                        <option value="">Any Language</option>
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                        <option value="de">German</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="flex justify-between items-center mt-6">
            <button class="btn btn-secondary">Reset Filters</button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Search Accounts
            </button>
        </div>
    </div>

    <!-- Results Section -->
    <div class="dashboard-card mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-primary-text">Search Results</h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-secondary-text">Found 1,234 accounts</span>
                <select class="form-input">
                    <option value="followers">Sort by Followers</option>
                    <option value="engagement">Sort by Engagement</option>
                    <option value="relevance">Sort by Relevance</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Account Card 1 -->
            <div class="border border-border-color rounded-lg p-6 hover:border-info transition-colors">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                        JD
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="font-semibold text-primary-text">@johndoe</h3>
                        <p class="text-sm text-secondary-text">John Doe</p>
                        <div class="flex items-center mt-1">
                            <svg class="w-4 h-4 text-blue-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-xs text-secondary-text">Verified</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-lg font-bold text-primary-text">156K</div>
                        <div class="text-xs text-secondary-text">Followers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-primary-text">1.2K</div>
                        <div class="text-xs text-secondary-text">Following</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-primary-text">3.4%</div>
                        <div class="text-xs text-secondary-text">Engagement</div>
                    </div>
                </div>
                
                <p class="text-sm text-secondary-text mb-4">
                    🎯 Digital Marketing Expert | 📱 Social Media Strategist | 🚀 Entrepreneur
                </p>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs bg-green-500/20 text-green-600 px-2 py-1 rounded">Business</span>
                        <span class="text-xs bg-blue-500/20 text-blue-600 px-2 py-1 rounded">Active</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="btn btn-sm btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <button class="btn btn-sm btn-primary">Contact</button>
                    </div>
                </div>
            </div>

            <!-- Account Card 2 -->
            <div class="border border-border-color rounded-lg p-6 hover:border-info transition-colors">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                        SA
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="font-semibold text-primary-text">@sarahmktg</h3>
                        <p class="text-sm text-secondary-text">Sarah Anderson</p>
                        <div class="flex items-center mt-1">
                            <svg class="w-4 h-4 text-orange-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-xs text-secondary-text">Creator</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-lg font-bold text-primary-text">89K</div>
                        <div class="text-xs text-secondary-text">Followers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-primary-text">892</div>
                        <div class="text-xs text-secondary-text">Following</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-primary-text">5.8%</div>
                        <div class="text-xs text-secondary-text">Engagement</div>
                    </div>
                </div>
                
                <p class="text-sm text-secondary-text mb-4">
                    ✨ Content Creator | 📸 Photography | 🌟 Lifestyle Blogger
                </p>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs bg-purple-500/20 text-purple-600 px-2 py-1 rounded">Creator</span>
                        <span class="text-xs bg-green-500/20 text-green-600 px-2 py-1 rounded">Active</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="btn btn-sm btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <button class="btn btn-sm btn-primary">Contact</button>
                    </div>
                </div>
            </div>

            <!-- Account Card 3 -->
            <div class="border border-border-color rounded-lg p-6 hover:border-info transition-colors">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                        MB
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="font-semibold text-primary-text">@mikebrand</h3>
                        <p class="text-sm text-secondary-text">Mike Brand</p>
                        <div class="flex items-center mt-1">
                            <span class="text-xs text-secondary-text">Personal</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-lg font-bold text-primary-text">42K</div>
                        <div class="text-xs text-secondary-text">Followers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-primary-text">1.8K</div>
                        <div class="text-xs text-secondary-text">Following</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-primary-text">2.9%</div>
                        <div class="text-xs text-secondary-text">Engagement</div>
                    </div>
                </div>
                
                <p class="text-sm text-secondary-text mb-4">
                    💪 Fitness Coach | 🏋️ Personal Trainer | 🥗 Health Enthusiast
                </p>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs bg-gray-500/20 text-gray-600 px-2 py-1 rounded">Personal</span>
                        <span class="text-xs bg-blue-500/20 text-blue-600 px-2 py-1 rounded">Active</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="btn btn-sm btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <button class="btn btn-sm btn-primary">Contact</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-8">
            <div class="text-sm text-secondary-text">
                Showing 1-12 of 1,234 results
            </div>
            <div class="flex items-center space-x-2">
                <button class="btn btn-sm btn-secondary" disabled>Previous</button>
                <button class="btn btn-sm btn-primary">1</button>
                <button class="btn btn-sm btn-secondary">2</button>
                <button class="btn btn-sm btn-secondary">3</button>
                <span class="text-secondary-text">...</span>
                <button class="btn btn-sm btn-secondary">103</button>
                <button class="btn btn-sm btn-secondary">Next</button>
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Search Statistics -->
        <div class="dashboard-card">
            <h3 class="text-lg font-semibold text-primary-text mb-4">Search Statistics</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-secondary-text">Total Searches Today</span>
                    <span class="text-sm font-medium text-primary-text">47</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-secondary-text">Accounts Discovered</span>
                    <span class="text-sm font-medium text-primary-text">12,483</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-secondary-text">Contact Attempts</span>
                    <span class="text-sm font-medium text-primary-text">89</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-secondary-text">Success Rate</span>
                    <span class="text-sm font-medium text-success">23%</span>
                </div>
            </div>
        </div>

        <!-- Saved Searches -->
        <div class="dashboard-card">
            <h3 class="text-lg font-semibold text-primary-text mb-4">Saved Searches</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div>
                        <div class="font-medium text-primary-text">Fashion Influencers</div>
                        <div class="text-sm text-secondary-text">50K-200K followers, Fashion niche</div>
                    </div>
                    <button class="btn btn-sm btn-secondary">Run</button>
                </div>
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div>
                        <div class="font-medium text-primary-text">Tech Entrepreneurs</div>
                        <div class="text-sm text-secondary-text">Business accounts, Tech keywords</div>
                    </div>
                    <button class="btn btn-sm btn-secondary">Run</button>
                </div>
                <div class="flex items-center justify-between p-3 bg-secondary-bg rounded-lg">
                    <div>
                        <div class="font-medium text-primary-text">Fitness Coaches</div>
                        <div class="text-sm text-secondary-text">Personal trainers, Health niche</div>
                    </div>
                    <button class="btn btn-sm btn-secondary">Run</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection