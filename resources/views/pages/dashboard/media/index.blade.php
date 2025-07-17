@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Media Library</h1>
            <p class="text-secondary-text mt-2">Manage your images, videos, and documents</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                </svg>
                Create Folder
            </button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload Files
            </button>
        </div>
    </div>

    <!-- Storage Stats -->
    <div class="dashboard-grid mb-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Storage Used</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
            <div class="dashboard-card-value">2.4 GB</div>
            <div class="dashboard-card-change">of 10 GB available</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Total Files</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">1,247</div>
            <div class="dashboard-card-change positive">+23 this week</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Images</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">892</div>
            <div class="dashboard-card-change">71.5% of total</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Videos</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">87</div>
            <div class="dashboard-card-change">7.0% of total</div>
        </div>
    </div>

    <!-- File Type Filter -->
    <div class="mb-6">
        <div class="flex items-center space-x-4">
            <span class="text-sm text-secondary-text">Filter by type:</span>
            <div class="flex rounded-lg border border-secondary-bg">
                <button class="px-4 py-2 bg-primary text-white rounded-l-lg text-sm">All Files</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text text-sm">Images</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text text-sm">Videos</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text text-sm">Documents</button>
                <button class="px-4 py-2 bg-secondary-bg text-secondary-text rounded-r-lg text-sm">Audio</button>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 mb-6">
        <a href="#" class="text-primary hover:underline">Media Library</a>
        <svg class="w-4 h-4 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-secondary-text">All Files</span>
    </div>

    <!-- Media Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
        @for ($i = 1; $i <= 32; $i++)
        <div class="group relative dashboard-card p-0 hover:transform hover:scale-105 transition-all cursor-pointer">
            <div class="aspect-square rounded-lg overflow-hidden">
                @if ($i % 4 == 0)
                <!-- Video -->
                <div class="w-full h-full bg-gradient-to-br from-red-500 to-pink-500 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                @elseif ($i % 3 == 0)
                <!-- Document -->
                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                @else
                <!-- Image -->
                <div class="w-full h-full bg-gradient-to-br from-green-500 to-blue-500 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                @endif
            </div>
            <div class="p-2">
                <div class="text-xs font-medium text-primary-text truncate">
                    {{ $i % 4 == 0 ? 'video-' . $i . '.mp4' : ($i % 3 == 0 ? 'document-' . $i . '.pdf' : 'image-' . $i . '.jpg') }}
                </div>
                <div class="text-xs text-secondary-text">{{ rand(100, 9999) }} KB</div>
            </div>
            <!-- Hover overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                <div class="flex space-x-2">
                    <button class="btn btn-sm btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button class="btn btn-sm btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </button>
                    <button class="btn btn-sm btn-error">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
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