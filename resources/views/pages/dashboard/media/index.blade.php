@extends('layouts.dashboard')

@section('title', 'Media Library')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Media Library</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Organize</button>
                <button class="btn btn-primary btn-sm">Upload Media</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Manage your images, videos, documents, and other media files in one centralized location.
            </p>
        </div>
    </div>

    <!-- Media Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">2,847</div>
            <div class="stat-label">Total Files</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +247 this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">8.4 GB</div>
            <div class="stat-label">Storage Used</div>
            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem;">
                of 50 GB available
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">156</div>
            <div class="stat-label">Images</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +12 this week
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">24</div>
            <div class="stat-label">Videos</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +3 this week
            </div>
        </div>
    </div>

    <!-- Upload Area -->
    <div class="card" style="border: 2px dashed var(--border-primary); background: var(--bg-primary);">
        <div style="padding: 2rem; text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-secondary);">📤</div>
            <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Drag & Drop Files Here</h3>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1.5rem;">
                Or click to browse and select files from your computer
            </p>
            <div class="flex gap-2 justify-center">
                <button class="btn btn-primary">Choose Files</button>
                <button class="btn btn-secondary">Upload from URL</button>
            </div>
            <div style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 1rem;">
                Supported formats: JPG, PNG, GIF, MP4, MOV, PDF, DOC, and more
            </div>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Media</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-secondary">Sort by Date</button>
                <button class="btn btn-sm btn-secondary">Sort by Size</button>
                <button class="btn btn-sm btn-secondary">Sort by Name</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            <!-- Image 1 -->
            <div class="card">
                <div style="height: 150px; background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">
                    <span style="color: white; font-size: 2rem;">🖼️</span>
                </div>
                <div style="text-align: center;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.75rem; margin-bottom: 0.25rem;">hero-image.jpg</div>
                    <div style="color: var(--text-secondary); font-size: 0.625rem;">2.4 MB • 2 hours ago</div>
                </div>
            </div>

            <!-- Video 1 -->
            <div class="card">
                <div style="height: 150px; background: linear-gradient(135deg, var(--accent-warning) 0%, var(--accent-error) 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem; position: relative;">
                    <span style="color: white; font-size: 2rem;">🎥</span>
                    <div style="position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(0, 0, 0, 0.7); color: white; padding: 0.25rem; border-radius: 4px; font-size: 0.625rem;">2:34</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.75rem; margin-bottom: 0.25rem;">promo-video.mp4</div>
                    <div style="color: var(--text-secondary); font-size: 0.625rem;">45.2 MB • 1 day ago</div>
                </div>
            </div>

            <!-- Document 1 -->
            <div class="card">
                <div style="height: 150px; background: var(--bg-primary); border: 2px dashed var(--border-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">
                    <span style="color: var(--text-secondary); font-size: 2rem;">📄</span>
                </div>
                <div style="text-align: center;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.75rem; margin-bottom: 0.25rem;">report.pdf</div>
                    <div style="color: var(--text-secondary); font-size: 0.625rem;">1.8 MB • 2 days ago</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection