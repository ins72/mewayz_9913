@extends('layouts.dashboard')

@section('title', 'Integrations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Integrations</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Integrations</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Third-Party Integrations</h5>
                </div>
                <div class="card-body">
                    <p>Connect your favorite tools and services.</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Google Analytics</h6>
                                    <p class="card-text">Track your website traffic and user behavior</p>
                                    <span class="badge bg-success">Connected</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-secondary">Configure</button>
                                        <button class="btn btn-sm btn-danger">Disconnect</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Stripe</h6>
                                    <p class="card-text">Process payments and manage subscriptions</p>
                                    <span class="badge bg-success">Connected</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-secondary">Configure</button>
                                        <button class="btn btn-sm btn-danger">Disconnect</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Mailchimp</h6>
                                    <p class="card-text">Manage your email marketing campaigns</p>
                                    <span class="badge bg-secondary">Not Connected</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Connect</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Facebook</h6>
                                    <p class="card-text">Connect your Facebook business account</p>
                                    <span class="badge bg-secondary">Not Connected</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Connect</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Instagram</h6>
                                    <p class="card-text">Manage your Instagram business account</p>
                                    <span class="badge bg-warning">Pending</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-secondary">Configure</button>
                                        <button class="btn btn-sm btn-danger">Disconnect</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Zapier</h6>
                                    <p class="card-text">Automate workflows with thousands of apps</p>
                                    <span class="badge bg-secondary">Not Connected</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Connect</button>
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