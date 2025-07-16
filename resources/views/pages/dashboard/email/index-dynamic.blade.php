<x-layouts.dashboard title="Email Marketing - Mewayz" page-title="Email Marketing">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Email Marketing</h1>
                <p class="text-secondary-text">Create campaigns, manage subscribers, and track performance</p>
            </div>
            <div class="flex gap-3">
                <button onclick="openTemplatesModal()" class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    View Templates
                </button>
                <button onclick="openCreateCampaignModal()" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Campaign
                </button>
            </div>
        </div>

        <!-- Email Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Subscribers</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="total-subscribers">-</div>
                <div class="text-sm text-success" id="subscribers-growth">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Open Rate</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="open-rate">-</div>
                <div class="text-sm text-info" id="open-rate-growth">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Click Rate</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="click-rate">-</div>
                <div class="text-sm text-warning" id="click-rate-growth">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Campaigns</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="total-campaigns">-</div>
                <div class="text-sm text-success" id="campaigns-sent">Loading...</div>
            </div>
        </div>

        <!-- Recent Campaigns -->
        <div class="card mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Recent Campaigns</h2>
                <button onclick="loadCampaigns()" class="btn btn-secondary">Refresh</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border-color">
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Campaign</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Status</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Recipients</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Opens</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Clicks</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Created</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="campaigns-table-body">
                        <tr>
                            <td colspan="7" class="text-center py-8 text-secondary-text">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
                                <span class="ml-2">Loading campaigns...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card text-center">
                <div class="w-16 h-16 bg-info/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-primary-text mb-2">Create Campaign</h3>
                <p class="text-secondary-text text-sm mb-4">Build and send beautiful email campaigns</p>
                <button onclick="openCreateCampaignModal()" class="btn btn-primary">Get Started</button>
            </div>

            <div class="card text-center">
                <div class="w-16 h-16 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-primary-text mb-2">Manage Subscribers</h3>
                <p class="text-secondary-text text-sm mb-4">Import, segment, and manage your audience</p>
                <button onclick="openSubscribersModal()" class="btn btn-primary">Manage</button>
            </div>

            <div class="card text-center">
                <div class="w-16 h-16 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-primary-text mb-2">View Analytics</h3>
                <p class="text-secondary-text text-sm mb-4">Track performance and optimize campaigns</p>
                <button onclick="openAnalyticsModal()" class="btn btn-primary">View Reports</button>
            </div>
        </div>
    </div>

    <!-- Create Campaign Modal -->
    <div id="create-campaign-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-card-bg rounded-lg p-6 w-full max-w-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Create Email Campaign</h3>
                    <button onclick="closeModal('create-campaign-modal')" class="text-secondary-text hover:text-primary-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="create-campaign-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-secondary-text mb-1">Campaign Name</label>
                        <input type="text" id="campaign-name" class="form-input w-full" placeholder="Enter campaign name" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-secondary-text mb-1">Subject Line</label>
                        <input type="text" id="campaign-subject" class="form-input w-full" placeholder="Enter email subject" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-secondary-text mb-1">Email Content</label>
                        <textarea id="campaign-content" class="form-input w-full h-32" placeholder="Enter email content..." required></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-secondary-text mb-1">Email Template</label>
                            <select id="campaign-template" class="form-input w-full">
                                <option value="">No template</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-secondary-text mb-1">Email Lists</label>
                            <select id="campaign-lists" class="form-input w-full" multiple>
                                <option disabled>Loading lists...</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-secondary-text mb-1">Schedule (Optional)</label>
                        <input type="datetime-local" id="campaign-schedule" class="form-input w-full">
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" onclick="closeModal('create-campaign-modal')" class="btn btn-secondary flex-1">Cancel</button>
                        <button type="submit" class="btn btn-primary flex-1">Create Campaign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Templates Modal -->
    <div id="templates-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-card-bg rounded-lg p-6 w-full max-w-4xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Email Templates</h3>
                    <button onclick="closeModal('templates-modal')" class="text-secondary-text hover:text-primary-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div id="templates-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="text-center py-8 text-secondary-text col-span-full">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
                        <span class="ml-2">Loading templates...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscribers Modal -->
    <div id="subscribers-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-card-bg rounded-lg p-6 w-full max-w-6xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Manage Subscribers</h3>
                    <button onclick="closeModal('subscribers-modal')" class="text-secondary-text hover:text-primary-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <input type="text" id="subscribers-search" class="form-input w-full" placeholder="Search subscribers..." oninput="searchSubscribers()">
                </div>
                
                <div id="subscribers-list" class="space-y-2 max-h-96 overflow-y-auto">
                    <div class="text-center py-8 text-secondary-text">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
                        <span class="ml-2">Loading subscribers...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Modal -->
    <div id="analytics-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-card-bg rounded-lg p-6 w-full max-w-4xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Email Analytics</h3>
                    <button onclick="closeModal('analytics-modal')" class="text-secondary-text hover:text-primary-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div id="analytics-content">
                    <div class="text-center py-8 text-secondary-text">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
                        <span class="ml-2">Loading analytics...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let analytics = null;
        let campaigns = [];
        let templates = [];
        let emailLists = [];
        let subscribers = [];

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadAnalytics();
            loadCampaigns();
            loadTemplates();
            loadEmailLists();
        });

        // Modal functions
        function openCreateCampaignModal() {
            loadTemplates();
            loadEmailLists();
            document.getElementById('create-campaign-modal').classList.remove('hidden');
        }

        function openTemplatesModal() {
            loadTemplates();
            document.getElementById('templates-modal').classList.remove('hidden');
        }

        function openSubscribersModal() {
            loadSubscribers();
            document.getElementById('subscribers-modal').classList.remove('hidden');
        }

        function openAnalyticsModal() {
            loadAnalytics();
            document.getElementById('analytics-modal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Load analytics data
        async function loadAnalytics() {
            try {
                const response = await fetch('/api/email-marketing/analytics', {
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
            
            document.getElementById('total-subscribers').textContent = overview.total_subscribers;
            document.getElementById('subscribers-growth').textContent = overview.active_subscribers + ' active';
            
            document.getElementById('open-rate').textContent = overview.avg_open_rate.toFixed(1) + '%';
            document.getElementById('open-rate-growth').textContent = 'Average open rate';
            
            document.getElementById('click-rate').textContent = overview.avg_click_rate.toFixed(1) + '%';
            document.getElementById('click-rate-growth').textContent = 'Average click rate';
            
            document.getElementById('total-campaigns').textContent = overview.total_campaigns;
            document.getElementById('campaigns-sent').textContent = overview.campaigns_sent + ' sent';
        }

        // Load campaigns
        async function loadCampaigns() {
            try {
                const response = await fetch('/api/email-marketing/campaigns', {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    campaigns = data.campaigns;
                    updateCampaignsDisplay();
                } else {
                    console.error('Failed to load campaigns');
                }
            } catch (error) {
                console.error('Error loading campaigns:', error);
            }
        }

        // Update campaigns display
        function updateCampaignsDisplay() {
            const tbody = document.getElementById('campaigns-table-body');
            
            if (campaigns.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-8 text-secondary-text">
                            <svg class="w-12 h-12 mx-auto mb-2 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <p>No campaigns found</p>
                            <button onclick="openCreateCampaignModal()" class="btn btn-primary mt-2">Create Campaign</button>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = campaigns.map(campaign => `
                <tr class="border-b border-border-color hover:bg-hover-bg">
                    <td class="py-4 px-4">
                        <div>
                            <div class="font-medium text-primary-text">${campaign.name}</div>
                            <div class="text-sm text-secondary-text">${campaign.subject}</div>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <span class="px-2 py-1 text-xs font-medium rounded ${getStatusClass(campaign.status)}">${campaign.status}</span>
                    </td>
                    <td class="py-4 px-4 text-primary-text">${campaign.total_recipients}</td>
                    <td class="py-4 px-4 text-primary-text">${campaign.opened_count} (${campaign.open_rate}%)</td>
                    <td class="py-4 px-4 text-primary-text">${campaign.clicked_count} (${campaign.click_rate}%)</td>
                    <td class="py-4 px-4 text-primary-text">${formatDate(campaign.created_at)}</td>
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-2">
                            <button onclick="viewCampaign(${campaign.id})" class="text-info hover:text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            ${campaign.status === 'draft' ? `
                                <button onclick="sendCampaign(${campaign.id})" class="text-success hover:text-green-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            ` : ''}
                            <button onclick="deleteCampaign(${campaign.id})" class="text-error hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Load templates
        async function loadTemplates() {
            try {
                const response = await fetch('/api/email-marketing/templates', {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    templates = data.templates;
                    updateTemplatesDisplay();
                } else {
                    console.error('Failed to load templates');
                }
            } catch (error) {
                console.error('Error loading templates:', error);
            }
        }

        // Update templates display
        function updateTemplatesDisplay() {
            const grid = document.getElementById('templates-grid');
            const select = document.getElementById('campaign-template');
            
            if (templates.length === 0) {
                grid.innerHTML = '<div class="text-center py-8 text-secondary-text col-span-full">No templates found</div>';
                return;
            }

            // Update templates grid
            grid.innerHTML = templates.map(template => `
                <div class="bg-app-bg rounded-lg p-4">
                    <div class="w-full h-32 bg-gradient-to-br from-info/20 to-success/20 rounded-lg mb-3 flex items-center justify-center">
                        <span class="text-xs text-secondary-text">${template.category}</span>
                    </div>
                    <div class="mb-2">
                        <h4 class="font-medium text-primary-text">${template.name}</h4>
                        <p class="text-sm text-secondary-text">${template.description || 'No description'}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-1 text-xs font-medium rounded" style="background-color: ${template.category_color}20; color: ${template.category_color}">
                            ${template.formatted_category}
                        </span>
                        <span class="text-xs text-secondary-text">${template.usage_count} uses</span>
                    </div>
                </div>
            `).join('');

            // Update template select
            if (select) {
                select.innerHTML = '<option value="">No template</option>' + 
                    templates.map(template => `<option value="${template.id}">${template.name}</option>`).join('');
            }
        }

        // Load email lists
        async function loadEmailLists() {
            try {
                const response = await fetch('/api/email-marketing/lists', {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    emailLists = data.lists;
                    updateEmailListsDisplay();
                } else {
                    console.error('Failed to load email lists');
                }
            } catch (error) {
                console.error('Error loading email lists:', error);
            }
        }

        // Update email lists display
        function updateEmailListsDisplay() {
            const select = document.getElementById('campaign-lists');
            
            if (emailLists.length === 0) {
                select.innerHTML = '<option disabled>No email lists found</option>';
                return;
            }

            select.innerHTML = emailLists.map(list => 
                `<option value="${list.id}">${list.name} (${list.subscriber_count} subscribers)</option>`
            ).join('');
        }

        // Load subscribers
        async function loadSubscribers() {
            try {
                const response = await fetch('/api/email-marketing/subscribers', {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    subscribers = data.subscribers;
                    updateSubscribersDisplay();
                } else {
                    console.error('Failed to load subscribers');
                }
            } catch (error) {
                console.error('Error loading subscribers:', error);
            }
        }

        // Update subscribers display
        function updateSubscribersDisplay() {
            const container = document.getElementById('subscribers-list');
            
            if (subscribers.length === 0) {
                container.innerHTML = '<div class="text-center py-8 text-secondary-text">No subscribers found</div>';
                return;
            }

            container.innerHTML = subscribers.map(subscriber => `
                <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-info/20 to-success/20 rounded-full mr-3 flex items-center justify-center">
                            <span class="text-sm font-medium text-primary-text">${subscriber.first_name ? subscriber.first_name.charAt(0) : subscriber.email.charAt(0)}</span>
                        </div>
                        <div>
                            <div class="text-primary-text font-medium">${subscriber.first_name || subscriber.email.split('@')[0]}</div>
                            <div class="text-sm text-secondary-text">${subscriber.email}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 text-xs font-medium rounded ${getStatusClass(subscriber.status)}">${subscriber.status}</span>
                        <div class="text-xs text-secondary-text mt-1">${subscriber.location || 'Unknown location'}</div>
                    </div>
                </div>
            `).join('');
        }

        // Create campaign form handler
        document.getElementById('create-campaign-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const selectedLists = Array.from(document.getElementById('campaign-lists').selectedOptions).map(option => parseInt(option.value));
            
            if (selectedLists.length === 0) {
                showNotification('Please select at least one email list', 'error');
                return;
            }
            
            const formData = {
                name: document.getElementById('campaign-name').value,
                subject: document.getElementById('campaign-subject').value,
                content: document.getElementById('campaign-content').value,
                template_id: document.getElementById('campaign-template').value || null,
                recipient_lists: selectedLists,
                scheduled_at: document.getElementById('campaign-schedule').value || null
            };

            try {
                const response = await fetch('/api/email-marketing/campaigns', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    closeModal('create-campaign-modal');
                    loadCampaigns();
                    loadAnalytics();
                    showNotification('Campaign created successfully!', 'success');
                    
                    // Reset form
                    document.getElementById('create-campaign-form').reset();
                } else {
                    const error = await response.json();
                    showNotification(error.error || 'Failed to create campaign', 'error');
                }
            } catch (error) {
                showNotification('Error creating campaign', 'error');
            }
        });

        // Campaign actions
        async function sendCampaign(campaignId) {
            if (!confirm('Are you sure you want to send this campaign?')) {
                return;
            }

            try {
                const response = await fetch(`/api/email-marketing/campaigns/${campaignId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    loadCampaigns();
                    loadAnalytics();
                    showNotification('Campaign sent successfully!', 'success');
                } else {
                    const error = await response.json();
                    showNotification(error.error || 'Failed to send campaign', 'error');
                }
            } catch (error) {
                showNotification('Error sending campaign', 'error');
            }
        }

        async function deleteCampaign(campaignId) {
            if (!confirm('Are you sure you want to delete this campaign?')) {
                return;
            }

            try {
                const response = await fetch(`/api/email-marketing/campaigns/${campaignId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    loadCampaigns();
                    loadAnalytics();
                    showNotification('Campaign deleted successfully!', 'success');
                } else {
                    const error = await response.json();
                    showNotification(error.error || 'Failed to delete campaign', 'error');
                }
            } catch (error) {
                showNotification('Error deleting campaign', 'error');
            }
        }

        // Search subscribers
        function searchSubscribers() {
            const query = document.getElementById('subscribers-search').value.toLowerCase();
            const filteredSubscribers = subscribers.filter(subscriber => 
                subscriber.email.toLowerCase().includes(query) ||
                (subscriber.first_name && subscriber.first_name.toLowerCase().includes(query)) ||
                (subscriber.last_name && subscriber.last_name.toLowerCase().includes(query))
            );
            
            // Update display with filtered results
            const container = document.getElementById('subscribers-list');
            container.innerHTML = filteredSubscribers.map(subscriber => `
                <div class="flex items-center justify-between p-3 bg-app-bg rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-info/20 to-success/20 rounded-full mr-3 flex items-center justify-center">
                            <span class="text-sm font-medium text-primary-text">${subscriber.first_name ? subscriber.first_name.charAt(0) : subscriber.email.charAt(0)}</span>
                        </div>
                        <div>
                            <div class="text-primary-text font-medium">${subscriber.first_name || subscriber.email.split('@')[0]}</div>
                            <div class="text-sm text-secondary-text">${subscriber.email}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 text-xs font-medium rounded ${getStatusClass(subscriber.status)}">${subscriber.status}</span>
                        <div class="text-xs text-secondary-text mt-1">${subscriber.location || 'Unknown location'}</div>
                    </div>
                </div>
            `).join('');
        }

        // Utility functions
        function getStatusClass(status) {
            const classes = {
                'draft': 'bg-secondary/10 text-secondary',
                'scheduled': 'bg-info/10 text-info',
                'sending': 'bg-warning/10 text-warning',
                'sent': 'bg-success/10 text-success',
                'paused': 'bg-error/10 text-error',
                'cancelled': 'bg-error/10 text-error',
                'subscribed': 'bg-success/10 text-success',
                'unsubscribed': 'bg-secondary/10 text-secondary',
                'bounced': 'bg-error/10 text-error',
                'complained': 'bg-error/10 text-error'
            };
            return classes[status] || 'bg-secondary/10 text-secondary';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
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

        // Handle authentication - session-based auth handled by Laravel middleware
    </script>
</x-layouts.dashboard>