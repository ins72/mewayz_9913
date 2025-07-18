@extends('layouts.dashboard')

@section('title', 'Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Reports</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Analytics & Reports</h5>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm">Generate Report</button>
                    </div>
                </div>
                <div class="card-body">
                    <p>View detailed analytics and generate custom reports.</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Traffic Report</h6>
                                    <p class="card-text">Website traffic and visitor analytics</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">View Report</button>
                                        <button class="btn btn-sm btn-secondary">Download</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Sales Report</h6>
                                    <p class="card-text">Revenue and sales performance</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">View Report</button>
                                        <button class="btn btn-sm btn-secondary">Download</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Email Report</h6>
                                    <p class="card-text">Email campaign performance</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">View Report</button>
                                        <button class="btn btn-sm btn-secondary">Download</button>
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