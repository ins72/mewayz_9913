<!-- Navigation -->
<nav class="bg-white border-b border-gray-200 sticky top-0 z-40 backdrop-blur-sm bg-white/95" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">{{ config('app.name', 'Mewayz') }}</span>
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-blue-600' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('social-media.index') }}" class="text-gray-700 hover:text-gray-900 font-medium transition-colors {{ request()->routeIs('social-media.*') ? 'text-blue-600' : '' }}">
                    Social Media
                </a>
                <a href="{{ route('link-bio.index') }}" class="text-gray-700 hover:text-gray-900 font-medium transition-colors {{ request()->routeIs('link-bio.*') ? 'text-blue-600' : '' }}">
                    Link in Bio
                </a>
                <a href="{{ route('ecommerce.index') }}" class="text-gray-700 hover:text-gray-900 font-medium transition-colors {{ request()->routeIs('ecommerce.*') ? 'text-blue-600' : '' }}">
                    E-commerce
                </a>
                <a href="{{ route('courses.index') }}" class="text-gray-700 hover:text-gray-900 font-medium transition-colors {{ request()->routeIs('courses.*') ? 'text-blue-600' : '' }}">
                    Courses
                </a>
                <a href="{{ route('analytics.index') }}" class="text-gray-700 hover:text-gray-900 font-medium transition-colors {{ request()->routeIs('analytics.*') ? 'text-blue-600' : '' }}">
                    Analytics
                </a>
            </div>
            
            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <!-- Theme Toggle -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" data-tooltip="Toggle theme">
                    <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>
                
                <!-- Notifications -->
                <div class="relative" x-data="{ notificationOpen: false }">
                    <button @click="notificationOpen = !notificationOpen" class="p-2 rounded-lg hover:bg-gray-100 transition-colors relative" data-tooltip="Notifications">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-400"></span>
                    </button>
                    
                    <!-- Notifications Dropdown -->
                    <div x-show="notificationOpen" @click.away="notificationOpen = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">New social media post scheduled</p>
                                        <p class="text-xs text-gray-500">Your Instagram post will be published in 2 hours</p>
                                        <p class="text-xs text-gray-400 mt-1">2 minutes ago</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">New course enrollment</p>
                                        <p class="text-xs text-gray-500">John Doe enrolled in "Digital Marketing Basics"</p>
                                        <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Payment received</p>
                                        <p class="text-xs text-gray-500">You received $99.00 for your course</p>
                                        <p class="text-xs text-gray-400 mt-1">3 hours ago</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="px-4 py-2 border-t border-gray-200">
                            <a href="#" class="text-xs text-blue-600 hover:text-blue-800">View all notifications</a>
                        </div>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="relative" x-data="{ userMenuOpen: false }">
                    <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <img class="w-8 h-8 rounded-full" src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=007AFF&color=fff' }}" alt="{{ auth()->user()->name }}">
                        <span class="hidden sm:block text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <!-- User Dropdown -->
                    <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Profile</span>
                            </div>
                        </a>
                        <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Settings</span>
                            </div>
                        </a>
                        <a href="{{ route('subscription.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span>Subscription</span>
                            </div>
                        </a>
                        <div class="border-t border-gray-200 my-2"></div>
                        <a href="{{ route('help.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Help & Support</span>
                            </div>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Sign out</span>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" x-transition class="md:hidden bg-white border-t border-gray-200">
        <div class="px-4 py-2 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('social-media.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors {{ request()->routeIs('social-media.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                Social Media
            </a>
            <a href="{{ route('link-bio.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors {{ request()->routeIs('link-bio.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                Link in Bio
            </a>
            <a href="{{ route('ecommerce.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors {{ request()->routeIs('ecommerce.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                E-commerce
            </a>
            <a href="{{ route('courses.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors {{ request()->routeIs('courses.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                Courses
            </a>
            <a href="{{ route('analytics.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors {{ request()->routeIs('analytics.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                Analytics
            </a>
        </div>
    </div>
</nav>