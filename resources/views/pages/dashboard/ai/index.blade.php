@extends('layouts.dashboard')

@section('title', 'AI Tools')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">AI-Powered Tools</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Usage Stats</button>
                <button class="btn btn-primary btn-sm">New AI Task</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Leverage advanced AI capabilities to automate content creation, analysis, and optimization.
            </p>
        </div>
    </div>

    <!-- AI Usage Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">2,847</div>
            <div class="stat-label">AI Tasks Completed</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +247 this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">89.2%</div>
            <div class="stat-label">Accuracy Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2.3% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">12.5h</div>
            <div class="stat-label">Time Saved</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +3.2h from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">$245</div>
            <div class="stat-label">Monthly Usage</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +$45 from last month
            </div>
        </div>
    </div>

    <!-- AI Tools Categories -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">AI Tool Categories</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">✍️</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Content Creation</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🎨</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Design & Media</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📊</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Analytics & Insights</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🤖</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Automation</div>
            </button>
        </div>
    </div>

    <!-- Content Creation Tools -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Content Creation Tools</h3>
            <a href="#" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Blog Writer -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">📝</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Blog Writer</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Generate engaging blog posts</div>
                        </div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Create SEO-optimized blog posts on any topic with AI assistance.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">47 posts generated</div>
                        <button class="btn btn-sm btn-primary">Use Tool</button>
                    </div>
                </div>
            </div>

            <!-- Social Media Generator -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-secondary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">📱</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Social Media Posts</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Create engaging social content</div>
                        </div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Generate posts for Facebook, Twitter, LinkedIn, and Instagram.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">156 posts generated</div>
                        <button class="btn btn-sm btn-primary">Use Tool</button>
                    </div>
                </div>
            </div>

            <!-- Email Templates -->
            <div class="card">
                <div class="space-y-4">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; background: var(--accent-warning); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: white; font-size: 1.25rem;">✉️</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--text-primary);">Email Templates</div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem;">Craft compelling email campaigns</div>
                        </div>
                    </div>
                    <div>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;">Create personalized email templates for marketing campaigns.</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">89 templates created</div>
                        <button class="btn btn-sm btn-primary">Use Tool</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Assistant Chat -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">AI Assistant</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-secondary">Clear Chat</button>
                <button class="btn btn-sm btn-primary">New Conversation</button>
            </div>
        </div>
        <div style="height: 400px; display: flex; flex-direction: column;">
            <!-- Chat Messages -->
            <div style="flex: 1; overflow-y: auto; padding: 1rem; background: var(--bg-primary); border-radius: 8px; margin-bottom: 1rem;">
                <div class="space-y-4">
                    <!-- AI Message -->
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <div style="width: 2rem; height: 2rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span style="color: white; font-size: 0.75rem;">AI</span>
                        </div>
                        <div style="flex: 1; background: var(--bg-secondary); padding: 1rem; border-radius: 8px;">
                            <p style="color: var(--text-primary); font-size: 0.875rem; margin: 0;">Hello! I'm your AI assistant. How can I help you today? I can help with content creation, data analysis, marketing strategies, and much more.</p>
                        </div>
                    </div>
                    
                    <!-- User Message -->
                    <div style="display: flex; align-items: start; gap: 1rem; justify-content: flex-end;">
                        <div style="flex: 1; max-width: 80%; background: var(--accent-primary); padding: 1rem; border-radius: 8px;">
                            <p style="color: white; font-size: 0.875rem; margin: 0;">Can you help me write a blog post about digital marketing trends for 2024?</p>
                        </div>
                        <div style="width: 2rem; height: 2rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span style="color: white; font-size: 0.75rem;">JD</span>
                        </div>
                    </div>
                    
                    <!-- AI Response -->
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <div style="width: 2rem; height: 2rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span style="color: white; font-size: 0.75rem;">AI</span>
                        </div>
                        <div style="flex: 1; background: var(--bg-secondary); padding: 1rem; border-radius: 8px;">
                            <p style="color: var(--text-primary); font-size: 0.875rem; margin: 0;">I'd be happy to help you create a blog post about digital marketing trends for 2024! Here are some key trends I can help you explore:</p>
                            <ul style="color: var(--text-primary); font-size: 0.875rem; margin: 0.5rem 0 0 1rem;">
                                <li>AI-powered personalization</li>
                                <li>Voice search optimization</li>
                                <li>Interactive content experiences</li>
                                <li>Privacy-first marketing strategies</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat Input -->
            <div style="display: flex; gap: 1rem; align-items: center;">
                <input type="text" placeholder="Ask me anything..." class="form-input" style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-primary); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
                <button class="btn btn-primary">Send</button>
            </div>
        </div>
    </div>

    <!-- Recent AI Tasks -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Tasks -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent AI Tasks</h3>
                <a href="#" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📝</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Blog Post Generated</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">"Top 10 Marketing Strategies for 2024" • 2 hours ago</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--accent-secondary); font-size: 0.75rem;">Completed</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🎨</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Image Generated</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Social media banner for campaign • 4 hours ago</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--accent-secondary); font-size: 0.75rem;">Completed</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📊</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Data Analysis</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Customer behavior analysis • 6 hours ago</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--accent-secondary); font-size: 0.75rem;">Completed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Models -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Available AI Models</h3>
                <button class="btn btn-secondary btn-sm">Manage Models</button>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🧠</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">GPT-4</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Advanced language model for content creation</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--accent-secondary); font-size: 0.75rem;">✅ Active</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🎨</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">DALL-E 3</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">AI image generation and editing</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--accent-secondary); font-size: 0.75rem;">✅ Active</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🔍</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Claude 3</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">Advanced reasoning and analysis</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">⚫ Inactive</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection