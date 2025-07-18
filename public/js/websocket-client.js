/**
 * WebSocket Collaboration Client
 * 
 * Handles real-time collaboration features for the Mewayz platform
 */

class WebSocketClient {
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || 'ws://localhost:6001';
        this.authToken = options.authToken || null;
        this.workspaceId = options.workspaceId || null;
        this.userId = options.userId || null;
        this.userName = options.userName || null;
        
        this.socket = null;
        this.connected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000;
        
        this.events = {
            'workspace.collaboration': [],
            'cursor.moved': [],
            'document.updated': [],
            'workspace.notification': [],
            'connection.established': [],
            'connection.lost': [],
            'user.joined': [],
            'user.left': [],
            'session.started': [],
            'session.ended': []
        };
        
        // Initialize cursor tracking
        this.cursorTracker = new CursorTracker(this);
        
        // Initialize document sync
        this.documentSync = new DocumentSync(this);
        
        // Initialize notifications
        this.notifications = new NotificationManager(this);
        
        this.init();
    }
    
    init() {
        if (!this.authToken) {
            console.error('WebSocket: Authentication token is required');
            return;
        }
        
        this.connect();
    }
    
    connect() {
        try {
            // For now, we'll use the Laravel Echo with Pusher-compatible API
            this.socket = new WebSocket(`${this.baseUrl}/app/websocket-key?protocol=7&client=js&version=4.3.1`);
            
            this.socket.onopen = () => {
                console.log('WebSocket connected');
                this.connected = true;
                this.reconnectAttempts = 0;
                this.trigger('connection.established');
                
                // Subscribe to workspace channel
                if (this.workspaceId) {
                    this.subscribeToWorkspace();
                }
            };
            
            this.socket.onmessage = (event) => {
                const data = JSON.parse(event.data);
                this.handleMessage(data);
            };
            
            this.socket.onclose = () => {
                console.log('WebSocket disconnected');
                this.connected = false;
                this.trigger('connection.lost');
                this.attemptReconnect();
            };
            
            this.socket.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.connected = false;
            };
            
        } catch (error) {
            console.error('WebSocket connection failed:', error);
            this.attemptReconnect();
        }
    }
    
    attemptReconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            console.log(`WebSocket: Attempting to reconnect... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
            
            setTimeout(() => {
                this.connect();
            }, this.reconnectDelay * this.reconnectAttempts);
        } else {
            console.error('WebSocket: Max reconnect attempts reached');
        }
    }
    
    handleMessage(data) {
        // Handle different message types
        if (data.event && this.events[data.event]) {
            this.events[data.event].forEach(callback => {
                callback(data.data);
            });
        }
    }
    
    on(event, callback) {
        if (!this.events[event]) {
            this.events[event] = [];
        }
        this.events[event].push(callback);
    }
    
    off(event, callback) {
        if (this.events[event]) {
            this.events[event] = this.events[event].filter(cb => cb !== callback);
        }
    }
    
    trigger(event, data = null) {
        if (this.events[event]) {
            this.events[event].forEach(callback => {
                callback(data);
            });
        }
    }
    
    // Workspace methods
    async joinWorkspace(workspaceId) {
        this.workspaceId = workspaceId;
        
        try {
            const response = await fetch('/api/websocket/join-workspace', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.authToken}`
                },
                body: JSON.stringify({
                    workspace_id: workspaceId
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                console.log('Joined workspace successfully');
                this.subscribeToWorkspace();
                return result.data;
            } else {
                console.error('Failed to join workspace:', result.error);
                throw new Error(result.error);
            }
        } catch (error) {
            console.error('Error joining workspace:', error);
            throw error;
        }
    }
    
    async leaveWorkspace() {
        if (!this.workspaceId) return;
        
        try {
            const response = await fetch('/api/websocket/leave-workspace', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.authToken}`
                },
                body: JSON.stringify({
                    workspace_id: this.workspaceId
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                console.log('Left workspace successfully');
                this.unsubscribeFromWorkspace();
                this.workspaceId = null;
            } else {
                console.error('Failed to leave workspace:', result.error);
            }
        } catch (error) {
            console.error('Error leaving workspace:', error);
        }
    }
    
    subscribeToWorkspace() {
        if (!this.workspaceId || !this.connected) return;
        
        // Subscribe to workspace channel
        const subscribeMessage = {
            event: 'pusher:subscribe',
            data: {
                auth: this.authToken,
                channel: `presence-workspace.${this.workspaceId}`
            }
        };
        
        this.send(subscribeMessage);
    }
    
    unsubscribeFromWorkspace() {
        if (!this.workspaceId || !this.connected) return;
        
        const unsubscribeMessage = {
            event: 'pusher:unsubscribe',
            data: {
                channel: `presence-workspace.${this.workspaceId}`
            }
        };
        
        this.send(unsubscribeMessage);
    }
    
    async sendNotification(type, message, data = null) {
        if (!this.workspaceId) return;
        
        try {
            const response = await fetch('/api/websocket/send-notification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.authToken}`
                },
                body: JSON.stringify({
                    workspace_id: this.workspaceId,
                    notification_type: type,
                    message: message,
                    data: data
                })
            });
            
            const result = await response.json();
            
            if (!result.success) {
                console.error('Failed to send notification:', result.error);
            }
        } catch (error) {
            console.error('Error sending notification:', error);
        }
    }
    
    send(message) {
        if (this.connected && this.socket) {
            this.socket.send(JSON.stringify(message));
        }
    }
    
    disconnect() {
        if (this.socket) {
            this.socket.close();
            this.socket = null;
            this.connected = false;
        }
    }
}

