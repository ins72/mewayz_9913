@extends('layouts.app')

@section('title', 'Features - Mewayz')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">Powerful Features</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                    Everything you need to create, manage, and grow your digital presence in one comprehensive platform.
                </p>
            </div>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @foreach($categories as $categoryKey => $category)
            <div class="mb-16">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $category['name'] }}</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">{{ $category['description'] }}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($features->where('category', $categoryKey) as $feature)
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 rounded-lg p-3 mr-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">{{ $feature->name }}</h3>
                        </div>
                        <p class="text-gray-600 mb-4">{{ $feature->description }}</p>
                        @if($feature->benefits)
                        <ul class="space-y-2">
                            @foreach($feature->benefits as $benefit)
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-500 mr-2 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ $benefit }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-20 bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Experience These Features?</h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto">
                Start your free trial today and see how Mewayz can transform your digital presence.
            </p>
            <div class="space-x-4">
                <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Start Free Trial
                </a>
                <a href="{{ route('pricing') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                    View Pricing
                </a>
            </div>
        </div>
    </div>
</div>
@endsection