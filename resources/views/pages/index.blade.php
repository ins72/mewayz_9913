@extends('layouts.app')

@section('title', 'Mewayz - The Ultimate All-in-One Business Platform')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="py-20">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div class="space-y-6">
                        <h1 style="font-size: 3.5rem; font-weight: 800; line-height: 1.1; color: var(--text-primary);">
                            Build Your Digital Empire
                        </h1>
                        <p style="font-size: 1.25rem; color: var(--text-secondary); line-height: 1.6;">
                            The ultimate all-in-one platform for creators, entrepreneurs, and businesses. 
                            Manage social media, sell products, create courses, and grow your audience—all in one place.
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap gap-4">
                        <a href="#" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1rem;">
                            Start Free Trial
                        </a>
                        <a href="#features" class="btn btn-secondary" style="padding: 1rem 2rem; font-size: 1rem;">
                            See Features
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-8">
                        <div class="text-center">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">10,000+</div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">Active Users</div>
                        </div>
                        <div class="text-center">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">$2M+</div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">Revenue Generated</div>
                        </div>
                        <div class="text-center">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">99.9%</div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">Uptime</div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center">
                    <div class="card" style="padding: 2rem; max-width: 400px;">
                        <div style="text-align: center; margin-bottom: 1.5rem;">
                            <div style="background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); width: 4rem; height: 4rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                <span style="font-size: 1.5rem; color: white;">🚀</span>
                            </div>
                            <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                                Get Started Today
                            </h3>
                            <p style="color: var(--text-secondary); font-size: 0.875rem;">
                                Join thousands of creators building their digital empire
                            </p>
                        </div>
                        
                        <div class="space-y-4">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="color: var(--accent-secondary); font-size: 1.25rem;">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">No credit card required</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="color: var(--accent-secondary); font-size: 1.25rem;">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">14-day free trial</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="color: var(--accent-secondary); font-size: 1.25rem;">✓</span>
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">Cancel anytime</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section id="features" class="py-20" style="background-color: var(--bg-secondary);">
        <div class="container">
            <div class="text-center mb-16">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">
                    Everything You Need to Succeed
                </h2>
                <p style="font-size: 1.125rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                    Powerful tools designed to help you build, grow, and monetize your digital presence
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="background: linear-gradient(135deg, var(--accent-primary), #2563eb); width: 3rem; height: 3rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem; color: white;">📱</span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Social Media Management
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Schedule posts, manage multiple accounts, and grow your social media presence across all platforms.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="background: linear-gradient(135deg, var(--accent-secondary), #059669); width: 3rem; height: 3rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem; color: white;">🛍️</span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            E-commerce Store
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Create and manage your online store with inventory tracking, payment processing, and order management.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="background: linear-gradient(135deg, var(--accent-warning), #d97706); width: 3rem; height: 3rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem; color: white;">📚</span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Course Creation
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Build and sell online courses with video hosting, quizzes, certificates, and student management.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); width: 3rem; height: 3rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem; color: white;">✉️</span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Email Marketing
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Create campaigns, automate sequences, and build relationships with your audience through email.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="background: linear-gradient(135deg, #ec4899, #db2777); width: 3rem; height: 3rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem; color: white;">🔗</span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Link in Bio
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Create beautiful landing pages to showcase all your links, products, and content in one place.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="background: linear-gradient(135deg, #06b6d4, #0891b2); width: 3rem; height: 3rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem; color: white;">📊</span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Analytics & Insights
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Track your performance with detailed analytics, audience insights, and revenue reports.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="background: linear-gradient(135deg, #10b981, #059669); width: 3rem; height: 3rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem; color: white;">🛒</span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            E-commerce Store
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Sell products, courses, and services directly through your personalized storefront.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); width: 3rem; height: 3rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem; color: white;">🎮</span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Gamification System
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Engage your audience with achievements, leaderboards, and reward systems.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); width: 3rem; height: 3rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.25rem; color: white;">🔐</span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Enterprise Security
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">
                            Advanced security features with role-based access control and audit logging.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="py-20" style="background: var(--surface-secondary);">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">
                    Production-Ready Platform
                </h2>
                <p style="font-size: 1.125rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                    Built with enterprise-grade infrastructure and comprehensive feature set
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card" style="text-align: center; padding: 2rem;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 0.5rem;">
                        62
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">
                        Database Tables
                    </div>
                </div>
                
                <div class="card" style="text-align: center; padding: 2rem;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 0.5rem;">
                        100%
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">
                        Legal Compliance
                    </div>
                </div>
                
                <div class="card" style="text-align: center; padding: 2rem;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 0.5rem;">
                        20+
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">
                        Core Features
                    </div>
                </div>
                
                <div class="card" style="text-align: center; padding: 2rem;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 0.5rem;">
                        ∞
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">
                        Scalability
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20">
        <div class="container">
            <div class="card" style="text-align: center; padding: 3rem; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); border: none;">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: white; margin-bottom: 1rem;">
                    Ready to Build Your Empire?
                </h2>
                <p style="font-size: 1.125rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                    Join thousands of creators and entrepreneurs who are already building their digital empire with Mewayz.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="#" class="btn" style="background: white; color: var(--accent-primary); padding: 1rem 2rem; font-size: 1rem; font-weight: 600;">
                        Start Free Trial
                    </a>
                    <a href="#" class="btn" style="background: transparent; color: white; border: 2px solid white; padding: 1rem 2rem; font-size: 1rem;">
                        Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection