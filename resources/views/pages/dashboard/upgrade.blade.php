<x-layouts.dashboard title="Upgrade - Mewayz" page-title="Upgrade">
    <div class="fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Upgrade Your Plan</h1>
                <p class="text-secondary-text">Choose the perfect plan for your business needs</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-sm text-secondary-text">Billing:</div>
                <div class="flex items-center bg-card-bg rounded-lg p-1">
                    <button class="px-3 py-1 text-sm bg-primary text-white rounded-md">Monthly</button>
                    <button class="px-3 py-1 text-sm text-secondary-text">Annual</button>
                </div>
            </div>
        </div>

        <!-- Current Plan Status -->
        <div class="card mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-primary-text">Current Plan: Free</h3>
                        <p class="text-secondary-text">You're currently on the free plan. Upgrade to unlock more features.</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-primary-text">$0</div>
                    <div class="text-sm text-secondary-text">per month</div>
                </div>
            </div>
        </div>

        <!-- Pricing Plans -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Starter Plan -->
            <div class="card border-2 border-border-color hover:border-info/50 transition-all duration-300">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-info/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary-text">Starter</h3>
                    <p class="text-secondary-text">Perfect for individuals getting started</p>
                </div>
                
                <div class="text-center mb-6">
                    <div class="text-4xl font-bold text-primary-text">$9.99</div>
                    <div class="text-sm text-secondary-text">per month</div>
                </div>

                <ul class="space-y-3 mb-6">
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Up to 5 sites
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Basic analytics
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Email support
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        1GB storage
                    </li>
                </ul>

                <button class="w-full btn btn-outline starter-plan-btn" data-package="starter">
                    Get Started
                </button>
            </div>

            <!-- Professional Plan -->
            <div class="card border-2 border-primary bg-gradient-to-b from-primary/5 to-transparent">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary-text">Professional</h3>
                    <p class="text-secondary-text">Best for growing businesses</p>
                    <div class="inline-block bg-primary text-white text-xs px-2 py-1 rounded mt-2">
                        Most Popular
                    </div>
                </div>
                
                <div class="text-center mb-6">
                    <div class="text-4xl font-bold text-primary-text">$29.99</div>
                    <div class="text-sm text-secondary-text">per month</div>
                </div>

                <ul class="space-y-3 mb-6">
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Up to 25 sites
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Advanced analytics
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Priority support
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        10GB storage
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Custom domains
                    </li>
                </ul>

                <button class="w-full btn btn-primary professional-plan-btn" data-package="professional">
                    Upgrade Now
                </button>
            </div>

            <!-- Enterprise Plan -->
            <div class="card border-2 border-border-color hover:border-warning/50 transition-all duration-300">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-warning/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary-text">Enterprise</h3>
                    <p class="text-secondary-text">For large organizations</p>
                </div>
                
                <div class="text-center mb-6">
                    <div class="text-4xl font-bold text-primary-text">$99.99</div>
                    <div class="text-sm text-secondary-text">per month</div>
                </div>

                <ul class="space-y-3 mb-6">
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Unlimited sites
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Enterprise analytics
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        24/7 phone support
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        100GB storage
                    </li>
                    <li class="flex items-center text-sm text-secondary-text">
                        <svg class="w-4 h-4 text-success mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        White-label solution
                    </li>
                </ul>

                <button class="w-full btn btn-outline enterprise-plan-btn" data-package="enterprise">
                    Contact Sales
                </button>
            </div>
        </div>

        <!-- Payment Processing Modal -->
        <div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-card-bg p-8 rounded-lg max-w-md w-full mx-4">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-text mb-2">Processing Payment</h3>
                    <p class="text-secondary-text">Please wait while we redirect you to Stripe...</p>
                </div>
            </div>
        </div>

        <!-- Features Comparison -->
        <div class="card">
            <h2 class="text-xl font-semibold text-primary-text mb-6">Feature Comparison</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border-color">
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Feature</th>
                            <th class="text-center py-3 px-4 text-secondary-text font-medium">Free</th>
                            <th class="text-center py-3 px-4 text-secondary-text font-medium">Starter</th>
                            <th class="text-center py-3 px-4 text-secondary-text font-medium">Professional</th>
                            <th class="text-center py-3 px-4 text-secondary-text font-medium">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-border-color">
                            <td class="py-3 px-4 text-primary-text">Number of Sites</td>
                            <td class="py-3 px-4 text-center text-secondary-text">1</td>
                            <td class="py-3 px-4 text-center text-secondary-text">5</td>
                            <td class="py-3 px-4 text-center text-secondary-text">25</td>
                            <td class="py-3 px-4 text-center text-secondary-text">Unlimited</td>
                        </tr>
                        <tr class="border-b border-border-color">
                            <td class="py-3 px-4 text-primary-text">Storage</td>
                            <td class="py-3 px-4 text-center text-secondary-text">100MB</td>
                            <td class="py-3 px-4 text-center text-secondary-text">1GB</td>
                            <td class="py-3 px-4 text-center text-secondary-text">10GB</td>
                            <td class="py-3 px-4 text-center text-secondary-text">100GB</td>
                        </tr>
                        <tr class="border-b border-border-color">
                            <td class="py-3 px-4 text-primary-text">Custom Domains</td>
                            <td class="py-3 px-4 text-center">
                                <svg class="w-4 h-4 text-error mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <svg class="w-4 h-4 text-error mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <svg class="w-4 h-4 text-success mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <svg class="w-4 h-4 text-success mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </td>
                        </tr>
                        <tr class="border-b border-border-color">
                            <td class="py-3 px-4 text-primary-text">Analytics</td>
                            <td class="py-3 px-4 text-center text-secondary-text">Basic</td>
                            <td class="py-3 px-4 text-center text-secondary-text">Basic</td>
                            <td class="py-3 px-4 text-center text-secondary-text">Advanced</td>
                            <td class="py-3 px-4 text-center text-secondary-text">Enterprise</td>
                        </tr>
                        <tr class="border-b border-border-color">
                            <td class="py-3 px-4 text-primary-text">Support</td>
                            <td class="py-3 px-4 text-center text-secondary-text">Community</td>
                            <td class="py-3 px-4 text-center text-secondary-text">Email</td>
                            <td class="py-3 px-4 text-center text-secondary-text">Priority</td>
                            <td class="py-3 px-4 text-center text-secondary-text">24/7 Phone</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Payment processing functionality
        function showPaymentModal() {
            document.getElementById('payment-modal').classList.remove('hidden');
        }

        function hidePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
        }

        // Error handling
        function showError(message) {
            alert(message); // Simple alert for now, can be enhanced with proper UI
        }

        // Get URL parameters
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            const results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Payment status polling
        async function pollPaymentStatus(sessionId, attempts = 0) {
            const maxAttempts = 5;
            const pollInterval = 2000; // 2 seconds

            if (attempts >= maxAttempts) {
                hidePaymentModal();
                showError('Payment status check timed out. Please refresh the page.');
                return;
            }

            try {
                const response = await fetch(`/api/payments/checkout/status/${sessionId}`);
                if (!response.ok) {
                    throw new Error('Failed to check payment status');
                }

                const data = await response.json();
                
                if (data.payment_status === 'paid') {
                    hidePaymentModal();
                    alert('Payment successful! Your plan has been upgraded.');
                    window.location.reload();
                    return;
                } else if (data.status === 'expired') {
                    hidePaymentModal();
                    showError('Payment session expired. Please try again.');
                    return;
                }

                // Continue polling
                setTimeout(() => pollPaymentStatus(sessionId, attempts + 1), pollInterval);
            } catch (error) {
                console.error('Error checking payment status:', error);
                hidePaymentModal();
                showError('Error checking payment status. Please try again.');
            }
        }

        // Check if returning from Stripe
        function checkReturnFromStripe() {
            const sessionId = getUrlParameter('session_id');
            if (sessionId) {
                showPaymentModal();
                pollPaymentStatus(sessionId);
            }
        }

        // Handle plan selection
        async function initiatePlanUpgrade(packageId) {
            showPaymentModal();

            try {
                const currentUrl = window.location.href.split('?')[0];
                const successUrl = `${currentUrl}?session_id={CHECKOUT_SESSION_ID}`;
                const cancelUrl = currentUrl;

                const requestBody = {
                    package_id: packageId,
                    success_url: successUrl,
                    cancel_url: cancelUrl,
                    metadata: {
                        source: 'dashboard_upgrade',
                        package: packageId
                    }
                };

                const response = await fetch('/api/payments/checkout/session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestBody)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Failed to create checkout session');
                }

                const data = await response.json();
                
                if (data.url) {
                    window.location.href = data.url;
                } else {
                    throw new Error('No checkout URL received');
                }
            } catch (error) {
                hidePaymentModal();
                showError(error.message);
                console.error('Payment error:', error);
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Check if returning from Stripe
            checkReturnFromStripe();

            // Add click handlers for plan buttons
            document.querySelectorAll('.starter-plan-btn').forEach(btn => {
                btn.addEventListener('click', () => initiatePlanUpgrade('starter'));
            });

            document.querySelectorAll('.professional-plan-btn').forEach(btn => {
                btn.addEventListener('click', () => initiatePlanUpgrade('professional'));
            });

            document.querySelectorAll('.enterprise-plan-btn').forEach(btn => {
                btn.addEventListener('click', () => initiatePlanUpgrade('enterprise'));
            });
        });
    </script>
</x-layouts.dashboard>