<?php
use function Livewire\Volt\{state, mount, placeholder, on};

state([
    'user' => fn() => iam()->toArray(),
    'current_plan' => 'Professional',
    'billing_cycle' => 'monthly',
    'next_billing_date' => '2024-08-15',
    'payment_method' => '**** **** **** 4242',
    'billing_history' => [
        [
            'date' => '2024-07-15',
            'amount' => '$29.99',
            'status' => 'paid',
            'invoice' => '#INV-001'
        ],
        [
            'date' => '2024-06-15',
            'amount' => '$29.99',
            'status' => 'paid',
            'invoice' => '#INV-002'
        ],
        [
            'date' => '2024-05-15',
            'amount' => '$29.99',
            'status' => 'paid',
            'invoice' => '#INV-003'
        ]
    ]
]);

$updateBillingCycle = function() {
    // Logic to update billing cycle
    $this->dispatch('billing-cycle-updated');
};

$updatePaymentMethod = function() {
    // Logic to update payment method
    $this->dispatch('payment-method-updated');
};

$downloadInvoice = function($invoice) {
    // Logic to download invoice
    $this->dispatch('invoice-downloaded', invoice: $invoice);
};
?>

<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Current Plan Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                Current Plan
            </h3>
            
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $current_plan }}
                    </h4>
                    <p class="text-gray-600 dark:text-gray-400">
                        Billed {{ $billing_cycle }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        ${{ $billing_cycle === 'monthly' ? '29.99' : '299.99' }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        per {{ $billing_cycle === 'monthly' ? 'month' : 'year' }}
                    </p>
                </div>
            </div>
            
            <div class="mt-6 flex space-x-4">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    Upgrade Plan
                </button>
                <button class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    Change Plan
                </button>
            </div>
        </div>
        
        <!-- Billing Cycle Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                Billing Cycle
            </h3>
            
            <div class="space-y-4">
                <label class="flex items-center">
                    <input type="radio" wire:model="billing_cycle" value="monthly" class="text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-gray-700 dark:text-gray-300">Monthly ($29.99/month)</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" wire:model="billing_cycle" value="yearly" class="text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-gray-700 dark:text-gray-300">Yearly ($299.99/year) - Save 17%</span>
                </label>
            </div>
            
            <div class="mt-6">
                <button wire:click="updateBillingCycle" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    Update Billing Cycle
                </button>
            </div>
        </div>
        
        <!-- Payment Method Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                Payment Method
            </h3>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-6 bg-blue-600 rounded mr-3"></div>
                    <span class="text-gray-700 dark:text-gray-300">{{ $payment_method }}</span>
                </div>
                <button wire:click="updatePaymentMethod" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    Update
                </button>
            </div>
            
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                Next billing date: {{ $next_billing_date }}
            </p>
        </div>
        
        <!-- Billing History Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                Billing History
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-400">Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-400">Amount</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-400">Invoice</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($billing_history as $item)
                            <tr>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $item['date'] }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $item['amount'] }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $item['status'] === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                        {{ ucfirst($item['status']) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $item['invoice'] }}
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <button wire:click="downloadInvoice('{{ $item['invoice'] }}')" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                        Download
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>