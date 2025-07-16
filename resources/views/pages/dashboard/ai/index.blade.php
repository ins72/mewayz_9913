<x-layouts.dashboard title="AI Assistant - Mewayz" page-title="AI Assistant">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">AI Assistant</h1>
                <p class="text-secondary-text">Get AI-powered help with content creation, business strategy, and more</p>
            </div>
            <div class="flex gap-3">
                <button class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Usage Stats
                </button>
                <button class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    New Chat
                </button>
            </div>
        </div>

        <!-- AI Features -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="card hover:bg-hover-bg cursor-pointer transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-info/10 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </div>
                    <span class="text-xs bg-info/10 text-info px-2 py-1 rounded">Popular</span>
                </div>
                <h3 class="text-lg font-semibold text-primary-text mb-2">Content Creation</h3>
                <p class="text-secondary-text text-sm mb-4">Generate blog posts, social media content, and marketing copy</p>
                <button class="btn btn-primary w-full">Start Creating</button>
            </div>

            <div class="card hover:bg-hover-bg cursor-pointer transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-success/10 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <span class="text-xs bg-success/10 text-success px-2 py-1 rounded">New</span>
                </div>
                <h3 class="text-lg font-semibold text-primary-text mb-2">Business Strategy</h3>
                <p class="text-secondary-text text-sm mb-4">Get strategic advice and business insights</p>
                <button class="btn btn-primary w-full">Get Advice</button>
            </div>

            <div class="card hover:bg-hover-bg cursor-pointer transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-warning/10 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v3M7 4H5a1 1 0 00-1 1v14a1 1 0 001 1h14a1 1 0 001-1V5a1 1 0 00-1-1h-2M7 4h10M9 9h6m-6 4h6m-6 4h6"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-primary-text mb-2">Email Templates</h3>
                <p class="text-secondary-text text-sm mb-4">Create professional email templates and campaigns</p>
                <button class="btn btn-primary w-full">Create Template</button>
            </div>

            <div class="card hover:bg-hover-bg cursor-pointer transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-error/10 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-primary-text mb-2">Image Generation</h3>
                <p class="text-secondary-text text-sm mb-4">Generate custom images for your content and marketing</p>
                <button class="btn btn-primary w-full">Generate Image</button>
            </div>

            <div class="card hover:bg-hover-bg cursor-pointer transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-info/10 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-primary-text mb-2">SEO Optimization</h3>
                <p class="text-secondary-text text-sm mb-4">Optimize your content for search engines</p>
                <button class="btn btn-primary w-full">Optimize Content</button>
            </div>

            <div class="card hover:bg-hover-bg cursor-pointer transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-success/10 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-primary-text mb-2">Customer Support</h3>
                <p class="text-secondary-text text-sm mb-4">AI-powered customer service responses</p>
                <button class="btn btn-primary w-full">Configure Bot</button>
            </div>
        </div>

        <!-- AI Chat Interface -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chat History -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">Recent Chats</h3>
                    <button class="btn btn-secondary text-sm">Clear All</button>
                </div>
                
                <div class="space-y-3">
                    <div class="p-3 bg-app-bg rounded-lg hover:bg-hover-bg cursor-pointer transition-colors">
                        <div class="text-primary-text font-medium text-sm mb-1">Blog post about AI trends</div>
                        <div class="text-secondary-text text-xs">2 hours ago</div>
                    </div>
                    
                    <div class="p-3 bg-app-bg rounded-lg hover:bg-hover-bg cursor-pointer transition-colors">
                        <div class="text-primary-text font-medium text-sm mb-1">Email marketing strategy</div>
                        <div class="text-secondary-text text-xs">Yesterday</div>
                    </div>
                    
                    <div class="p-3 bg-app-bg rounded-lg hover:bg-hover-bg cursor-pointer transition-colors">
                        <div class="text-primary-text font-medium text-sm mb-1">Social media captions</div>
                        <div class="text-secondary-text text-xs">2 days ago</div>
                    </div>
                    
                    <div class="p-3 bg-app-bg rounded-lg hover:bg-hover-bg cursor-pointer transition-colors">
                        <div class="text-primary-text font-medium text-sm mb-1">Product descriptions</div>
                        <div class="text-secondary-text text-xs">1 week ago</div>
                    </div>
                </div>
            </div>

            <!-- Main Chat Interface -->
            <div class="lg:col-span-2 card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-primary-text">AI Assistant</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-success/10 text-success px-2 py-1 rounded">Online</span>
                        <select class="form-input text-sm">
                            <option>GPT-4</option>
                            <option>Claude</option>
                            <option>Gemini</option>
                        </select>
                    </div>
                </div>
                
                <!-- Chat Messages -->
                <div class="h-96 bg-app-bg rounded-lg p-4 mb-4 overflow-y-auto">
                    <div class="space-y-4">
                        <!-- AI Message -->
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-info/10 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <div class="bg-card-bg p-3 rounded-lg max-w-md">
                                <p class="text-primary-text text-sm">Hello! I'm your AI assistant. How can I help you today? I can help with content creation, business strategy, marketing, and much more.</p>
                            </div>
                        </div>

                        <!-- User Message -->
                        <div class="flex items-start gap-3 justify-end">
                            <div class="bg-info/10 p-3 rounded-lg max-w-md">
                                <p class="text-primary-text text-sm">I need help writing a blog post about the benefits of AI in business</p>
                            </div>
                            <div class="w-8 h-8 bg-success/10 rounded-full flex items-center justify-center">
                                <span class="text-success text-sm font-medium">U</span>
                            </div>
                        </div>

                        <!-- AI Response -->
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-info/10 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <div class="bg-card-bg p-3 rounded-lg max-w-md">
                                <p class="text-primary-text text-sm">I'd be happy to help you create a compelling blog post about AI in business! Here's a structured approach:</p>
                                <div class="mt-2 text-sm text-secondary-text">
                                    <p><strong>Title:</strong> "How AI is Transforming Modern Business: 5 Key Benefits"</p>
                                    <p><strong>Key points to cover:</strong></p>
                                    <ul class="list-disc list-inside mt-1">
                                        <li>Automation and efficiency</li>
                                        <li>Data-driven decision making</li>
                                        <li>Enhanced customer experience</li>
                                        <li>Cost reduction</li>
                                        <li>Competitive advantage</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Chat Input -->
                <div class="flex gap-3">
                    <input 
                        type="text" 
                        class="form-input flex-1" 
                        placeholder="Type your message..."
                        id="chatInput"
                    >
                    <button class="btn btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>