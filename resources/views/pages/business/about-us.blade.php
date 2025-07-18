@extends('layouts.app')

@section('title', 'About Us - Mewayz')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="py-20">
        <div class="container">
            <div class="text-center mb-16">
                <h1 style="font-size: 3rem; font-weight: 800; line-height: 1.1; color: var(--text-primary); margin-bottom: 1.5rem;">
                    About Mewayz
                </h1>
                <p style="font-size: 1.25rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                    Empowering creators and entrepreneurs to build their digital empire with cutting-edge technology
                </p>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="py-20" style="background-color: var(--bg-secondary);">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem;">
                        Our Story
                    </h2>
                    <p style="font-size: 1.125rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
                        Founded in 2024, Mewayz was born from a simple idea: every creator and entrepreneur deserves access to professional-grade tools without the complexity or cost.
                    </p>
                    <p style="font-size: 1.125rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
                        We've built an all-in-one platform that combines social media management, e-commerce, course creation, email marketing, and analytics into one seamless experience.
                    </p>
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">
                        Today, we're proud to serve over 10,000 creators worldwide, helping them generate millions in revenue while building meaningful connections with their audiences.
                    </p>
                </div>
                <div class="card" style="padding: 2rem;">
                    <div class="text-center">
                        <div style="background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); width: 4rem; height: 4rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                            <span style="font-size: 1.5rem; color: white;">🚀</span>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
                            Our Mission
                        </h3>
                        <p style="color: var(--text-secondary);">
                            To democratize access to powerful business tools, enabling anyone to build, grow, and monetize their digital presence without technical barriers.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-20">
        <div class="container">
            <div class="text-center mb-16">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">
                    Our Values
                </h2>
                <p style="font-size: 1.125rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                    The principles that guide everything we do
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card text-center">
                    <div style="background: linear-gradient(135deg, var(--accent-primary), #2563eb); width: 3rem; height: 3rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <span style="font-size: 1.25rem; color: white;">💡</span>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
                        Innovation
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">
                        We constantly push the boundaries of what's possible, integrating the latest technologies to give our users a competitive edge.
                    </p>
                </div>
                
                <div class="card text-center">
                    <div style="background: linear-gradient(135deg, var(--accent-secondary), #059669); width: 3rem; height: 3rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <span style="font-size: 1.25rem; color: white;">🤝</span>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
                        Empowerment
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">
                        We believe in empowering creators with the tools and knowledge they need to succeed on their own terms.
                    </p>
                </div>
                
                <div class="card text-center">
                    <div style="background: linear-gradient(135deg, var(--accent-warning), #d97706); width: 3rem; height: 3rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <span style="font-size: 1.25rem; color: white;">⚡</span>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">
                        Simplicity
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">
                        Complex technology should be simple to use. We design with user experience at the forefront of every decision.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-20" style="background-color: var(--bg-secondary);">
        <div class="container">
            <div class="text-center mb-16">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">
                    Meet Our Team
                </h2>
                <p style="font-size: 1.125rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                    The passionate people behind Mewayz
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card text-center">
                    <div style="width: 4rem; height: 4rem; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.25rem; color: white; font-weight: 600;">
                        JS
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                        Jane Smith
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;">
                        CEO & Founder
                    </p>
                    <p style="color: var(--text-secondary); font-size: 0.8125rem;">
                        Former product manager at major tech companies, passionate about democratizing business tools.
                    </p>
                </div>
                
                <div class="card text-center">
                    <div style="width: 4rem; height: 4rem; background: linear-gradient(135deg, var(--accent-secondary), var(--accent-warning)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.25rem; color: white; font-weight: 600;">
                        MD
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                        Mike Davis
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;">
                        CTO & Co-Founder
                    </p>
                    <p style="color: var(--text-secondary); font-size: 0.8125rem;">
                        Full-stack engineer with 15+ years of experience building scalable platforms.
                    </p>
                </div>
                
                <div class="card text-center">
                    <div style="width: 4rem; height: 4rem; background: linear-gradient(135deg, var(--accent-warning), #ec4899); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.25rem; color: white; font-weight: 600;">
                        SJ
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                        Sarah Johnson
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;">
                        Head of Design
                    </p>
                    <p style="color: var(--text-secondary); font-size: 0.8125rem;">
                        Award-winning designer focused on creating intuitive and beautiful user experiences.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20">
        <div class="container">
            <div class="card" style="text-align: center; padding: 3rem; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); border: none;">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: white; margin-bottom: 1rem;">
                    Ready to Join Our Community?
                </h2>
                <p style="font-size: 1.125rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                    Join thousands of creators who are already building their digital empire with Mewayz.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="#" class="btn" style="background: white; color: var(--accent-primary); padding: 1rem 2rem; font-size: 1rem; font-weight: 600;">
                        Get Started Free
                    </a>
                    <a href="#" class="btn" style="background: transparent; color: white; border: 2px solid white; padding: 1rem 2rem; font-size: 1rem;">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection