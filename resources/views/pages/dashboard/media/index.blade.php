@extends('layouts.dashboard')

@section('title', 'Media Library')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Media Library</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Media</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Media Management</h5>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm">Upload Media</button>
                    </div>
                </div>
                <div class="card-body">
                    <p>Manage your images, videos, and documents.</p>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-primary">156</h4>
                                    <p class="text-muted">Total Files</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-success">89</h4>
                                    <p class="text-muted">Images</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-info">23</h4>
                                    <p class="text-muted">Videos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="text-warning">44</h4>
                                    <p class="text-muted">Documents</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-image fa-3x text-primary"></i>
                                    </div>
                                    <h6 class="card-title">hero-image.jpg</h6>
                                    <p class="text-muted">2.5 MB</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-video fa-3x text-success"></i>
                                    </div>
                                    <h6 class="card-title">intro-video.mp4</h6>
                                    <p class="text-muted">15.8 MB</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                    </div>
                                    <h6 class="card-title">guide.pdf</h6>
                                    <p class="text-muted">1.2 MB</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-image fa-3x text-warning"></i>
                                    </div>
                                    <h6 class="card-title">logo.png</h6>
                                    <p class="text-muted">456 KB</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-danger">Delete</button>
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