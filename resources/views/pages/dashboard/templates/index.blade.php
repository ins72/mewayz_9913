@extends('layouts.dashboard')

@section('title', 'Templates')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Templates</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Templates</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Template Library</h5>
                </div>
                <div class="card-body">
                    <p>Choose from our collection of professional templates.</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Modern Portfolio</h6>
                                    <p class="card-text">Clean and modern portfolio template</p>
                                    <span class="badge bg-primary">Popular</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Use Template</button>
                                        <button class="btn btn-sm btn-secondary">Preview</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Business Professional</h6>
                                    <p class="card-text">Professional business template</p>
                                    <span class="badge bg-success">New</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Use Template</button>
                                        <button class="btn btn-sm btn-secondary">Preview</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Creative Agency</h6>
                                    <p class="card-text">Creative and colorful agency template</p>
                                    <span class="badge bg-warning">Featured</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Use Template</button>
                                        <button class="btn btn-sm btn-secondary">Preview</button>
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