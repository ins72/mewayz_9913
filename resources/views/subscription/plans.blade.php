@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Choose Your Plan
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Select the features you need and pay only for what you use. 
                All plans include 14-day free trial with full access.
            </p>
        </div>

        <div x-data="subscriptionPlans()" x-init="init()" class="space-y-8">
            
            <!-- Billing Toggle -->
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-gray-700">Monthly</span>
                    <button 
                        @click="toggleBillingCycle()"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        :class="billingCycle === 'yearly' ? 'bg-blue-600' : 'bg-gray-200'"
                    >
                        <span 
                            class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            :class="billingCycle === 'yearly' ? 'translate-x-5' : 'translate-x-0'"
                        ></span>
                    </button>
                    <span class="text-sm font-medium text-gray-700">
                        Yearly
                        <span class="ml-1 text-green-600 font-semibold">Save up to 20%</span>
                    </span>
                </div>
            </div>

            <!-- Feature Selection -->
            <div class="card p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Select Features You Need</h2>
                
                <div class="space-y-6">
                    <template x-for="goal in goals" :key="goal.key">
                        <div class="border rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <div 
                                    class="w-4 h-4 rounded-full mr-3"
                                    :style="`background-color: ${goal.color}`"
                                ></div>
                                <h3 class="text-lg font-semibold text-gray-900" x-text="goal.name"></h3>
                                <div class="ml-auto">
                                    <label class="inline-flex items-center">
                                        <input 
                                            type="checkbox" 
                                            class="form-checkbox"
                                            @change="toggleGoal(goal.key)"
                                            :checked="isGoalSelected(goal.key)"
                                        >
                                        <span class="ml-2 text-sm text-gray-600">Select All</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <template x-for="feature in getFeaturesByGoal(goal.key)" :key="feature.key">
                                    <div class="flex items-center space-x-3 p-3 rounded-lg border hover:bg-gray-50">
                                        <input 
                                            type="checkbox" 
                                            class="form-checkbox"
                                            :value="feature.key"
                                            x-model="selectedFeatures"
                                            @change="updatePricing()"
                                        >
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900" x-text="feature.name"></h4>
                                            <p class="text-sm text-gray-600" x-text="feature.description"></p>
                                            <span 
                                                :class="{'badge-primary': feature.type === 'binary', 'badge-success': feature.type === 'quota', 'badge-warning': feature.type === 'tiered'}" 
                                                class="badge text-xs mt-1"
                                                x-text="feature.type"
                                            ></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Plan Comparison -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <template x-for="plan in plans" :key="plan.id">
                    <div class="card relative" :class="{'ring-2 ring-blue-500': plan.id === recommendedPlan}">
                        <!-- Recommended Badge -->
                        <div x-show="plan.id === recommendedPlan" class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                            <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Recommended
                            </span>
                        </div>
                        
                        <div class="p-6">
                            <!-- Plan Header -->
                            <div class="text-center mb-6">
                                <h3 class="text-xl font-semibold text-gray-900" x-text="plan.name"></h3>
                                <p class="text-gray-600 mt-2" x-text="plan.description"></p>
                                
                                <!-- Pricing -->
                                <div class="mt-4">
                                    <div class="flex items-center justify-center">
                                        <span class="text-3xl font-bold text-gray-900">
                                            $<span x-text="getPlanPrice(plan.id)"></span>
                                        </span>
                                        <span class="text-gray-600 ml-2" x-text="'/' + billingCycle"></span>
                                    </div>
                                    <template x-if="billingCycle === 'yearly' && getPlanSavings(plan.id) > 0">
                                        <div class="text-sm text-green-600 mt-1">
                                            Save $<span x-text="getPlanSavings(plan.id)"></span> per year
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Feature List -->
                            <div class="mb-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">
                                    Selected Features (<span x-text="selectedFeatures.length"></span>)
                                </h4>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    <template x-for="featureKey in selectedFeatures" :key="featureKey">
                                        <div class="flex items-center text-sm">
                                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span x-text="getFeatureName(featureKey)"></span>
                                        </div>
                                    </template>
                                </div>
                                
                                <template x-if="selectedFeatures.length === 0">
                                    <p class="text-gray-500 text-sm">No features selected</p>
                                </template>
                            </div>

                            <!-- Plan Benefits -->
                            <div class="mb-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Plan Benefits</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>14-day free trial</span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>24/7 support</span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>Cancel anytime</span>
                                    </div>
                                    <template x-if="plan.includes_whitelabel">
                                        <div class="flex items-center text-sm">
                                            <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span>White-label branding</span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <button 
                                @click="selectPlan(plan.id)"
                                class="w-full btn-primary"
                                :disabled="selectedFeatures.length === 0"
                                :class="{'opacity-50 cursor-not-allowed': selectedFeatures.length === 0}"
                            >
                                <template x-if="currentSubscription && currentSubscription.plan_id === plan.id">
                                    <span>Current Plan</span>
                                </template>
                                <template x-if="!currentSubscription">
                                    <span>Start Free Trial</span>
                                </template>
                                <template x-if="currentSubscription && currentSubscription.plan_id !== plan.id">
                                    <span>Switch to This Plan</span>
                                </template>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Pricing Calculator -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Pricing Calculator</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Selected Features Summary -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Selected Features</h3>
                        <div class="space-y-3">
                            <template x-for="featureKey in selectedFeatures" :key="featureKey">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-gray-900" x-text="getFeatureName(featureKey)"></h4>
                                        <p class="text-sm text-gray-600" x-text="getFeatureDescription(featureKey)"></p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">
                                            $<span x-text="getFeaturePrice()"></span>/<span x-text="billingCycle"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="selectedFeatures.length === 0">
                                <p class="text-gray-500 text-center py-8">Select features to see pricing</p>
                            </template>
                        </div>
                    </div>

                    <!-- Pricing Summary -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Selected Features (<span x-text="selectedFeatures.length"></span>)</span>
                                <span class="font-medium">$<span x-text="getTotalFeatureCost()"></span></span>
                            </div>
                            <div class="flex justify-between py-2 border-t">
                                <span class="text-gray-600">Billing Cycle</span>
                                <span class="font-medium capitalize" x-text="billingCycle"></span>
                            </div>
                            <template x-if="billingCycle === 'yearly'">
                                <div class="flex justify-between py-2 text-green-600">
                                    <span>Yearly Savings</span>
                                    <span class="font-medium">-$<span x-text="getYearlySavings()"></span></span>
                                </div>
                            </template>
                            <div class="flex justify-between py-2 border-t text-lg font-semibold">
                                <span>Total per <span x-text="billingCycle"></span></span>
                                <span>$<span x-text="getTotalCost()"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Frequently Asked Questions</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">How does feature-based pricing work?</h3>
                        <p class="text-gray-600 text-sm">You only pay for the features you actually use. Select the features you need and get a custom price based on your selection.</p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Can I change my plan anytime?</h3>
                        <p class="text-gray-600 text-sm">Yes, you can upgrade or downgrade your plan at any time. Changes are prorated and take effect immediately.</p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">What happens during the free trial?</h3>
                        <p class="text-gray-600 text-sm">You get full access to all selected features for 14 days. No credit card required to start.</p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Are there any setup fees?</h3>
                        <p class="text-gray-600 text-sm">No setup fees, no hidden costs. You only pay for the features you use.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function subscriptionPlans() {
    return {
        goals: @json($goals),
        features: @json($features),
        plans: @json($plans),
        currentSubscription: @json($currentSubscription),
        selectedFeatures: [],
        billingCycle: 'monthly',
        recommendedPlan: null,
        pricingData: {},
        
        init() {
            this.calculateRecommendedPlan();
        },
        
        toggleBillingCycle() {
            this.billingCycle = this.billingCycle === 'monthly' ? 'yearly' : 'monthly';
            this.updatePricing();
        },
        
        getFeaturesByGoal(goalKey) {
            return this.features.filter(f => f.goal?.key === goalKey);
        },
        
        isGoalSelected(goalKey) {
            const goalFeatures = this.getFeaturesByGoal(goalKey);
            return goalFeatures.every(f => this.selectedFeatures.includes(f.key));
        },
        
        toggleGoal(goalKey) {
            const goalFeatures = this.getFeaturesByGoal(goalKey);
            const isSelected = this.isGoalSelected(goalKey);
            
            if (isSelected) {
                // Remove all goal features
                goalFeatures.forEach(f => {
                    const index = this.selectedFeatures.indexOf(f.key);
                    if (index > -1) {
                        this.selectedFeatures.splice(index, 1);
                    }
                });
            } else {
                // Add all goal features
                goalFeatures.forEach(f => {
                    if (!this.selectedFeatures.includes(f.key)) {
                        this.selectedFeatures.push(f.key);
                    }
                });
            }
            
            this.updatePricing();
        },
        
        async updatePricing() {
            if (this.selectedFeatures.length === 0) {
                this.pricingData = {};
                return;
            }
            
            try {
                const promises = this.plans.map(plan => 
                    Mewayz.api('/api/subscription/calculate-pricing', {
                        method: 'POST',
                        body: JSON.stringify({
                            plan_id: plan.id,
                            features: this.selectedFeatures,
                            billing_cycle: this.billingCycle
                        })
                    })
                );
                
                const responses = await Promise.all(promises);
                
                responses.forEach((response, index) => {
                    if (response.success) {
                        this.pricingData[this.plans[index].id] = response.data;
                    }
                });
                
                this.calculateRecommendedPlan();
            } catch (error) {
                console.error('Failed to update pricing:', error);
            }
        },
        
        calculateRecommendedPlan() {
            if (this.selectedFeatures.length === 0) {
                this.recommendedPlan = this.plans[1]?.id; // Default to middle plan
                return;
            }
            
            // Find the plan with the best value score
            let bestPlan = null;
            let bestValue = 0;
            
            this.plans.forEach(plan => {
                const pricing = this.pricingData[plan.id];
                if (pricing) {
                    const supportedFeatures = this.selectedFeatures.filter(f => 
                        plan.features.some(pf => pf.key === f)
                    );
                    const supportedCount = supportedFeatures.length;
                    const totalCost = pricing.costs.selected.amount;
                    const value = supportedCount / Math.max(1, totalCost / 100);
                    
                    if (value > bestValue) {
                        bestValue = value;
                        bestPlan = plan.id;
                    }
                }
            });
            
            this.recommendedPlan = bestPlan || this.plans[1]?.id;
        },
        
        getPlanPrice(planId) {
            const pricing = this.pricingData[planId];
            if (!pricing) return '0';
            
            return pricing.costs.selected.amount.toFixed(2);
        },
        
        getPlanSavings(planId) {
            const pricing = this.pricingData[planId];
            if (!pricing) return 0;
            
            return pricing.savings.yearly_vs_monthly.amount.toFixed(2);
        },
        
        getFeatureName(featureKey) {
            const feature = this.features.find(f => f.key === featureKey);
            return feature ? feature.name : featureKey;
        },
        
        getFeatureDescription(featureKey) {
            const feature = this.features.find(f => f.key === featureKey);
            return feature ? feature.description : '';
        },
        
        getFeaturePrice() {
            if (this.selectedFeatures.length === 0) return '0';
            
            const plan = this.plans.find(p => p.id === this.recommendedPlan);
            if (!plan) return '0';
            
            const price = this.billingCycle === 'yearly' ? 
                plan.feature_price_yearly : 
                plan.feature_price_monthly;
            
            return price.toFixed(2);
        },
        
        getTotalFeatureCost() {
            if (this.selectedFeatures.length === 0) return '0';
            
            const plan = this.plans.find(p => p.id === this.recommendedPlan);
            if (!plan) return '0';
            
            const price = this.billingCycle === 'yearly' ? 
                plan.feature_price_yearly : 
                plan.feature_price_monthly;
            
            return (price * this.selectedFeatures.length).toFixed(2);
        },
        
        getYearlySavings() {
            if (this.selectedFeatures.length === 0) return '0';
            
            const plan = this.plans.find(p => p.id === this.recommendedPlan);
            if (!plan) return '0';
            
            const monthlyTotal = plan.feature_price_monthly * this.selectedFeatures.length * 12;
            const yearlyTotal = plan.feature_price_yearly * this.selectedFeatures.length;
            
            return (monthlyTotal - yearlyTotal).toFixed(2);
        },
        
        getTotalCost() {
            return this.getTotalFeatureCost();
        },
        
        async selectPlan(planId) {
            if (this.selectedFeatures.length === 0) {
                Mewayz.notify('Please select at least one feature', 'warning');
                return;
            }
            
            // Store selection in localStorage for the checkout process
            localStorage.setItem('selectedPlan', JSON.stringify({
                plan_id: planId,
                features: this.selectedFeatures,
                billing_cycle: this.billingCycle
            }));
            
            // Redirect to checkout or subscription flow
            window.location.href = '/subscription/checkout';
        }
    }
}
</script>
@endsection