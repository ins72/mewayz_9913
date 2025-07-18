@extends('layouts.dashboard')

@section('title', 'Help & Support')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Help & Support</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Documentation</button>
                <button class="btn btn-primary btn-sm">Contact Support</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Get help with your account, find answers to common questions, and reach out to our support team.
            </p>
        </div>
    </div>

    <!-- Quick Help Options -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Getting Started -->
        <div class="card">
            <div class="space-y-4">
                <div style="text-align: center;">
                    <div style="width: 4rem; height: 4rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span style="color: white; font-size: 1.5rem;">🚀</span>
                    </div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Getting Started</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Learn the basics and set up your account</p>
                </div>
                <div class="space-y-2">
                    <a href="#" style="display: block; padding: 0.5rem; color: var(--accent-primary); text-decoration: none; font-size: 0.875rem;">• Account Setup Guide</a>
                    <a href="#" style="display: block; padding: 0.5rem; color: var(--accent-primary); text-decoration: none; font-size: 0.875rem;">• First Steps Tutorial</a>
                    <a href="#" style="display: block; padding: 0.5rem; color: var(--accent-primary); text-decoration: none; font-size: 0.875rem;">• Platform Overview</a>
                </div>
                <button class="btn btn-primary w-full">View All Guides</button>
            </div>
        </div>

        <!-- Troubleshooting -->
        <div class="card">
            <div class="space-y-4">
                <div style="text-align: center;">
                    <div style="width: 4rem; height: 4rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span style="color: white; font-size: 1.5rem;">🔧</span>
                    </div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Troubleshooting</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Fix common issues and problems</p>
                </div>
                <div class="space-y-2">
                    <a href="#" style="display: block; padding: 0.5rem; color: var(--accent-primary); text-decoration: none; font-size: 0.875rem;">• Login Issues</a>
                    <a href="#" style="display: block; padding: 0.5rem; color: var(--accent-primary); text-decoration: none; font-size: 0.875rem;">• Payment Problems</a>
                    <a href="#" style="display: block; padding: 0.5rem; color: var(--accent-primary); text-decoration: none; font-size: 0.875rem;">• Feature Not Working</a>
                </div>
                <button class="btn btn-primary w-full">Common Issues</button>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="card">
            <div class="space-y-4">
                <div style="text-align: center;">
                    <div style="width: 4rem; height: 4rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span style="color: white; font-size: 1.5rem;">💬</span>
                    </div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Contact Support</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Get personalized help from our team</p>
                </div>
                <div class="space-y-2">
                    <a href="#" style="display: block; padding: 0.5rem; color: var(--accent-primary); text-decoration: none; font-size: 0.875rem;">• Live Chat</a>
                    <a href="#" style="display: block; padding: 0.5rem; color: var(--accent-primary); text-decoration: none; font-size: 0.875rem;">• Email Support</a>
                    <a href="#" style="display: block; padding: 0.5rem; color: var(--accent-primary); text-decoration: none; font-size: 0.875rem;">• Schedule Call</a>
                </div>
                <button class="btn btn-primary w-full">Get Help Now</button>
            </div>
        </div>
    </div>

    <!-- Search Help -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Search Help Articles</h3>
        </div>
        <div style="max-width: 600px; margin: 0 auto;">
            <div style="display: flex; gap: 1rem; align-items: center;">
                <input type="text" placeholder="Search for help articles, tutorials, or guides..." class="form-input" style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
                <button class="btn btn-primary">Search</button>
            </div>
            <div style="margin-top: 1rem; color: var(--text-secondary); font-size: 0.875rem;">
                Popular searches: <a href="#" style="color: var(--accent-primary); text-decoration: none;">billing</a>, <a href="#" style="color: var(--accent-primary); text-decoration: none;">integrations</a>, <a href="#" style="color: var(--accent-primary); text-decoration: none;">API</a>
            </div>
        </div>
    </div>

    <!-- Popular Articles -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Popular Help Articles</h3>
            <a href="#" class="btn btn-secondary btn-sm">View All Articles</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span style="color: white; font-size: 0.875rem;">📚</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem; margin-bottom: 0.5rem;">How to set up your first campaign</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Step-by-step guide to creating your first marketing campaign</div>
                </div>
            </div>
            
            <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span style="color: white; font-size: 0.875rem;">🔗</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem; margin-bottom: 0.5rem;">Connecting third-party integrations</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Learn how to connect popular tools and services</div>
                </div>
            </div>
            
            <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span style="color: white; font-size: 0.875rem;">💳</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem; margin-bottom: 0.5rem;">Managing billing and subscriptions</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Update payment methods and manage your subscription</div>
                </div>
            </div>
            
            <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span style="color: white; font-size: 0.875rem;">🛡️</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem; margin-bottom: 0.5rem;">Account security and privacy</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Keep your account secure with these best practices</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Channels -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Contact Options -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Contact Support</h3>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">💬</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Live Chat</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Get instant help from our support team</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--accent-secondary); font-size: 0.75rem;">🟢 Available</div>
                        <button class="btn btn-sm btn-primary">Start Chat</button>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📧</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Email Support</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">support@mewayz.com • Response within 24 hours</div>
                    </div>
                    <div style="text-align: right;">
                        <button class="btn btn-sm btn-primary">Send Email</button>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📞</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Schedule Call</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Book a call with our technical team</div>
                    </div>
                    <div style="text-align: right;">
                        <button class="btn btn-sm btn-primary">Book Call</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Status -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">System Status</h3>
                <a href="#" class="btn btn-secondary btn-sm">Status Page</a>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 0.5rem; height: 0.5rem; background: var(--accent-secondary); border-radius: 50%;"></div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Platform Status</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">All systems operational</div>
                    </div>
                    <div style="color: var(--accent-secondary); font-size: 0.75rem;">✅ Healthy</div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 0.5rem; height: 0.5rem; background: var(--accent-secondary); border-radius: 50%;"></div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">API Services</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">All endpoints responding normally</div>
                    </div>
                    <div style="color: var(--accent-secondary); font-size: 0.75rem;">✅ Healthy</div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 0.5rem; height: 0.5rem; background: var(--accent-warning); border-radius: 50%;"></div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Email Delivery</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Experiencing minor delays</div>
                    </div>
                    <div style="color: var(--accent-warning); font-size: 0.75rem;">⚠️ Degraded</div>
                </div>
                
                <div style="padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem; margin-bottom: 0.5rem;">Recent Updates</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">
                        <div>• New AI features released - 2 hours ago</div>
                        <div>• Performance improvements - 1 day ago</div>
                        <div>• Security updates applied - 3 days ago</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Frequently Asked Questions</h3>
            <a href="#" class="btn btn-secondary btn-sm">View All FAQs</a>
        </div>
        <div class="space-y-4">
            <div style="border: 1px solid var(--border-primary); border-radius: 8px; overflow: hidden;">
                <div style="padding: 1rem; background: var(--bg-primary); cursor: pointer; display: flex; justify-content: between; align-items: center;">
                    <div style="font-weight: 500; color: var(--text-primary);">How do I upgrade my subscription plan?</div>
                    <div style="color: var(--text-secondary);">+</div>
                </div>
                <div style="padding: 1rem; border-top: 1px solid var(--border-primary); display: none;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">You can upgrade your subscription plan at any time from your account settings. Navigate to Billing & Subscriptions and select your desired plan.</p>
                </div>
            </div>
            
            <div style="border: 1px solid var(--border-primary); border-radius: 8px; overflow: hidden;">
                <div style="padding: 1rem; background: var(--bg-primary); cursor: pointer; display: flex; justify-content: between; align-items: center;">
                    <div style="font-weight: 500; color: var(--text-primary);">Can I cancel my subscription anytime?</div>
                    <div style="color: var(--text-secondary);">+</div>
                </div>
                <div style="padding: 1rem; border-top: 1px solid var(--border-primary); display: none;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Yes, you can cancel your subscription at any time. Your account will remain active until the end of your current billing period.</p>
                </div>
            </div>
            
            <div style="border: 1px solid var(--border-primary); border-radius: 8px; overflow: hidden;">
                <div style="padding: 1rem; background: var(--bg-primary); cursor: pointer; display: flex; justify-content: between; align-items: center;">
                    <div style="font-weight: 500; color: var(--text-primary);">How do I connect my social media accounts?</div>
                    <div style="color: var(--text-secondary);">+</div>
                </div>
                <div style="padding: 1rem; border-top: 1px solid var(--border-primary); display: none;">
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Go to Integrations in your dashboard and click on the social media platform you want to connect. Follow the authentication process to link your accounts.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection