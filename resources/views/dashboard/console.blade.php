@extends('layouts.app')

@section('title', 'Console - Mewayz')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left side: Logo and Workspace selector -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <img class="h-8 w-8" src="/images/mewayz-logo.png" alt="Mewayz" onerror="this.style.display='none'">
                        <div class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm" style="display: none;">M</div>
                    </div>
                    
                    <!-- Workspace Selector -->
                    <div class="ml-4">
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" id="workspace-selector">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8h1m-1 4h1m4-4h1m-1 4h1"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white" id="workspace-name">Loading...</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Current Workspace</div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right side: User menu and settings -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="p-2 text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200 relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h3v-3h-3v3zM6 4h8v8H6V4zm0 16l5-5h-5v5z"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-xs text-white">3</span>
                    </button>
                    
                    <!-- Settings -->
                    <button class="p-2 text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200" id="settings-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                    
                    <!-- User Menu -->
                    <div class="relative">
                        <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" id="user-menu-btn">
                            <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">U</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Top Navigation Tabs -->
        <div class="mb-8">
            <nav class="flex space-x-8">
                <button class="tab-button active border-b-2 border-blue-500 text-blue-600 font-medium pb-2" data-tab="dashboard">
                    Dashboard
                </button>
                <button class="tab-button text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 pb-2" data-tab="social">
                    Social
                </button>
                <button class="tab-button text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 pb-2" data-tab="crm">
                    CRM
                </button>
                <button class="tab-button text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 pb-2" data-tab="store">
                    Store
                </button>
                <button class="tab-button text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 pb-2" data-tab="analytics">
                    Analytics
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div id="dashboard-tab" class="tab-content">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">2,847</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Followers</div>
                        </div>
                        <div class="text-green-500 text-sm font-medium">+12.5%</div>
                    </div>
                    <div class="mt-4 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded opacity-20"></div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">$45,320</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Revenue</div>
                        </div>
                        <div class="text-green-500 text-sm font-medium">+8.2%</div>
                    </div>
                    <div class="mt-4 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded opacity-20"></div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">1,234</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Active Students</div>
                        </div>
                        <div class="text-green-500 text-sm font-medium">+15.3%</div>
                    </div>
                    <div class="mt-4 h-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded opacity-20"></div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">89.2%</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Conversion Rate</div>
                        </div>
                        <div class="text-green-500 text-sm font-medium">+2.1%</div>
                    </div>
                    <div class="mt-4 h-8 bg-gradient-to-r from-orange-500 to-orange-600 rounded opacity-20"></div>
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="grid grid-cols-3 md:grid-cols-6 lg:grid-cols-9 gap-4" id="quick-actions-grid">
                    <!-- Quick actions will be populated dynamically based on enabled features -->
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                    <div class="space-y-4" id="recent-activity">
                        <!-- Activity items will be populated dynamically -->
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Overview</h3>
                    <div class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Analytics chart will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Tab -->
        <div id="social-tab" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Social Media Management</h3>
                <p class="text-gray-600 dark:text-gray-300">Social media tools and features will be displayed here based on your selected goals and features.</p>
            </div>
        </div>

        <!-- CRM Tab -->
        <div id="crm-tab" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Relationship Management</h3>
                <p class="text-gray-600 dark:text-gray-300">CRM tools and features will be displayed here based on your selected goals and features.</p>
            </div>
        </div>

        <!-- Store Tab -->
        <div id="store-tab" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">E-commerce Store</h3>
                <p class="text-gray-600 dark:text-gray-300">E-commerce tools and features will be displayed here based on your selected goals and features.</p>
            </div>
        </div>

        <!-- Analytics Tab -->
        <div id="analytics-tab" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Analytics & Insights</h3>
                <p class="text-gray-600 dark:text-gray-300">Analytics and reporting features will be displayed here based on your selected goals and features.</p>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div id="settings-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Workspace Settings</h2>
                        <button id="close-settings" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Workspace Management</h3>
                            <div class="space-y-3">
                                <button class="w-full text-left p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4a2 2 0 100-4m6 4a2 2 0 100-4m0 4a2 2 0 100 4m0-4a2 2 0 100-4m6-8a2 2 0 100-4m0 4a2 2 0 100 4m0-4a2 2 0 100-4"/>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Features</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Manage enabled features</div>
                                        </div>
                                    </div>
                                </button>
                                
                                <button class="w-full text-left p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Team Management</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Invite and manage team members</div>
                                        </div>
                                    </div>
                                </button>
                                
                                <button class="w-full text-left p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v1m0 0h6m-6 0V5a2 2 0 012-2h4a2 2 0 012 2v1m0 0h6m-6 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h4a2 2 0 012 2v1m0 0h6"/>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Subscription</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Manage your plan and billing</div>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Settings</h3>
                            <div class="space-y-3">
                                <button class="w-full text-left p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Profile</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Update your profile information</div>
                                        </div>
                                    </div>
                                </button>
                                
                                <button class="w-full text-left p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Security</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Password and security settings</div>
                                        </div>
                                    </div>
                                </button>
                                
                                <button class="w-full text-left p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Preferences</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Notifications and preferences</div>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
