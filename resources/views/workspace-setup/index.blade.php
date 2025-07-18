@extends('layouts.app')

@section('content')
<x-workspace-setup-wizard 
    :step="$step"
    :workspace="$workspace"
    :goals="$goals"
    :features="$features"
    :subscription-plans="$subscriptionPlans"
/>
@endsection

@push('styles')
<style>
    .goal-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }
    
    .goal-card.selected {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
        border-color: #3B82F6;
    }
    
    .subscription-plan:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }
    
    .subscription-plan.selected {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
        border-color: #3B82F6;
    }
    
    .feature-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .team-member-row {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .progress-bar {
        background: linear-gradient(90deg, #3B82F6 0%, #1D4ED8 100%);
        transition: width 0.5s ease-in-out;
    }
    
    .wizard-content {
        min-height: 600px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .step-indicator {
        position: relative;
    }
    
    .step-indicator::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 100%;
        width: 30px;
        height: 2px;
        background: #374151;
        transform: translateY(-50%);
    }
    
    .step-indicator.active::after {
        background: #3B82F6;
    }
    
    .feature-category {
        border-left: 4px solid #3B82F6;
        padding-left: 1rem;
        margin-bottom: 2rem;
    }
    
    .pricing-summary {
        background: linear-gradient(135deg, #1F2937 0%, #374151 100%);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    
    .success-animation {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '{{ url("/api/workspace-setup") }}';
    let currentWorkspaceId = null;
    let currentStep = {{ $step }};
    
    // Initialize wizard
    initializeWizard();
    
    function initializeWizard() {
        // Set up goal selection
        setupGoalSelection();
        
        // Set up feature selection
        setupFeatureSelection();
        
        // Set up plan selection
        setupPlanSelection();
        
        // Set up team invitations
        setupTeamInvitations();
        
        // Set up navigation
        setupNavigation();
    }
    
    function setupGoalSelection() {
        const goalCards = document.querySelectorAll('.goal-card');
        goalCards.forEach(card => {
            card.addEventListener('click', function() {
                const checkbox = this.querySelector('.goal-checkbox');
                checkbox.checked = !checkbox.checked;
                
                if (checkbox.checked) {
                    this.classList.add('selected');
                } else {
                    this.classList.remove('selected');
                }
                
                // Update available features when goals change
                updateAvailableFeatures();
            });
        });
    }
    
    function setupFeatureSelection() {
        const featureCheckboxes = document.querySelectorAll('.feature-checkbox');
        featureCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updatePricingCalculation();
            });
        });
    }
    
    function setupPlanSelection() {
        const planCards = document.querySelectorAll('.subscription-plan');
        planCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove selection from other cards
                planCards.forEach(c => c.classList.remove('selected'));
                
                // Add selection to clicked card
                this.classList.add('selected');
                
                // Check the radio button
                const radio = this.querySelector('.plan-radio');
                radio.checked = true;
                
                // Update pricing display
                updatePlanPricing();
            });
        });
    }
    
    function setupTeamInvitations() {
        const addTeamMemberBtn = document.getElementById('add-team-member-btn');
        const teamMembersContainer = document.getElementById('team-members-container');
        
        if (addTeamMemberBtn && teamMembersContainer) {
            addTeamMemberBtn.addEventListener('click', function() {
                const memberCount = teamMembersContainer.children.length;
                const memberRow = createTeamMemberRow(memberCount);
                teamMembersContainer.appendChild(memberRow);
            });
        }
    }
    
    function setupNavigation() {
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        const skipBtn = document.getElementById('skip-btn');
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                processCurrentStep();
            });
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                navigateToStep(currentStep - 1);
            });
        }
        
        if (skipBtn) {
            skipBtn.addEventListener('click', function() {
                navigateToStep(currentStep + 1);
            });
        }
    }
    
    function updateAvailableFeatures() {
        const selectedGoals = Array.from(document.querySelectorAll('.goal-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedGoals.length === 0) return;
        
        fetch(`${API_BASE}/available-features`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ goal_ids: selectedGoals })
        })
        .then(response => response.json())
        .then(data => {
            if (data.features) {
                // Update features display
                console.log('Available features:', data.features);
            }
        })
        .catch(error => {
            console.error('Error fetching available features:', error);
        });
    }
    
    function updatePricingCalculation() {
        const selectedFeatures = Array.from(document.querySelectorAll('.feature-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedFeatures.length === 0) return;
        
        fetch(`${API_BASE}/calculate-pricing`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                feature_ids: selectedFeatures,
                billing_cycle: 'monthly'
            })
        })
        .then(response => response.json())
        .then(data => {
            const totalCostElement = document.getElementById('total-cost');
            if (totalCostElement) {
                totalCostElement.textContent = data.current_total.toFixed(2);
            }
        })
        .catch(error => {
            console.error('Error calculating pricing:', error);
        });
    }
    
    function updatePlanPricing() {
        const selectedPlan = document.querySelector('.plan-radio:checked');
        if (selectedPlan) {
            const planCard = selectedPlan.closest('.subscription-plan');
            const planSlug = planCard.dataset.planSlug;
            
            // Update pricing display based on selected plan
            console.log('Selected plan:', planSlug);
        }
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
    
    function processCurrentStep() {
        switch (currentStep) {
            case 1:
                processStep1();
                break;
            case 2:
                processStep2();
                break;
            case 3:
                processStep3();
                break;
            case 4:
                processStep4();
                break;
            case 5:
                processStep5();
                break;
        }
    }
    
    function processStep1() {
        const form = document.getElementById('workspace-info-form');
        if (!form) return;
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        fetch(`${API_BASE}/step-1`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentWorkspaceId = data.workspace_id;
                navigateToStep(data.next_step);
            }
        })
        .catch(error => {
            console.error('Error processing step 1:', error);
            showError('Failed to save workspace information. Please try again.');
        });
    }
    
    function processStep2() {
        const selectedGoals = Array.from(document.querySelectorAll('.goal-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedGoals.length === 0) {
            showError('Please select at least one goal for your workspace.');
            return;
        }
        
        const data = {
            workspace_id: currentWorkspaceId,
            selected_goals: selectedGoals
        };
        
        fetch(`${API_BASE}/step-2`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                navigateToStep(data.next_step);
            }
        })
        .catch(error => {
            console.error('Error processing step 2:', error);
            showError('Failed to save goal selection. Please try again.');
        });
    }
    
    function processStep3() {
        const selectedFeatures = Array.from(document.querySelectorAll('.feature-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedFeatures.length === 0) {
            showError('Please select at least one feature for your workspace.');
            return;
        }
        
        const data = {
            workspace_id: currentWorkspaceId,
            selected_features: selectedFeatures
        };
        
        fetch(`${API_BASE}/step-3`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                navigateToStep(data.next_step);
            }
        })
        .catch(error => {
            console.error('Error processing step 3:', error);
            showError('Failed to save feature selection. Please try again.');
        });
    }
    
    function processStep4() {
        const selectedPlan = document.querySelector('.plan-radio:checked');
        
        if (!selectedPlan) {
            showError('Please select a subscription plan.');
            return;
        }
        
        const data = {
            workspace_id: currentWorkspaceId,
            selected_plan: selectedPlan.value,
            billing_cycle: 'monthly' // Default to monthly
        };
        
        fetch(`${API_BASE}/step-4`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                navigateToStep(data.next_step);
            }
        })
        .catch(error => {
            console.error('Error processing step 4:', error);
            showError('Failed to save subscription plan. Please try again.');
        });
    }
    
    function processStep5() {
        const teamMemberRows = document.querySelectorAll('.team-member-row');
        const teamMembers = [];
        
        teamMemberRows.forEach(row => {
            const email = row.querySelector('input[type="email"]').value;
            const role = row.querySelector('select').value;
            
            if (email) {
                teamMembers.push({ email, role });
            }
        });
        
        const data = {
            workspace_id: currentWorkspaceId,
            team_members: teamMembers
        };
        
        fetch(`${API_BASE}/step-5`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Workspace setup completed successfully!');
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error processing step 5:', error);
            showError('Failed to complete workspace setup. Please try again.');
        });
    }
    
    function navigateToStep(step) {
        window.location.href = `{{ route('workspace-setup.index') }}?step=${step}&workspace_id=${currentWorkspaceId}`;
    }
    
    function showError(message) {
        // Create and show error notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-error text-white px-6 py-3 rounded-lg shadow-lg z-50';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
    
    function showSuccess(message) {
        // Create and show success notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-success text-white px-6 py-3 rounded-lg shadow-lg z-50';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
});
</script>
@endpush