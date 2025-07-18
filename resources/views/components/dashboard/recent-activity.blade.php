@php
    $activities = $activities ?? [
        [
            'type' => 'social_post',
            'title' => 'Instagram post published',
            'description' => 'Your post "Summer Sale is here!" was successfully published',
            'time' => '2 hours ago',
            'icon' => 'photo',
            'color' => 'bg-pink-500'
        ],
        [
            'type' => 'course_enrollment',
            'title' => 'New course enrollment',
            'description' => 'John Doe enrolled in "Digital Marketing Basics"',
            'time' => '4 hours ago',
            'icon' => 'academic-cap',
            'color' => 'bg-purple-500'
        ],
        [
            'type' => 'payment_received',
            'title' => 'Payment received',
            'description' => 'You received $99.00 for course purchase',
            'time' => '6 hours ago',
            'icon' => 'currency-dollar',
            'color' => 'bg-green-500'
        ],
        [
            'type' => 'product_sold',
            'title' => 'Product sold',
            'description' => 'Premium Template Package was purchased',
            'time' => '1 day ago',
            'icon' => 'shopping-bag',
            'color' => 'bg-blue-500'
        ],
        [
            'type' => 'email_sent',
            'title' => 'Email campaign sent',
            'description' => 'Newsletter sent to 1,247 subscribers',
            'time' => '2 days ago',
            'icon' => 'mail',
            'color' => 'bg-orange-500'
        ]
    ];
@endphp

<div class="card">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Recent Activity</h2>
            <a href="{{ route('activity.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                View All
            </a>
        </div>
        
        <div class="space-y-4">
            @foreach($activities as $activity)
                <div class="flex items-start space-x-4 p-4 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 {{ $activity['color'] }} rounded-full flex items-center justify-center">
                            @if($activity['icon'] === 'photo')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            @elseif($activity['icon'] === 'academic-cap')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                </svg>
                            @elseif($activity['icon'] === 'currency-dollar')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            @elseif($activity['icon'] === 'shopping-bag')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            @elseif($activity['icon'] === 'mail')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <span class="text-xs text-gray-500 whitespace-nowrap">{{ $activity['time'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $activity['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if(empty($activities))
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="empty-state-title">No Recent Activity</h3>
                <p class="empty-state-description">Your recent activities will appear here once you start using the platform.</p>
                <div class="empty-state-actions">
                    <a href="{{ route('social-media.posts.create') }}" class="btn-primary">
                        Create Your First Post
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>