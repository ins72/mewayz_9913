@props([
    'step' => 1,
    'workspace' => null,
    'goals' => [],
    'features' => [],
    'subscriptionPlans' => []
])

<div class="min-h-screen bg-app-bg">
    <!-- Progress Bar -->
    <div class="bg-card-bg border-b border-border-color">
        <div class="max-w-4xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img src="/images/mewayz-logo.png" alt="Mewayz" class="h-8 w-8">
                    <h1 class="text-xl font-bold text-primary-text">Setup Your Workspace</h1>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-secondary-text">Step {{ $step }} of 5</span>
                    <div class="w-32 bg-gray-700 rounded-full h-2">
                        <div class="bg-info h-2 rounded-full transition-all duration-300" style="width: {{ ($step / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wizard Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        <!-- Step 1: Workspace Info -->
        @if($step === 1)
        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-primary-text mb-2">Welcome to Mewayz!</h2>
                <p class="text-secondary-text text-lg">Let's start by setting up your workspace</p>
            </div>

            <div class="bg-card-bg rounded-lg p-6 border border-border-color">
                <form id="workspace-info-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Workspace Name</label>
                        <input type="text" 
                               name="workspace_name" 
                               class="w-full px-4 py-3 bg-background border border-border-color rounded-lg text-primary-text focus:outline-none focus:ring-2 focus:ring-info focus:border-transparent"
                               placeholder="Enter your workspace name"
                               value="{{ $workspace->name ?? '' }}"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Description (Optional)</label>
                        <textarea name="workspace_description" 
                                  rows="3"
                                  class="w-full px-4 py-3 bg-background border border-border-color rounded-lg text-primary-text focus:outline-none focus:ring-2 focus:ring-info focus:border-transparent resize-none"
                                  placeholder="Describe your workspace...">{{ $workspace->description ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Industry</label>
                        <select name="workspace_industry" 
                                class="w-full px-4 py-3 bg-background border border-border-color rounded-lg text-primary-text focus:outline-none focus:ring-2 focus:ring-info focus:border-transparent">
                            <option value="">Select your industry</option>
                            <option value="e-commerce">E-commerce</option>
                            <option value="education">Education</option>
                            <option value="healthcare">Healthcare</option>
                            <option value="technology">Technology</option>
                            <option value="marketing">Marketing</option>
                            <option value="consulting">Consulting</option>
                            <option value="finance">Finance</option>
                            <option value="real-estate">Real Estate</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Step 2: Goal Selection -->
        @if($step === 2)
        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-primary-text mb-2">Choose Your Goals</h2>
                <p class="text-secondary-text text-lg">Select the main goals for your workspace</p>
            </div>

            <form id="goals-form" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($goals as $goal)
                    <div class="goal-card bg-card-bg rounded-lg p-4 border border-border-color cursor-pointer hover:border-info transition-all duration-300 transform hover:scale-105" 
                         data-goal-id="{{ $goal->id }}"
                         data-goal-slug="{{ $goal->slug }}">
                        <div class="flex flex-col items-center text-center space-y-3">
                            <div class="w-16 h-16 rounded-lg flex items-center justify-center" style="background-color: {{ $goal->color }}20;">
                                <x-icon name="{{ $goal->icon }}" size="2xl" class="text-white" style="color: {{ $goal->color }}" alt="{{ $goal->name }}" />
                            </div>
                            <h3 class="font-semibold text-primary-text">{{ $goal->name }}</h3>
                            <p class="text-sm text-secondary-text">{{ $goal->description }}</p>
                        </div>
                        <input type="checkbox" name="selected_goals[]" value="{{ $goal->id }}" class="hidden goal-checkbox">
                    </div>
                    @endforeach
                </div>

                <div class="text-center">
                    <p class="text-sm text-secondary-text">Select multiple goals to build your perfect workspace</p>
                </div>
            </form>
        </div>
        @endif

        <!-- Step 3: Feature Selection -->
        @if($step === 3)
        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-primary-text mb-2">Select Features</h2>
                <p class="text-secondary-text text-lg">Choose the features you need for your selected goals</p>
            </div>

            <div class="bg-card-bg rounded-lg p-6 border border-border-color">
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-primary-text">Selected Features</h3>
                        <div class="text-right">
                            <div class="text-sm text-secondary-text">Total Cost:</div>
                            <div class="text-xl font-bold text-info">$<span id="total-cost">0.00</span>/month</div>
                        </div>
                    </div>
                    <div class="h-px bg-border-color"></div>
                </div>

                <form id="features-form" class="space-y-6">
                    @foreach($features->groupBy('category') as $category => $categoryFeatures)
                    <div class="feature-category">
                        <h4 class="text-lg font-semibold text-primary-text mb-3 flex items-center">
                            <x-icon name="lightbulb" size="sm" class="mr-2 text-info" alt="Category" />
                            {{ $category }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($categoryFeatures as $feature)
                            <div class="feature-item bg-background rounded-lg p-4 border border-border-color hover:border-info transition-all duration-300">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 pt-1">
                                        <input type="checkbox" 
                                               name="selected_features[]" 
                                               value="{{ $feature->id }}"
                                               class="feature-checkbox w-4 h-4 text-info bg-background border-border-color rounded focus:ring-info focus:ring-2"
                                               data-monthly-price="{{ $feature->monthly_price }}"
                                               data-yearly-price="{{ $feature->yearly_price }}"
                                               {{ $feature->is_free ? 'checked disabled' : '' }}>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h5 class="font-medium text-primary-text">{{ $feature->name }}</h5>
                                            <div class="text-right ml-2">
                                                @if($feature->is_free)
                                                    <span class="text-xs bg-success text-white px-2 py-1 rounded">FREE</span>
                                                @else
                                                    <div class="text-sm font-semibold text-info">${{ $feature->monthly_price }}/mo</div>
                                                    <div class="text-xs text-secondary-text">${{ $feature->yearly_price }}/yr</div>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-sm text-secondary-text mt-1">{{ $feature->description }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </form>
            </div>
        </div>
        @endif

        <!-- Step 4: Subscription Plan -->
        @if($step === 4)
        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-primary-text mb-2">Choose Your Plan</h2>
                <p class="text-secondary-text text-lg">Select the subscription plan that works best for you</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($subscriptionPlans as $plan)
                <div class="subscription-plan bg-card-bg rounded-lg p-6 border border-border-color cursor-pointer hover:border-info transition-all duration-300 transform hover:scale-105 {{ $plan->slug === 'professional' ? 'ring-2 ring-info' : '' }}"
                     data-plan-id="{{ $plan->id }}"
                     data-plan-slug="{{ $plan->slug }}">
                    
                    @if($plan->slug === 'professional')
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                        <span class="bg-info text-white px-3 py-1 rounded-full text-sm font-medium">Most Popular</span>
                    </div>
                    @endif

                    <div class="text-center">
                        <h3 class="text-xl font-bold text-primary-text mb-2">{{ $plan->name }}</h3>
                        <p class="text-secondary-text mb-4">{{ $plan->description }}</p>
                        
                        <div class="mb-6">
                            @if($plan->slug === 'free')
                                <div class="text-3xl font-bold text-primary-text">FREE</div>
                                <div class="text-sm text-secondary-text">Up to {{ $plan->max_features }} features</div>
                            @else
                                <div class="text-3xl font-bold text-primary-text">
                                    ${{ $plan->feature_price_monthly }}
                                    <span class="text-lg font-normal text-secondary-text">/feature/month</span>
                                </div>
                                <div class="text-sm text-success">
                                    ${{ $plan->feature_price_yearly }}/feature/year (save 17%)
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2 text-left">
                            @if($plan->slug === 'free')
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    Up to {{ $plan->max_features }} features
                                </div>
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    Basic templates
                                </div>
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    Community support
                                </div>
                            @elseif($plan->slug === 'professional')
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    All standard features
                                </div>
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    Custom domains
                                </div>
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    Email support
                                </div>
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    API access
                                </div>
                            @else
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    All features included
                                </div>
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    White-label solutions
                                </div>
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    Priority support
                                </div>
                                <div class="flex items-center text-sm text-secondary-text">
                                    <x-icon name="check" size="xs" class="mr-2 text-success" alt="Included" />
                                    Advanced security
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <input type="radio" name="selected_plan" value="{{ $plan->id }}" class="hidden plan-radio">
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Step 5: Team Invitations -->
        @if($step === 5)
        <div class="space-y-6">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-primary-text mb-2">Invite Your Team</h2>
                <p class="text-secondary-text text-lg">Add team members to collaborate on your workspace</p>
            </div>

            <div class="bg-card-bg rounded-lg p-6 border border-border-color">
                <form id="team-invitations-form" class="space-y-4">
                    <div id="team-members-container">
                        <!-- Team member invitation rows will be added here -->
                    </div>

                    <button type="button" 
                            id="add-team-member-btn"
                            class="w-full border-2 border-dashed border-border-color rounded-lg p-4 text-secondary-text hover:border-info hover:text-info transition-all duration-300">
                        <div class="flex items-center justify-center space-x-2">
                            <x-icon name="plus" size="md" alt="Add member" />
                            <span>Add Team Member</span>
                        </div>
                    </button>

                    <div class="text-center">
                        <p class="text-sm text-secondary-text">Team members will receive an email invitation to join your workspace</p>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex justify-between items-center mt-8 pt-6 border-t border-border-color">
            <div>
                @if($step > 1)
                <button type="button" 
                        id="prev-btn"
                        class="px-6 py-3 bg-secondary-bg text-primary-text rounded-lg hover:bg-hover-bg transition-all duration-300 flex items-center">
                    <x-icon name="back" size="sm" class="mr-2" alt="Previous" />
                    Previous
                </button>
                @endif
            </div>

            <div class="flex space-x-3">
                @if($step < 5)
                <button type="button" 
                        id="skip-btn"
                        class="px-6 py-3 text-secondary-text hover:text-primary-text transition-all duration-300">
                    Skip
                </button>
                @endif

                <button type="button" 
                        id="next-btn"
                        class="px-6 py-3 bg-info text-white rounded-lg hover:bg-blue-600 transition-all duration-300 flex items-center">
                    @if($step === 5)
                        Complete Setup
                        <x-icon name="check" size="sm" class="ml-2" alt="Complete" />
                    @else
                        Next
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Goal selection functionality
    const goalCards = document.querySelectorAll('.goal-card');
    goalCards.forEach(card => {
        card.addEventListener('click', function() {
            const checkbox = this.querySelector('.goal-checkbox');
            checkbox.checked = !checkbox.checked;
            
            if (checkbox.checked) {
                this.classList.add('ring-2', 'ring-info', 'bg-info', 'bg-opacity-10');
            } else {
                this.classList.remove('ring-2', 'ring-info', 'bg-info', 'bg-opacity-10');
            }
        });
    });

    // Feature selection and cost calculation
    const featureCheckboxes = document.querySelectorAll('.feature-checkbox');
    const totalCostElement = document.getElementById('total-cost');
    
    function updateTotalCost() {
        let total = 0;
        featureCheckboxes.forEach(checkbox => {
            if (checkbox.checked && !checkbox.disabled) {
                total += parseFloat(checkbox.dataset.monthlyPrice || 0);
            }
        });
        if (totalCostElement) {
            totalCostElement.textContent = total.toFixed(2);
        }
    }

    featureCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotalCost);
    });

    // Subscription plan selection
    const planCards = document.querySelectorAll('.subscription-plan');
    planCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selection from other cards
            planCards.forEach(c => c.classList.remove('ring-2', 'ring-info', 'bg-info', 'bg-opacity-10'));
            
            // Add selection to clicked card
            this.classList.add('ring-2', 'ring-info', 'bg-info', 'bg-opacity-10');
            
            // Check the radio button
            const radio = this.querySelector('.plan-radio');
            radio.checked = true;
        });
    });

    // Team member invitation functionality
    const addTeamMemberBtn = document.getElementById('add-team-member-btn');
    const teamMembersContainer = document.getElementById('team-members-container');
    
    if (addTeamMemberBtn && teamMembersContainer) {
        addTeamMemberBtn.addEventListener('click', function() {
            const memberCount = teamMembersContainer.children.length;
            const memberRow = createTeamMemberRow(memberCount);
            teamMembersContainer.appendChild(memberRow);
        });
    }

    function createTeamMemberRow(index) {
        const row = document.createElement('div');
        row.className = 'team-member-row flex items-center space-x-3 p-3 bg-background rounded-lg border border-border-color';
        row.innerHTML = `
            <div class="flex-1">
                <input type="email" 
                       name="team_members[${index}][email]" 
                       class="w-full px-3 py-2 bg-background border border-border-color rounded-lg text-primary-text focus:outline-none focus:ring-2 focus:ring-info focus:border-transparent"
                       placeholder="Email address"
                       required>
            </div>
            <div class="w-32">
                <select name="team_members[${index}][role]" 
                        class="w-full px-3 py-2 bg-background border border-border-color rounded-lg text-primary-text focus:outline-none focus:ring-2 focus:ring-info focus:border-transparent">
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>
            <button type="button" 
                    class="remove-member-btn p-2 text-error hover:bg-error hover:bg-opacity-10 rounded-lg transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        
        // Add remove functionality
        const removeBtn = row.querySelector('.remove-member-btn');
        removeBtn.addEventListener('click', function() {
            row.remove();
        });
        
        return row;
    }

    // Navigation functionality
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const skipBtn = document.getElementById('skip-btn');

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            // Handle form submission and navigation
            const currentStep = {{ $step }};
            // Add your form submission logic here
            console.log('Next button clicked for step:', currentStep);
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            // Handle previous step navigation
            const currentStep = {{ $step }};
            // Add your navigation logic here
            console.log('Previous button clicked for step:', currentStep);
        });
    }

    if (skipBtn) {
        skipBtn.addEventListener('click', function() {
            // Handle skip functionality
            const currentStep = {{ $step }};
            // Add your skip logic here
            console.log('Skip button clicked for step:', currentStep);
        });
    }
});
</script>