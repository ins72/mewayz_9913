{{-- WebSocket Collaboration Component --}}

<div class="websocket-collaboration-wrapper">
    {{-- Include CSS --}}
    <link rel="stylesheet" href="{{ asset('css/websocket-collaboration.css') }}">
    <link rel="stylesheet" href="{{ asset('css/advanced-document-editing.css') }}">
    
    {{-- Hidden data elements for JavaScript --}}
    <div style="display: none;">
        <div data-workspace-id="{{ $workspaceId ?? '' }}"></div>
        <div data-auth-token="{{ $authToken ?? '' }}"></div>
        <div data-current-user="{{ json_encode($currentUser ?? []) }}"></div>
    </div>
    
    {{-- Advanced Document Editing Tools --}}
    <div class="advanced-editing-tools">
        <div class="editing-toolbar">
            <button class="tool-btn" data-tool="rich-text" title="Rich Text Editor">
                <span class="tool-icon">📝</span>
                <span class="tool-label">Rich Text</span>
            </button>
            <button class="tool-btn" data-tool="code" title="Code Editor">
                <span class="tool-icon">💻</span>
                <span class="tool-label">Code</span>
            </button>
            <button class="tool-btn" data-tool="whiteboard" title="Whiteboard">
                <span class="tool-icon">🎨</span>
                <span class="tool-label">Whiteboard</span>
            </button>
            <button class="tool-btn" data-tool="table" title="Table Editor">
                <span class="tool-icon">📊</span>
                <span class="tool-label">Table</span>
            </button>
        </div>
        
        <div class="editing-container" id="collaborative-editing-container"></div>
    </div>
    
    {{-- Collaboration Status UI --}}
    <div class="collaboration-ui">
        {{-- This will be populated by JavaScript --}}
    </div>
    
    {{-- Include JavaScript --}}
    <script src="{{ asset('js/websocket-client.js') }}"></script>
    <script src="{{ asset('js/collaborative-rich-text-editor.js') }}"></script>
    <script src="{{ asset('js/collaborative-code-editor.js') }}"></script>
    <script src="{{ asset('js/collaborative-whiteboard.js') }}"></script>
    <script src="{{ asset('js/collaborative-table-editor.js') }}"></script>
    
    {{-- Enhanced WebSocket Styles --}}
    <style>
        .advanced-editing-tools {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border: 1px solid #e0e0e0;
            z-index: 9999;
            max-width: 300px;
            opacity: 0.9;
            transition: all 0.3s ease;
        }
        
        .advanced-editing-tools:hover {
            opacity: 1;
            box-shadow: 0 6px 25px rgba(0,0,0,0.2);
        }
        
        .editing-toolbar {
            display: flex;
            padding: 12px;
            gap: 8px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .tool-btn {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 8px 6px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 12px;
            color: #666;
        }
        
        .tool-btn:hover {
            background: #e9ecef;
            border-color: #007bff;
            color: #007bff;
        }
        
        .tool-btn.active {
            background: #007bff;
            border-color: #007bff;
            color: white;
        }
        
        .tool-icon {
            font-size: 16px;
        }
        
        .tool-label {
            font-size: 10px;
            font-weight: 500;
        }
        
        .editing-container {
            padding: 15px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }
        
        .editing-container.active {
            display: block;
        }
        
        .session-controls {
            position: fixed;
            top: 20px;
            right: 340px;
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
        
        .editing-quick-actions {
            display: flex;
            gap: 6px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f0f0f0;
        }
        
        .quick-action-btn {
            flex: 1;
            padding: 4px 8px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            cursor: pointer;
            font-size: 10px;
            transition: all 0.2s;
        }
        
        .quick-action-btn:hover {
            background: #e9ecef;
        }
        
        .collaborative-mini-editor {
            min-height: 200px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px;
            background: white;
            font-size: 12px;
            overflow-y: auto;
        }
        
        .collaborative-mini-editor[contenteditable="true"] {
            outline: none;
        }
        
        .collaborative-mini-editor:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }
        
        @media (max-width: 768px) {
            .advanced-editing-tools {
                position: relative;
                top: auto;
                right: auto;
                margin: 10px;
                max-width: calc(100% - 20px);
            }
            
            .session-controls {
                position: relative;
                top: auto;
                right: auto;
                margin: 10px;
                width: calc(100% - 20px);
            }
        }
        
        .mini-toolbar {
            display: flex;
            gap: 4px;
            padding: 6px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            border-radius: 6px 6px 0 0;
        }
        
        .mini-toolbar-btn {
            padding: 3px 6px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 3px;
            cursor: pointer;
            font-size: 10px;
            transition: all 0.2s;
        }
        
        .mini-toolbar-btn:hover {
            background: #e9ecef;
        }
        
        .mini-toolbar-btn.active {
            background: #007bff;
            color: white;
        }
    </style>
    
    {{-- Initialize Advanced WebSocket Collaboration --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if required data is available
            const workspaceId = document.querySelector('[data-workspace-id]')?.dataset.workspaceId;
            const authToken = document.querySelector('[data-auth-token]')?.dataset.authToken;
            const currentUser = document.querySelector('[data-current-user]')?.dataset.currentUser;
            
            if (!workspaceId || !authToken || !currentUser) {
                console.warn('Advanced WebSocket: Missing required data for collaboration features');
                return;
            }
            
            // Initialize advanced collaboration features
            initializeAdvancedCollaboration(workspaceId, authToken, JSON.parse(currentUser));
            
            // Log initialization
            console.log('Advanced WebSocket: Initializing collaboration features', {
                workspaceId: workspaceId,
                hasAuthToken: !!authToken,
                currentUser: JSON.parse(currentUser)
            });
        });
        
        function initializeAdvancedCollaboration(workspaceId, authToken, currentUser) {
            // Initialize WebSocket client
            const wsClient = new WebSocketClient({
                baseUrl: 'ws://localhost:6001',
                authToken: authToken,
                workspaceId: workspaceId,
                userId: currentUser.id,
                userName: currentUser.name
            });
            
            // Initialize document editor
            const documentEditor = new CollaborativeDocumentEditor(wsClient, 'collaborative-editing-container');
            
            // Setup tool switching
            setupAdvancedToolSwitching(documentEditor);
            
            // Auto-join workspace
            setTimeout(() => {
                wsClient.joinWorkspace(workspaceId).then(result => {
                    console.log('Advanced collaboration initialized:', result);
                    showCollaborationNotification('Advanced collaboration ready!', 'success');
                }).catch(error => {
                    console.error('Failed to initialize advanced collaboration:', error);
                    showCollaborationNotification('Collaboration unavailable', 'error');
                });
            }, 1000);
            
            // Store globally
            window.advancedWSClient = wsClient;
            window.advancedDocumentEditor = documentEditor;
        }
        
        function setupAdvancedToolSwitching(documentEditor) {
            const toolButtons = document.querySelectorAll('.tool-btn');
            const container = document.getElementById('collaborative-editing-container');
            
            toolButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const tool = btn.dataset.tool;
                    
                    // Update active button
                    toolButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    // Show editing container
                    container.style.display = 'block';
                    
                    // Clear and create new editor
                    container.innerHTML = '';
                    
                    let editor;
                    switch (tool) {
                        case 'rich-text':
                            editor = createMiniRichTextEditor(container);
                            break;
                        case 'code':
                            editor = createMiniCodeEditor(container);
                            break;
                        case 'whiteboard':
                            editor = createMiniWhiteboard(container);
                            break;
                        case 'table':
                            editor = createMiniTableEditor(container);
                            break;
                    }
                    
                    // Add quick actions
                    addQuickActions(container, tool);
                });
            });
        }
        
        function createMiniRichTextEditor(container) {
            const editor = document.createElement('div');
            editor.innerHTML = `
                <div class="mini-toolbar">
                    <button class="mini-toolbar-btn" data-action="bold">B</button>
                    <button class="mini-toolbar-btn" data-action="italic">I</button>
                    <button class="mini-toolbar-btn" data-action="underline">U</button>
                    <button class="mini-toolbar-btn" data-action="link">🔗</button>
                </div>
                <div class="collaborative-mini-editor" contenteditable="true" placeholder="Start typing...">
                    <p>This is a mini rich text editor with collaboration features.</p>
                    <p>Try <strong>bold</strong>, <em>italic</em>, and <u>underline</u> formatting.</p>
                </div>
            `;
            
            container.appendChild(editor);
            
            // Setup toolbar
            editor.addEventListener('click', (e) => {
                if (e.target.matches('.mini-toolbar-btn')) {
                    const action = e.target.dataset.action;
                    if (action === 'link') {
                        const url = prompt('Enter URL:');
                        if (url) document.execCommand('createLink', false, url);
                    } else {
                        document.execCommand(action, false, null);
                    }
                }
            });
            
            return editor;
        }
        
        function createMiniCodeEditor(container) {
            const editor = document.createElement('div');
            editor.innerHTML = `
                <div class="mini-toolbar">
                    <select class="mini-toolbar-btn" data-action="language">
                        <option value="javascript">JavaScript</option>
                        <option value="html">HTML</option>
                        <option value="css">CSS</option>
                        <option value="python">Python</option>
                    </select>
                    <button class="mini-toolbar-btn" data-action="format">Format</button>
                </div>
                <textarea class="collaborative-mini-editor" style="font-family: monospace; resize: vertical;" placeholder="// Start coding...">
// Welcome to collaborative coding!
function hello() {
    console.log("Hello, World!");
}

hello();</textarea>
            `;
            
            container.appendChild(editor);
            return editor;
        }
        
        function createMiniWhiteboard(container) {
            const editor = document.createElement('div');
            editor.innerHTML = `
                <div class="mini-toolbar">
                    <button class="mini-toolbar-btn active" data-tool="pen">✏️</button>
                    <button class="mini-toolbar-btn" data-tool="eraser">🧹</button>
                    <input type="color" class="mini-toolbar-btn" data-tool="color" value="#000000" style="width: 30px;">
                    <button class="mini-toolbar-btn" data-action="clear">🗑️</button>
                </div>
                <canvas class="collaborative-mini-editor" width="260" height="180" style="border: 1px solid #ddd; cursor: crosshair;"></canvas>
            `;
            
            container.appendChild(editor);
            
            // Setup simple drawing
            const canvas = editor.querySelector('canvas');
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            
            canvas.addEventListener('mousedown', (e) => {
                isDrawing = true;
                ctx.beginPath();
                ctx.moveTo(e.offsetX, e.offsetY);
            });
            
            canvas.addEventListener('mousemove', (e) => {
                if (!isDrawing) return;
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.stroke();
            });
            
            canvas.addEventListener('mouseup', () => {
                isDrawing = false;
            });
            
            // Setup toolbar
            editor.addEventListener('click', (e) => {
                if (e.target.dataset.action === 'clear') {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }
            });
            
            editor.addEventListener('change', (e) => {
                if (e.target.dataset.tool === 'color') {
                    ctx.strokeStyle = e.target.value;
                }
            });
            
            return editor;
        }
        
        function createMiniTableEditor(container) {
            const editor = document.createElement('div');
            editor.innerHTML = `
                <div class="mini-toolbar">
                    <button class="mini-toolbar-btn" data-action="add-row">+ Row</button>
                    <button class="mini-toolbar-btn" data-action="add-col">+ Col</button>
                    <button class="mini-toolbar-btn" data-action="export">Export</button>
                </div>
                <div class="collaborative-mini-editor" style="overflow: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 4px;">Name</th>
                            <th style="border: 1px solid #ddd; padding: 4px;">Value</th>
                            <th style="border: 1px solid #ddd; padding: 4px;">Status</th>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 4px;" contenteditable="true">Item 1</td>
                            <td style="border: 1px solid #ddd; padding: 4px;" contenteditable="true">100</td>
                            <td style="border: 1px solid #ddd; padding: 4px;" contenteditable="true">Active</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 4px;" contenteditable="true">Item 2</td>
                            <td style="border: 1px solid #ddd; padding: 4px;" contenteditable="true">200</td>
                            <td style="border: 1px solid #ddd; padding: 4px;" contenteditable="true">Pending</td>
                        </tr>
                    </table>
                </div>
            `;
            
            container.appendChild(editor);
            return editor;
        }
        
        function addQuickActions(container, tool) {
            const actions = document.createElement('div');
            actions.className = 'editing-quick-actions';
            actions.innerHTML = `
                <button class="quick-action-btn" onclick="saveContent('${tool}')">Save</button>
                <button class="quick-action-btn" onclick="shareContent('${tool}')">Share</button>
                <button class="quick-action-btn" onclick="exportContent('${tool}')">Export</button>
            `;
            
            container.appendChild(actions);
        }
        
        function saveContent(tool) {
            showCollaborationNotification(`${tool} content saved!`, 'success');
        }
        
        function shareContent(tool) {
            const link = window.location.href + '#' + tool;
            navigator.clipboard.writeText(link).then(() => {
                showCollaborationNotification('Share link copied to clipboard!', 'success');
            });
        }
        
        function exportContent(tool) {
            showCollaborationNotification(`${tool} content exported!`, 'info');
        }
        
        function showCollaborationNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `collaboration-notification ${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: ${type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#cce7ff'};
                color: ${type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#004085'};
                padding: 8px 12px;
                border-radius: 4px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                z-index: 10000;
                font-size: 12px;
                max-width: 200px;
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</div>

{{-- Enhanced Collaborative Elements --}}
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
                    <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; animation: pulse 2s infinite;"></div>
                    <span style="font-size: 12px; color: #0369a1;">Advanced real-time collaboration enabled</span>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" style="margin-left: auto; background: transparent; border: none; color: #0369a1; cursor: pointer;">×</button>
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
            
            // Add advanced collaboration badge
            const badge = document.createElement('div');
            badge.innerHTML = '✨';
            badge.title = 'Advanced collaboration features available';
            badge.style.cssText = `
                position: absolute;
                top: -8px;
                right: -8px;
                background: linear-gradient(45deg, #007bff, #00c851);
                color: white;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 8px;
                opacity: 0;
                transition: opacity 0.3s;
                z-index: 10;
                animation: sparkle 2s infinite;
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
                badge.style.animation = 'sparkle 1s infinite';
            });
            
            element.addEventListener('blur', () => {
                badge.style.opacity = '0';
                badge.style.animation = 'sparkle 2s infinite';
            });
        });
        
        // Add sparkle animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes sparkle {
                0%, 100% { transform: scale(1) rotate(0deg); }
                50% { transform: scale(1.2) rotate(180deg); }
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
        `;
        document.head.appendChild(style);
    });
</script>