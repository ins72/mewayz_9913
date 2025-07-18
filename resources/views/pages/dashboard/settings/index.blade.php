@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Account Settings</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Export Settings</button>
                <button class="btn btn-primary btn-sm">Save Changes</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Manage your account preferences and platform settings.
            </p>
        </div>
    </div>

    <!-- Settings Navigation -->
    <div class="card">
        <div class="flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-primary">General</button>
            <button class="btn btn-sm btn-secondary">Security</button>
            <button class="btn btn-sm btn-secondary">Notifications</button>
            <button class="btn btn-sm btn-secondary">Billing</button>
            <button class="btn btn-sm btn-secondary">API Keys</button>
        </div>
    </div>

    <!-- Profile Settings -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Profile Information</h3>
        </div>
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">First Name</label>
                    <input type="text" value="John" class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">Last Name</label>
                    <input type="text" value="Doe" class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
                </div>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">Email Address</label>
                <input type="email" value="john.doe@example.com" class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">Bio</label>
                <textarea rows="3" class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary); resize: vertical;">Digital marketing expert and content creator helping businesses grow online.</textarea>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">Time Zone</label>
                <select class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
                    <option>UTC-08:00 (Pacific Time)</option>
                    <option>UTC-05:00 (Eastern Time)</option>
                    <option>UTC+00:00 (Greenwich Mean Time)</option>
                    <option>UTC+01:00 (Central European Time)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Security</h3>
        </div>
        <div class="space-y-4">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">Current Password</label>
                <input type="password" class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">New Password</label>
                <input type="password" class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-primary);">Confirm New Password</label>
                <input type="password" class="form-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
            </div>
            
            <div style="padding: 1rem; background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🔐</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Two-Factor Authentication</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Add an extra layer of security to your account</div>
                    </div>
                    <button class="btn btn-sm btn-primary">Enable</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Notification Preferences</h3>
        </div>
        <div class="space-y-4">
            <div style="display: flex; align-items: center; justify-content: between; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Email Notifications</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Receive email updates about your account activity</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            
            <div style="display: flex; align-items: center; justify-content: between; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Push Notifications</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Receive push notifications in your browser</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            
            <div style="display: flex; align-items: center; justify-content: between; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Marketing Emails</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Receive emails about new features and promotions</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="card" style="border-color: var(--accent-error);">
        <div class="card-header">
            <h3 class="card-title" style="color: var(--accent-error);">Danger Zone</h3>
        </div>
        <div class="space-y-4">
            <div style="padding: 1rem; background: rgba(239, 68, 68, 0.1); border-radius: 8px; border: 1px solid var(--accent-error);">
                <div style="font-weight: 500; color: var(--accent-error); margin-bottom: 0.5rem;">Delete Account</div>
                <div style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;">
                    Once you delete your account, there is no going back. Please be certain.
                </div>
                <button class="btn btn-sm" style="background: var(--accent-error); color: white; border: none;">Delete Account</button>
            </div>
        </div>
    </div>
</div>

<style>
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: var(--accent-primary);
}

input:checked + .toggle-slider:before {
    transform: translateX(26px);
}
</style>
@endsection