/**
 * Cursor Tracker
 * 
 * Handles real-time cursor tracking and display
 */
class CursorTracker {
    constructor(webSocketClient) {
        this.client = webSocketClient;
        this.cursors = new Map();
        this.throttleDelay = 100; // ms
        this.lastUpdate = 0;
        
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Track mouse movement
        document.addEventListener('mousemove', this.throttle((e) => {
            this.updateCursor(e.clientX, e.clientY);
        }, this.throttleDelay));
        
        // Listen for cursor updates from other users
        this.client.on('cursor.moved', (data) => {
            this.updateOtherUserCursor(data);
        });
        
        // Clean up cursors when users leave
        this.client.on('user.left', (data) => {
            this.removeCursor(data.user_id);
        });
    }
    
    async updateCursor(x, y) {
        if (!this.client.workspaceId) return;
        
        const now = Date.now();
        if (now - this.lastUpdate < this.throttleDelay) return;
        
        this.lastUpdate = now;
        
        try {
            const response = await fetch('/api/websocket/update-cursor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.client.authToken}`
                },
                body: JSON.stringify({
                    workspace_id: this.client.workspaceId,
                    cursor_position: { x, y },
                    page_url: window.location.href
                })
            });
            
            const result = await response.json();
            
            if (!result.success) {
                console.error('Failed to update cursor:', result.error);
            }
        } catch (error) {
            console.error('Error updating cursor:', error);
        }
    }
    
    updateOtherUserCursor(data) {
        if (data.user_id === this.client.userId) return;
        
        let cursor = this.cursors.get(data.user_id);
        
        if (!cursor) {
            cursor = this.createCursor(data.user_id, data.user_name);
            this.cursors.set(data.user_id, cursor);
        }
        
        // Update cursor position
        cursor.style.left = data.cursor_position.x + 'px';
        cursor.style.top = data.cursor_position.y + 'px';
        
        // Update timestamp
        cursor.dataset.lastUpdate = data.timestamp;
    }
    
    createCursor(userId, userName) {
        const cursor = document.createElement('div');
        cursor.className = 'websocket-cursor';
        cursor.dataset.userId = userId;
        cursor.innerHTML = `
            <div class="cursor-pointer"></div>
            <div class="cursor-label">${userName}</div>
        `;
        
        document.body.appendChild(cursor);
        return cursor;
    }
    
    removeCursor(userId) {
        const cursor = this.cursors.get(userId);
        if (cursor) {
            cursor.remove();
            this.cursors.delete(userId);
        }
    }
    
    throttle(func, delay) {
        let timeoutId;
        let lastExecTime = 0;
        
        return function (...args) {
            const currentTime = Date.now();
            
            if (currentTime - lastExecTime > delay) {
                func.apply(this, args);
                lastExecTime = currentTime;
            } else {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                    lastExecTime = Date.now();
                }, delay - (currentTime - lastExecTime));
            }
        };
    }
}

/**
 * Document Synchronizer
 * 
 * Handles real-time document collaboration
 */
