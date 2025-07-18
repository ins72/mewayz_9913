@extends('layouts.app')

@section('title', 'Account Removal')
@section('meta_description', 'Request permanent deletion of your Mewayz account and personal data in compliance with privacy regulations.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-900 mb-4">Account Removal</h1>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                    Request permanent deletion of your account and associated data in compliance with privacy regulations.
                </p>
            </div>

            <!-- Warning Notice -->
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 18.5c-.77.833-.228 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-red-800 mb-2">Important Notice</h3>
                        <p class="text-red-700">
                            Account deletion is <strong>permanent and irreversible</strong>. Once processed, all your data will be permanently removed from our systems and cannot be recovered.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Account Removal Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">Request Account Deletion</h2>
                
                <form id="accountRemovalForm" class="space-y-6">
                    @csrf
                    
                    <!-- User Authentication -->
                    <div class="border-b border-slate-200 pb-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Verify Your Identity</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                                <input type="password" id="password" name="password" required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Deletion Reason -->
                    <div class="border-b border-slate-200 pb-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Reason for Deletion</h3>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="reason" value="no_longer_needed" class="mr-3">
                                <span class="text-slate-700">I no longer need this service</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="reason" value="privacy_concerns" class="mr-3">
                                <span class="text-slate-700">Privacy concerns</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="reason" value="switching_service" class="mr-3">
                                <span class="text-slate-700">Switching to another service</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="reason" value="technical_issues" class="mr-3">
                                <span class="text-slate-700">Technical issues</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="reason" value="other" class="mr-3">
                                <span class="text-slate-700">Other (please specify)</span>
                            </label>
                        </div>
                        
                        <div class="mt-4">
                            <label for="additional_details" class="block text-sm font-medium text-slate-700 mb-2">Additional Details (Optional)</label>
                            <textarea id="additional_details" name="additional_details" rows="4"
                                      class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Please provide any additional details about your request..."></textarea>
                        </div>
                    </div>

                    <!-- Data Options -->
                    <div class="border-b border-slate-200 pb-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Data Handling Options</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <input type="checkbox" id="export_data" name="export_data" class="mt-1">
                                <div>
                                    <label for="export_data" class="text-slate-700 font-medium">Export my data before deletion</label>
                                    <p class="text-sm text-slate-500">We'll send you a copy of your data before permanently deleting your account</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <input type="checkbox" id="keep_anonymous" name="keep_anonymous" class="mt-1">
                                <div>
                                    <label for="keep_anonymous" class="text-slate-700 font-medium">Keep anonymized data for analytics</label>
                                    <p class="text-sm text-slate-500">Allow us to keep non-personal aggregated data for service improvement</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation -->
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="understand_permanent" name="understand_permanent" required class="mt-1">
                            <label for="understand_permanent" class="text-slate-700">
                                I understand that account deletion is <strong>permanent and irreversible</strong>
                            </label>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="understand_subscriptions" name="understand_subscriptions" required class="mt-1">
                            <label for="understand_subscriptions" class="text-slate-700">
                                I understand that all active subscriptions will be cancelled
                            </label>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="understand_data_loss" name="understand_data_loss" required class="mt-1">
                            <label for="understand_data_loss" class="text-slate-700">
                                I understand that all my data, including courses, progress, and files, will be permanently deleted
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex space-x-4">
                        <button type="submit" 
                                class="bg-red-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-red-700 transition-colors">
                            Request Account Deletion
                        </button>
                        <a href="{{ route('dashboard') }}" 
                           class="bg-slate-200 text-slate-700 py-3 px-6 rounded-xl font-semibold hover:bg-slate-300 transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Information Section -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">What Happens Next?</h2>
                
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">1</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Request Submission</h3>
                            <p class="text-slate-600">Your deletion request is submitted and you'll receive a confirmation email.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">2</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">72-Hour Waiting Period</h3>
                            <p class="text-slate-600">You have 72 hours to cancel your deletion request if you change your mind.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">3</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Data Export (Optional)</h3>
                            <p class="text-slate-600">If requested, we'll send you a copy of your data before deletion.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">4</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Account Deletion</h3>
                            <p class="text-slate-600">After 72 hours, your account and all associated data will be permanently deleted.</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-blue-900 mb-2">Need Help?</h3>
                    <p class="text-blue-800">
                        If you're having issues with our service, please consider 
                        <a href="{{ route('business.contact') }}" class="underline">contacting our support team</a> 
                        before deleting your account. We're here to help resolve any problems.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('accountRemovalForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Additional confirmation
    const confirmed = confirm(
        'Are you absolutely sure you want to permanently delete your account? This action cannot be undone.'
    );
    
    if (!confirmed) {
        return;
    }
    
    try {
        const response = await fetch('/api/legal/data-deletion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            alert('Your account deletion request has been submitted. You will receive a confirmation email shortly.');
            window.location.href = '/';
        } else {
            alert('Error: ' + (result.message || 'Unable to process your request. Please try again.'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('There was an error processing your request. Please try again or contact support.');
    }
});
</script>
@endsection