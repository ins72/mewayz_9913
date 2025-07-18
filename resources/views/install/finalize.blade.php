@extends('install.layout')

@section('title', 'Finalize Installation - Mewayz Installation')

@section('content')
<div class="install-card rounded-2xl shadow-2xl p-8 max-w-3xl mx-auto">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Finalize Installation</h2>
        <p class="text-gray-600">Complete the setup process</p>
    </div>

    <div class="space-y-6">
        <!-- Installation Steps -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Final Setup Steps</h3>
            <div class="space-y-4">
                <div class="flex items-center space-x-3" id="step-migrations">
                    <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                    </div>
                    <span class="text-gray-700">Database migrations</span>
                </div>
                
                <div class="flex items-center space-x-3" id="step-seeders">
                    <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                    </div>
                    <span class="text-gray-700">Database seeders</span>
                </div>
                
                <div class="flex items-center space-x-3" id="step-config">
                    <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                    </div>
                    <span class="text-gray-700">Application optimization</span>
                </div>
                
                <div class="flex items-center space-x-3" id="step-storage">
                    <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                    </div>
                    <span class="text-gray-700">Storage links</span>
                </div>
                
                <div class="flex items-center space-x-3" id="step-services">
                    <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                    </div>
                    <span class="text-gray-700">Service configuration</span>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Installation Progress</span>
                <span class="text-sm font-medium text-gray-700" id="progress-text">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>
        </div>

        <!-- Installation Log -->
        <div class="bg-black rounded-lg p-4 max-h-64 overflow-y-auto" id="installation-log">
            <div class="text-green-400 font-mono text-sm">
                <div>Mewayz Installation System v1.0.0</div>
                <div>Waiting for installation to begin...</div>
            </div>
        </div>

        <!-- Warning Notice -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h4 class="font-medium text-yellow-800">Important Notice</h4>
            </div>
            <p class="text-sm text-yellow-700">
                This process may take several minutes. Please do not close your browser or navigate away from this page.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center pt-6">
            <button type="button" onclick="window.installer.nextStep('admin')" 
                    class="bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg hover:bg-gray-300 transition-colors">
                Back
            </button>
            
            <button id="startInstallBtn" onclick="startFinalInstallation()" 
                    class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-2 px-6 rounded-lg shadow-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-300">
                Start Installation
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let installationInProgress = false;
    
    function startFinalInstallation() {
        if (installationInProgress) return;
        
        installationInProgress = true;
        const startBtn = document.getElementById('startInstallBtn');
        const backBtn = document.querySelector('[onclick="window.installer.nextStep(\'admin\')"]');
        
        startBtn.disabled = true;
        backBtn.disabled = true;
        startBtn.innerHTML = '<div class="spinner mr-2"></div> Installing...';
        
        logMessage('Starting final installation process...');
        updateProgress(0, 'Initializing...');
        
        // Simulate installation steps
        setTimeout(() => {
            performInstallation();
        }, 1000);
    }
    
    function performInstallation() {
        logMessage('Running database migrations...');
        updateProgress(20, 'Running migrations...');
        updateStepStatus('step-migrations', 'running');
        
        axios.post('/install/process/finalize')
            .then(response => {
                if (response.data.success) {
                    logMessage('✓ Installation completed successfully!');
                    updateProgress(100, 'Installation complete!');
                    
                    // Update all steps to completed
                    updateStepStatus('step-migrations', 'completed');
                    updateStepStatus('step-seeders', 'completed');
                    updateStepStatus('step-config', 'completed');
                    updateStepStatus('step-storage', 'completed');
                    updateStepStatus('step-services', 'completed');
                    
                    // Show completion message
                    setTimeout(() => {
                        window.installer.showSuccess('Installation completed successfully!');
                        setTimeout(() => {
                            window.installer.nextStep('complete');
                        }, 2000);
                    }, 1000);
                } else {
                    logMessage('✗ Installation failed: ' + response.data.message);
                    updateProgress(0, 'Installation failed');
                    showInstallationError(response.data.message);
                }
            })
            .catch(error => {
                let errorMessage = 'Installation failed';
                if (error.response && error.response.data) {
                    errorMessage = error.response.data.message || errorMessage;
                }
                logMessage('✗ Installation failed: ' + errorMessage);
                updateProgress(0, 'Installation failed');
                showInstallationError(errorMessage);
            });
    }
    
    function updateProgress(percentage, text) {
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        
        progressBar.style.width = percentage + '%';
        progressText.textContent = percentage + '%';
        
        if (text) {
            progressText.textContent = text;
        }
    }
    
    function updateStepStatus(stepId, status) {
        const stepElement = document.getElementById(stepId);
        const indicator = stepElement.querySelector('.w-6');
        
        if (status === 'running') {
            indicator.className = 'w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center';
            indicator.innerHTML = '<div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>';
        } else if (status === 'completed') {
            indicator.className = 'w-6 h-6 bg-green-100 rounded-full flex items-center justify-center';
            indicator.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        } else if (status === 'failed') {
            indicator.className = 'w-6 h-6 bg-red-100 rounded-full flex items-center justify-center';
            indicator.innerHTML = '<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        }
    }
    
    function logMessage(message) {
        const logElement = document.getElementById('installation-log');
        const messageElement = document.createElement('div');
        messageElement.className = 'text-green-400 font-mono text-sm';
        messageElement.textContent = '> ' + message;
        logElement.appendChild(messageElement);
        logElement.scrollTop = logElement.scrollHeight;
    }
    
    function showInstallationError(message) {
        const startBtn = document.getElementById('startInstallBtn');
        const backBtn = document.querySelector('[onclick="window.installer.nextStep(\'admin\')"]');
        
        startBtn.disabled = false;
        backBtn.disabled = false;
        startBtn.innerHTML = 'Retry Installation';
        installationInProgress = false;
        
        window.installer.showError(message);
    }
</script>
@endpush
@endsection