@extends('layouts.dashboard')

@section('title', 'Team')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Team Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Team</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Team Members</h5>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm">Invite Member</button>
                    </div>
                </div>
                <div class="card-body">
                    <p>Manage your team members and their permissions.</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-primary text-white rounded-circle">
                                            JD
                                        </div>
                                    </div>
                                    <h6 class="card-title">John Doe</h6>
                                    <p class="text-muted">Administrator</p>
                                    <span class="badge bg-success">Active</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-secondary">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-info text-white rounded-circle">
                                            JS
                                        </div>
                                    </div>
                                    <h6 class="card-title">Jane Smith</h6>
                                    <p class="text-muted">Editor</p>
                                    <span class="badge bg-success">Active</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-secondary">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-warning text-white rounded-circle">
                                            MJ
                                        </div>
                                    </div>
                                    <h6 class="card-title">Mike Johnson</h6>
                                    <p class="text-muted">Viewer</p>
                                    <span class="badge bg-warning">Pending</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-secondary">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection