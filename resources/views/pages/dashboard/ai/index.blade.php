@extends('layouts.dashboard')

@section('title', 'AI Assistant')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">AI Assistant</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">AI Assistant</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">AI-Powered Tools</h5>
                </div>
                <div class="card-body">
                    <p>Leverage AI to enhance your content creation and marketing.</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Content Generator</h6>
                                    <p class="card-text">Generate blog posts, social media content, and more</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Generate Content</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">SEO Assistant</h6>
                                    <p class="card-text">Optimize your content for search engines</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Optimize SEO</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Email Templates</h6>
                                    <p class="card-text">Create engaging email campaigns with AI</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Create Email</button>
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