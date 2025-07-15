<?php
use function Livewire\Volt\{state, mount, placeholder, on};

state([
    'selectedCategory' => 'website',
    'categories' => [
        'website' => 'Website',
        'landing' => 'Landing Page',
        'blog' => 'Blog',
        'ecommerce' => 'E-commerce',
        'portfolio' => 'Portfolio',
        'business' => 'Business'
    ],
    'templates' => [
        'website' => [
            ['name' => 'Modern Business', 'image' => '/assets/templates/business1.jpg', 'premium' => false],
            ['name' => 'Creative Agency', 'image' => '/assets/templates/agency1.jpg', 'premium' => true],
            ['name' => 'Tech Startup', 'image' => '/assets/templates/startup1.jpg', 'premium' => false],
        ],
        'landing' => [
            ['name' => 'Product Launch', 'image' => '/assets/templates/launch1.jpg', 'premium' => false],
            ['name' => 'SaaS Landing', 'image' => '/assets/templates/saas1.jpg', 'premium' => true],
            ['name' => 'App Download', 'image' => '/assets/templates/app1.jpg', 'premium' => false],
        ],
        'blog' => [
            ['name' => 'Personal Blog', 'image' => '/assets/templates/blog1.jpg', 'premium' => false],
            ['name' => 'News Magazine', 'image' => '/assets/templates/news1.jpg', 'premium' => true],
            ['name' => 'Travel Blog', 'image' => '/assets/templates/travel1.jpg', 'premium' => false],
        ],
        'ecommerce' => [
            ['name' => 'Fashion Store', 'image' => '/assets/templates/fashion1.jpg', 'premium' => false],
            ['name' => 'Electronics Shop', 'image' => '/assets/templates/electronics1.jpg', 'premium' => true],
            ['name' => 'Bookstore', 'image' => '/assets/templates/book1.jpg', 'premium' => false],
        ],
        'portfolio' => [
            ['name' => 'Designer Portfolio', 'image' => '/assets/templates/design1.jpg', 'premium' => false],
            ['name' => 'Photography', 'image' => '/assets/templates/photo1.jpg', 'premium' => true],
            ['name' => 'Developer Portfolio', 'image' => '/assets/templates/dev1.jpg', 'premium' => false],
        ],
        'business' => [
            ['name' => 'Corporate', 'image' => '/assets/templates/corp1.jpg', 'premium' => false],
            ['name' => 'Restaurant', 'image' => '/assets/templates/restaurant1.jpg', 'premium' => true],
            ['name' => 'Medical Practice', 'image' => '/assets/templates/medical1.jpg', 'premium' => false],
        ]
    ]
]);

$selectCategory = function($category) {
    $this->selectedCategory = $category;
};

$selectTemplate = function($template) {
    $this->dispatch('template-selected', template: $template);
};

$createBlank = function() {
    $this->dispatch('create-blank-site');
};
?>

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                Advanced Website Creation
            </h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Choose from professional templates or start from scratch
            </p>
        </div>
        
        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    AI Builder
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Let AI create your website based on your business description
                </p>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    Start AI Builder
                </button>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Blank Canvas
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Start with a blank page and build your website from scratch
                </p>
                <button wire:click="createBlank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    Create Blank
                </button>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Import Design
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Import your existing design or PSD files
                </p>
                <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    Import Design
                </button>
            </div>
        </div>
        
        <!-- Template Categories -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Professional Templates
            </h3>
            
            <!-- Category Tabs -->
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach ($categories as $key => $category)
                    <button 
                        wire:click="selectCategory('{{ $key }}')"
                        class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ $selectedCategory === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                    >
                        {{ $category }}
                    </button>
                @endforeach
            </div>
            
            <!-- Template Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($templates[$selectedCategory] as $template)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow duration-200">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-gray-700">
                            <img src="{{ $template['image'] }}" alt="{{ $template['name'] }}" class="w-full h-48 object-cover" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjE4MCIgdmlld0JveD0iMCAwIDMyMCAxODAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMjAiIGhlaWdodD0iMTgwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xNDQgOTBIMTc2VjEwNEgxNDRWOTBaIiBmaWxsPSIjOTCA5MDNCL0wiPjwvcGF0aD4KPC9zdmc+'" />
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $template['name'] }}
                                </h4>
                                @if ($template['premium'])
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                        Premium
                                    </span>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                    Preview
                                </button>
                                <button wire:click="selectTemplate('{{ $template['name'] }}')" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                    Use Template
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Advanced Features -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Advanced Features
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">Custom Code</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Add custom HTML, CSS, and JavaScript</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">Performance Optimization</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Automatic image optimization and caching</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">Advanced Security</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">SSL certificates and security monitoring</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>