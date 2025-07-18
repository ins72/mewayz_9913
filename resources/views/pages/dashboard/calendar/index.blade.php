@extends('layouts.dashboard')

@section('title', 'Content Calendar')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Content Calendar</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Plan and schedule your content across all platforms</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-pink-600/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Content Calendar</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Your content calendar will be displayed here</p>
            <button class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition-colors">
                Create Content
            </button>
        </div>
    </div>
</div>
@endsection