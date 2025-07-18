@extends('install.layout')

@section('title', 'Welcome to Mewayz Installation')

@section('content')
<div class="card-header">
    <h2 class="card-title">Welcome to Mewayz</h2>
    <p class="card-subtitle">The Ultimate All-in-One Business Platform</p>
</div>

<div class="mb-6">
    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">What you'll get:</h3>
    <div class="grid grid-cols-2 gap-4">
        <div style="display: flex; align-items: start; gap: 0.75rem;">
            <div style="width: 2rem; height: 2rem; background: var(--install-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span style="color: white; font-size: 0.75rem;">✓</span>
            </div>
            <div>
                <h4 style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Social Media Management</h4>
                <p style="color: var(--text-secondary); font-size: 0.75rem;">Manage all your social platforms</p>
            </div>
        </div>
        
        <div style="display: flex; align-items: start; gap: 0.75rem;">
            <div style="width: 2rem; height: 2rem; background: var(--install-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span style="color: white; font-size: 0.75rem;">✓</span>
            </div>
            <div>
                <h4 style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">E-commerce Store</h4>
                <p style="color: var(--text-secondary); font-size: 0.75rem;">Sell products online</p>
            </div>
        </div>
        
        <div style="display: flex; align-items: start; gap: 0.75rem;">
            <div style="width: 2rem; height: 2rem; background: var(--install-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span style="color: white; font-size: 0.75rem;">✓</span>
            </div>
            <div>
                <h4 style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Course Creation</h4>
                <p style="color: var(--text-secondary); font-size: 0.75rem;">Create and sell online courses</p>
            </div>
        </div>
        
        <div style="display: flex; align-items: start; gap: 0.75rem;">
            <div style="width: 2rem; height: 2rem; background: var(--install-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span style="color: white; font-size: 0.75rem;">✓</span>
            </div>
            <div>
                <h4 style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Email Marketing</h4>
                <p style="color: var(--text-secondary); font-size: 0.75rem;">Build and engage your audience</p>
            </div>
        </div>
        
        <div style="display: flex; align-items: start; gap: 0.75rem;">
            <div style="width: 2rem; height: 2rem; background: var(--install-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span style="color: white; font-size: 0.75rem;">✓</span>
            </div>
            <div>
                <h4 style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Analytics & Reporting</h4>
                <p style="color: var(--text-secondary); font-size: 0.75rem;">Track your performance</p>
            </div>
        </div>
        
        <div style="display: flex; align-items: start; gap: 0.75rem;">
            <div style="width: 2rem; height: 2rem; background: var(--install-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span style="color: white; font-size: 0.75rem;">✓</span>
            </div>
            <div>
                <h4 style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">AI Integration</h4>
                <p style="color: var(--text-secondary); font-size: 0.75rem;">Powered by latest AI technology</p>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info mb-6">
    <h4 style="font-weight: 500; margin-bottom: 0.5rem;">Installation Requirements</h4>
    <p style="font-size: 0.875rem; margin: 0;">
        This installer will guide you through setting up Mewayz on your server. 
        Make sure you have your database credentials ready and proper server permissions.
    </p>
</div>

<div class="btn-actions">
    <div></div>
    <button onclick="startInstallation()" class="btn btn-primary">
        Start Installation
    </button>
</div>

@push('scripts')
<script>
    function startInstallation() {
        window.installer.nextStep('requirements');
    }
</script>
@endpush
@endsection