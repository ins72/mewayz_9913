@extends('layouts.dashboard')

@section('title', 'Templates')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Templates</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Import Template</button>
                <button class="btn btn-primary btn-sm">Create Template</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Browse and use professional templates for your bio sites, emails, and marketing materials.
            </p>
        </div>
    </div>

    <!-- Template Categories -->
    <div class="card">
        <div class="flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-primary">All Templates</button>
            <button class="btn btn-sm btn-secondary">Bio Sites</button>
            <button class="btn btn-sm btn-secondary">Email</button>
            <button class="btn btn-sm btn-secondary">Social Media</button>
            <button class="btn btn-sm btn-secondary">Landing Pages</button>
            <button class="btn btn-sm btn-secondary">My Templates</button>
        </div>
    </div>

    <!-- Template Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Template 1 -->
        <div class="card">
            <div style="height: 250px; background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                <div style="color: white; font-size: 3rem;">📱</div>
            </div>
            <div class="space-y-3">
                <div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Modern Bio Site</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Clean and professional bio site template perfect for creators and professionals.</p>
                </div>
                <div class="flex justify-between items-center">
                    <div>
                        <div style="background: var(--accent-primary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Bio Site</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Preview</button>
                        <button class="btn btn-sm btn-primary">Use Template</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template 2 -->
        <div class="card">
            <div style="height: 250px; background: linear-gradient(135deg, var(--accent-warning) 0%, var(--accent-error) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                <div style="color: white; font-size: 3rem;">✉️</div>
            </div>
            <div class="space-y-3">
                <div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Newsletter Template</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Professional newsletter template with modern design and engaging layout.</p>
                </div>
                <div class="flex justify-between items-center">
                    <div>
                        <div style="background: var(--accent-warning); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Email</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Preview</button>
                        <button class="btn btn-sm btn-primary">Use Template</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template 3 -->
        <div class="card">
            <div style="height: 250px; background: linear-gradient(135deg, var(--accent-secondary) 0%, var(--accent-primary) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                <div style="color: white; font-size: 3rem;">🎨</div>
            </div>
            <div class="space-y-3">
                <div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Creative Portfolio</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Showcase your creative work with this stunning portfolio template.</p>
                </div>
                <div class="flex justify-between items-center">
                    <div>
                        <div style="background: var(--accent-secondary); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Portfolio</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Preview</button>
                        <button class="btn btn-sm btn-primary">Use Template</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection