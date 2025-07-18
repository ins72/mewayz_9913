@extends('layouts.app')

@section('title', 'Pricing - Mewayz')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="py-20">
        <div class="container">
            <div class="text-center mb-16">
                <h1 style="font-size: 3rem; font-weight: 800; line-height: 1.1; color: var(--text-primary); margin-bottom: 1.5rem;">
                    Simple, Transparent Pricing
                </h1>
                <p style="font-size: 1.25rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                    Choose the plan that's right for your business. Start free, upgrade as you grow.
                </p>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-20" style="background-color: var(--bg-secondary);">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Starter Plan -->
                <div class="card">
                    <div style="text-align: center; padding: 2rem 1rem;">
                        <h3 style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
                            Starter
                        </h3>
                        <div style="margin-bottom: 2rem;">
                            <span style="font-size: 3rem; font-weight: 700; color: var(--text-primary);">$0</span>
                            <span style="color: var(--text-secondary);">/month</span>
                        </div>
                        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                            Perfect for getting started with basic features
                        </p>
                        
                        <ul style="text-align: left; margin-bottom: 2rem; list-style: none; padding: 0;">
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">1 Bio Site</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">Basic Analytics</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">100 Email Contacts</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">Community Support</span>
                            </li>
                        </ul>
                        
                        <a href="#" class="btn btn-secondary w-full">
                            Get Started Free
                        </a>
                    </div>
                </div>
                
                <!-- Pro Plan -->
                <div class="card" style="border: 2px solid var(--accent-primary); position: relative;">
                    <div style="position: absolute; top: -0.75rem; left: 50%; transform: translateX(-50%); background: var(--accent-primary); color: white; padding: 0.25rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                        Most Popular
                    </div>
                    <div style="text-align: center; padding: 2rem 1rem;">
                        <h3 style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
                            Pro
                        </h3>
                        <div style="margin-bottom: 2rem;">
                            <span style="font-size: 3rem; font-weight: 700; color: var(--text-primary);">$29</span>
                            <span style="color: var(--text-secondary);">/month</span>
                        </div>
                        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                            Everything you need to grow your business
                        </p>
                        
                        <ul style="text-align: left; margin-bottom: 2rem; list-style: none; padding: 0;">
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">Unlimited Bio Sites</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">Advanced Analytics</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">10,000 Email Contacts</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">E-commerce Store</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">Course Creation</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">Priority Support</span>
                            </li>
                        </ul>
                        
                        <a href="#" class="btn btn-primary w-full">
                            Start Pro Trial
                        </a>
                    </div>
                </div>
                
                <!-- Enterprise Plan -->
                <div class="card">
                    <div style="text-align: center; padding: 2rem 1rem;">
                        <h3 style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
                            Enterprise
                        </h3>
                        <div style="margin-bottom: 2rem;">
                            <span style="font-size: 3rem; font-weight: 700; color: var(--text-primary);">$99</span>
                            <span style="color: var(--text-secondary);">/month</span>
                        </div>
                        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                            Advanced features for scaling businesses
                        </p>
                        
                        <ul style="text-align: left; margin-bottom: 2rem; list-style: none; padding: 0;">
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">Everything in Pro</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">Unlimited Contacts</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">White-label Options</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">API Access</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <span style="color: var(--accent-secondary);">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">Dedicated Support</span>
                            </li>
                        </ul>
                        
                        <a href="#" class="btn btn-secondary w-full">
                            Contact Sales
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20">
        <div class="container">
            <div class="text-center mb-16">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">
                    Frequently Asked Questions
                </h2>
                <p style="font-size: 1.125rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                    Everything you need to know about our pricing
                </p>
            </div>
            
            <div style="max-width: 800px; margin: 0 auto;">
                <div class="space-y-6">
                    <div class="card">
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Can I change my plan at any time?
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Yes! You can upgrade or downgrade your plan at any time. Changes take effect immediately, and we'll prorate any differences.
                        </p>
                    </div>
                    
                    <div class="card">
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Is there a free trial?
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Yes! We offer a 14-day free trial on all paid plans. No credit card required to start.
                        </p>
                    </div>
                    
                    <div class="card">
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            What payment methods do you accept?
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            We accept all major credit cards (Visa, MasterCard, American Express) and PayPal. All payments are processed securely.
                        </p>
                    </div>
                    
                    <div class="card">
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Do you offer refunds?
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Yes, we offer a 30-day money-back guarantee. If you're not satisfied, contact us for a full refund.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection