@extends('layouts.app')

@section('title', 'About Us - Mewayz')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">About Mewayz</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                    We're building the future of digital presence management, empowering millions to create, connect, and grow their online identity.
                </p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($stats['total_users']) }}</div>
                        <div class="text-sm opacity-90">Total Users</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($stats['active_users']) }}</div>
                        <div class="text-sm opacity-90">Active Users</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($stats['total_projects']) }}</div>
                        <div class="text-sm opacity-90">Projects Created</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $stats['years_in_business'] }}+</div>
                        <div class="text-sm opacity-90">Years in Business</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mission Section -->
    <div class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Our Mission</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    To democratize digital presence creation and make it accessible for everyone, from individuals to large enterprises, 
                    providing them with the tools they need to succeed in the digital world.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Innovation</h3>
                    <p class="text-gray-600">Constantly pushing the boundaries of what's possible in digital presence management.</p>
                </div>
                <div class="text-center">
                    <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Community</h3>
                    <p class="text-gray-600">Building a supportive community where everyone can learn, grow, and succeed together.</p>
                </div>
                <div class="text-center">
                    <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Trust</h3>
                    <p class="text-gray-600">Maintaining the highest standards of security, privacy, and reliability in everything we do.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    @if(count($teamMembers) > 0)
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Meet Our Team</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Our diverse team of experts is passionate about creating exceptional digital experiences.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($teamMembers as $member)
                <div class="text-center">
                    <img src="{{ $member['image'] }}" alt="{{ $member['name'] }}" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold mb-1">{{ $member['name'] }}</h3>
                    <p class="text-blue-600 mb-2">{{ $member['position'] }}</p>
                    <p class="text-gray-600 mb-4">{{ $member['bio'] }}</p>
                    <div class="flex justify-center space-x-4">
                        @foreach($member['social'] as $platform => $url)
                        <a href="{{ $url }}" target="_blank" class="text-gray-400 hover:text-gray-600">
                            <span class="sr-only">{{ ucfirst($platform) }}</span>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Timeline Section -->
    @if(count($milestones) > 0)
    <div class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Our Journey</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    From humble beginnings to becoming a leading platform in digital presence management.
                </p>
            </div>
            
            <div class="relative">
                <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-0.5 bg-gray-300"></div>
                <div class="space-y-12">
                    @foreach($milestones as $index => $milestone)
                    <div class="relative flex items-center {{ $index % 2 === 0 ? 'justify-start' : 'justify-end' }}">
                        <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-blue-600 rounded-full"></div>
                        <div class="bg-white p-6 rounded-lg shadow-lg {{ $index % 2 === 0 ? 'mr-8' : 'ml-8' }} max-w-md">
                            <div class="text-2xl font-bold text-blue-600 mb-2">{{ $milestone['year'] }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $milestone['event'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- CTA Section -->
    <div class="py-20 bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Get Started?</h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto">
                Join thousands of users who trust Mewayz to manage their digital presence.
            </p>
            <div class="space-x-4">
                <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Get Started Free
                </a>
                <a href="{{ route('contact') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>
@endsection