@extends('layouts.dashboard')

@section('title', 'Community')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Community</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Community</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Community Management</h5>
                </div>
                <div class="card-body">
                    <p>Manage your community and engagement here.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Community Stats</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-primary">2,456</h4>
                                                <p class="text-muted">Members</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-success">89</h4>
                                                <p class="text-muted">Active Today</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Recent Activity</h6>
                                    <div class="activity-feed">
                                        <div class="activity-item">
                                            <strong>John Doe</strong> joined the community
                                            <small class="text-muted">2 hours ago</small>
                                        </div>
                                        <div class="activity-item">
                                            <strong>Jane Smith</strong> started a discussion
                                            <small class="text-muted">5 hours ago</small>
                                        </div>
                                        <div class="activity-item">
                                            <strong>Mike Johnson</strong> shared a resource
                                            <small class="text-muted">1 day ago</small>
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
</div>
@endsection