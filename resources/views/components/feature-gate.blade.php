@php
    $workspace = $workspace ?? auth()->user()->currentWorkspace ?? null;
    $hasFeature = $workspace && $workspace->hasFeature($feature);
    $featureData = $featureData ?? [
        'name' => ucwords(str_replace(['_', '-'], ' ', $feature)),
        'description' => 'This feature is not available in your current plan.',
        'icon' => 'lock-closed'
    ];
@endphp

@if($hasFeature)
    {{ $slot }}
@else
    <div class="feature-gate" x-data="{ showUpgradeModal: false }">
        <div class="feature-gate-icon">
            @if($featureData['icon'] === 'lock-closed')
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            @endif
        </div>
        
        <h3 class="feature-gate-title">{{ $featureData['name'] }} Feature</h3>
        <p class="feature-gate-description">{{ $featureData['description'] }}</p>
        
        <div class="space-y-3">
            <button @click="showUpgradeModal = true" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Upgrade to Access
            </button>
            
            <a href="{{ route('features.index') }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Learn More
            </a>
        </div>
        
        <!-- Upgrade Modal -->
        <div x-show="showUpgradeModal" class="modal-overlay" @click.self="showUpgradeModal = false">
            <div class="upgrade-modal">
                <div class="modal-header">
                    <h3 class="modal-title">Upgrade Your Plan</h3>
                    <button @click="showUpgradeModal = false" class="modal-close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Unlock {{ $featureData['name'] }}</h4>
                        <p class="text-sm text-gray-600">Get access to this feature and many more with our Professional plan</p>
                    </div>
                    
                    <div class="upgrade-features">
                        <div class="upgrade-feature">
                            <div class="upgrade-feature-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="upgrade-feature-text">{{ $featureData['name'] }} Feature</span>
                        </div>
                        
                        <div class="upgrade-feature">
                            <div class="upgrade-feature-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="upgrade-feature-text">Advanced Analytics</span>
                        </div>
                        
                        <div class="upgrade-feature">
                            <div class="upgrade-feature-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="upgrade-feature-text">Priority Support</span>
                        </div>
                        
                        <div class="upgrade-feature">
                            <div class="upgrade-feature-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="upgrade-feature-text">Unlimited Usage</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-900">Professional Plan</p>
                                <p class="text-xs text-blue-700">Feature-based pricing</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-blue-900">$1<span class="text-sm font-normal">/feature/month</span></p>
                                <p class="text-xs text-blue-700">or $10/feature/year</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button @click="showUpgradeModal = false" class="btn-secondary">Maybe Later</button>
                    <a href="{{ route('subscription.plans') }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Upgrade Now
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif