@php
    $metrics = $metrics ?? [
        [
            'title' => 'Total Revenue',
            'value' => '$12,345',
            'change' => '+12.5%',
            'change_type' => 'positive',
            'icon' => 'currency-dollar'
        ],
        [
            'title' => 'Active Users',
            'value' => '2,847',
            'change' => '+8.2%',
            'change_type' => 'positive',
            'icon' => 'users'
        ],
        [
            'title' => 'Social Posts',
            'value' => '156',
            'change' => '+23.1%',
            'change_type' => 'positive',
            'icon' => 'photo'
        ],
        [
            'title' => 'Course Sales',
            'value' => '89',
            'change' => '-3.2%',
            'change_type' => 'negative',
            'icon' => 'academic-cap'
        ]
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @foreach($metrics as $metric)
        <div class="metric-card hover-lift">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg">
                    @if($metric['icon'] === 'currency-dollar')
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    @elseif($metric['icon'] === 'users')
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    @elseif($metric['icon'] === 'photo')
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    @elseif($metric['icon'] === 'academic-cap')
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                    @endif
                </div>
                <div class="text-right">
                    <div class="metric-change {{ $metric['change_type'] }}">
                        @if($metric['change_type'] === 'positive')
                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
                            </svg>
                        @else
                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"></path>
                            </svg>
                        @endif
                        {{ $metric['change'] }}
                    </div>
                </div>
            </div>
            <div class="metric-value">{{ $metric['value'] }}</div>
            <div class="metric-label">{{ $metric['title'] }}</div>
        </div>
    @endforeach
</div>