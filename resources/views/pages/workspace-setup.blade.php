<x-layouts.app title="Workspace Setup - Mewayz">
    <div class="min-h-screen bg-background">
        <!-- Header -->
        <div class="bg-card-bg border-b border-border-color">
            <div class="max-w-4xl mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-info rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-primary-text">Workspace Setup</h1>
                            <p class="text-secondary-text" id="step-description">Let's set up your workspace in 6 simple steps</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-secondary-text mb-1">Progress</div>
                        <div class="text-2xl font-bold text-primary-text" id="progress-text">0%</div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="bg-secondary-bg rounded-full h-2">
                        <div class="bg-info h-2 rounded-full transition-all duration-300 ease-out" id="progress-bar" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between mt-4">
                        <div class="flex items-center text-sm" id="step-goals">
                            <div class="w-6 h-6 rounded-full bg-info text-white flex items-center justify-center text-xs font-semibold mr-2">1</div>
                            <span class="text-secondary-text">Goals</span>
                        </div>
                        <div class="flex items-center text-sm" id="step-features">
                            <div class="w-6 h-6 rounded-full bg-secondary-bg text-secondary-text flex items-center justify-center text-xs font-semibold mr-2">2</div>
                            <span class="text-secondary-text">Features</span>
                        </div>
                        <div class="flex items-center text-sm" id="step-team">
                            <div class="w-6 h-6 rounded-full bg-secondary-bg text-secondary-text flex items-center justify-center text-xs font-semibold mr-2">3</div>
                            <span class="text-secondary-text">Team</span>
                        </div>
                        <div class="flex items-center text-sm" id="step-subscription">
                            <div class="w-6 h-6 rounded-full bg-secondary-bg text-secondary-text flex items-center justify-center text-xs font-semibold mr-2">4</div>
                            <span class="text-secondary-text">Subscription</span>
                        </div>
                        <div class="flex items-center text-sm" id="step-branding">
                            <div class="w-6 h-6 rounded-full bg-secondary-bg text-secondary-text flex items-center justify-center text-xs font-semibold mr-2">5</div>
                            <span class="text-secondary-text">Branding</span>
                        </div>
                        <div class="flex items-center text-sm" id="step-complete">
                            <div class="w-6 h-6 rounded-full bg-secondary-bg text-secondary-text flex items-center justify-center text-xs font-semibold mr-2">6</div>
                            <span class="text-secondary-text">Complete</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div id="setup-content">
                <!-- Loading State -->
                <div class="text-center py-12" id="loading-state">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-info mx-auto mb-4"></div>
                    <p class="text-secondary-text">Loading workspace setup...</p>
                </div>

                <!-- Step 1: Goals Selection -->
                <div class="hidden" id="step-1-goals">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-primary-text mb-4">What are your main goals?</h2>
                        <p class="text-secondary-text text-lg">Select the business goals you want to achieve with Mewayz</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="goals-grid">
                        <!-- Goals will be populated here -->
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="button" class="btn btn-secondary" disabled>Previous</button>
                        <button type="button" class="btn btn-primary" id="next-goals" disabled>Next: Select Features</button>
                    </div>
                </div>

                <!-- Step 2: Features Selection -->
                <div class="hidden" id="step-2-features">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-primary-text mb-4">Choose your features</h2>
                        <p class="text-secondary-text text-lg">Select the features you need for your selected goals</p>
                    </div>
                    
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-primary-text">Available Features</h3>
                            <div class="text-sm text-secondary-text">
                                <span id="selected-features-count">0</span> features selected
                            </div>
                        </div>
                        
                        <div id="features-by-category">
                            <!-- Features will be populated here -->
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="button" class="btn btn-secondary" id="prev-features">Previous</button>
                        <button type="button" class="btn btn-primary" id="next-features" disabled>Next: Setup Team</button>
                    </div>
                </div>

                <!-- Step 3: Team Setup -->
                <div class="hidden" id="step-3-team">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-primary-text mb-4">Setup your team</h2>
                        <p class="text-secondary-text text-lg">Invite team members and assign roles</p>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-primary-text">Team Members</h3>
                            <button type="button" class="btn btn-secondary btn-sm" id="add-team-member">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Member
                            </button>
                        </div>
                        
                        <div id="team-members-list">
                            <!-- Team members will be populated here -->
                        </div>
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-ghost" id="skip-team-setup">Skip team setup for now</button>
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="button" class="btn btn-secondary" id="prev-team">Previous</button>
                        <button type="button" class="btn btn-primary" id="next-team">Next: Choose Plan</button>
                    </div>
                </div>

                <!-- Step 4: Subscription Plan -->
                <div class="hidden" id="step-4-subscription">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-primary-text mb-4">Choose your plan</h2>
                        <p class="text-secondary-text text-lg">Select the subscription plan that fits your needs</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" id="subscription-plans">
                        <!-- Subscription plans will be populated here -->
                    </div>
                    
                    <div class="bg-card-bg rounded-lg p-6 border border-border-color">
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-semibold text-primary-text">Pricing Summary</h3>
                        </div>
                        <div id="pricing-summary">
                            <!-- Pricing summary will be populated here -->
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="button" class="btn btn-secondary" id="prev-subscription">Previous</button>
                        <button type="button" class="btn btn-primary" id="next-subscription" disabled>Next: Branding</button>
                    </div>
                </div>

                <!-- Step 5: Branding -->
                <div class="hidden" id="step-5-branding">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-primary-text mb-4">Customize your branding</h2>
                        <p class="text-secondary-text text-lg">Make your workspace uniquely yours</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-primary-text mb-2">Workspace Name</label>
                                <input type="text" id="workspace-name" class="form-input w-full" placeholder="Enter workspace name">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-primary-text mb-2">Logo URL</label>
                                <input type="url" id="logo-url" class="form-input w-full" placeholder="https://example.com/logo.png">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-primary-text mb-2">Primary Color</label>
                                <input type="color" id="primary-color" class="form-input w-full h-12" value="#3B82F6">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-primary-text mb-2">Secondary Color</label>
                                <input type="color" id="secondary-color" class="form-input w-full h-12" value="#10B981">
                            </div>
                        </div>
                        
                        <div class="bg-card-bg rounded-lg p-6 border border-border-color">
                            <h3 class="text-lg font-semibold text-primary-text mb-4">Preview</h3>
                            <div id="branding-preview">
                                <!-- Branding preview will be populated here -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-6">
                        <button type="button" class="btn btn-ghost" id="skip-branding">Skip branding for now</button>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="button" class="btn btn-secondary" id="prev-branding">Previous</button>
                        <button type="button" class="btn btn-primary" id="complete-setup">Complete Setup</button>
                    </div>
                </div>

                <!-- Step 6: Complete -->
                <div class="hidden" id="step-6-complete">
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-primary-text mb-4">Setup Complete!</h2>
                        <p class="text-secondary-text text-lg mb-8">Your workspace is ready to use</p>
                        
                        <div class="bg-card-bg rounded-lg p-6 border border-border-color max-w-md mx-auto mb-8">
                            <div id="setup-summary">
                                <!-- Setup summary will be populated here -->
                            </div>
                        </div>
                        
                        <a href="/dashboard" class="btn btn-primary btn-lg">Go to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Workspace Setup Wizard JavaScript
        let currentStep = 1;
        let selectedGoals = [];
        let selectedFeatures = [];
        let teamMembers = [];
        let selectedPlan = null;
        let billingInterval = 'monthly';
        let workspaceData = null;

        // Initialize the wizard
        document.addEventListener('DOMContentLoaded', function() {
            loadInitialData();
        });

        async function loadInitialData() {
            try {
                const response = await fetch('/api/workspace-setup/initial-data', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    workspaceData = data.data;
                    
                    // Set current step based on workspace data
                    currentStep = getStepNumber(workspaceData.current_step);
                    
                    // Load goals
                    loadGoals(workspaceData.goals);
                    
                    // Load subscription plans
                    loadSubscriptionPlans(workspaceData.subscription_plans);
                    
                    // Update UI
                    updateProgressBar();
                    showCurrentStep();
                    
                    document.getElementById('loading-state').classList.add('hidden');
                } else {
                    console.error('Failed to load initial data');
                }
            } catch (error) {
                console.error('Error loading initial data:', error);
            }
        }

        function getStepNumber(stepName) {
            const steps = {
                'goals': 1,
                'features': 2,
                'team': 3,
                'subscription': 4,
                'branding': 5,
                'complete': 6
            };
            return steps[stepName] || 1;
        }

        function loadGoals(goals) {
            const goalsGrid = document.getElementById('goals-grid');
            goalsGrid.innerHTML = '';

            goals.forEach(goal => {
                const goalCard = document.createElement('div');
                goalCard.className = 'goal-card p-6 border-2 border-border-color rounded-lg cursor-pointer hover:border-info transition-colors';
                goalCard.dataset.goalSlug = goal.slug;
                
                goalCard.innerHTML = `
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" style="background-color: ${goal.color}20;">
                            ${goal.icon}
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-primary-text">${goal.name}</h3>
                            <p class="text-secondary-text text-sm">${goal.description}</p>
                        </div>
                        <div class="goal-checkbox w-6 h-6 rounded-full border-2 border-border-color flex items-center justify-center">
                            <svg class="w-4 h-4 text-white hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                `;
                
                goalCard.addEventListener('click', () => toggleGoal(goal.slug));
                goalsGrid.appendChild(goalCard);
            });
        }

        function loadSubscriptionPlans(plans) {
            const plansContainer = document.getElementById('subscription-plans');
            plansContainer.innerHTML = '';

            plans.forEach(plan => {
                const planCard = document.createElement('div');
                planCard.className = 'plan-card p-6 border-2 border-border-color rounded-lg cursor-pointer hover:border-info transition-colors';
                planCard.dataset.planId = plan.id;
                
                const badge = plan.metadata?.popular ? 
                    '<div class="bg-info text-white px-3 py-1 rounded-full text-sm font-medium mb-4">Most Popular</div>' : 
                    '<div class="h-8 mb-4"></div>';
                
                planCard.innerHTML = `
                    ${badge}
                    <h3 class="text-xl font-bold text-primary-text mb-2">${plan.name}</h3>
                    <p class="text-secondary-text mb-4">${plan.description}</p>
                    <div class="text-2xl font-bold text-primary-text mb-4">
                        ${plan.type === 'free' ? 'Free' : plan.feature_price_monthly + '/feature/month'}
                    </div>
                    <ul class="space-y-2 text-sm">
                        ${plan.metadata?.features_list?.map(feature => `<li class="flex items-center"><svg class="w-4 h-4 text-success mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>${feature}</li>`).join('') || ''}
                    </ul>
                `;
                
                planCard.addEventListener('click', () => selectPlan(plan.id));
                plansContainer.appendChild(planCard);
            });
        }

        function toggleGoal(goalSlug) {
            const goalCard = document.querySelector(`[data-goal-slug="${goalSlug}"]`);
            const checkbox = goalCard.querySelector('.goal-checkbox');
            const checkIcon = checkbox.querySelector('svg');
            
            if (selectedGoals.includes(goalSlug)) {
                selectedGoals = selectedGoals.filter(g => g !== goalSlug);
                goalCard.classList.remove('border-info', 'bg-info/5');
                checkbox.classList.remove('bg-info', 'border-info');
                checkIcon.classList.add('hidden');
            } else {
                selectedGoals.push(goalSlug);
                goalCard.classList.add('border-info', 'bg-info/5');
                checkbox.classList.add('bg-info', 'border-info');
                checkIcon.classList.remove('hidden');
            }
            
            document.getElementById('next-goals').disabled = selectedGoals.length === 0;
        }

        function selectPlan(planId) {
            // Remove previous selection
            document.querySelectorAll('.plan-card').forEach(card => {
                card.classList.remove('border-info', 'bg-info/5');
            });
            
            // Add selection to clicked card
            const planCard = document.querySelector(`[data-plan-id="${planId}"]`);
            planCard.classList.add('border-info', 'bg-info/5');
            
            selectedPlan = planId;
            document.getElementById('next-subscription').disabled = false;
            
            // Calculate pricing
            calculatePricing();
        }

        async function calculatePricing() {
            if (!selectedPlan) return;
            
            try {
                const response = await fetch('/api/workspace-setup/pricing/calculate', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_id: selectedPlan,
                        billing_interval: billingInterval
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    updatePricingSummary(data.data);
                }
            } catch (error) {
                console.error('Error calculating pricing:', error);
            }
        }

        function updatePricingSummary(pricingData) {
            const summaryContainer = document.getElementById('pricing-summary');
            summaryContainer.innerHTML = `
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-secondary-text">Plan:</span>
                        <span class="text-primary-text font-medium">${pricingData.plan.name}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-secondary-text">Features:</span>
                        <span class="text-primary-text font-medium">${pricingData.feature_count} features</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-secondary-text">Billing:</span>
                        <span class="text-primary-text font-medium">${pricingData.billing_interval}</span>
                    </div>
                    <div class="border-t border-border-color pt-3">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold text-primary-text">Total:</span>
                            <span class="text-lg font-bold text-primary-text">$${pricingData.pricing.total_price}</span>
                        </div>
                        ${pricingData.billing_interval === 'yearly' ? `
                            <div class="text-sm text-success mt-1">
                                Save $${pricingData.pricing.yearly_savings} per year
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }

        function updateProgressBar() {
            const progress = (currentStep / 6) * 100;
            document.getElementById('progress-bar').style.width = progress + '%';
            document.getElementById('progress-text').textContent = Math.round(progress) + '%';
            
            // Update step indicators
            for (let i = 1; i <= 6; i++) {
                const stepElement = document.getElementById(`step-${getStepName(i)}`);
                const circle = stepElement.querySelector('div');
                
                if (i <= currentStep) {
                    circle.classList.remove('bg-secondary-bg', 'text-secondary-text');
                    circle.classList.add('bg-info', 'text-white');
                } else {
                    circle.classList.remove('bg-info', 'text-white');
                    circle.classList.add('bg-secondary-bg', 'text-secondary-text');
                }
            }
        }

        function getStepName(stepNumber) {
            const steps = {
                1: 'goals',
                2: 'features',
                3: 'team',
                4: 'subscription',
                5: 'branding',
                6: 'complete'
            };
            return steps[stepNumber];
        }

        function showCurrentStep() {
            // Hide all steps
            for (let i = 1; i <= 6; i++) {
                document.getElementById(`step-${i}-${getStepName(i)}`).classList.add('hidden');
            }
            
            // Show current step
            document.getElementById(`step-${currentStep}-${getStepName(currentStep)}`).classList.remove('hidden');
            
            // Update step description
            const descriptions = {
                1: 'Select your main business goals',
                2: 'Choose the features you need',
                3: 'Invite your team members',
                4: 'Select your subscription plan',
                5: 'Customize your branding',
                6: 'Your workspace is ready!'
            };
            document.getElementById('step-description').textContent = descriptions[currentStep];
        }

        // Event listeners for navigation
        document.getElementById('next-goals').addEventListener('click', async () => {
            if (selectedGoals.length > 0) {
                await saveGoals();
            }
        });

        document.getElementById('prev-features').addEventListener('click', () => {
            currentStep = 1;
            updateProgressBar();
            showCurrentStep();
        });

        document.getElementById('next-features').addEventListener('click', async () => {
            if (selectedFeatures.length > 0) {
                await saveFeatures();
            }
        });

        document.getElementById('prev-team').addEventListener('click', () => {
            currentStep = 2;
            updateProgressBar();
            showCurrentStep();
        });

        document.getElementById('next-team').addEventListener('click', async () => {
            await saveTeamSetup();
        });

        document.getElementById('prev-subscription').addEventListener('click', () => {
            currentStep = 3;
            updateProgressBar();
            showCurrentStep();
        });

        document.getElementById('next-subscription').addEventListener('click', async () => {
            if (selectedPlan) {
                await saveSubscription();
            }
        });

        document.getElementById('prev-branding').addEventListener('click', () => {
            currentStep = 4;
            updateProgressBar();
            showCurrentStep();
        });

        document.getElementById('complete-setup').addEventListener('click', async () => {
            await saveBranding();
        });

        // API calls
        async function saveGoals() {
            try {
                const response = await fetch('/api/workspace-setup/goals', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        goals: selectedGoals
                    })
                });

                if (response.ok) {
                    await loadFeatures();
                    currentStep = 2;
                    updateProgressBar();
                    showCurrentStep();
                }
            } catch (error) {
                console.error('Error saving goals:', error);
            }
        }

        async function loadFeatures() {
            try {
                const response = await fetch('/api/workspace-setup/features', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    displayFeatures(data.data.features_grouped);
                }
            } catch (error) {
                console.error('Error loading features:', error);
            }
        }

        function displayFeatures(featuresGrouped) {
            const container = document.getElementById('features-by-category');
            container.innerHTML = '';

            Object.entries(featuresGrouped).forEach(([category, features]) => {
                const categoryDiv = document.createElement('div');
                categoryDiv.className = 'mb-8';
                
                categoryDiv.innerHTML = `
                    <h4 class="text-lg font-semibold text-primary-text mb-4">${category}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${features.map(feature => `
                            <div class="feature-card p-4 border border-border-color rounded-lg cursor-pointer hover:border-info transition-colors" data-feature-id="${feature.id}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h5 class="font-medium text-primary-text">${feature.name}</h5>
                                        <p class="text-sm text-secondary-text">${feature.description}</p>
                                        <div class="text-xs text-success mt-1">$${feature.monthly_price}/month</div>
                                    </div>
                                    <div class="feature-checkbox w-6 h-6 rounded border-2 border-border-color flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
                
                container.appendChild(categoryDiv);
            });

            // Add click listeners to feature cards
            document.querySelectorAll('.feature-card').forEach(card => {
                card.addEventListener('click', () => {
                    const featureId = parseInt(card.dataset.featureId);
                    toggleFeature(featureId);
                });
            });
        }

        function toggleFeature(featureId) {
            const featureCard = document.querySelector(`[data-feature-id="${featureId}"]`);
            const checkbox = featureCard.querySelector('.feature-checkbox');
            const checkIcon = checkbox.querySelector('svg');
            
            if (selectedFeatures.includes(featureId)) {
                selectedFeatures = selectedFeatures.filter(f => f !== featureId);
                featureCard.classList.remove('border-info', 'bg-info/5');
                checkbox.classList.remove('bg-info', 'border-info');
                checkIcon.classList.add('hidden');
            } else {
                selectedFeatures.push(featureId);
                featureCard.classList.add('border-info', 'bg-info/5');
                checkbox.classList.add('bg-info', 'border-info');
                checkIcon.classList.remove('hidden');
            }
            
            document.getElementById('selected-features-count').textContent = selectedFeatures.length;
            document.getElementById('next-features').disabled = selectedFeatures.length === 0;
        }

        async function saveFeatures() {
            try {
                const response = await fetch('/api/workspace-setup/features', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        features: selectedFeatures
                    })
                });

                if (response.ok) {
                    currentStep = 3;
                    updateProgressBar();
                    showCurrentStep();
                }
            } catch (error) {
                console.error('Error saving features:', error);
            }
        }

        async function saveTeamSetup() {
            try {
                const response = await fetch('/api/workspace-setup/team', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        team_members: teamMembers,
                        skip_team_setup: teamMembers.length === 0
                    })
                });

                if (response.ok) {
                    currentStep = 4;
                    updateProgressBar();
                    showCurrentStep();
                }
            } catch (error) {
                console.error('Error saving team setup:', error);
            }
        }

        async function saveSubscription() {
            try {
                const response = await fetch('/api/workspace-setup/subscription', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_id: selectedPlan,
                        billing_interval: billingInterval
                    })
                });

                if (response.ok) {
                    currentStep = 5;
                    updateProgressBar();
                    showCurrentStep();
                }
            } catch (error) {
                console.error('Error saving subscription:', error);
            }
        }

        async function saveBranding() {
            try {
                const response = await fetch('/api/workspace-setup/branding', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        workspace_name: document.getElementById('workspace-name').value,
                        logo: document.getElementById('logo-url').value,
                        primary_color: document.getElementById('primary-color').value,
                        secondary_color: document.getElementById('secondary-color').value,
                        skip_branding: false
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    displaySetupSummary(data.data);
                    currentStep = 6;
                    updateProgressBar();
                    showCurrentStep();
                }
            } catch (error) {
                console.error('Error saving branding:', error);
            }
        }

        function displaySetupSummary(data) {
            const summaryContainer = document.getElementById('setup-summary');
            summaryContainer.innerHTML = `
                <div class="text-center space-y-4">
                    <div class="text-lg font-semibold text-primary-text">${data.workspace.name}</div>
                    <div class="text-sm text-secondary-text">
                        ${selectedGoals.length} goals • ${selectedFeatures.length} features
                    </div>
                    <div class="text-sm text-secondary-text">
                        ${data.workspace.subscription_plan?.name || 'No plan selected'}
                    </div>
                </div>
            `;
        }

        // Skip buttons
        document.getElementById('skip-team-setup').addEventListener('click', () => {
            teamMembers = [];
            saveTeamSetup();
        });

        document.getElementById('skip-branding').addEventListener('click', () => {
            document.getElementById('workspace-name').value = '';
            document.getElementById('logo-url').value = '';
            saveBranding();
        });
    </script>
</x-layouts.app>