<x-layouts.dashboard title="Workspace Setup - Mewayz" page-title="Workspace Setup">
    <div class="fade-in">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-primary-text">Workspace Setup</h1>
                <div class="text-sm text-secondary-text">Step 1 of 6</div>
            </div>
            <div class="w-full bg-card-bg rounded-full h-2">
                <div class="bg-gradient-to-r from-info to-success h-2 rounded-full transition-all duration-300" style="width: 16.67%"></div>
            </div>
        </div>

        <!-- Setup Steps -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Current Step -->
            <div class="card border-2 border-info glow-effect">
                <div class="text-center">
                    <div class="w-16 h-16 bg-info/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Step 1: Basic Information</h3>
                    <p class="text-secondary-text text-sm">Set up your profile and business details</p>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card opacity-60">
                <div class="text-center">
                    <div class="w-16 h-16 bg-secondary-text/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Step 2: Pricing Plans</h3>
                    <p class="text-secondary-text text-sm">Choose your pricing structure</p>
                </div>
            </div>

            <div class="card opacity-60">
                <div class="text-center">
                    <div class="w-16 h-16 bg-secondary-text/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Step 3: Branding</h3>
                    <p class="text-secondary-text text-sm">Upload logo and set brand colors</p>
                </div>
            </div>
        </div>

        <!-- Current Step Form -->
        <div class="card">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-primary-text mb-2">Let's get started with your basic information</h2>
                <p class="text-secondary-text">This helps us personalize your experience and set up your workspace.</p>
            </div>

            <form class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-input" placeholder="Enter your first name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-input" placeholder="Enter your last name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Business Name</label>
                    <input type="text" class="form-input" placeholder="Enter your business name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Business Type</label>
                    <select class="form-input" required>
                        <option value="">Select your business type</option>
                        <option value="creator">Content Creator</option>
                        <option value="coach">Coach/Consultant</option>
                        <option value="freelancer">Freelancer</option>
                        <option value="agency">Agency</option>
                        <option value="ecommerce">E-commerce</option>
                        <option value="educator">Educator</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Primary Goal</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:border-info cursor-pointer">
                            <input type="radio" name="goal" value="audience" class="mr-3">
                            <div>
                                <div class="text-primary-text font-medium">Grow Audience</div>
                                <div class="text-secondary-text text-sm">Build and engage your community</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:border-info cursor-pointer">
                            <input type="radio" name="goal" value="sales" class="mr-3">
                            <div>
                                <div class="text-primary-text font-medium">Increase Sales</div>
                                <div class="text-secondary-text text-sm">Sell products and services</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:border-info cursor-pointer">
                            <input type="radio" name="goal" value="courses" class="mr-3">
                            <div>
                                <div class="text-primary-text font-medium">Teach Online</div>
                                <div class="text-secondary-text text-sm">Create and sell courses</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:border-info cursor-pointer">
                            <input type="radio" name="goal" value="brand" class="mr-3">
                            <div>
                                <div class="text-primary-text font-medium">Build Brand</div>
                                <div class="text-secondary-text text-sm">Establish online presence</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Website URL (Optional)</label>
                    <input type="url" class="form-input" placeholder="https://your-website.com">
                </div>

                <div class="flex justify-between pt-6">
                    <button type="button" class="btn btn-secondary" disabled>Previous</button>
                    <button type="submit" class="btn btn-primary">
                        Next Step
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card text-center">
                <div class="w-12 h-12 bg-info/10 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-primary-text font-semibold mb-1">Quick Setup</h3>
                <p class="text-secondary-text text-sm">Complete setup in 5 minutes</p>
            </div>

            <div class="card text-center">
                <div class="w-12 h-12 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-primary-text font-semibold mb-1">AI Assistance</h3>
                <p class="text-secondary-text text-sm">Get help with setup decisions</p>
            </div>

            <div class="card text-center">
                <div class="w-12 h-12 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                    </svg>
                </div>
                <h3 class="text-primary-text font-semibold mb-1">Support</h3>
                <p class="text-secondary-text text-sm">Get help when you need it</p>
            </div>
        </div>
    </div>
</x-layouts.dashboard>