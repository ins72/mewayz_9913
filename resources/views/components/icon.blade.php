@props([
    'name' => '',
    'size' => 'md',
    'class' => '',
    'color' => 'currentColor',
    'fill' => 'none',
    'stroke' => 'currentColor',
    'strokeWidth' => '2',
    'alt' => '',
    'role' => 'img'
])

@php
    $sizeClasses = [
        'xs' => 'w-4 h-4',
        'sm' => 'w-5 h-5', 
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-10 h-10',
        '2xl' => 'w-12 h-12',
        '3xl' => 'w-16 h-16',
        '4xl' => 'w-20 h-20'
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $classes = "$sizeClass $class";
    
    // Accessibility attributes
    $accessibilityAttrs = '';
    if ($alt) {
        $accessibilityAttrs .= ' aria-label="' . $alt . '"';
    }
    if ($role) {
        $accessibilityAttrs .= ' role="' . $role . '"';
    }
@endphp

@if($name === 'plus')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
    </svg>
@elseif($name === 'edit')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    </svg>
@elseif($name === 'delete')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
    </svg>
@elseif($name === 'eye')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>
@elseif($name === 'users')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
@elseif($name === 'chart')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
@elseif($name === 'mail')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
    </svg>
@elseif($name === 'link')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
    </svg>
@elseif($name === 'calendar')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
@elseif($name === 'shopping-bag')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
    </svg>
@elseif($name === 'book')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
    </svg>
@elseif($name === 'dollar')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
    </svg>
@elseif($name === 'download')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
@elseif($name === 'back')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
@elseif($name === 'send')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
    </svg>
@elseif($name === 'chat')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
    </svg>
@elseif($name === 'lightbulb')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
    </svg>
@elseif($name === 'globe')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
    </svg>
@elseif($name === 'dashboard')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v2H8V5z"/>
    </svg>
@elseif($name === 'image')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
@elseif($name === 'video')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
    </svg>
@elseif($name === 'check')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M5 13l4 4L19 7"/>
    </svg>
@elseif($name === 'instagram')
    <svg class="{{ $classes }}" fill="currentColor" stroke="none" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
    </svg>
@elseif($name === 'twitter')
    <svg class="{{ $classes }}" fill="currentColor" stroke="none" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
    </svg>
@elseif($name === 'facebook')
    <svg class="{{ $classes }}" fill="currentColor" stroke="none" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
    </svg>
@elseif($name === 'linkedin')
    <svg class="{{ $classes }}" fill="currentColor" stroke="none" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
    </svg>
@elseif($name === 'settings')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
@elseif($name === 'trending')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
    </svg>
@elseif($name === 'notification')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M15 17h5l-3.595-3.595a.908.908 0 00-1.28 0L15 17z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 17h3"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 13h.01"/>
    </svg>
@elseif($name === 'menu')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
@elseif($name === 'close')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M6 18L18 6M6 6l12 12"/>
    </svg>
@elseif($name === 'search')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
@elseif($name === 'filter')
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
    </svg>
@else
    <!-- Default icon if name not found -->
    <svg class="{{ $classes }}" fill="{{ $fill }}" stroke="{{ $stroke }}" viewBox="0 0 24 24" {!! $accessibilityAttrs !!}>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
@endif