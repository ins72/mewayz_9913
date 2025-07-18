@extends('layouts.app')

@section('title', 'Cancel Subscription')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div x-data="cancellationFlow()" x-init="init()" class="space-y-8">
            
            <!-- Progress Steps -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div 
                                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                                :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'"
                            >
                                1
                            </div>
                            <span class="text-sm font-medium text-gray-900">Tell us why</span>
                        </div>
                        <div class="w-8 h-0.5 bg-gray-200"></div>
                        <div class="flex items-center space-x-2">
                            <div 
                                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                                :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'"
                            >
                                2
                            </div>
                            <span class="text-sm font-medium text-gray-900">Special offers</span>
                        </div>
                        <div class="w-8 h-0.5 bg-gray-200"></div>
                        <div class="flex items-center space-x-2">
                            <div 
                                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                                :class="step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'"
                            >
                                3
                            </div>
                            <span class="text-sm font-medium text-gray-900">Confirm</span>
                        </div>
                    </div>
                    <button @click="goBack()" class="text-blue-600 hover:text-blue-800 font-medium">
                        ← Back to Dashboard
                    </button>
                </div>
            </div>

            <!-- Step 1: Reason Collection -->
            <div x-show="step === 1" class="bg-white rounded-lg shadow-sm p-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">We're sorry to see you go</h1>
                    <p class="text-gray-600">Help us understand why you're cancelling so we can improve our service.</p>
                </div>

                <div class="max-w-2xl mx-auto">
                    <div class="space-y-4">
                        <template x-for="reason in cancellationReasons" :key="reason.key">
                            <label class="flex items-start space-x-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="selectedReason === reason.key ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                <input 
                                    type="radio" 
                                    :value="reason.key" 
                                    x-model="selectedReason"
                                    class="form-radio mt-1"
                                >
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900" x-text="reason.title"></h3>
                                    <p class="text-sm text-gray-600" x-text="reason.description"></p>
                                </div>
                            </label>
                        </template>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Additional feedback (optional)
                        </label>
                        <textarea 
                            x-model="feedback"
                            rows="4" 
                            class="form-textarea w-full"
                            placeholder="Tell us more about your experience..."
                        ></textarea>
                    </div>

                    <div class="mt-8 flex justify-center">
                        <button 
                            @click="processCancellationReason()"
                            :disabled="!selectedReason || processing"
                            class="btn-primary"
                            :class="{'opacity-50 cursor-not-allowed': !selectedReason || processing}"
                        >
                            <span x-show="!processing">Continue</span>
                            <span x-show="processing">Processing...</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Retention Offers -->
            <div x-show="step === 2" class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Wait! We have something special for you</h1>
                        <p class="text-gray-600">Before you go, check out these exclusive offers just for you.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                        <template x-for="offer in retentionOffers" :key="offer.type">
                            <div class="border rounded-lg p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2" x-text="offer.title"></h3>
                                        <p class="text-gray-600 text-sm" x-text="offer.description"></p>
                                    </div>
                                    <div class="ml-4">
                                        <template x-if="offer.type === 'discount'">
                                            <div class="text-right">
                                                <div class="text-2xl font-bold text-green-600" x-text="offer.discount_percentage + '%'"></div>
                                                <div class="text-sm text-gray-500">OFF</div>
                                            </div>
                                        </template>
                                        <template x-if="offer.type === 'downgrade'">
                                            <div class="text-right">
                                                <div class="text-2xl font-bold text-blue-600">$<span x-text="offer.new_price"></span></div>
                                                <div class="text-sm text-gray-500">/month</div>
                                            </div>
                                        </template>
                                        <template x-if="offer.type === 'pause'">
                                            <div class="text-right">
                                                <div class="text-2xl font-bold text-purple-600" x-text="offer.pause_duration"></div>
                                                <div class="text-sm text-gray-500">days</div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <template x-if="offer.includes">
                                    <div class="mb-4">
                                        <h4 class="font-medium text-gray-900 mb-2">What's included:</h4>
                                        <ul class="space-y-1">
                                            <template x-for="item in offer.includes" :key="item">
                                                <li class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <span x-text="item"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </template>

                                <template x-if="offer.total_savings">
                                    <div class="mb-4 p-3 bg-green-50 rounded-lg">
                                        <div class="text-sm font-medium text-green-800">
                                            Total Savings: $<span x-text="offer.total_savings"></span>
                                        </div>
                                    </div>
                                </template>

                                <button 
                                    @click="acceptOffer(offer)"
                                    :disabled="processing"
                                    class="w-full btn-primary"
                                    :class="{'opacity-50 cursor-not-allowed': processing}"
                                >
                                    <span x-text="offer.cta"></span>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div class="mt-8 text-center">
                        <button 
                            @click="proceedToCancellation()"
                            class="text-gray-500 hover:text-gray-700 font-medium"
                        >
                            No thanks, I still want to cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Final Confirmation -->
            <div x-show="step === 3" class="bg-white rounded-lg shadow-sm p-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Final Confirmation</h1>
                    <p class="text-gray-600">Are you sure you want to cancel your subscription?</p>
                </div>

                <div class="max-w-2xl mx-auto">
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="font-semibold text-gray-900 mb-4">What happens when you cancel:</h3>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-700">You'll keep access until your current billing period ends</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-700">Your data will be safely stored for 90 days</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-700">You can reactivate anytime within 90 days</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-700">You can export your data anytime</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-6 mb-6">
                        <h3 class="font-semibold text-blue-900 mb-2">Still need help?</h3>
                        <p class="text-sm text-blue-800 mb-4">Our support team is here to help resolve any issues you might be having.</p>
                        <div class="flex space-x-3">
                            <a href="{{ route('support.contact') }}" class="btn-sm btn-outline-primary">Contact Support</a>
                            <a href="{{ route('support.live-chat') }}" class="btn-sm btn-outline-primary">Live Chat</a>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="confirmCancel" class="form-checkbox">
                            <span class="ml-2 text-sm text-gray-700">I understand the consequences and want to proceed with cancellation</span>
                        </label>
                    </div>

                    <div class="mt-8 flex justify-center space-x-4">
                        <button 
                            @click="step = 2"
                            class="btn-secondary"
                        >
                            Go Back
                        </button>
                        <button 
                            @click="confirmCancellation()"
                            :disabled="!confirmCancel || processing"
                            class="btn-danger"
                            :class="{'opacity-50 cursor-not-allowed': !confirmCancel || processing}"
                        >
                            <span x-show="!processing">Cancel Subscription</span>
                            <span x-show="processing">Cancelling...</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div x-show="step === 4" class="bg-white rounded-lg shadow-sm p-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Subscription Cancelled</h1>
                    <p class="text-gray-600 mb-6">Your subscription has been successfully cancelled. You'll continue to have access until your current billing period ends.</p>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="text-sm text-gray-700">
                            <p><strong>Access until:</strong> <span x-text="formatDate(accessUntil)"></span></p>
                            <p><strong>Data retention:</strong> 90 days</p>
                        </div>
                    </div>

                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('account.export-data') }}" class="btn-secondary">Export Data</a>
                        <a href="{{ route('dashboard') }}" class="btn-primary">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cancellationFlow() {
    return {
        step: 1,
        selectedReason: '',
        feedback: '',
        confirmCancel: false,
        processing: false,
        retentionAttemptId: null,
        retentionOffers: [],
        accessUntil: null,
        
        cancellationReasons: [
            {
                key: 'too_expensive',
                title: 'Too expensive',
                description: 'The pricing doesn\'t fit my budget'
            },
            {
                key: 'not_using_enough',
                title: 'Not using it enough',
                description: 'I\'m not getting enough value from the features'
            },
            {
                key: 'missing_features',
                title: 'Missing features',
                description: 'I need features that aren\'t available'
            },
            {
                key: 'technical_issues',
                title: 'Technical issues',
                description: 'I\'m experiencing problems with the platform'
            },
            {
                key: 'found_alternative',
                title: 'Found an alternative',
                description: 'I found a better solution elsewhere'
            },
            {
                key: 'business_closure',
                title: 'Closing my business',
                description: 'I no longer need this service'
            },
            {
                key: 'temporary_pause',
                title: 'Temporary pause',
                description: 'I need to pause temporarily'
            }
        ],
        
        init() {
            // Initialize component
        },
        
        async processCancellationReason() {
            if (!this.selectedReason) return;
            
            this.processing = true;
            
            try {
                const response = await Mewayz.api('/api/subscription/cancel-request', {
                    method: 'POST',
                    body: JSON.stringify({
                        reason: this.selectedReason,
                        feedback: this.feedback
                    })
                });
                
                if (response.success) {
                    this.retentionAttemptId = response.data.retention_attempt_id;
                    this.retentionOffers = response.data.offers;
                    this.step = 2;
                }
            } catch (error) {
                Mewayz.notify('Failed to process cancellation request', 'error');
            } finally {
                this.processing = false;
            }
        },
        
        async acceptOffer(offer) {
            this.processing = true;
            
            try {
                const response = await Mewayz.api('/api/subscription/accept-retention-offer', {
                    method: 'POST',
                    body: JSON.stringify({
                        retention_attempt_id: this.retentionAttemptId,
                        offer: offer
                    })
                });
                
                if (response.success) {
                    Mewayz.notify('Offer accepted successfully!', 'success');
                    window.location.href = '/subscription/dashboard';
                }
            } catch (error) {
                Mewayz.notify('Failed to accept offer', 'error');
            } finally {
                this.processing = false;
            }
        },
        
        proceedToCancellation() {
            this.step = 3;
        },
        
        async confirmCancellation() {
            if (!this.confirmCancel) return;
            
            this.processing = true;
            
            try {
                const response = await Mewayz.api('/api/subscription/confirm-cancellation', {
                    method: 'POST',
                    body: JSON.stringify({
                        retention_attempt_id: this.retentionAttemptId,
                        cancel_immediately: false
                    })
                });
                
                if (response.success) {
                    this.accessUntil = response.data.access_until;
                    this.step = 4;
                }
            } catch (error) {
                Mewayz.notify('Failed to cancel subscription', 'error');
            } finally {
                this.processing = false;
            }
        },
        
        goBack() {
            window.location.href = '/subscription/dashboard';
        },
        
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    }
}
</script>
@endsection