class DocumentSync {
    constructor(webSocketClient) {
        this.client = webSocketClient;
        this.documents = new Map();
        this.pendingChanges = new Map();
        this.conflictResolver = new ConflictResolver();
        
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Listen for document updates from other users
        this.client.on('document.updated', (data) => {
            this.handleDocumentUpdate(data);
        });
        
        // Setup observers for editable elements
        this.observeEditableElements();
    }
    
    observeEditableElements() {
        const editableElements = document.querySelectorAll('[contenteditable="true"], textarea, input[type="text"]');
        
        editableElements.forEach(element => {
            element.addEventListener('input', this.throttle((e) => {
                this.handleLocalChange(e);
            }, 300));
        });
    }
    
    async handleLocalChange(event) {
        const element = event.target;
        const documentId = element.dataset.documentId || this.generateDocumentId(element);
        
        if (!documentId) return;
        
        const changes = {
            type: 'text_change',
            content: element.value || element.textContent,
            selection: this.getSelection(element),
            timestamp: Date.now()
        };
        
        try {
            const response = await fetch('/api/websocket/update-document', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.client.authToken}`
                },
                body: JSON.stringify({
                    workspace_id: this.client.workspaceId,
                    document_id: documentId,
                    changes: changes,
                    document_type: 'text'
                })
            });
            
            const result = await response.json();
            
            if (!result.success) {
                console.error('Failed to update document:', result.error);
            }
        } catch (error) {
            console.error('Error updating document:', error);
        }
    }
    
    handleDocumentUpdate(data) {
        if (data.user_id === this.client.userId) return;
        
        const documentId = data.document_id;
        const element = document.querySelector(`[data-document-id="${documentId}"]`);
        
        if (!element) return;
        
        // Check for conflicts
        const hasConflict = this.conflictResolver.detectConflict(data.changes, element);
        
        if (hasConflict) {
            this.conflictResolver.resolveConflict(data.changes, element);
        } else {
            // Apply changes directly
            this.applyChanges(element, data.changes);
        }
        
        // Show user indicator
        this.showUserIndicator(element, data.user_name);
    }
    
    applyChanges(element, changes) {
        if (changes.type === 'text_change') {
            const currentContent = element.value || element.textContent;
            
            // Simple text replacement for now
            // In a production system, you'd want to use operational transformation
            if (currentContent !== changes.content) {
                if (element.value !== undefined) {
                    element.value = changes.content;
                } else {
                    element.textContent = changes.content;
                }
            }
        }
    }
    
    showUserIndicator(element, userName) {
        // Remove existing indicators
        const existingIndicator = element.parentNode.querySelector('.user-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }
        
        // Create new indicator
        const indicator = document.createElement('div');
        indicator.className = 'user-indicator';
        indicator.textContent = `${userName} is editing...`;
        
        element.parentNode.insertBefore(indicator, element.nextSibling);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            indicator.remove();
        }, 3000);
    }
    
    generateDocumentId(element) {
        // Generate a unique ID for the document element
        const id = element.id || element.name || `doc_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        element.dataset.documentId = id;
        return id;
    }
    
    getSelection(element) {
        if (element.selectionStart !== undefined) {
            return {
                start: element.selectionStart,
                end: element.selectionEnd
            };
        }
        return null;
    }
    
    throttle(func, delay) {
        let timeoutId;
        let lastExecTime = 0;
        
        return function (...args) {
            const currentTime = Date.now();
            
            if (currentTime - lastExecTime > delay) {
                func.apply(this, args);
                lastExecTime = currentTime;
            } else {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                    lastExecTime = Date.now();
                }, delay - (currentTime - lastExecTime));
            }
        };
    }
}

/**
 * Conflict Resolver
 * 
 * Handles merge conflicts in collaborative editing
 */
class ConflictResolver {
    detectConflict(incomingChanges, element) {
        // Simple conflict detection
        const currentContent = element.value || element.textContent;
        const lastKnownContent = element.dataset.lastKnownContent || '';
        
        return currentContent !== lastKnownContent && 
               incomingChanges.content !== lastKnownContent;
    }
    
    resolveConflict(incomingChanges, element) {
        // Simple conflict resolution - show both versions
        const currentContent = element.value || element.textContent;
        const incomingContent = incomingChanges.content;
        
        const mergedContent = `${currentContent}\n\n--- Incoming Changes ---\n${incomingContent}`;
        
        if (element.value !== undefined) {
            element.value = mergedContent;
        } else {
            element.textContent = mergedContent;
        }
        
        // Highlight the conflict
        element.classList.add('conflict-detected');
        
        // Show conflict resolution UI
        this.showConflictResolutionUI(element, currentContent, incomingContent);
    }
    
