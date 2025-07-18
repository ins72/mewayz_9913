@extends('layouts.dashboard')

@section('title', 'Booking')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Booking Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Booking</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Booking System</h5>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm">Create New Service</button>
                    </div>
                </div>
                <div class="card-body">
                    <p>Manage your booking services and appointments here.</p>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-primary">24</h4>
                                    <p class="text-muted">Total Bookings</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-success">8</h4>
                                    <p class="text-muted">Today</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-info">15</h4>
                                    <p class="text-muted">This Week</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-warning">5</h4>
                                    <p class="text-muted">Services</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Consultation</td>
                                    <td>John Doe</td>
                                    <td>2023-12-25</td>
                                    <td>10:00 AM</td>
                                    <td><span class="badge bg-success">Confirmed</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-danger">Cancel</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Strategy Session</td>
                                    <td>Jane Smith</td>
                                    <td>2023-12-26</td>
                                    <td>2:00 PM</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-danger">Cancel</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection