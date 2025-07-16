<x-layouts.dashboard title="Settings - Mewayz" page-title="Settings">
    <div class="fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Settings</h1>
                <p class="text-secondary-text">Manage your account settings and preferences</p>
            </div>
            <div class="flex gap-3">
                <button class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export Data
                </button>
            </div>
        </div>

        <!-- Settings Navigation -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Settings Menu -->
            <div class="lg:col-span-1">
                <div class="card">
                    <nav class="space-y-1">
                        <a href="#profile" class="nav-item active">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </a>
                        <a href="#account" class="nav-item">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Account
                        </a>
                        <a href="#notifications" class="nav-item">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5m-5-9a3 3 0 11-6 0 3 3 0 016 0zm5 0a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Notifications
                        </a>
                        <a href="#billing" class="nav-item">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Billing
                        </a>
                        <a href="#security" class="nav-item">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Security
                        </a>
                        <a href="#integrations" class="nav-item">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path>
                            </svg>
                            Integrations
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="lg:col-span-3">
                <!-- Profile Settings -->
                <div class="card" id="profile-section">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-primary-text mb-2">Profile Settings</h2>
                        <p class="text-secondary-text">Update your profile information and preferences</p>
                    </div>

                    <form class="space-y-6">
                        <!-- Profile Picture -->
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 bg-gradient-to-br from-info/20 to-success/20 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-primary-text">{{ substr(auth()->user()->name ?? 'User', 0, 1) }}</span>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary text-sm mb-2">Change Photo</button>
                                <p class="text-secondary-text text-sm">JPG, PNG or GIF. Max size 2MB.</p>
                            </div>
                        </div>

                        <!-- Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-input" value="{{ auth()->user()->name ?? '' }}" placeholder="Enter your first name">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-input" placeholder="Enter your last name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-input" value="{{ auth()->user()->email ?? '' }}" placeholder="Enter your email">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Bio</label>
                            <textarea class="form-input h-24" placeholder="Tell us about yourself..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label class="form-label">Website</label>
                                <input type="url" class="form-input" placeholder="https://yourwebsite.com">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-input" placeholder="City, Country">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>

                <!-- Account Settings -->
                <div class="card mt-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-primary-text mb-2">Account Settings</h2>
                        <p class="text-secondary-text">Manage your account preferences and privacy settings</p>
                    </div>

                    <div class="space-y-6">
                        <!-- Email Preferences -->
                        <div>
                            <h3 class="text-lg font-medium text-primary-text mb-4">Email Preferences</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-3" checked>
                                    <span class="text-primary-text">Marketing emails</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-3" checked>
                                    <span class="text-primary-text">Product updates</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-3">
                                    <span class="text-primary-text">Weekly digest</span>
                                </label>
                            </div>
                        </div>

                        <!-- Privacy Settings -->
                        <div>
                            <h3 class="text-lg font-medium text-primary-text mb-4">Privacy Settings</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-3" checked>
                                    <span class="text-primary-text">Make profile public</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-3">
                                    <span class="text-primary-text">Allow search engines to index my profile</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="mr-3" checked>
                                    <span class="text-primary-text">Show online status</span>
                                </label>
                            </div>
                        </div>

                        <!-- Danger Zone -->
                        <div class="border-t border-border-color pt-6">
                            <h3 class="text-lg font-medium text-error mb-4">Danger Zone</h3>
                            <div class="space-y-3">
                                <button class="btn btn-secondary text-error hover:bg-error/10">
                                    Deactivate Account
                                </button>
                                <button class="btn btn-secondary text-error hover:bg-error/10">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>