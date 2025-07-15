<?php
use function Livewire\Volt\{state, mount, placeholder, on};

state([
    'plans' => [
        [
            'name' => 'Starter',
            'price' => 9.99,
            'features' => [
                'Basic Website Builder',
                'Link in Bio',
                'Basic Analytics',
                'Email Support',
                '5 Sites'
            ],
            'popular' => false
        ],
        [
            'name' => 'Professional',
            'price' => 29.99,
            'features' => [
                'Advanced Website Builder',
                'Link in Bio + E-commerce',
                'Advanced Analytics',
                'Priority Support',
                'Unlimited Sites',
                'Custom Domain',
                'CRM Integration'
            ],
            'popular' => true
        ],
        [
            'name' => 'Enterprise',
            'price' => 99.99,
            'features' => [
                'All Professional Features',
                'White Label Solution',
                'API Access',
                'Custom Integrations',
                'Dedicated Support',
                'Advanced Security',
                'Team Collaboration'
            ],
            'popular' => false
        ]
    ]
]);
?>

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                Choose Your Plan
            </h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                Select the perfect plan for your business needs
            </p>
        </div>

        <!-- Plans Grid -->
        <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-3">
            @foreach ($plans as $plan)
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg {{ $plan['popular'] ? 'ring-2 ring-blue-500 dark:ring-blue-400' : '' }}">
                    @if ($plan['popular'])
                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-500 text-white">
                                Most Popular
                            </span>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <!-- Plan Name -->
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ $plan['name'] }}
                        </h3>
                        
                        <!-- Price -->
                        <div class="mt-4 flex items-baseline">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                ${{ number_format($plan['price'], 2) }}
                            </span>
                            <span class="ml-1 text-sm text-gray-500 dark:text-gray-400">
                                /month
                            </span>
                        </div>
                        
                        <!-- Features -->
                        <ul class="mt-6 space-y-4">
                            @foreach ($plan['features'] as $feature)
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-green-500 dark:text-green-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="ml-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $feature }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        
                        <!-- CTA Button -->
                        <div class="mt-8">
                            <button class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200">
                                {{ $plan['popular'] ? 'Get Started' : 'Choose Plan' }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- FAQ Section -->
        <div class="mt-16">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                Frequently Asked Questions
            </h3>
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Can I change my plan later?
                    </h4>
                    <p class="text-gray-600 dark:text-gray-400">
                        Yes, you can upgrade or downgrade your plan at any time. Changes will be reflected in your next billing cycle.
                    </p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        What payment methods do you accept?
                    </h4>
                    <p class="text-gray-600 dark:text-gray-400">
                        We accept all major credit cards, PayPal, and bank transfers for annual plans.
                    </p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Is there a free trial?
                    </h4>
                    <p class="text-gray-600 dark:text-gray-400">
                        Yes! All plans come with a 14-day free trial. No credit card required.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>