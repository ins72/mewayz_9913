@extends('layouts.dashboard')

@section('title', 'Sites')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Sites</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sites</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Site Management</h5>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm">Create New Site</button>
                    </div>
                </div>
                <div class="card-body">
                    <p>Manage your websites and bio sites here.</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">My Portfolio</h6>
                                    <p class="card-text">Personal portfolio website</p>
                                    <span class="badge bg-success">Active</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-secondary">View</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Business Site</h6>
                                    <p class="card-text">Professional business website</p>
                                    <span class="badge bg-warning">Draft</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-secondary">View</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Link in Bio</h6>
                                    <p class="card-text">Social media bio page</p>
                                    <span class="badge bg-success">Active</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-secondary">View</button>
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