@extends('layouts.admin')

@section('title', 'Feature Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Feature Management</h1>
                    <p class="text-gray-600 mt-2">
                        Manage features, goals, and plan assignments for your platform.
                    </p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button @click="exportConfiguration()" class="btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Config
                    </button>
                    
                    <button @click="showCreateFeatureModal = true" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Feature
                    </button>
                </div>
            </div>
        </div>

        <div x-data="featureManagement()" x-init="init()" class="space-y-8">
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Features</p>
                            <p class="text-2xl font-semibold text-gray-900" x-text="features.length">{{ $features->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Active Goals</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $goals->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Subscription Plans</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $plans->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Usage This Month</p>
                            <p class="text-2xl font-semibold text-gray-900">12.4K</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="card">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button 
                            @click="activeTab = 'matrix'" 
                            :class="{'border-blue-500 text-blue-600': activeTab === 'matrix', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'matrix'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        >
                            Feature Matrix
                        </button>
                        <button 
                            @click="activeTab = 'features'" 
                            :class="{'border-blue-500 text-blue-600': activeTab === 'features', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'features'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        >
                            Feature List
                        </button>
                        <button 
                            @click="activeTab = 'goals'" 
                            :class="{'border-blue-500 text-blue-600': activeTab === 'goals', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'goals'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        >
                            Goals
                        </button>
                        <button 
                            @click="activeTab = 'analytics'" 
                            :class="{'border-blue-500 text-blue-600': activeTab === 'analytics', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'analytics'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        >
                            Analytics
                        </button>
                    </nav>
                </div>

                <!-- Feature Matrix Tab -->
                <div x-show="activeTab === 'matrix'" class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Plan vs Feature Matrix</h2>
                        <div class="flex items-center space-x-2">
                            <button @click="bulkSelectAll()" class="btn-sm btn-secondary">Select All</button>
                            <button @click="bulkSelectNone()" class="btn-sm btn-secondary">Select None</button>
                            <button @click="saveBulkChanges()" class="btn-sm btn-primary" :disabled="!hasUnsavedChanges">
                                Save Changes
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Feature
                                    </th>
                                    <template x-for="plan in plans" :key="plan.id">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="text-center">
                                                <div x-text="plan.name" class="font-semibold"></div>
                                                <div class="text-xs text-gray-400" x-text="plan.pricing_type === 'feature_based' ? '$' + plan.feature_price_monthly + '/mo' : 'Flat Rate'"></div>
                                            </div>
                                        </th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="feature in features" :key="feature.key">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div 
                                                    class="w-3 h-3 rounded-full mr-3"
                                                    :style="`background-color: ${feature.goal?.color || '#6B7280'}`"
                                                ></div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900" x-text="feature.name"></div>
                                                    <div class="text-xs text-gray-500" x-text="feature.goal?.name"></div>
                                                    <div class="text-xs">
                                                        <span :class="{'badge-primary': feature.type === 'binary', 'badge-success': feature.type === 'quota', 'badge-warning': feature.type === 'tiered'}" class="badge" x-text="feature.type"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <template x-for="plan in plans" :key="plan.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex flex-col items-center space-y-1">
                                                    <label class="inline-flex items-center">
                                                        <input 
                                                            type="checkbox" 
                                                            class="form-checkbox"
                                                            :checked="getFeaturePlanStatus(feature.key, plan.id)"
                                                            @change="toggleFeaturePlan(feature.key, plan.id)"
                                                        >
                                                    </label>
                                                    <template x-if="feature.type === 'quota' && getFeaturePlanStatus(feature.key, plan.id)">
                                                        <input 
                                                            type="number"
                                                            class="w-16 text-xs form-input py-1 px-2"
                                                            placeholder="Limit"
                                                            :value="getFeatureQuotaLimit(feature.key, plan.id)"
                                                            @input="updateFeatureQuotaLimit(feature.key, plan.id, $event.target.value)"
                                                        >
                                                    </template>
                                                </div>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Feature List Tab -->
                <div x-show="activeTab === 'features'" class="p-6">
                    <div class="space-y-6">
                        <template x-for="goal in goals" :key="goal.key">
                            <div class="border rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <div 
                                        class="w-4 h-4 rounded-full mr-3"
                                        :style="`background-color: ${goal.color}`"
                                    ></div>
                                    <h3 class="text-lg font-semibold text-gray-900" x-text="goal.name"></h3>
                                    <span class="ml-2 badge-secondary" x-text="getFeaturesByGoal(goal.key).length + ' features'"></span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <template x-for="feature in getFeaturesByGoal(goal.key)" :key="feature.key">
                                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <div class="flex items-start justify-between mb-2">
                                                <h4 class="font-medium text-gray-900" x-text="feature.name"></h4>
                                                <div class="flex items-center space-x-1">
                                                    <button @click="editFeature(feature)" class="p-1 text-gray-400 hover:text-blue-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button @click="toggleFeatureStatus(feature)" class="p-1 text-gray-400 hover:text-green-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2" x-text="feature.description"></p>
                                            <div class="flex items-center justify-between">
                                                <span 
                                                    :class="{'badge-primary': feature.type === 'binary', 'badge-success': feature.type === 'quota', 'badge-warning': feature.type === 'tiered'}" 
                                                    class="badge text-xs"
                                                    x-text="feature.type"
                                                ></span>
                                                <span class="text-xs text-gray-500" x-text="getPlanCount(feature.key) + ' plans'"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Goals Tab -->
                <div x-show="activeTab === 'goals'" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template x-for="goal in goals" :key="goal.key">
                            <div class="border rounded-lg p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-center mb-4">
                                    <div 
                                        class="w-6 h-6 rounded-full mr-3"
                                        :style="`background-color: ${goal.color}`"
                                    ></div>
                                    <h3 class="text-lg font-semibold text-gray-900" x-text="goal.name"></h3>
                                </div>
                                
                                <p class="text-gray-600 mb-4" x-text="goal.description"></p>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">Features</span>
                                        <span class="font-medium" x-text="getFeaturesByGoal(goal.key).length"></span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">Category</span>
                                        <span class="font-medium capitalize" x-text="goal.category"></span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">Status</span>
                                        <span :class="goal.is_active ? 'text-green-600' : 'text-red-600'" class="font-medium">
                                            <span x-text="goal.is_active ? 'Active' : 'Inactive'"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Analytics Tab -->
                <div x-show="activeTab === 'analytics'" class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Feature Usage Chart -->
                        <div class="border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Used Features</h3>
                            <div class="space-y-4">
                                <template x-for="usage in usageData.most_used" :key="usage.feature_key">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="font-medium" x-text="getFeatureName(usage.feature_key)"></span>
                                                <span class="text-gray-500" x-text="usage.usage_count.toLocaleString()"></span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                <div 
                                                    class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                    :style="`width: ${(usage.usage_count / Math.max(...usageData.most_used.map(u => u.usage_count))) * 100}%`"
                                                ></div>
                                            </div>
                                        </div>
                                        <div class="ml-4 text-right">
                                            <span 
                                                :class="usage.growth > 0 ? 'text-green-600' : 'text-red-600'"
                                                class="text-sm font-medium"
                                                x-text="(usage.growth > 0 ? '+' : '') + usage.growth + '%'"
                                            ></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Plan Distribution -->
                        <div class="border rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Usage by Plan</h3>
                            <div class="space-y-4">
                                <template x-for="(data, planName) in usageData.by_plan" :key="planName">
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-900" x-text="planName"></h4>
                                            <p class="text-sm text-gray-600" x-text="data.active_users + ' active users'"></p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-semibold text-gray-900" x-text="data.total_usage.toLocaleString()"></div>
                                            <div class="text-sm text-gray-500">total usage</div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Feature Modal -->
    <div x-show="showCreateFeatureModal" class="modal-overlay" @click.self="showCreateFeatureModal = false">
        <div class="modal-content max-w-lg">
            <div class="modal-header">
                <h3 class="modal-title">Create New Feature</h3>
                <button @click="showCreateFeatureModal = false" class="modal-close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="modal-body">
                <form @submit.prevent="createFeature()">
                    <div class="space-y-4">
                        <div class="form-group">
                            <label class="form-label">Feature Key</label>
                            <input type="text" x-model="newFeature.key" class="form-input" placeholder="feature_key" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Feature Name</label>
                            <input type="text" x-model="newFeature.name" class="form-input" placeholder="Feature Name" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea x-model="newFeature.description" class="form-textarea" placeholder="Feature description"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Goal</label>
                            <select x-model="newFeature.goal_key" class="form-select" required>
                                <option value="">Select Goal</option>
                                <template x-for="goal in goals" :key="goal.key">
                                    <option :value="goal.key" x-text="goal.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select x-model="newFeature.type" class="form-select" required>
                                <option value="binary">Binary (On/Off)</option>
                                <option value="quota">Quota-based</option>
                                <option value="tiered">Tiered</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <input type="text" x-model="newFeature.category" class="form-input" placeholder="Category" required>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" @click="showCreateFeatureModal = false" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Create Feature</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function featureManagement() {
    return {
        activeTab: 'matrix',
        features: @json($features),
        goals: @json($goals),
        plans: @json($plans),
        featureMatrix: {},
        unsavedChanges: {},
        hasUnsavedChanges: false,
        showCreateFeatureModal: false,
        usageData: {},
        newFeature: {
            key: '',
            name: '',
            description: '',
            goal_key: '',
            type: 'binary',
            category: ''
        },
        
        async init() {
            await this.loadFeatureMatrix();
            await this.loadUsageData();
        },
        
        async loadFeatureMatrix() {
            try {
                const response = await Mewayz.api('/api/admin/feature-management/matrix');
                this.featureMatrix = response.data.matrix.reduce((acc, feature) => {
                    acc[feature.key] = feature.plans;
                    return acc;
                }, {});
            } catch (error) {
                console.error('Failed to load feature matrix:', error);
            }
        },
        
        async loadUsageData() {
            try {
                const response = await Mewayz.api('/api/admin/feature-management/usage');
                this.usageData = response.data;
            } catch (error) {
                console.error('Failed to load usage data:', error);
            }
        },
        
        getFeaturePlanStatus(featureKey, planId) {
            return this.featureMatrix[featureKey]?.[planId]?.is_included || false;
        },
        
        getFeatureQuotaLimit(featureKey, planId) {
            return this.featureMatrix[featureKey]?.[planId]?.quota_limit || '';
        },
        
        toggleFeaturePlan(featureKey, planId) {
            if (!this.featureMatrix[featureKey]) {
                this.featureMatrix[featureKey] = {};
            }
            if (!this.featureMatrix[featureKey][planId]) {
                this.featureMatrix[featureKey][planId] = {};
            }
            
            const currentStatus = this.featureMatrix[featureKey][planId].is_included || false;
            this.featureMatrix[featureKey][planId].is_included = !currentStatus;
            
            this.markUnsavedChange(featureKey, planId);
        },
        
        updateFeatureQuotaLimit(featureKey, planId, limit) {
            if (!this.featureMatrix[featureKey]) {
                this.featureMatrix[featureKey] = {};
            }
            if (!this.featureMatrix[featureKey][planId]) {
                this.featureMatrix[featureKey][planId] = {};
            }
            
            this.featureMatrix[featureKey][planId].quota_limit = limit ? parseInt(limit) : null;
            this.markUnsavedChange(featureKey, planId);
        },
        
        markUnsavedChange(featureKey, planId) {
            if (!this.unsavedChanges[planId]) {
                this.unsavedChanges[planId] = [];
            }
            
            const existing = this.unsavedChanges[planId].find(f => f.key === featureKey);
            if (!existing) {
                this.unsavedChanges[planId].push({
                    key: featureKey,
                    is_included: this.featureMatrix[featureKey][planId].is_included,
                    quota_limit: this.featureMatrix[featureKey][planId].quota_limit,
                });
            } else {
                existing.is_included = this.featureMatrix[featureKey][planId].is_included;
                existing.quota_limit = this.featureMatrix[featureKey][planId].quota_limit;
            }
            
            this.hasUnsavedChanges = Object.keys(this.unsavedChanges).length > 0;
        },
        
        async saveBulkChanges() {
            if (!this.hasUnsavedChanges) return;
            
            try {
                const updates = Object.entries(this.unsavedChanges).map(([planId, features]) => ({
                    plan_id: parseInt(planId),
                    features: features
                }));
                
                const response = await Mewayz.api('/api/admin/feature-management/bulk-update', {
                    method: 'POST',
                    body: JSON.stringify({ updates })
                });
                
                if (response.success) {
                    Mewayz.notify('Changes saved successfully', 'success');
                    this.unsavedChanges = {};
                    this.hasUnsavedChanges = false;
                }
            } catch (error) {
                Mewayz.notify('Failed to save changes', 'error');
            }
        },
        
        bulkSelectAll() {
            this.features.forEach(feature => {
                this.plans.forEach(plan => {
                    if (!this.getFeaturePlanStatus(feature.key, plan.id)) {
                        this.toggleFeaturePlan(feature.key, plan.id);
                    }
                });
            });
        },
        
        bulkSelectNone() {
            this.features.forEach(feature => {
                this.plans.forEach(plan => {
                    if (this.getFeaturePlanStatus(feature.key, plan.id)) {
                        this.toggleFeaturePlan(feature.key, plan.id);
                    }
                });
            });
        },
        
        getFeaturesByGoal(goalKey) {
            return this.features.filter(f => f.goal?.key === goalKey);
        },
        
        getPlanCount(featureKey) {
            if (!this.featureMatrix[featureKey]) return 0;
            return Object.values(this.featureMatrix[featureKey]).filter(p => p.is_included).length;
        },
        
        getFeatureName(featureKey) {
            const feature = this.features.find(f => f.key === featureKey);
            return feature ? feature.name : featureKey;
        },
        
        async createFeature() {
            try {
                const response = await Mewayz.api('/api/admin/feature-management/features', {
                    method: 'POST',
                    body: JSON.stringify(this.newFeature)
                });
                
                if (response.success) {
                    this.features.push(response.data.feature);
                    this.showCreateFeatureModal = false;
                    this.newFeature = { key: '', name: '', description: '', goal_key: '', type: 'binary', category: '' };
                    Mewayz.notify('Feature created successfully', 'success');
                }
            } catch (error) {
                Mewayz.notify('Failed to create feature', 'error');
            }
        },
        
        async exportConfiguration() {
            try {
                const response = await Mewayz.api('/api/admin/feature-management/export');
                if (response.success) {
                    const blob = new Blob([JSON.stringify(response.data, null, 2)], { type: 'application/json' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `mewayz-feature-config-${new Date().toISOString().split('T')[0]}.json`;
                    a.click();
                    window.URL.revokeObjectURL(url);
                }
            } catch (error) {
                Mewayz.notify('Failed to export configuration', 'error');
            }
        }
    }
}
</script>
@endsection