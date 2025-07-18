@extends('layouts.dashboard')

@section('title', 'Automation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Automation</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Automation</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Marketing Automation</h5>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm">Create Automation</button>
                    </div>
                </div>
                <div class="card-body">
                    <p>Set up automated workflows for your marketing campaigns.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Welcome Series</h6>
                                    <p class="card-text">Automated welcome email sequence for new subscribers</p>
                                    <span class="badge bg-success">Active</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-secondary">View Stats</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Abandoned Cart</h6>
                                    <p class="card-text">Recover abandoned shopping carts</p>
                                    <span class="badge bg-warning">Paused</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-secondary">View Stats</button>
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