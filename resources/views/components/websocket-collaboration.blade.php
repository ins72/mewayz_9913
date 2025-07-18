{{-- WebSocket Collaboration Component --}}

<div class="websocket-collaboration-wrapper">
    {{-- Include CSS --}}
    <link rel="stylesheet" href="{{ asset('css/websocket-collaboration.css') }}">
    
    {{-- Hidden data elements for JavaScript --}}
    <div style="display: none;">
        <div data-workspace-id="{{ $workspaceId ?? '' }}"></div>
        <div data-auth-token="{{ $authToken ?? '' }}"></div>
        <div data-current-user="{{ json_encode($currentUser ?? []) }}"></div>
    </div>
    
    {{-- Collaboration Status UI --}}
    <div class="collaboration-ui">
        {{-- This will be populated by JavaScript --}}
    </div>
    
    {{-- Include JavaScript --}}
    <script src="{{ asset('js/websocket-client.js') }}"></script>
    <script src="{{ asset('js/websocket-init.js') }}"></script>
    
    {{-- WebSocket Styles --}}
    <style>
        .session-controls {
            position: fixed;
            top: 20px;
            right: 380px;
            z-index: 9994;
            display: flex;
            gap: 8px;
            background: white;
            padding: 8px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        .session-controls .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }
        
        .session-controls .btn-primary {
            background: #3b82f6;
            color: white;
        }
        
        .session-controls .btn-primary:hover {
            background: #2563eb;
        }
        
        .session-controls .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .session-controls .btn-secondary:hover {
            background: #4b5563;
        }
        
        .session-status {
            background: #f3f4f6;
            padding: 8px;
            border-radius: 4px;
            font-size: 12px;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .participant-count {
            background: #3b82f6;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .offline-notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border: 1px solid #e5e7eb;
            z-index: 10001;
            max-width: 400px;
            text-align: center;
        }
        
        .offline-notification .notification-content {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .offline-notification button {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .offline-notification button:hover {
            background: #2563eb;
        }
        
        @media (max-width: 768px) {
            .session-controls {
                position: relative;
                top: auto;
                right: auto;
                margin: 10px;
                width: calc(100% - 20px);
            }
        }
    </style>
    
    {{-- Initialize WebSocket connection --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if required data is available
            const workspaceId = document.querySelector('[data-workspace-id]')?.dataset.workspaceId;
            const authToken = document.querySelector('[data-auth-token]')?.dataset.authToken;
            const currentUser = document.querySelector('[data-current-user]')?.dataset.currentUser;
            
            if (!workspaceId || !authToken || !currentUser) {
                console.warn('WebSocket: Missing required data for collaboration features');
                return;
            }
            
            // Log initialization
            console.log('WebSocket: Initializing collaboration features', {
                workspaceId: workspaceId,
                hasAuthToken: !!authToken,
                currentUser: JSON.parse(currentUser)
            });
            
            // Add notification for development
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                const devNotice = document.createElement('div');
                devNotice.className = 'dev-notice';
                devNotice.innerHTML = `
                    <div style="position: fixed; bottom: 20px; left: 20px; background: #1f2937; color: white; padding: 12px; border-radius: 8px; font-size: 12px; z-index: 9999;">
                        <strong>Development Mode:</strong> WebSocket collaboration features are active.
                        <button onclick="this.parentNode.remove()" style="margin-left: 8px; background: transparent; border: 1px solid white; color: white; padding: 2px 6px; border-radius: 3px; cursor: pointer;">×</button>
                    </div>
                `;
                document.body.appendChild(devNotice);
            }
        });
    </script>
</div>

{{-- Collaborative Elements Enhancement --}}
<script>
    // Enhance existing form elements for collaboration
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            // Add collaboration indicator to form
            const indicator = document.createElement('div');
            indicator.className = 'form-collaboration-indicator';
            indicator.innerHTML = `
                <div style="display: flex; align-items: center; gap: 8px; background: #f0f9ff; padding: 8px 12px; border-radius: 6px; margin-bottom: 16px; border: 1px solid #e0f2fe;">
                    <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                    <span style="font-size: 12px; color: #0369a1;">Real-time collaboration enabled</span>
                </div>
            `;
            
            form.insertBefore(indicator, form.firstChild);
        });
        
        // Add collaboration features to text inputs and textareas
        const editableElements = document.querySelectorAll('input[type="text"], input[type="email"], textarea, [contenteditable="true"]');
        
        editableElements.forEach(element => {
            // Skip if already processed
            if (element.dataset.collaborationEnabled) return;
            
            element.dataset.collaborationEnabled = 'true';
            
            // Add visual indicator
            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            wrapper.style.display = 'inline-block';
            wrapper.style.width = '100%';
            
            element.parentNode.insertBefore(wrapper, element);
            wrapper.appendChild(element);
            
            // Add collaboration badge
            const badge = document.createElement('div');
            badge.innerHTML = '👥';
            badge.style.cssText = `
                position: absolute;
                top: -6px;
                right: -6px;
                background: #3b82f6;
                color: white;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 8px;
                opacity: 0;
                transition: opacity 0.2s;
                z-index: 10;
            `;
            
            wrapper.appendChild(badge);
            
            // Show badge on hover
            element.addEventListener('mouseenter', () => {
                badge.style.opacity = '1';
            });
            
            element.addEventListener('mouseleave', () => {
                badge.style.opacity = '0';
            });
            
            // Show badge on focus
            element.addEventListener('focus', () => {
                badge.style.opacity = '1';
            });
            
            element.addEventListener('blur', () => {
                badge.style.opacity = '0';
            });
        });
    });
</script>