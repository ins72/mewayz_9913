<x-layouts.dashboard title="Instagram Management - Mewayz" page-title="Instagram Management">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Instagram Management</h1>
                <p class="text-secondary-text">Manage your Instagram accounts, schedule posts, and track analytics</p>
            </div>
            <div class="flex gap-3">
                <button onclick="openConnectAccountModal()" class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Connect Account
                </button>
                <button onclick="openCreatePostModal()" class="btn btn-primary">
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
                <div class="text-2xl font-bold text-primary-text" id="total-followers">-</div>
                <div class="text-sm text-success" id="followers-growth">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Engagement Rate</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="engagement-rate">-</div>
                <div class="text-sm text-info" id="engagement-growth">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Posts This Month</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="posts-count">-</div>
                <div class="text-sm text-warning" id="posts-info">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Accounts</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="accounts-count">-</div>
                <div class="text-sm text-success" id="accounts-info">Loading...</div>
            </div>
        </div>

        <!-- Content Planning -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Connected Accounts</h3>
                    <button onclick="openConnectAccountModal()" class="btn btn-secondary text-sm">Add Account</button>
                </div>
                
                <div id="accounts-list" class="space-y-3">
                    <div class="flex items-center justify-center py-8 text-secondary-text">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                        <span class="ml-2">Loading accounts...</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Top Hashtags</h3>
                    <button onclick="openHashtagResearch()" class="btn btn-secondary text-sm">Research</button>
                </div>
                
                <div id="hashtags-list" class="space-y-2">
                    <div class="flex items-center justify-center py-8 text-secondary-text">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                        <span class="ml-2">Loading hashtags...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Recent Posts</h2>
                <div class="flex items-center gap-3">
                    <select class="form-input" onchange="filterPosts(this.value)">
                        <option value="">All Posts</option>
                        <option value="published">Published</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

            <div id="posts-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <div class="flex items-center justify-center col-span-full py-8 text-secondary-text">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    <span class="ml-2">Loading posts...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Connect Account Modal -->
    <div id="connect-account-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-card-bg rounded-lg p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Connect Instagram Account</h3>
                    <button onclick="closeModal('connect-account-modal')" class="text-secondary-text hover:text-primary-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="connect-account-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-secondary-text mb-1">Username</label>
                        <input type="text" id="account-username" class="form-input w-full" placeholder="@username" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-secondary-text mb-1">Display Name</label>
                        <input type="text" id="account-display-name" class="form-input w-full" placeholder="Your Display Name" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-secondary-text mb-1">Profile Picture URL</label>
                        <input type="url" id="account-profile-picture" class="form-input w-full" placeholder="https://example.com/profile.jpg">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-secondary-text mb-1">Bio</label>
                        <textarea id="account-bio" class="form-input w-full h-20" placeholder="Account bio..."></textarea>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" onclick="closeModal('connect-account-modal')" class="btn btn-secondary flex-1">Cancel</button>
                        <button type="submit" class="btn btn-primary flex-1">Connect Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div id="create-post-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-card-bg rounded-lg p-6 w-full max-w-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Create Instagram Post</h3>
                    <button onclick="closeModal('create-post-modal')" class="text-secondary-text hover:text-primary-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="create-post-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-secondary-text mb-1">Caption</label>
                        <textarea id="post-caption" class="form-input w-full h-32" placeholder="Write your caption..." required></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-secondary-text mb-1">Media URLs</label>
                            <textarea id="post-media-urls" class="form-input w-full h-24" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg" required></textarea>
                            <p class="text-xs text-secondary-text mt-1">One URL per line</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-secondary-text mb-1">Hashtags</label>
                            <textarea id="post-hashtags" class="form-input w-full h-24" placeholder="business marketing entrepreneur"></textarea>
                            <p class="text-xs text-secondary-text mt-1">Space separated (# optional)</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-secondary-text mb-1">Post Type</label>
                            <select id="post-type" class="form-input w-full">
                                <option value="photo">Photo</option>
                                <option value="video">Video</option>
                                <option value="carousel">Carousel</option>
                                <option value="reel">Reel</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-secondary-text mb-1">Schedule (Optional)</label>
                            <input type="datetime-local" id="post-schedule" class="form-input w-full">
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" onclick="closeModal('create-post-modal')" class="btn btn-secondary flex-1">Cancel</button>
                        <button type="submit" class="btn btn-primary flex-1">Create Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hashtag Research Modal -->
    <div id="hashtag-research-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-card-bg rounded-lg p-6 w-full max-w-4xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Hashtag Research</h3>
                    <button onclick="closeModal('hashtag-research-modal')" class="text-secondary-text hover:text-primary-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="flex gap-2">
                        <input type="text" id="hashtag-search" class="form-input flex-1" placeholder="Search hashtags...">
                        <button onclick="searchHashtags()" class="btn btn-primary">Search</button>
                    </div>
                </div>
                
                <div id="hashtag-results" class="space-y-3 max-h-96 overflow-y-auto">
                    <div class="text-center text-secondary-text py-8">
                        Enter a keyword to search for hashtags
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentFilter = '';
        let analytics = null;
        let accounts = [];
        let posts = [];

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadAnalytics();
            loadAccounts();
            loadPosts();
        });

        // Modal functions
        function openConnectAccountModal() {
            document.getElementById('connect-account-modal').classList.remove('hidden');
        }

        function openCreatePostModal() {
            document.getElementById('create-post-modal').classList.remove('hidden');
        }

        function openHashtagResearch() {
            document.getElementById('hashtag-research-modal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Load analytics data
        async function loadAnalytics() {
            try {
                const response = await fetch('/api/instagram-management/analytics', {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    analytics = data.analytics;
                    updateAnalyticsDisplay();
                } else {
                    console.error('Failed to load analytics');
                }
            } catch (error) {
                console.error('Error loading analytics:', error);
            }
        }

        // Update analytics display
        function updateAnalyticsDisplay() {
            if (!analytics) return;

            const overview = analytics.overview;
            
            document.getElementById('total-followers').textContent = formatNumber(overview.total_followers);
            document.getElementById('followers-growth').textContent = 'Active followers';
            
            document.getElementById('engagement-rate').textContent = overview.engagement_rate + '%';
            document.getElementById('engagement-growth').textContent = 'Avg engagement rate';
            
            document.getElementById('posts-count').textContent = overview.total_posts;
            document.getElementById('posts-info').textContent = overview.total_posts + ' total posts';
            
            document.getElementById('accounts-count').textContent = overview.accounts_count;
            document.getElementById('accounts-info').textContent = overview.accounts_count + ' connected';
        }

        // Load accounts
        async function loadAccounts() {
            try {
                const response = await fetch('/api/instagram-management/accounts', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    accounts = data.accounts;
                    updateAccountsDisplay();
                } else {
                    console.error('Failed to load accounts');
                }
            } catch (error) {
                console.error('Error loading accounts:', error);
            }
        }

        // Update accounts display
        function updateAccountsDisplay() {
            const container = document.getElementById('accounts-list');
            
            if (accounts.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-secondary-text">
                        <svg class="w-12 h-12 mx-auto mb-2 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <p>No Instagram accounts connected</p>
                        <button onclick="openConnectAccountModal()" class="btn btn-primary mt-2">Connect Account</button>
                    </div>
                `;
                return;
            }

            container.innerHTML = accounts.map(account => `
                <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                    <div class="flex items-center">
                        <img src="${account.profile_picture_url || '/default-avatar.png'}" alt="${account.username}" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <div class="text-primary-text font-medium">@${account.username}</div>
                            <div class="text-sm text-secondary-text">${account.display_name || account.username}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-secondary-text">${formatNumber(account.followers_count)} followers</div>
                        <div class="text-xs text-info">${account.engagement_rate}% engagement</div>
                    </div>
                </div>
            `).join('');
        }

        // Load posts
        async function loadPosts() {
            try {
                const url = `/api/instagram-management/posts${currentFilter ? `?status=${currentFilter}` : ''}`;
                const response = await fetch(url, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    posts = data.posts;
                    updatePostsDisplay();
                } else {
                    console.error('Failed to load posts');
                }
            } catch (error) {
                console.error('Error loading posts:', error);
            }
        }

        // Update posts display
        function updatePostsDisplay() {
            const container = document.getElementById('posts-container');
            
            if (posts.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-8 text-secondary-text">
                        <svg class="w-12 h-12 mx-auto mb-2 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p>No posts found</p>
                        <button onclick="openCreatePostModal()" class="btn btn-primary mt-2">Create Post</button>
                    </div>
                `;
                return;
            }

            container.innerHTML = posts.map(post => `
                <div class="bg-app-bg rounded-lg p-4">
                    <div class="w-full h-32 bg-gradient-to-br from-info/20 to-success/20 rounded-lg mb-3 flex items-center justify-center">
                        <span class="text-xs text-secondary-text">${post.post_type}</span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-secondary-text">${formatDate(post.created_at)}</span>
                        <span class="px-2 py-1 text-xs font-medium rounded ${getStatusClass(post.status)}">${post.status}</span>
                    </div>
                    <p class="text-sm text-primary-text line-clamp-2">${post.caption}</p>
                    ${post.hashtags && post.hashtags.length > 0 ? `
                        <div class="mt-2 flex flex-wrap gap-1">
                            ${post.hashtags.slice(0, 3).map(tag => `<span class="text-xs text-info">${tag}</span>`).join('')}
                            ${post.hashtags.length > 3 ? `<span class="text-xs text-secondary-text">+${post.hashtags.length - 3} more</span>` : ''}
                        </div>
                    ` : ''}
                </div>
            `).join('');
        }

        // Filter posts
        function filterPosts(status) {
            currentFilter = status;
            loadPosts();
        }

        // Connect account form handler
        document.getElementById('connect-account-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                username: document.getElementById('account-username').value,
                display_name: document.getElementById('account-display-name').value,
                profile_picture_url: document.getElementById('account-profile-picture').value,
                bio: document.getElementById('account-bio').value
            };

            try {
                const response = await fetch('/api/instagram-management/accounts', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    closeModal('connect-account-modal');
                    loadAccounts();
                    loadAnalytics();
                    showNotification('Account connected successfully!', 'success');
                } else {
                    const error = await response.json();
                    showNotification(error.error || 'Failed to connect account', 'error');
                }
            } catch (error) {
                showNotification('Error connecting account', 'error');
            }
        });

        // Create post form handler
        document.getElementById('create-post-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const mediaUrls = document.getElementById('post-media-urls').value.split('\n').filter(url => url.trim());
            const hashtags = document.getElementById('post-hashtags').value.split(' ').filter(tag => tag.trim());
            
            const formData = {
                caption: document.getElementById('post-caption').value,
                media_urls: mediaUrls,
                hashtags: hashtags,
                post_type: document.getElementById('post-type').value,
                scheduled_at: document.getElementById('post-schedule').value || null
            };

            try {
                const response = await fetch('/api/instagram-management/posts', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    closeModal('create-post-modal');
                    loadPosts();
                    loadAnalytics();
                    showNotification('Post created successfully!', 'success');
                } else {
                    const error = await response.json();
                    showNotification(error.error || 'Failed to create post', 'error');
                }
            } catch (error) {
                showNotification('Error creating post', 'error');
            }
        });

        // Search hashtags
        async function searchHashtags() {
            const keyword = document.getElementById('hashtag-search').value.trim();
            if (!keyword) return;

            const container = document.getElementById('hashtag-results');
            container.innerHTML = '<div class="text-center py-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div></div>';

            try {
                const response = await fetch(`/api/instagram-management/hashtag-research?keyword=${encodeURIComponent(keyword)}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const hashtags = data.hashtags || [];
                    
                    if (hashtags.length === 0) {
                        container.innerHTML = '<div class="text-center py-8 text-secondary-text">No hashtags found</div>';
                        return;
                    }

                    container.innerHTML = hashtags.map(hashtag => `
                        <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                            <div class="flex items-center">
                                <span class="text-info font-medium">${hashtag.hashtag}</span>
                                <span class="ml-2 px-2 py-1 text-xs rounded" style="background-color: ${hashtag.difficulty_color}20; color: ${hashtag.difficulty_color}">
                                    ${hashtag.difficulty}
                                </span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-secondary-text">${hashtag.formatted_count} posts</div>
                                <div class="text-xs text-secondary-text">${hashtag.engagement_rate}% engagement</div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="text-center py-8 text-error">Failed to search hashtags</div>';
                }
            } catch (error) {
                container.innerHTML = '<div class="text-center py-8 text-error">Error searching hashtags</div>';
            }
        }

        // Utility functions
        function formatNumber(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toString();
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor(diff / (1000 * 60 * 60));
            
            if (days > 0) {
                return `${days} day${days > 1 ? 's' : ''} ago`;
            } else if (hours > 0) {
                return `${hours} hour${hours > 1 ? 's' : ''} ago`;
            } else {
                return 'Just now';
            }
        }

        function getStatusClass(status) {
            const classes = {
                'published': 'bg-success/10 text-success',
                'scheduled': 'bg-info/10 text-info',
                'draft': 'bg-warning/10 text-warning',
                'failed': 'bg-error/10 text-error'
            };
            return classes[status] || 'bg-secondary/10 text-secondary';
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${type === 'success' ? 'bg-success' : 'bg-error'}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Handle authentication
        if (!localStorage.getItem('auth_token')) {
            // Redirect to login if no token
            window.location.href = '/login';
        }
    </script>
</x-layouts.dashboard>