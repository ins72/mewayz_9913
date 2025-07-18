@extends('layouts.app')

@section('title', 'Pricing - Mewayz')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">Simple, Transparent Pricing</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                    Choose the plan that works best for you. All plans include our core features and 24/7 support.
                </p>
                <div class="flex justify-center items-center space-x-4 mb-8">
                    <span class="text-lg">Monthly</span>
                    <button class="bg-white bg-opacity-20 rounded-full p-1 w-16 h-8 flex items-center transition duration-300" id="billing-toggle">
                        <div class="bg-white w-6 h-6 rounded-full shadow-md transform transition duration-300" id="billing-slider"></div>
                    </button>
                    <span class="text-lg">Yearly <span class="text-sm bg-green-400 text-green-900 px-2 py-1 rounded-full ml-2">Save 20%</span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($plans as $plan)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden {{ $plan->is_popular ? 'ring-4 ring-blue-500 relative' : '' }}">
                    @if($plan->is_popular)
                    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white px-4 py-1 rounded-b-lg text-sm font-semibold">
                        Most Popular
                    </div>
                    @endif
                    
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <p class="text-gray-600 mb-6">{{ $plan->description }}</p>
                        
                        <div class="mb-8">
                            <span class="text-4xl font-bold text-gray-900">${{ number_format($plan->base_price, 2) }}</span>
                            <span class="text-gray-600">/month</span>
                        </div>
                        
                        <ul class="space-y-3 mb-8">
                            @foreach($plan->features as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </li>
                            @endforeach
                        </ul>
                        
                        <button class="w-full {{ $plan->is_popular ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-900 hover:bg-gray-800' }} text-white py-3 px-6 rounded-lg font-semibold transition duration-300"
                                onclick="selectPlan('{{ $plan->slug }}')">
                            @if($plan->base_price == 0)
                                Get Started Free
                            @else
                                Start Free Trial
                            @endif
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Features Comparison -->
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Compare Plans</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    See exactly what's included in each plan and find the perfect fit for your needs.
                </p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-4 px-6 text-lg font-semibold">Features</th>
                            @foreach($plans as $plan)
                            <th class="text-center py-4 px-6 text-lg font-semibold">{{ $plan->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($features->groupBy('category') as $category => $categoryFeatures)
                        <tr class="border-b bg-gray-50">
                            <td colspan="{{ count($plans) + 1 }}" class="py-3 px-6 text-sm font-semibold text-gray-600 uppercase">
                                {{ $category }}
                            </td>
                        </tr>
                        @foreach($categoryFeatures as $feature)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="font-medium">{{ $feature->name }}</div>
                                @if($feature->description)
                                <div class="text-sm text-gray-500">{{ $feature->description }}</div>
                                @endif
                            </td>
                            @foreach($plans as $plan)
                            <td class="py-4 px-6 text-center">
                                @if($feature->is_included_in_plan($plan->slug))
                                <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                @else
                                <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Have questions about our pricing? We've got answers.
                </p>
            </div>
            
            <div class="max-w-3xl mx-auto">
                <div class="space-y-6">
                    @foreach($faq as $item)
                    <div class="bg-white rounded-lg shadow-md">
                        <button class="w-full text-left p-6 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset"
                                onclick="toggleFAQ(this)">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $item['question'] }}</h3>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>
                        <div class="hidden px-6 pb-6">
                            <p class="text-gray-600">{{ $item['answer'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    @if(count($testimonials) > 0)
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">What Our Customers Say</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($testimonials as $testimonial)
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 {{ $i < $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-4">"{{ $testimonial->content }}"</p>
                    <div class="flex items-center">
                        @if($testimonial->avatar)
                        <img src="{{ $testimonial->avatar }}" alt="{{ $testimonial->name }}" class="w-10 h-10 rounded-full mr-3">
                        @endif
                        <div>
                            <div class="font-semibold text-gray-900">{{ $testimonial->name }}</div>
                            <div class="text-sm text-gray-500">{{ $testimonial->position }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- CTA Section -->
    <div class="py-20 bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Get Started?</h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto">
                Join thousands of users who trust Mewayz to manage their digital presence. Start your free trial today.
            </p>
            <div class="space-x-4">
                <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Start Free Trial
                </a>
                <a href="{{ route('contact') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                    Contact Sales
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function selectPlan(planSlug) {
    // Here you would typically redirect to the signup page with the selected plan
    window.location.href = '/register?plan=' + planSlug;
}

function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('svg');
    
    content.classList.toggle('hidden');
    icon.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
}

// Billing toggle functionality
document.getElementById('billing-toggle').addEventListener('click', function() {
    const slider = document.getElementById('billing-slider');
    const isYearly = slider.style.transform === 'translateX(2rem)';
    
    if (isYearly) {
        slider.style.transform = 'translateX(0)';
        // Update prices to monthly
    } else {
        slider.style.transform = 'translateX(2rem)';
        // Update prices to yearly (with 20% discount)
    }
});
</script>
@endsection