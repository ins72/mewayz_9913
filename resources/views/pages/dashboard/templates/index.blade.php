@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Templates</h1>
            <p class="text-secondary-text mt-2">Browse and manage website templates</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Template
            </button>
        </div>
    </div>

    <!-- Categories -->
    <div class="mb-8">
        <div class="flex flex-wrap gap-2">
            <button class="btn btn-sm btn-primary">All Templates</button>
            <button class="btn btn-sm btn-secondary">Business</button>
            <button class="btn btn-sm btn-secondary">E-commerce</button>
            <button class="btn btn-sm btn-secondary">Portfolio</button>
            <button class="btn btn-sm btn-secondary">Blog</button>
            <button class="btn btn-sm btn-secondary">Landing</button>
            <button class="btn btn-sm btn-secondary">Creative</button>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @for ($i = 1; $i <= 12; $i++)
        <div class="dashboard-card hover:transform hover:scale-105 transition-all">
            <div class="aspect-video bg-secondary-bg rounded-lg mb-4 flex items-center justify-center">
                <svg class="w-16 h-16 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-primary-text mb-2">Template {{ $i }}</h3>
            <p class="text-secondary-text text-sm mb-4">Professional {{ $i % 3 == 0 ? 'business' : ($i % 2 == 0 ? 'e-commerce' : 'portfolio') }} template</p>
            <div class="flex items-center justify-between">
                <span class="text-success font-medium">{{ $i % 4 == 0 ? 'Free' : '$' . (19 + $i) }}</span>
                <div class="flex space-x-2">
                    <button class="btn btn-sm btn-secondary">Preview</button>
                    <button class="btn btn-sm btn-primary">Use</button>
                </div>
            </div>
        </div>
        @endfor
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-center mt-8">
        <div class="flex space-x-2">
            <button class="btn btn-sm btn-secondary">Previous</button>
            <button class="btn btn-sm btn-primary">1</button>
            <button class="btn btn-sm btn-secondary">2</button>
            <button class="btn btn-sm btn-secondary">3</button>
            <button class="btn btn-sm btn-secondary">Next</button>
        </div>
    </div>
</div>
@endsection