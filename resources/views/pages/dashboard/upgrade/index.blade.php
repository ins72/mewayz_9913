@extends('layouts.dashboard')

@section('title', 'Upgrade')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Upgrade Your Plan</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Upgrade</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Choose Your Plan</h5>
                </div>
                <div class="card-body">
                    <p>Upgrade to unlock more features and grow your business.</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Basic Plan</h6>
                                    <h3 class="text-primary">$9.99<small>/month</small></h3>
                                    <ul class="list-unstyled">
                                        <li>✓ Up to 5 bio sites</li>
                                        <li>✓ Basic analytics</li>
                                        <li>✓ Standard support</li>
                                        <li>✓ 1GB storage</li>
                                    </ul>
                                    <div class="mt-3">
                                        <button class="btn btn-primary w-100">Current Plan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="badge bg-primary mb-2">Popular</div>
                                    <h6 class="card-title">Pro Plan</h6>
                                    <h3 class="text-primary">$19.99<small>/month</small></h3>
                                    <ul class="list-unstyled">
                                        <li>✓ Unlimited bio sites</li>
                                        <li>✓ Advanced analytics</li>
                                        <li>✓ Priority support</li>
                                        <li>✓ 10GB storage</li>
                                        <li>✓ Custom domains</li>
                                        <li>✓ Email marketing</li>
                                    </ul>
                                    <div class="mt-3">
                                        <button class="btn btn-primary w-100">Upgrade Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Enterprise Plan</h6>
                                    <h3 class="text-primary">$49.99<small>/month</small></h3>
                                    <ul class="list-unstyled">
                                        <li>✓ Everything in Pro</li>
                                        <li>✓ White-label solution</li>
                                        <li>✓ API access</li>
                                        <li>✓ 100GB storage</li>
                                        <li>✓ Dedicated support</li>
                                        <li>✓ Advanced integrations</li>
                                        <li>✓ Team collaboration</li>
                                    </ul>
                                    <div class="mt-3">
                                        <button class="btn btn-primary w-100">Contact Sales</button>
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