    showConflictResolutionUI(element, currentContent, incomingContent) {
        const dialog = document.createElement('div');
        dialog.className = 'conflict-resolution-dialog';
        dialog.innerHTML = `
            <div class="dialog-content">
                <h3>Merge Conflict Detected</h3>
                <p>Another user has modified this content. Please choose how to resolve:</p>
                
                <div class="conflict-options">
                    <div class="option">
                        <h4>Your Version:</h4>
                        <textarea readonly>${currentContent}</textarea>
                        <button class="btn-accept" data-action="keep-local">Keep Mine</button>
                    </div>
                    
                    <div class="option">
                        <h4>Their Version:</h4>
                        <textarea readonly>${incomingContent}</textarea>
                        <button class="btn-accept" data-action="keep-remote">Keep Theirs</button>
                    </div>
                    
                    <div class="option">
                        <h4>Manual Merge:</h4>
                        <textarea class="manual-merge">${currentContent}\n\n--- Incoming ---\n${incomingContent}</textarea>
                        <button class="btn-accept" data-action="manual-merge">Use Manual Merge</button>
                    </div>
                </div>
                
                <button class="btn-close">Close</button>
            </div>
        `;
        
        document.body.appendChild(dialog);
        
        // Handle resolution actions
        dialog.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-accept')) {
                const action = e.target.dataset.action;
                let resolvedContent;
                
                switch (action) {
                    case 'keep-local':
                        resolvedContent = currentContent;
                        break;
                    case 'keep-remote':
                        resolvedContent = incomingContent;
                        break;
                    case 'manual-merge':
                        resolvedContent = dialog.querySelector('.manual-merge').value;
                        break;
                }
                
                // Apply resolved content
                if (element.value !== undefined) {
                    element.value = resolvedContent;
                } else {
                    element.textContent = resolvedContent;
                }
                
                element.classList.remove('conflict-detected');
                element.dataset.lastKnownContent = resolvedContent;
                
                dialog.remove();
            } else if (e.target.classList.contains('btn-close')) {
                dialog.remove();
            }
        });
    }
}

/**
 * Notification Manager
 * 
 * Handles real-time notifications display
 */
class NotificationManager {
    constructor(webSocketClient) {
        this.client = webSocketClient;
        this.notifications = [];
        this.container = null;
        
        this.setupEventListeners();
        this.createNotificationContainer();
    }
    
    setupEventListeners() {
        this.client.on('workspace.notification', (data) => {
            this.showNotification(data);
        });
        
        this.client.on('user.joined', (data) => {
            this.showNotification({
                notification_type: 'info',
                message: `${data.user_name} joined the workspace`,
                user_name: data.user_name
            });
        });
        
        this.client.on('user.left', (data) => {
            this.showNotification({
                notification_type: 'info',
                message: `${data.user_name} left the workspace`,
                user_name: data.user_name
            });
        });
    }
    
    createNotificationContainer() {
        this.container = document.createElement('div');
        this.container.className = 'websocket-notifications';
        this.container.innerHTML = `
            <div class="notifications-header">
                <h4>Live Updates</h4>
                <button class="btn-clear" onclick="this.parentNode.parentNode.querySelector('.notifications-list').innerHTML = ''">Clear All</button>
            </div>
            <div class="notifications-list"></div>
        `;
        
        document.body.appendChild(this.container);
    }
    
    showNotification(data) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${data.notification_type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-user">${data.user_name}:</span>
                <span class="notification-message">${data.message}</span>
                <span class="notification-time">${new Date().toLocaleTimeString()}</span>
            </div>
            <button class="btn-close" onclick="this.parentNode.remove()">×</button>
        `;
        
        const list = this.container.querySelector('.notifications-list');
        list.insertBefore(notification, list.firstChild);
        
        // Auto-remove after 10 seconds for info notifications
        if (data.notification_type === 'info') {
            setTimeout(() => {
                notification.remove();
            }, 10000);
        }
        
        // Keep only last 20 notifications
        const notifications = list.querySelectorAll('.notification');
        if (notifications.length > 20) {
            notifications[notifications.length - 1].remove();
        }
    }
}

// Export for use in other files
window.WebSocketClient = WebSocketClient;
window.CursorTracker = CursorTracker;
window.DocumentSync = DocumentSync;
window.NotificationManager = NotificationManager;