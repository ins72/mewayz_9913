@extends('layouts.app')

@section('title', 'Complete Your Workspace Setup')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome to Mewayz</h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 mt-2">Let's set up your workspace to get you started</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Setup Progress</span>
                    </div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                        <span id="progress-text">Step 1 of 6</span>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 16.67%"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                    <span>Goals</span>
                    <span>Features</span>
                    <span>Team</span>
                    <span>Plan</span>
                    <span>Brand</span>
                    <span>Launch</span>
                </div>
            </div>
        </div>

        <!-- Setup Form Container -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <div id="setup-container" class="min-h-[600px]">
                <!-- Dynamic content will be loaded here -->
                <div class="flex items-center justify-center h-96">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-4 text-gray-600 dark:text-gray-300">Loading setup wizard...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-8 flex justify-between">
            <button id="back-btn" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Previous
            </button>
            <button id="next-btn" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Continue
            </button>
        </div>
    </div>
</div>

<script>
class WorkspaceSetupWizard {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 6;
        this.setupData = {};
        this.authToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        this.initializeEventListeners();
        this.loadInitialStep();
    }

    initializeEventListeners() {
        document.getElementById('back-btn').addEventListener('click', () => this.goToPreviousStep());
        document.getElementById('next-btn').addEventListener('click', () => this.goToNextStep());
    }

    async loadInitialStep() {
        try {
            const response = await fetch('/api/workspace-setup/current-step', {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                }
            });
            
            const data = await response.json();
            if (data.success) {
                this.currentStep = data.current_step;
                this.setupData = data.setup_progress || {};
                this.updateProgressBar();
                this.loadStep(this.currentStep);
            } else {
                this.loadStep(1);
            }
        } catch (error) {
            console.error('Error loading initial step:', error);
            this.loadStep(1);
        }
    }

    async loadStep(stepNumber) {
        const container = document.getElementById('setup-container');
        container.innerHTML = '<div class="flex items-center justify-center h-96"><div class="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600 mx-auto"></div></div>';

        try {
            let stepContent = '';
            
            switch (stepNumber) {
                case 1:
                    stepContent = await this.loadMainGoalsStep();
                    break;
                case 2:
                    stepContent = await this.loadFeatureSelectionStep();
                    break;
                case 3:
                    stepContent = await this.loadTeamSetupStep();
                    break;
                case 4:
                    stepContent = await this.loadSubscriptionStep();
                    break;
                case 5:
                    stepContent = await this.loadBrandingStep();
                    break;
                case 6:
                    stepContent = await this.loadFinalReviewStep();
                    break;
            }
            
            container.innerHTML = stepContent;
            this.updateProgressBar();
            this.updateNavigationButtons();
        } catch (error) {
            console.error('Error loading step:', error);
            container.innerHTML = '<div class="p-8 text-center text-red-600">Error loading step. Please try again.</div>';
        }
    }

    async loadMainGoalsStep() {
        const response = await fetch('/api/workspace-setup/main-goals', {
            headers: {
                'Authorization': `Bearer ${this.getAuthToken()}`,
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        const goals = data.goals || {};
        
        return `
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">What are your main business goals?</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-8">Select the areas where you want to focus your business efforts. You can choose multiple goals.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="goals-grid">
                    ${Object.entries(goals).map(([key, goal]) => `
                        <div class="goal-card border-2 border-gray-200 dark:border-gray-700 rounded-lg p-6 cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors"
                             data-goal="${key}">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4">
                                    <span class="text-2xl">${goal.icon}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">${goal.name}</h3>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">${goal.description}</p>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <span>${goal.features.length} features available</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
                
                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Primary Goal (most important)
                    </label>
                    <select id="primary-goal" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" disabled>
                        <option value="">Select your primary goal first</option>
                    </select>
                </div>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Business Type
                        </label>
                        <input type="text" id="business-type" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="e.g., E-commerce, Content Creator, Agency">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Target Audience
                        </label>
                        <input type="text" id="target-audience" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="e.g., Young professionals, Small businesses">
                    </div>
                </div>
            </div>
        `;
    }

    async loadFeatureSelectionStep() {
        // Get selected goals from previous step
        const selectedGoals = this.setupData.selected_goals || [];
        
        const response = await fetch('/api/workspace-setup/available-features', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.getAuthToken()}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ selected_goals: selectedGoals })
        });
        
        const data = await response.json();
        const features = data.features || {};
        
        return `
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Choose Your Features</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-8">Select the features you want to use in your workspace. You can add or remove features later.</p>
                
                <div class="mb-6">
                    <div class="flex items-center justify-between bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                        <div>
                            <h3 class="font-semibold text-blue-900 dark:text-blue-100">Free Plan Limit</h3>
                            <p class="text-sm text-blue-700 dark:text-blue-300">You can select up to 3 features on the free plan</p>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-blue-900 dark:text-blue-100" id="selected-count">0</span>
                            <span class="text-blue-700 dark:text-blue-300">/ 3</span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4" id="features-list">
                    ${Object.entries(features).map(([key, feature]) => `
                        <div class="feature-card border border-gray-200 dark:border-gray-700 rounded-lg p-4 cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors"
                             data-feature="${key}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">${feature.name}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">${feature.description}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400 mr-4">Free</span>
                                    <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-600 rounded"></div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    async loadTeamSetupStep() {
        return `
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Team Setup</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-8">Invite team members to collaborate on your workspace. You can skip this step and add team members later.</p>
                
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" id="skip-team" class="mr-3">
                        <span class="text-gray-700 dark:text-gray-300">Skip team setup for now</span>
                    </label>
                </div>
                
                <div id="team-setup-form">
                    <div class="space-y-4" id="team-members">
                        <div class="team-member-row flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1">
                                <input type="email" placeholder="Email address" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            </div>
                            <div class="w-32">
                                <select class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                    <option value="member">Member</option>
                                    <option value="editor">Editor</option>
                                    <option value="manager">Manager</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="button" class="text-red-600 hover:text-red-800 p-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <button type="button" id="add-team-member" class="mt-4 text-blue-600 hover:text-blue-800 font-medium">
                        + Add another team member
                    </button>
                </div>
            </div>
        `;
    }

    async loadSubscriptionStep() {
        const response = await fetch('/api/workspace-setup/subscription-plans', {
            headers: {
                'Authorization': `Bearer ${this.getAuthToken()}`,
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        const plans = data.plans || {};
        
        return `
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Choose Your Plan</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-8">Select the plan that best fits your needs. You can upgrade or downgrade at any time.</p>
                
                <div class="mb-6">
                    <div class="flex items-center justify-center">
                        <div class="bg-gray-100 dark:bg-gray-700 p-1 rounded-lg">
                            <button type="button" id="monthly-toggle" class="px-4 py-2 rounded-md text-sm font-medium bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">Monthly</button>
                            <button type="button" id="yearly-toggle" class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300">Yearly (Save 17%)</button>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="plans-grid">
                    ${Object.entries(plans).map(([key, plan]) => `
                        <div class="plan-card border-2 border-gray-200 dark:border-gray-700 rounded-lg p-6 cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors ${plan.metadata?.popular ? 'ring-2 ring-blue-500' : ''}"
                             data-plan="${key}">
                            ${plan.metadata?.popular ? '<div class="bg-blue-500 text-white text-xs font-bold py-1 px-3 rounded-full inline-block mb-4">POPULAR</div>' : ''}
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">${plan.name}</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">${plan.description}</p>
                            <div class="mb-6">
                                <span class="text-3xl font-bold text-gray-900 dark:text-white">$${plan.feature_price_monthly}</span>
                                <span class="text-gray-600 dark:text-gray-300">/feature/month</span>
                            </div>
                            <ul class="space-y-2 mb-6">
                                ${plan.metadata?.features_list?.map(feature => `
                                    <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        ${feature}
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    async loadBrandingStep() {
        return `
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Customize Your Brand</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-8">Add your brand colors and logo to personalize your workspace and external-facing content.</p>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Company Name
                        </label>
                        <input type="text" id="company-name" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Your Company Name">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Primary Color
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="color" id="primary-color" value="#3B82F6" class="w-12 h-12 rounded-lg border border-gray-300 dark:border-gray-600">
                                <input type="text" value="#3B82F6" class="flex-1 p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Secondary Color
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="color" id="secondary-color" value="#10B981" class="w-12 h-12 rounded-lg border border-gray-300 dark:border-gray-600">
                                <input type="text" value="#10B981" class="flex-1 p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Logo (Optional)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="mt-4">
                                <label class="cursor-pointer">
                                    <span class="text-blue-600 hover:text-blue-500 font-medium">Upload a logo</span>
                                    <input type="file" class="hidden" accept="image/*" id="logo-upload">
                                </label>
                                <p class="text-gray-500 text-sm mt-1">PNG, JPG, SVG up to 5MB</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Brand Voice
                        </label>
                        <select id="brand-voice" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="professional">Professional</option>
                            <option value="casual">Casual</option>
                            <option value="friendly">Friendly</option>
                            <option value="authoritative">Authoritative</option>
                            <option value="playful">Playful</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
    }

    async loadFinalReviewStep() {
        return `
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Review & Launch</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-8">Review your workspace configuration and launch your Mewayz experience!</p>
                
                <div class="space-y-6">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Workspace Overview</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Selected Goals</div>
                                <div class="font-medium text-gray-900 dark:text-white" id="review-goals">Loading...</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Features Count</div>
                                <div class="font-medium text-gray-900 dark:text-white" id="review-features">Loading...</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Plan</div>
                                <div class="font-medium text-gray-900 dark:text-white" id="review-plan">Loading...</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Team Members</div>
                                <div class="font-medium text-gray-900 dark:text-white" id="review-team">Loading...</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 dark:bg-green-900 rounded-lg p-6">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-green-900 dark:text-green-100">Ready to Launch!</h3>
                                <p class="text-green-700 dark:text-green-300 text-sm">Your workspace is configured and ready to use</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    updateProgressBar() {
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        
        const percentage = (this.currentStep / this.totalSteps) * 100;
        progressBar.style.width = `${percentage}%`;
        progressText.textContent = `Step ${this.currentStep} of ${this.totalSteps}`;
    }

    updateNavigationButtons() {
        const backBtn = document.getElementById('back-btn');
        const nextBtn = document.getElementById('next-btn');
        
        backBtn.disabled = this.currentStep === 1;
        
        if (this.currentStep === this.totalSteps) {
            nextBtn.textContent = 'Launch Workspace';
            nextBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            nextBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        } else {
            nextBtn.textContent = 'Continue';
            nextBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            nextBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
    }

    async goToNextStep() {
        if (await this.validateCurrentStep()) {
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.loadStep(this.currentStep);
            } else {
                await this.completeSetup();
            }
        }
    }

    async goToPreviousStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.loadStep(this.currentStep);
        }
    }

    async validateCurrentStep() {
        // Add validation logic for each step
        switch (this.currentStep) {
            case 1:
                return await this.validateMainGoalsStep();
            case 2:
                return await this.validateFeatureSelectionStep();
            case 3:
                return await this.validateTeamSetupStep();
            case 4:
                return await this.validateSubscriptionStep();
            case 5:
                return await this.validateBrandingStep();
            case 6:
                return true;
            default:
                return true;
        }
    }

    async validateMainGoalsStep() {
        const selectedGoals = document.querySelectorAll('.goal-card.selected');
        const businessType = document.getElementById('business-type').value;
        const targetAudience = document.getElementById('target-audience').value;
        const primaryGoal = document.getElementById('primary-goal').value;
        
        if (selectedGoals.length === 0) {
            alert('Please select at least one business goal.');
            return false;
        }
        
        if (!businessType || !targetAudience || !primaryGoal) {
            alert('Please fill in all required fields.');
            return false;
        }
        
        // Save data
        const goalData = {
            selected_goals: Array.from(selectedGoals).map(el => el.dataset.goal),
            primary_goal: primaryGoal,
            business_type: businessType,
            target_audience: targetAudience
        };
        
        try {
            const response = await fetch('/api/workspace-setup/main-goals', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(goalData)
            });
            
            const result = await response.json();
            if (result.success) {
                this.setupData.main_goals = goalData;
                return true;
            } else {
                alert('Error saving goals: ' + result.message);
                return false;
            }
        } catch (error) {
            console.error('Error saving goals:', error);
            alert('Error saving goals. Please try again.');
            return false;
        }
    }

    async validateFeatureSelectionStep() {
        const selectedFeatures = document.querySelectorAll('.feature-card.selected');
        
        if (selectedFeatures.length === 0) {
            alert('Please select at least one feature.');
            return false;
        }
        
        if (selectedFeatures.length > 3) {
            alert('Free plan allows maximum 3 features.');
            return false;
        }
        
        // Save data
        const featureData = {
            selected_features: Array.from(selectedFeatures).map(el => el.dataset.feature),
            subscription_plan: 'free'
        };
        
        try {
            const response = await fetch('/api/workspace-setup/feature-selection', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(featureData)
            });
            
            const result = await response.json();
            if (result.success) {
                this.setupData.feature_selection = featureData;
                return true;
            } else {
                alert('Error saving features: ' + result.message);
                return false;
            }
        } catch (error) {
            console.error('Error saving features:', error);
            alert('Error saving features. Please try again.');
            return false;
        }
    }

    async validateTeamSetupStep() {
        const skipTeam = document.getElementById('skip-team').checked;
        
        if (skipTeam) {
            this.setupData.team_setup = { skip_team_setup: true };
            return true;
        }
        
        const teamMemberRows = document.querySelectorAll('.team-member-row');
        const teamMembers = [];
        
        teamMemberRows.forEach(row => {
            const email = row.querySelector('input[type="email"]').value;
            const role = row.querySelector('select').value;
            
            if (email) {
                teamMembers.push({ email, role });
            }
        });
        
        // Save data
        const teamData = {
            team_members: teamMembers,
            skip_team_setup: false
        };
        
        try {
            const response = await fetch('/api/workspace-setup/team-setup', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(teamData)
            });
            
            const result = await response.json();
            if (result.success) {
                this.setupData.team_setup = teamData;
                return true;
            } else {
                alert('Error saving team setup: ' + result.message);
                return false;
            }
        } catch (error) {
            console.error('Error saving team setup:', error);
            alert('Error saving team setup. Please try again.');
            return false;
        }
    }

    async validateSubscriptionStep() {
        const selectedPlan = document.querySelector('.plan-card.selected');
        
        if (!selectedPlan) {
            alert('Please select a subscription plan.');
            return false;
        }
        
        // Save data
        const subscriptionData = {
            subscription_plan: selectedPlan.dataset.plan,
            billing_cycle: document.getElementById('yearly-toggle').classList.contains('bg-white') ? 'yearly' : 'monthly'
        };
        
        try {
            const response = await fetch('/api/workspace-setup/subscription-selection', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(subscriptionData)
            });
            
            const result = await response.json();
            if (result.success) {
                this.setupData.subscription_selection = subscriptionData;
                return true;
            } else {
                alert('Error saving subscription: ' + result.message);
                return false;
            }
        } catch (error) {
            console.error('Error saving subscription:', error);
            alert('Error saving subscription. Please try again.');
            return false;
        }
    }

    async validateBrandingStep() {
        const companyName = document.getElementById('company-name').value;
        const primaryColor = document.getElementById('primary-color').value;
        const secondaryColor = document.getElementById('secondary-color').value;
        const brandVoice = document.getElementById('brand-voice').value;
        
        if (!companyName) {
            alert('Please enter your company name.');
            return false;
        }
        
        // Save data
        const brandingData = {
            company_name: companyName,
            primary_color: primaryColor,
            secondary_color: secondaryColor,
            brand_voice: brandVoice
        };
        
        try {
            const response = await fetch('/api/workspace-setup/branding-configuration', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(brandingData)
            });
            
            const result = await response.json();
            if (result.success) {
                this.setupData.branding_configuration = brandingData;
                return true;
            } else {
                alert('Error saving branding: ' + result.message);
                return false;
            }
        } catch (error) {
            console.error('Error saving branding:', error);
            alert('Error saving branding. Please try again.');
            return false;
        }
    }

    async completeSetup() {
        try {
            const response = await fetch('/api/workspace-setup/complete', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Content-Type': 'application/json',
                }
            });
            
            const result = await response.json();
            if (result.success) {
                window.location.href = '/console'; // Redirect to dashboard
            } else {
                alert('Error completing setup: ' + result.message);
            }
        } catch (error) {
            console.error('Error completing setup:', error);
            alert('Error completing setup. Please try again.');
        }
    }

    getAuthToken() {
        return localStorage.getItem('auth_token') || this.authToken;
    }
}

