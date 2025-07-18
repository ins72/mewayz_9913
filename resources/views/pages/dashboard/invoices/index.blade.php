@extends('layouts.dashboard')

@section('title', 'Invoices')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Invoices</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Invoices</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Invoice Management</h5>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm">Create Invoice</button>
                    </div>
                </div>
                <div class="card-body">
                    <p>Manage your invoices and billing here.</p>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-primary">24</h4>
                                    <p class="text-muted">Total Invoices</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-success">18</h4>
                                    <p class="text-muted">Paid</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-warning">4</h4>
                                    <p class="text-muted">Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-danger">2</h4>
                                    <p class="text-muted">Overdue</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>INV-001</td>
                                    <td>John Doe</td>
                                    <td>2023-12-15</td>
                                    <td>$500.00</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View</button>
                                        <button class="btn btn-sm btn-secondary">Download</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>INV-002</td>
                                    <td>Jane Smith</td>
                                    <td>2023-12-20</td>
                                    <td>$750.00</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View</button>
                                        <button class="btn btn-sm btn-secondary">Download</button>
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