class DashboardManager {
    constructor() {
        this.currentWorkspace = null;
        this.enabledFeatures = [];
        this.authToken = this.getAuthToken();
        
        this.initializeEventListeners();
        this.loadWorkspaceData();
        this.loadQuickActions();
    }

    initializeEventListeners() {
        // Tab switching
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const tab = e.target.dataset.tab;
                this.switchTab(tab);
            });
        });

        // Settings modal
        document.getElementById('settings-btn').addEventListener('click', () => {
            document.getElementById('settings-modal').classList.remove('hidden');
        });

        document.getElementById('close-settings').addEventListener('click', () => {
            document.getElementById('settings-modal').classList.add('hidden');
        });

        // Close modal when clicking outside
        document.getElementById('settings-modal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('settings-modal')) {
                document.getElementById('settings-modal').classList.add('hidden');
            }
        });
    }

    switchTab(tabName) {
        // Update tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'border-b-2', 'border-blue-500', 'text-blue-600', 'font-medium');
            button.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
        });

        // Activate current tab
        const currentButton = document.querySelector(`[data-tab="${tabName}"]`);
        currentButton.classList.add('active', 'border-b-2', 'border-blue-500', 'text-blue-600', 'font-medium');
        currentButton.classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        document.getElementById(`${tabName}-tab`).classList.remove('hidden');
    }

    async loadWorkspaceData() {
        try {
            const response = await fetch('/api/auth/me', {
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Content-Type': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.currentWorkspace = data.workspace;
                this.enabledFeatures = data.enabled_features || [];
                
                // Update workspace name
                document.getElementById('workspace-name').textContent = this.currentWorkspace?.name || 'Default Workspace';
                
                this.loadRecentActivity();
            }
        } catch (error) {
            console.error('Error loading workspace data:', error);
        }
    }

    loadQuickActions() {
        const quickActionsGrid = document.getElementById('quick-actions-grid');
        
        // Define all possible quick actions with their icons and colors
        const allQuickActions = [
            {
                name: 'Instagram Search',
                icon: '📱',
                color: 'bg-red-500',
                action: 'instagram-search',
                feature: 'instagram-post-scheduling'
            },
            {
                name: 'Post Scheduler',
                icon: '📅',
                color: 'bg-blue-500',
                action: 'post-scheduler',
                feature: 'instagram-post-scheduling'
            },
            {
                name: 'Link Builder',
                icon: '🔗',
                color: 'bg-green-500',
                action: 'link-builder',
                feature: 'bio-page-builder'
            },
            {
                name: 'Course Creator',
                icon: '🎓',
                color: 'bg-orange-500',
                action: 'course-creator',
                feature: 'course-builder'
            },
            {
                name: 'Store Manager',
                icon: '🛍️',
                color: 'bg-purple-500',
                action: 'store-manager',
                feature: 'product-catalog'
            },
            {
                name: 'CRM Hub',
                icon: '👥',
                color: 'bg-red-500',
                action: 'crm-hub',
                feature: 'contact-management'
            },
            {
                name: 'Email Marketing',
                icon: '📧',
                color: 'bg-green-500',
                action: 'email-marketing',
                feature: 'email-campaigns'
            },
            {
                name: 'Content Calendar',
                icon: '📊',
                color: 'bg-pink-500',
                action: 'content-calendar',
                feature: 'instagram-content-calendar'
            },
            {
                name: 'QR Generator',
                icon: '⚡',
                color: 'bg-gray-500',
                action: 'qr-generator',
                feature: 'bio-page-builder'
            }
        ];

        // Filter actions based on enabled features
        const enabledActions = allQuickActions.filter(action => 
            this.enabledFeatures.includes(action.feature)
        );

        // If no features are enabled, show all actions (for demo purposes)
        const actionsToShow = enabledActions.length > 0 ? enabledActions : allQuickActions;

        quickActionsGrid.innerHTML = actionsToShow.map(action => `
            <div class="quick-action-card cursor-pointer hover:scale-105 transition-transform" data-action="${action.action}">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm text-center">
                    <div class="w-12 h-12 ${action.color} rounded-lg mx-auto mb-3 flex items-center justify-center">
                        <span class="text-xl">${action.icon}</span>
                    </div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">${action.name}</div>
                </div>
            </div>
        `).join('');

        // Add click event listeners to quick actions
        document.querySelectorAll('.quick-action-card').forEach(card => {
            card.addEventListener('click', (e) => {
                const action = e.currentTarget.dataset.action;
                this.handleQuickAction(action);
            });
        });
    }

    handleQuickAction(action) {
        switch (action) {
            case 'instagram-search':
                this.switchTab('social');
                break;
            case 'post-scheduler':
                this.switchTab('social');
                break;
            case 'link-builder':
                window.open('/link-in-bio', '_blank');
                break;
            case 'course-creator':
                window.open('/courses', '_blank');
                break;
            case 'store-manager':
                this.switchTab('store');
                break;
            case 'crm-hub':
                this.switchTab('crm');
                break;
            case 'email-marketing':
                window.open('/email-marketing', '_blank');
                break;
            case 'content-calendar':
                this.switchTab('social');
                break;
            case 'qr-generator':
                window.open('/qr-generator', '_blank');
                break;
            default:
                console.log('Quick action not implemented:', action);
        }
    }

    loadRecentActivity() {
        const recentActivityContainer = document.getElementById('recent-activity');
        
        // Mock recent activity data
        const mockActivities = [
            {
                action: 'New Instagram post scheduled',
                time: '2 minutes ago',
                icon: '📱',
                color: 'text-blue-500'
            },
            {
                action: 'Email campaign sent to 1,234 subscribers',
                time: '15 minutes ago',
                icon: '📧',
                color: 'text-green-500'
            },
            {
                action: 'New course student enrolled',
                time: '1 hour ago',
                icon: '🎓',
                color: 'text-orange-500'
            },
            {
                action: 'Product added to store',
                time: '2 hours ago',
                icon: '🛍️',
                color: 'text-purple-500'
            },
            {
                action: 'New contact added to CRM',
                time: '3 hours ago',
                icon: '👥',
                color: 'text-red-500'
            }
        ];

        recentActivityContainer.innerHTML = mockActivities.map(activity => `
            <div class="flex items-center p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center mr-3">
                    <span class="text-sm">${activity.icon}</span>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">${activity.action}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${activity.time}</div>
                </div>
            </div>
        `).join('');
    }

    getAuthToken() {
        return localStorage.getItem('auth_token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new DashboardManager();
});
</script>
@endsection