// Initialize the wizard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new WorkspaceSetupWizard();
    
    // Add event delegation for dynamic elements
    document.addEventListener('click', function(e) {
        // Goal card selection
        if (e.target.closest('.goal-card')) {
            const card = e.target.closest('.goal-card');
            card.classList.toggle('selected');
            card.classList.toggle('border-blue-500');
            card.classList.toggle('bg-blue-50');
            
            // Update primary goal dropdown
            const primaryGoalSelect = document.getElementById('primary-goal');
            const selectedGoals = document.querySelectorAll('.goal-card.selected');
            
            primaryGoalSelect.innerHTML = '<option value="">Select primary goal</option>';
            selectedGoals.forEach(goalCard => {
                const goalKey = goalCard.dataset.goal;
                const goalName = goalCard.querySelector('h3').textContent;
                primaryGoalSelect.innerHTML += `<option value="${goalKey}">${goalName}</option>`;
            });
            
            primaryGoalSelect.disabled = selectedGoals.length === 0;
        }
        
        // Feature card selection
        if (e.target.closest('.feature-card')) {
            const card = e.target.closest('.feature-card');
            const selectedCount = document.querySelectorAll('.feature-card.selected').length;
            
            if (card.classList.contains('selected')) {
                card.classList.remove('selected', 'border-blue-500', 'bg-blue-50');
            } else if (selectedCount < 3) {
                card.classList.add('selected', 'border-blue-500', 'bg-blue-50');
            } else {
                alert('Free plan allows maximum 3 features. Please upgrade to add more features.');
                return;
            }
            
            // Update counter
            const newCount = document.querySelectorAll('.feature-card.selected').length;
            document.getElementById('selected-count').textContent = newCount;
        }
        
        // Plan card selection
        if (e.target.closest('.plan-card')) {
            document.querySelectorAll('.plan-card').forEach(card => {
                card.classList.remove('selected', 'border-blue-500', 'bg-blue-50');
            });
            
            const card = e.target.closest('.plan-card');
            card.classList.add('selected', 'border-blue-500', 'bg-blue-50');
        }
        
        // Add team member
        if (e.target.id === 'add-team-member') {
            const teamMembersContainer = document.getElementById('team-members');
            const newRow = document.createElement('div');
            newRow.className = 'team-member-row flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg';
            newRow.innerHTML = `
                <div class="flex-1">
                    <input type="email" placeholder="Email address" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>
                <div class="w-32">
                    <select class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="member">Member</option>
                        <option value="editor">Editor</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="button" class="text-red-600 hover:text-red-800 p-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            `;
            teamMembersContainer.appendChild(newRow);
        }
        
        // Remove team member
        if (e.target.closest('.team-member-row button')) {
            const row = e.target.closest('.team-member-row');
            row.remove();
        }
        
        // Toggle billing cycle
        if (e.target.id === 'yearly-toggle') {
            document.getElementById('monthly-toggle').classList.remove('bg-white', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white', 'shadow-sm');
            document.getElementById('monthly-toggle').classList.add('text-gray-700', 'dark:text-gray-300');
            document.getElementById('yearly-toggle').classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white', 'shadow-sm');
            document.getElementById('yearly-toggle').classList.remove('text-gray-700', 'dark:text-gray-300');
        }
        
        if (e.target.id === 'monthly-toggle') {
            document.getElementById('yearly-toggle').classList.remove('bg-white', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white', 'shadow-sm');
            document.getElementById('yearly-toggle').classList.add('text-gray-700', 'dark:text-gray-300');
            document.getElementById('monthly-toggle').classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white', 'shadow-sm');
            document.getElementById('monthly-toggle').classList.remove('text-gray-700', 'dark:text-gray-300');
        }
    });
    
    // Skip team setup toggle
    document.addEventListener('change', function(e) {
        if (e.target.id === 'skip-team') {
            const teamSetupForm = document.getElementById('team-setup-form');
            teamSetupForm.style.display = e.target.checked ? 'none' : 'block';
        }
    });
});
</script>
@endsection