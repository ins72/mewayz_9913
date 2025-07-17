@props([
    'name' => '',
    'size' => 'md',
    'class' => '',
    'color' => 'currentColor',
    'fill' => 'none',
    'stroke' => 'currentColor',
    'strokeWidth' => '2'
])

@php
    $sizeClasses = [
        'xs' => 'w-4 h-4',
        'sm' => 'w-5 h-5', 
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-10 h-10',
        '2xl' => 'w-12 h-12'
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $classes = "$sizeClass $class";
@endphp

@if($name === 'plus')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
    </svg>
@elseif($name === 'edit')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    </svg>
@elseif($name === 'delete')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
    </svg>
@elseif($name === 'eye')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>
@elseif($name === 'users')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
@elseif($name === 'chart')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
@elseif($name === 'mail')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
    </svg>
@elseif($name === 'link')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
    </svg>
@elseif($name === 'calendar')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
@elseif($name === 'shopping-bag')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
    </svg>
@elseif($name === 'book')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
    </svg>
@elseif($name === 'dollar')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
    </svg>
@elseif($name === 'download')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
@elseif($name === 'back')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
@elseif($name === 'send')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
    </svg>
@elseif($name === 'chat')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
    </svg>
@elseif($name === 'lightbulb')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
    </svg>
@elseif($name === 'globe')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
    </svg>
@else
    <!-- Default icon if name not found -->
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
@endif