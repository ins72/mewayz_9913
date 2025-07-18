@extends('install.layout')

@section('title', 'System Requirements - Mewayz Installation')

@section('content')
<div class="install-card rounded-2xl shadow-2xl p-8 max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">System Requirements</h2>
        <p class="text-gray-600">Checking your server configuration</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- PHP Requirements -->
        <div class="space-y-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    PHP Requirements
                </h3>
                <div class="space-y-3">
                    @foreach($requirements['requirements'] as $key => $requirement)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $requirement['status'] ? 'bg-green-100' : 'bg-red-100' }}">
                                    @if($requirement['status'])
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800">{{ $requirement['name'] }}</div>
                                    @if($requirement['required'])
                                        <div class="text-xs text-red-600">Required</div>
                                    @else
                                        <div class="text-xs text-gray-500">Recommended</div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">{{ $requirement['current'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- File Permissions -->
        <div class="space-y-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    File Permissions
                </h3>
                <div class="space-y-3">
                    @foreach($requirements['permissions'] as $key => $permission)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $permission['status'] ? 'bg-green-100' : 'bg-red-100' }}">
                                    @if($permission['status'])
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800">{{ $permission['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $permission['path'] }}</div>
                                </div>
                            </div>
                            <div class="text-sm {{ $permission['status'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $permission['status'] ? 'Writable' : 'Not Writable' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Status -->
    <div class="mt-8 p-6 rounded-lg {{ $requirements['overall']['canProceed'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $requirements['overall']['canProceed'] ? 'bg-green-100' : 'bg-red-100' }}">
                @if($requirements['overall']['canProceed'])
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @else
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                @endif
            </div>
            <div>
                <h4 class="font-semibold {{ $requirements['overall']['canProceed'] ? 'text-green-800' : 'text-red-800' }}">
                    {{ $requirements['overall']['canProceed'] ? 'All requirements met!' : 'Some requirements not met' }}
                </h4>
                <p class="text-sm {{ $requirements['overall']['canProceed'] ? 'text-green-700' : 'text-red-700' }}">
                    {{ $requirements['overall']['canProceed'] ? 'Your server meets all the requirements to install Mewayz.' : 'Please fix the issues above before continuing.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center mt-8">
        <button onclick="window.history.back()" class="bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg hover:bg-gray-300 transition-colors">
            Back
        </button>
        
        <div class="flex space-x-4">
            <button onclick="checkRequirements()" class="bg-blue-500 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-600 transition-colors">
                Recheck
            </button>
            
            <button id="continueBtn" onclick="continueInstallation()" 
                    class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-2 px-6 rounded-lg shadow-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 {{ $requirements['overall']['canProceed'] ? '' : 'opacity-50 cursor-not-allowed' }}"
                    {{ $requirements['overall']['canProceed'] ? '' : 'disabled' }}>
                Continue
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function checkRequirements() {
        const recheckBtn = document.querySelector('[onclick="checkRequirements()"]');
        window.installer.showLoading(recheckBtn, 'Checking...');
        
        axios.post('/install/process/requirements')
            .then(response => {
                if (response.data.success) {
                    window.installer.showSuccess(response.data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    window.installer.showError(response.data.message);
                }
            })
            .catch(error => {
                window.installer.showError('Failed to check requirements: ' + error.message);
            })
            .finally(() => {
                window.installer.hideLoading(recheckBtn, 'Recheck');
            });
    }

    function continueInstallation() {
        const continueBtn = document.getElementById('continueBtn');
        if (continueBtn.disabled) return;
        
        window.installer.showLoading(continueBtn, 'Processing...');
        
        axios.post('/install/process/requirements')
            .then(response => {
                if (response.data.success) {
                    window.installer.showSuccess(response.data.message);
                    setTimeout(() => {
                        window.installer.nextStep(response.data.nextStep);
                    }, 1000);
                } else {
                    window.installer.showError(response.data.message);
                }
            })
            .catch(error => {
                window.installer.showError('Failed to process requirements: ' + error.message);
            })
            .finally(() => {
                window.installer.hideLoading(continueBtn, 'Continue');
            });
    }
</script>
@endpush
@endsection