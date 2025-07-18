@php
    $actions = $actions ?? [
        [
            'title' => 'Schedule Post',
            'description' => 'Create and schedule social media posts',
            'icon' => 'calendar',
            'color' => 'bg-blue-500',
            'url' => route('social-media.posts.create')
        ],
        [
            'title' => 'Add Product',
            'description' => 'Add new product to your store',
            'icon' => 'shopping-bag',
            'color' => 'bg-green-500',
            'url' => route('ecommerce.products.create')
        ],
        [
            'title' => 'Create Course',
            'description' => 'Start building your online course',
            'icon' => 'academic-cap',
            'color' => 'bg-purple-500',
            'url' => route('courses.create')
        ],
        [
            'title' => 'Email Campaign',
            'description' => 'Send targeted email campaigns',
            'icon' => 'mail',
            'color' => 'bg-pink-500',
            'url' => route('email-marketing.campaigns.create')
        ],
        [
            'title' => 'Bio Link',
            'description' => 'Create professional bio pages',
            'icon' => 'link',
            'color' => 'bg-orange-500',
            'url' => route('link-bio.create')
        ],
        [
            'title' => 'Analytics',
            'description' => 'View detailed performance analytics',
            'icon' => 'chart-bar',
            'color' => 'bg-teal-500',
            'url' => route('analytics.index')
        ],
        [
            'title' => 'Team Members',
            'description' => 'Invite and manage team members',
            'icon' => 'user-group',
            'color' => 'bg-indigo-500',
            'url' => route('team.index')
        ],
        [
            'title' => 'Settings',
            'description' => 'Configure your workspace settings',
            'icon' => 'cog',
            'color' => 'bg-gray-500',
            'url' => route('settings.index')
        ]
    ];
@endphp

<div class="card mb-8">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
            <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Customize
            </button>
        </div>
        
        <div class="quick-actions-grid">
            @foreach($actions as $action)
                <a href="{{ $action['url'] }}" class="quick-action-item group">
                    <div class="quick-action-icon {{ $action['color'] }} group-hover:scale-110 transition-transform">
                        @if($action['icon'] === 'calendar')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @elseif($action['icon'] === 'shopping-bag')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        @elseif($action['icon'] === 'academic-cap')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                        @elseif($action['icon'] === 'mail')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        @elseif($action['icon'] === 'link')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        @elseif($action['icon'] === 'chart-bar')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        @elseif($action['icon'] === 'user-group')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        @elseif($action['icon'] === 'cog')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="quick-action-title">{{ $action['title'] }}</div>
                    <div class="quick-action-description">{{ $action['description'] }}</div>
                </a>
            @endforeach
        </div>
    </div>
</div>