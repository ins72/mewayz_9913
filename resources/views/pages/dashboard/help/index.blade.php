@extends('layouts.dashboard')

@section('title', 'Help & Support')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Help & Support</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Help</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Support Center</h5>
                </div>
                <div class="card-body">
                    <p>Find answers to your questions and get help.</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Documentation</h6>
                                    <p class="card-text">Browse our comprehensive guides and tutorials</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">View Docs</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Video Tutorials</h6>
                                    <p class="card-text">Watch step-by-step video guides</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Watch Videos</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Contact Support</h6>
                                    <p class="card-text">Get help from our support team</p>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary">Contact Us</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Frequently Asked Questions</h6>
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                            How do I create a new bio site?
                                        </button>
                                    </h2>
                                    <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            To create a new bio site, navigate to the Sites section and click "Create New Site". Follow the wizard to set up your bio site.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq2">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                            How do I connect my social media accounts?
                                        </button>
                                    </h2>
                                    <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Go to Integrations and click "Connect" next to the social media platform you want to connect. Follow the authorization process.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq3">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                            How do I set up payment processing?
                                        </button>
                                    </h2>
                                    <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Navigate to Integrations and connect your Stripe account. This will enable payment processing for your services and products.
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
</div>
@endsection