@extends('layouts.app')

@section('title', 'Accessibility Statement')
@section('meta_description', 'Learn about our commitment to accessibility and how we make Mewayz accessible to everyone.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-900 mb-4">Accessibility Statement</h1>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                    We are committed to making Mewayz accessible to everyone, regardless of ability or technology.
                </p>
                @if($document && $document->effective_date)
                    <p class="text-sm text-slate-500 mt-4">
                        Last updated: {{ $document->effective_date->format('F j, Y') }}
                        | Version: {{ $document->version }}
                    </p>
                @endif
            </div>

            <!-- Accessibility Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
                @if($document && $document->content)
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($document->content)) !!}
                    </div>
                @else
                    <div class="prose prose-lg max-w-none">
                        <h2>1. Our Commitment</h2>
                        <p>Mewayz is committed to ensuring digital accessibility for people with disabilities. We are continually improving the user experience for everyone and applying the relevant accessibility standards.</p>
                        
                        <h2>2. Standards</h2>
                        <p>We aim to conform to the Web Content Accessibility Guidelines (WCAG) 2.1 Level AA standards. These guidelines explain how to make web content more accessible to people with disabilities.</p>
                        
                        <h2>3. Current Accessibility Features</h2>
                        <p>Our platform includes the following accessibility features:</p>
                        
                        <h3>Keyboard Navigation</h3>
                        <ul>
                            <li>All interactive elements are keyboard accessible</li>
                            <li>Logical tab order throughout the interface</li>
                            <li>Visible focus indicators</li>
                            <li>Skip links to main content</li>
                        </ul>
                        
                        <h3>Screen Reader Support</h3>
                        <ul>
                            <li>Semantic HTML structure</li>
                            <li>Proper heading hierarchy</li>
                            <li>Alt text for images</li>
                            <li>ARIA labels and descriptions</li>
                        </ul>
                        
                        <h3>Visual Design</h3>
                        <ul>
                            <li>High contrast color schemes</li>
                            <li>Scalable fonts and interface elements</li>
                            <li>Clear visual hierarchy</li>
                            <li>Consistent navigation patterns</li>
                        </ul>
                        
                        <h3>Media Accessibility</h3>
                        <ul>
                            <li>Captions for video content</li>
                            <li>Audio descriptions where applicable</li>
                            <li>Transcript alternatives</li>
                            <li>Adjustable playback controls</li>
                        </ul>
                        
                        <h2>4. Assistive Technology Support</h2>
                        <p>Our platform is designed to work with assistive technologies including:</p>
                        <ul>
                            <li>Screen readers (NVDA, JAWS, VoiceOver, TalkBack)</li>
                            <li>Voice recognition software</li>
                            <li>Keyboard navigation tools</li>
                            <li>Switch navigation devices</li>
                            <li>Magnification software</li>
                        </ul>
                        
                        <h2>5. Browser Compatibility</h2>
                        <p>Our platform is tested and optimized for:</p>
                        <ul>
                            <li>Chrome (latest version)</li>
                            <li>Firefox (latest version)</li>
                            <li>Safari (latest version)</li>
                            <li>Edge (latest version)</li>
                            <li>Mobile browsers on iOS and Android</li>
                        </ul>
                        
                        <h2>6. Known Limitations</h2>
                        <p>While we strive for full accessibility, we acknowledge some current limitations:</p>
                        <ul>
                            <li>Some third-party embedded content may not be fully accessible</li>
                            <li>Legacy content may not meet current accessibility standards</li>
                            <li>Some complex interactive features are being improved</li>
                        </ul>
                        
                        <h2>7. Ongoing Efforts</h2>
                        <p>We are actively working to improve accessibility through:</p>
                        <ul>
                            <li>Regular accessibility audits</li>
                            <li>User testing with people with disabilities</li>
                            <li>Staff training on accessibility best practices</li>
                            <li>Continuous monitoring and updates</li>
                        </ul>
                        
                        <h2>8. User Customization</h2>
                        <p>Users can customize their experience through:</p>
                        <ul>
                            <li>Font size adjustments</li>
                            <li>Color scheme preferences</li>
                            <li>Animation and motion reduction</li>
                            <li>Keyboard shortcut customization</li>
                        </ul>
                        
                        <h2>9. Third-Party Content</h2>
                        <p>Some content on our platform is provided by third parties. We:</p>
                        <ul>
                            <li>Encourage partners to follow accessibility guidelines</li>
                            <li>Provide alternative access methods when possible</li>
                            <li>Work with vendors to improve accessibility</li>
                            <li>Clearly label third-party content</li>
                        </ul>
                        
                        <h2>10. Feedback and Assistance</h2>
                        <p>We welcome feedback on accessibility. If you encounter any accessibility barriers:</p>
                        <ul>
                            <li>Please contact our accessibility team</li>
                            <li>Describe the issue and provide your contact information</li>
                            <li>We will respond within 2 business days</li>
                            <li>We will work with you to find alternative solutions</li>
                        </ul>
                        
                        <h2>11. Contact Information</h2>
                        <p>For accessibility support or to report accessibility issues:</p>
                        <p><strong>Email:</strong> accessibility@mewayz.com<br>
                        <strong>Phone:</strong> [Your Phone Number]<br>
                        <strong>Mail:</strong> [Your Company Address]</p>
                        
                        <h2>12. Legal Information</h2>
                        <p>This statement was prepared on [Date] and reflects our current accessibility status. We review and update this statement regularly to ensure it remains accurate and current.</p>
                    </div>
                @endif
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-8">
                <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection