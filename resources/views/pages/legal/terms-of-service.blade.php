@extends('layouts.app')

@section('title', 'Terms of Service')
@section('meta_description', 'Read our Terms of Service to understand the rules and guidelines for using Mewayz platform.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-900 mb-4">Terms of Service</h1>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                    Please read these terms and conditions carefully before using our platform
                </p>
                @if($document && $document->effective_date)
                    <p class="text-sm text-slate-500 mt-4">
                        Last updated: {{ $document->effective_date->format('F j, Y') }}
                        | Version: {{ $document->version }}
                    </p>
                @endif
            </div>

            <!-- Terms Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
                @if($document && $document->content)
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($document->content)) !!}
                    </div>
                @else
                    <div class="prose prose-lg max-w-none">
                        <h2>1. Acceptance of Terms</h2>
                        <p>By accessing and using Mewayz, you accept and agree to be bound by the terms and provision of this agreement.</p>
                        
                        <h2>2. Use License</h2>
                        <p>Permission is granted to temporarily download one copy of the materials on Mewayz's platform for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
                        <ul>
                            <li>modify or copy the materials;</li>
                            <li>use the materials for any commercial purpose or for any public display (commercial or non-commercial);</li>
                            <li>attempt to decompile or reverse engineer any software contained on Mewayz's platform;</li>
                            <li>remove any copyright or other proprietary notations from the materials.</li>
                        </ul>
                        
                        <h2>3. Disclaimer</h2>
                        <p>The materials on Mewayz's platform are provided on an 'as is' basis. Mewayz makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>
                        
                        <h2>4. Limitations</h2>
                        <p>In no event shall Mewayz or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Mewayz's platform, even if Mewayz or a Mewayz authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.</p>
                        
                        <h2>5. Accuracy of Materials</h2>
                        <p>The materials appearing on Mewayz's platform could include technical, typographical, or photographic errors. Mewayz does not warrant that any of the materials on its platform are accurate, complete, or current. Mewayz may make changes to the materials contained on its platform at any time without notice. However, Mewayz does not make any commitment to update the materials.</p>
                        
                        <h2>6. Links</h2>
                        <p>Mewayz has not reviewed all of the sites linked to our platform and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by Mewayz of the site. Use of any such linked website is at the user's own risk.</p>
                        
                        <h2>7. Modifications</h2>
                        <p>Mewayz may revise these terms of service for its platform at any time without notice. By using this platform, you are agreeing to be bound by the then current version of these terms of service.</p>
                        
                        <h2>8. Governing Law</h2>
                        <p>These terms and conditions are governed by and construed in accordance with the laws of the jurisdiction in which Mewayz operates and you irrevocably submit to the exclusive jurisdiction of the courts in that state or location.</p>
                        
                        <h2>9. Contact Information</h2>
                        <p>If you have any questions about these Terms of Service, please contact us at:</p>
                        <p><strong>Email:</strong> legal@mewayz.com<br>
                        <strong>Address:</strong> [Your Company Address]</p>
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