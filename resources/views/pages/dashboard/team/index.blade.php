@extends('layouts.dashboard')

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-primary-text">Team Management</h1>
            <p class="text-secondary-text mt-2">Manage your team members and permissions</p>
        </div>
        <div class="flex items-center space-x-4">
            <button class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </button>
            <button class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Invite Member
            </button>
        </div>
    </div>

    <!-- Team Stats -->
    <div class="dashboard-grid mb-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Total Members</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">8</div>
            <div class="dashboard-card-change positive">+2 this month</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Active Members</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">7</div>
            <div class="dashboard-card-change positive">87.5% active</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Pending Invites</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">3</div>
            <div class="dashboard-card-change">Awaiting response</div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Roles Created</h3>
                <svg class="dashboard-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="dashboard-card-value">5</div>
            <div class="dashboard-card-change">Custom roles</div>
        </div>
    </div>

    <!-- Team Members Table -->
    <div class="dashboard-table">
        <div class="dashboard-table-header">
            <h3 class="dashboard-table-title">Team Members</h3>
            <div class="flex items-center space-x-2">
                <input type="text" placeholder="Search members..." class="form-input w-64">
                <button class="btn btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= 8; $i++)
                    <tr>
                        <td>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-info rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-medium">{{ substr('User ' . $i, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-primary-text">{{ $i == 1 ? auth()->user()->name : 'User ' . $i }}</div>
                                    <div class="text-sm text-secondary-text">{{ $i == 1 ? auth()->user()->email : 'user' . $i . '@example.com' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $i == 1 ? 'primary' : ($i % 3 == 0 ? 'warning' : 'secondary') }}">
                                {{ $i == 1 ? 'Owner' : ($i % 3 == 0 ? 'Admin' : ($i % 2 == 0 ? 'Editor' : 'Viewer')) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $i <= 7 ? 'success' : 'warning' }}">
                                {{ $i <= 7 ? 'Active' : 'Pending' }}
                            </span>
                        </td>
                        <td>{{ $i <= 7 ? rand(1, 30) . ' min ago' : 'Never' }}</td>
                        <td>
                            <div class="flex items-center space-x-2">
                                @if ($i != 1)
                                <button class="btn btn-sm btn-secondary">Edit</button>
                                <button class="btn btn-sm btn-error">Remove</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection