@extends('layouts.app')

@section('title', 'Support')
@section('meta_description', 'Get help and support for Mewayz. Find answers to common questions and access our knowledge base.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-900 mb-4">Support Center</h1>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                    Find answers to your questions and get the help you need to make the most of Mewayz.
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">FAQ</h3>
                    <p class="text-slate-600 mb-4">Find answers to commonly asked questions</p>
                    <button onclick="scrollToSection('faq')" class="text-blue-600 hover:text-blue-800 font-semibold">
                        Browse FAQ →
                    </button>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Contact Support</h3>
                    <p class="text-slate-600 mb-4">Get personalized help from our team</p>
                    <a href="/contact" class="text-green-600 hover:text-green-800 font-semibold">
                        Contact Us →
                    </a>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Knowledge Base</h3>
                    <p class="text-slate-600 mb-4">Detailed guides and tutorials</p>
                    <button onclick="scrollToSection('knowledge-base')" class="text-purple-600 hover:text-purple-800 font-semibold">
                        Browse Guides →
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-12">
                <div class="max-w-2xl mx-auto">
                    <div class="relative">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Search for help topics..."
                               class="w-full px-6 py-4 pr-12 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute right-3 top-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div id="faq" class="bg-white rounded-2xl shadow-lg p-8 mb-12">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">Frequently Asked Questions</h2>
                
                <div class="space-y-4">
                    <div class="border border-slate-200 rounded-lg">
                        <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-slate-50 transition-colors" onclick="toggleFAQ(this)">
                            <span class="font-semibold text-slate-900">How do I get started with Mewayz?</span>
                            <svg class="w-5 h-5 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-4 hidden">
                            <p class="text-slate-600">Getting started is easy! Simply create an account, complete the workspace setup wizard, and you'll be guided through the initial configuration of your chosen features.</p>
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-lg">
                        <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-slate-50 transition-colors" onclick="toggleFAQ(this)">
                            <span class="font-semibold text-slate-900">What payment methods do you accept?</span>
                            <svg class="w-5 h-5 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-4 hidden">
                            <p class="text-slate-600">We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and bank transfers. All payments are processed securely through our encrypted payment gateway.</p>
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-lg">
                        <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-slate-50 transition-colors" onclick="toggleFAQ(this)">
                            <span class="font-semibold text-slate-900">Can I cancel my subscription anytime?</span>
                            <svg class="w-5 h-5 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-4 hidden">
                            <p class="text-slate-600">Yes, you can cancel your subscription at any time from your account settings. Your service will continue until the end of your current billing period.</p>
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-lg">
                        <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-slate-50 transition-colors" onclick="toggleFAQ(this)">
                            <span class="font-semibold text-slate-900">How do I integrate with social media platforms?</span>
                            <svg class="w-5 h-5 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-4 hidden">
                            <p class="text-slate-600">You can connect your social media accounts through the Integrations section in your dashboard. We support Instagram, Facebook, Twitter, LinkedIn, and more.</p>
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-lg">
                        <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-slate-50 transition-colors" onclick="toggleFAQ(this)">
                            <span class="font-semibold text-slate-900">Is my data secure?</span>
                            <svg class="w-5 h-5 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-4 hidden">
                            <p class="text-slate-600">Absolutely! We use enterprise-grade security measures including SSL encryption, regular security audits, and compliance with data protection regulations like GDPR and CCPA.</p>
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-lg">
                        <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-slate-50 transition-colors" onclick="toggleFAQ(this)">
                            <span class="font-semibold text-slate-900">How do I export my data?</span>
                            <svg class="w-5 h-5 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-4 hidden">
                            <p class="text-slate-600">You can export your data from the Privacy section in your account settings. We provide data in multiple formats including JSON, CSV, and PDF.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Knowledge Base Section -->
            <div id="knowledge-base" class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">Knowledge Base</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="border border-slate-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">Getting Started</h3>
                        <ul class="space-y-2 text-slate-600">
                            <li><a href="#" class="hover:text-blue-600">Account Setup</a></li>
                            <li><a href="#" class="hover:text-blue-600">Workspace Configuration</a></li>
                            <li><a href="#" class="hover:text-blue-600">First Steps Guide</a></li>
                            <li><a href="#" class="hover:text-blue-600">Feature Overview</a></li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">Social Media</h3>
                        <ul class="space-y-2 text-slate-600">
                            <li><a href="#" class="hover:text-blue-600">Instagram Integration</a></li>
                            <li><a href="#" class="hover:text-blue-600">Content Scheduling</a></li>
                            <li><a href="#" class="hover:text-blue-600">Analytics & Insights</a></li>
                            <li><a href="#" class="hover:text-blue-600">Link in Bio Setup</a></li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">Course Creation</h3>
                        <ul class="space-y-2 text-slate-600">
                            <li><a href="#" class="hover:text-blue-600">Creating Your First Course</a></li>
                            <li><a href="#" class="hover:text-blue-600">Video Upload & Management</a></li>
                            <li><a href="#" class="hover:text-blue-600">Student Enrollment</a></li>
                            <li><a href="#" class="hover:text-blue-600">Certificates & Completion</a></li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">E-commerce</h3>
                        <ul class="space-y-2 text-slate-600">
                            <li><a href="#" class="hover:text-blue-600">Product Setup</a></li>
                            <li><a href="#" class="hover:text-blue-600">Payment Processing</a></li>
                            <li><a href="#" class="hover:text-blue-600">Order Management</a></li>
                            <li><a href="#" class="hover:text-blue-600">Shipping & Fulfillment</a></li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">Email Marketing</h3>
                        <ul class="space-y-2 text-slate-600">
                            <li><a href="#" class="hover:text-blue-600">Campaign Creation</a></li>
                            <li><a href="#" class="hover:text-blue-600">List Management</a></li>
                            <li><a href="#" class="hover:text-blue-600">Automation Workflows</a></li>
                            <li><a href="#" class="hover:text-blue-600">Performance Tracking</a></li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">Account & Billing</h3>
                        <ul class="space-y-2 text-slate-600">
                            <li><a href="#" class="hover:text-blue-600">Subscription Management</a></li>
                            <li><a href="#" class="hover:text-blue-600">Billing & Invoices</a></li>
                            <li><a href="#" class="hover:text-blue-600">Team Management</a></li>
                            <li><a href="#" class="hover:text-blue-600">Privacy Settings</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFAQ(button) {
    const answer = button.nextElementSibling;
    const icon = button.querySelector('svg');
    
    if (answer.classList.contains('hidden')) {
        answer.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        answer.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

// Simple search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    const faqItems = document.querySelectorAll('#faq .border');
    
    faqItems.forEach(item => {
        const question = item.querySelector('span').textContent.toLowerCase();
        const answer = item.querySelector('.px-6.pb-4 p').textContent.toLowerCase();
        
        if (question.includes(query) || answer.includes(query)) {
            item.style.display = 'block';
        } else {
            item.style.display = query === '' ? 'block' : 'none';
        }
    });
});
</script>
@endsection