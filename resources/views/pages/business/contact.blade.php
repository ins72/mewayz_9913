@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="min-h-screen bg-secondary">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-primary to-secondary text-white py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">Get In Touch</h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8">
                    We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                </p>
            </div>
        </div>
    </div>

    <!-- Contact Form & Info -->
    <div class="py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Send us a message</h2>
                        </div>
                        <form id="contactForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-primary mb-2">First Name</label>
                                    <input type="text" name="first_name" required class="form-input w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-primary mb-2">Last Name</label>
                                    <input type="text" name="last_name" required class="form-input w-full">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary mb-2">Email Address</label>
                                <input type="email" name="email" required class="form-input w-full">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary mb-2">Company</label>
                                <input type="text" name="company" class="form-input w-full">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary mb-2">Subject</label>
                                <select name="subject" required class="form-input w-full">
                                    <option value="">Select a subject</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="support">Technical Support</option>
                                    <option value="sales">Sales Question</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="press">Press Inquiry</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary mb-2">Message</label>
                                <textarea name="message" rows="6" required class="form-input w-full" placeholder="Tell us how we can help you..."></textarea>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="consent" required class="mr-3">
                                <label class="text-sm text-secondary">I agree to receive marketing communications from Mewayz</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-full">Send Message</button>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Contact Information</h3>
                            </div>
                            <div class="space-y-6">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-primary">Email</h4>
                                        <p class="text-secondary">support@mewayz.com</p>
                                        <p class="text-secondary">sales@mewayz.com</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-primary">Phone</h4>
                                        <p class="text-secondary">+1 (555) 123-4567</p>
                                        <p class="text-sm text-gray-400">Monday - Friday, 9AM - 6PM EST</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-primary">Address</h4>
                                        <p class="text-secondary">123 Business Street</p>
                                        <p class="text-secondary">San Francisco, CA 94103</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Business Hours</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-secondary">Monday - Friday</span>
                                    <span class="text-primary">9:00 AM - 6:00 PM EST</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-secondary">Saturday</span>
                                    <span class="text-primary">10:00 AM - 4:00 PM EST</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-secondary">Sunday</span>
                                    <span class="text-primary">Closed</span>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Follow Us</h3>
                            </div>
                            <div class="flex space-x-4">
                                <a href="#" class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                    </svg>
                                </a>
                                <a href="#" class="w-12 h-12 bg-blue-800 rounded-lg flex items-center justify-center hover:bg-blue-900 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                </a>
                                <a href="#" class="w-12 h-12 bg-pink-600 rounded-lg flex items-center justify-center hover:bg-pink-700 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="py-20 bg-primary">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12 text-white">Frequently Asked Questions</h2>
                <div class="space-y-6">
                    <div class="bg-white rounded-lg p-6 shadow-lg">
                        <h3 class="font-semibold text-primary mb-3">How quickly will I receive a response?</h3>
                        <p class="text-secondary">We aim to respond to all inquiries within 24 hours during business days. For urgent technical issues, we typically respond within 2-4 hours.</p>
                    </div>
                    <div class="bg-white rounded-lg p-6 shadow-lg">
                        <h3 class="font-semibold text-primary mb-3">Do you offer phone support?</h3>
                        <p class="text-secondary">Yes, we offer phone support for premium plan customers. Contact us through this form to schedule a call or speak with our sales team.</p>
                    </div>
                    <div class="bg-white rounded-lg p-6 shadow-lg">
                        <h3 class="font-semibold text-primary mb-3">Can I schedule a demo?</h3>
                        <p class="text-secondary">Absolutely! We'd love to show you how Mewayz can help your business. Select "Sales Question" as your subject and mention you'd like to schedule a demo.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('contactForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch('/api/business/contact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            alert('Thank you for your message! We\'ll get back to you soon.');
            this.reset();
        } else {
            alert('There was an error sending your message. Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('There was an error sending your message. Please try again.');
    }
});
</script>
@endsection