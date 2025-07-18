// Mewayz Platform v2 - Real-Time Collaboration Manager
class RealTimeManager {
    constructor() {
        this.pusher = null;
        this.currentSession = null;
        this.channels = new Map();
        this.participants = new Map();
        this.cursors = new Map();
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        
        this.init();
    }
    
    async init() {
        console.log('Real-Time Manager: Initializing...');
        
        // Initialize Pusher
        await this.initializePusher();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Connect to workspace channel
        this.connectToWorkspace();
        
        console.log('Real-Time Manager: Initialized successfully');
    }
    
    async initializePusher() {
        try {
            // Load Pusher library if not already loaded
            if (typeof Pusher === 'undefined') {
                await this.loadPusherLibrary();
            }
            
            this.pusher = new Pusher(window.pusherConfig?.key || '', {
                cluster: window.pusherConfig?.cluster || 'us2',
                encrypted: true,
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                },
                authEndpoint: '/api/realtime/auth',
                userAuthentication: {
                    endpoint: '/api/realtime/user-auth',
                    transport: 'ajax'
                }
            });
            
            // Connection event handlers
            this.pusher.connection.bind('connected', () => {
                console.log('Real-Time Manager: Connected to Pusher');
                this.isConnected = true;
                this.reconnectAttempts = 0;
                this.showConnectionStatus('connected');
            });
            
            this.pusher.connection.bind('disconnected', () => {
                console.log('Real-Time Manager: Disconnected from Pusher');
                this.isConnected = false;
                this.showConnectionStatus('disconnected');
            });
            
            this.pusher.connection.bind('error', (error) => {
                console.error('Real-Time Manager: Connection error:', error);
                this.handleConnectionError(error);
            });
            
        } catch (error) {
            console.error('Real-Time Manager: Failed to initialize Pusher:', error);
        }
    }
    
    async loadPusherLibrary() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://js.pusher.com/7.2/pusher.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    
    setupEventListeners() {
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.handlePageHidden();
            } else {
                this.handlePageVisible();
            }
        });
        
        // Handle mouse movement for cursor tracking
        document.addEventListener('mousemove', (e) => {
            if (this.currentSession) {
                this.throttledCursorUpdate(e.clientX, e.clientY);
            }
        });
        
        // Handle window beforeunload
        window.addEventListener('beforeunload', () => {
            if (this.currentSession) {
                this.leaveSession();
            }
        });
    }
    
    connectToWorkspace() {
        const workspaceId = window.currentWorkspace?.id;
        if (!workspaceId) {
            console.warn('Real-Time Manager: No workspace ID found');
            return;
        }
        
        const channelName = `private-workspace.${workspaceId}`;
        const channel = this.pusher.subscribe(channelName);
        
        channel.bind('session.started', (data) => {
            this.handleSessionStarted(data);
        });
        
        channel.bind('session.ended', (data) => {
            this.handleSessionEnded(data);
        });
        
        channel.bind('user.online', (data) => {
            this.handleUserOnline(data);
        });
        
        channel.bind('user.offline', (data) => {
            this.handleUserOffline(data);
        });
        
        this.channels.set(channelName, channel);
        console.log('Real-Time Manager: Connected to workspace channel:', channelName);
    }
    
    async startSession(sessionType, channelName, permissions = {}) {
        try {
            const response = await fetch('/api/realtime/start-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    workspace_id: window.currentWorkspace?.id,
                    session_type: sessionType,
                    channel_name: channelName,
                    permissions: permissions
                })
            });
            
            if (!response.ok) {
                throw new Error('Failed to start session');
            }
            
            const data = await response.json();
            this.currentSession = data.session;
            
            // Subscribe to session channel
            this.subscribeToSession(data.channel);
            
            // Show session UI
            this.showSessionUI();
            
            console.log('Real-Time Manager: Session started:', data.session.session_id);
            
            return data.session;
            
        } catch (error) {
            console.error('Real-Time Manager: Failed to start session:', error);
            throw error;
        }
    }
    
    async joinSession(sessionId) {
        try {
            const response = await fetch('/api/realtime/join-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: sessionId
                })
            });
            
            if (!response.ok) {
                throw new Error('Failed to join session');
            }
            
            const data = await response.json();
            this.currentSession = data.session;
            
            // Subscribe to session channel
            this.subscribeToSession(data.session.channel_name);
            
            // Show session UI
            this.showSessionUI();
            
            console.log('Real-Time Manager: Joined session:', sessionId);
            
            return data.session;
            
        } catch (error) {
            console.error('Real-Time Manager: Failed to join session:', error);
            throw error;
        }
    }
    
    async leaveSession() {
        if (!this.currentSession) {
            return;
        }
        
        try {
            const response = await fetch('/api/realtime/leave-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: this.currentSession.session_id
                })
            });
            
            if (!response.ok) {
                throw new Error('Failed to leave session');
            }
            
            // Unsubscribe from session channel
            this.unsubscribeFromSession();
            
            // Hide session UI
            this.hideSessionUI();
            
            console.log('Real-Time Manager: Left session:', this.currentSession.session_id);
            
            this.currentSession = null;
            
        } catch (error) {
            console.error('Real-Time Manager: Failed to leave session:', error);
        }
    }
    
    subscribeToSession(channelName) {
        const channel = this.pusher.subscribe(channelName);
        
        channel.bind('user.joined', (data) => {
            this.handleUserJoined(data);
        });
        
        channel.bind('user.left', (data) => {
            this.handleUserLeft(data);
        });
        
        channel.bind('message.new', (data) => {
            this.handleNewMessage(data);
        });
        
        channel.bind('data.update', (data) => {
            this.handleDataUpdate(data);
        });
        
        channel.bind('cursor.update', (data) => {
            this.handleCursorUpdate(data);
        });
        
        channel.bind('session.ended', (data) => {
            this.handleSessionEnded(data);
        });
        
        this.channels.set(channelName, channel);
        console.log('Real-Time Manager: Subscribed to session channel:', channelName);
    }
    
    unsubscribeFromSession() {
        if (!this.currentSession) {
            return;
        }
        
        const channelName = this.currentSession.channel_name;
        const channel = this.channels.get(channelName);
        
        if (channel) {
            this.pusher.unsubscribe(channelName);
            this.channels.delete(channelName);
            console.log('Real-Time Manager: Unsubscribed from session channel:', channelName);
        }
    }
    
    async sendMessage(message, messageType = 'text', metadata = {}) {
        if (!this.currentSession) {
            throw new Error('No active session');
        }
        
        try {
            const response = await fetch('/api/realtime/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: this.currentSession.session_id,
                    message: message,
                    message_type: messageType,
                    metadata: metadata
                })
            });
            
            if (!response.ok) {
                throw new Error('Failed to send message');
            }
            
            const data = await response.json();
            return data.message;
            
        } catch (error) {
            console.error('Real-Time Manager: Failed to send message:', error);
            throw error;
        }
    }
    
    async sendDataUpdate(dataType, data, operation = 'update') {
        if (!this.currentSession) {
            throw new Error('No active session');
        }
        
        try {
            const response = await fetch('/api/realtime/send-data-update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: this.currentSession.session_id,
                    data_type: dataType,
                    data: data,
                    operation: operation
                })
            });
            
            if (!response.ok) {
                throw new Error('Failed to send data update');
            }
            
            const responseData = await response.json();
            return responseData.update;
            
        } catch (error) {
            console.error('Real-Time Manager: Failed to send data update:', error);
            throw error;
        }
    }
    
    sendCursorUpdate(x, y, elementId = null) {
        if (!this.currentSession) {
            return;
        }
        
        fetch('/api/realtime/send-cursor-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                session_id: this.currentSession.session_id,
                x: x,
                y: y,
                element_id: elementId
            })
        }).catch(error => {
            console.error('Real-Time Manager: Failed to send cursor update:', error);
        });
    }
    
    // Throttled cursor update to prevent spam
    throttledCursorUpdate = this.throttle((x, y) => {
        this.sendCursorUpdate(x, y);
    }, 100);
    
    // Event Handlers
    handleSessionStarted(data) {
        console.log('Real-Time Manager: Session started:', data);
        this.showSessionNotification(`${data.started_by} started a ${data.type} session`);
    }
    
    handleSessionEnded(data) {
        console.log('Real-Time Manager: Session ended:', data);
        
        if (this.currentSession && this.currentSession.session_id === data.session_id) {
            this.currentSession = null;
            this.hideSessionUI();
        }
        
        this.showSessionNotification('Session ended');
    }
    
    handleUserJoined(data) {
        console.log('Real-Time Manager: User joined:', data);
        this.participants.set(data.user_id, data);
        this.updateParticipantsList();
        this.showSessionNotification(`${data.user_name} joined the session`);
    }
    
    handleUserLeft(data) {
        console.log('Real-Time Manager: User left:', data);
        this.participants.delete(data.user_id);
        this.cursors.delete(data.user_id);
        this.updateParticipantsList();
        this.removeCursor(data.user_id);
        this.showSessionNotification(`${data.user_name} left the session`);
    }
    
    handleNewMessage(data) {
        console.log('Real-Time Manager: New message:', data);
        this.displayMessage(data);
    }
    
    handleDataUpdate(data) {
        console.log('Real-Time Manager: Data update:', data);
        this.processDataUpdate(data);
    }
    
    handleCursorUpdate(data) {
        this.cursors.set(data.user_id, data);
        this.updateCursor(data);
    }
    
    handleUserOnline(data) {
        console.log('Real-Time Manager: User online:', data);
        this.updateUserStatus(data.user_id, 'online');
    }
    
    handleUserOffline(data) {
        console.log('Real-Time Manager: User offline:', data);
        this.updateUserStatus(data.user_id, 'offline');
    }
    
    handleConnectionError(error) {
        console.error('Real-Time Manager: Connection error:', error);
        this.showConnectionStatus('error');
        
        // Attempt to reconnect
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            setTimeout(() => {
                this.reconnect();
            }, 5000 * this.reconnectAttempts);
        }
    }
    
    handlePageHidden() {
        // Reduce activity when page is hidden
        this.throttledCursorUpdate = this.throttle((x, y) => {
            this.sendCursorUpdate(x, y);
        }, 1000);
    }
    
    handlePageVisible() {
        // Resume normal activity when page is visible
        this.throttledCursorUpdate = this.throttle((x, y) => {
            this.sendCursorUpdate(x, y);
        }, 100);
    }
    
    // UI Methods
    showSessionUI() {
        let sessionUI = document.getElementById('realtime-session-ui');
        
        if (!sessionUI) {
            sessionUI = document.createElement('div');
            sessionUI.id = 'realtime-session-ui';
            sessionUI.className = 'fixed bottom-4 right-4 bg-card-bg border border-border-color rounded-lg shadow-lg p-4 z-50';
            sessionUI.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-primary-text">Live Session</h3>
                    <button onclick="window.realTimeManager.leaveSession()" 
                            class="text-secondary-text hover:text-error">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="participants-list" class="mb-3">
                    <p class="text-xs text-secondary-text mb-2">Participants:</p>
                    <div id="participants-avatars" class="flex space-x-2"></div>
                </div>
                <div id="session-chat" class="max-h-32 overflow-y-auto mb-3">
                    <div id="chat-messages" class="space-y-2"></div>
                </div>
                <div class="flex">
                    <input type="text" 
                           id="chat-input" 
                           placeholder="Type a message..." 
                           class="flex-1 px-2 py-1 bg-background border border-border-color rounded-l text-sm text-primary-text focus:outline-none focus:ring-1 focus:ring-info">
                    <button onclick="window.realTimeManager.sendChatMessage()" 
                            class="px-3 py-1 bg-info text-white rounded-r hover:bg-blue-600">
                        Send
                    </button>
                </div>
            `;
            
            document.body.appendChild(sessionUI);
        }
        
        sessionUI.style.display = 'block';
    }
    
    hideSessionUI() {
        const sessionUI = document.getElementById('realtime-session-ui');
        if (sessionUI) {
            sessionUI.style.display = 'none';
        }
        
        // Remove all cursors
        this.cursors.clear();
        this.removeAllCursors();
    }
    
    updateParticipantsList() {
        const participantsAvatars = document.getElementById('participants-avatars');
        if (!participantsAvatars) return;
        
        participantsAvatars.innerHTML = '';
        
        this.participants.forEach((participant) => {
            const avatar = document.createElement('div');
            avatar.className = 'w-6 h-6 bg-info rounded-full flex items-center justify-center text-xs text-white';
            avatar.textContent = participant.user_name.charAt(0).toUpperCase();
            avatar.title = participant.user_name;
            participantsAvatars.appendChild(avatar);
        });
    }
    
    displayMessage(message) {
        const chatMessages = document.getElementById('chat-messages');
        if (!chatMessages) return;
        
        const messageElement = document.createElement('div');
        messageElement.className = 'text-xs';
        messageElement.innerHTML = `
            <span class="font-medium text-primary-text">${message.user_name}:</span>
            <span class="text-secondary-text">${message.message}</span>
        `;
        
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    sendChatMessage() {
        const chatInput = document.getElementById('chat-input');
        if (!chatInput || !chatInput.value.trim()) return;
        
        this.sendMessage(chatInput.value.trim())
            .then(() => {
                chatInput.value = '';
            })
            .catch(error => {
                console.error('Failed to send chat message:', error);
            });
    }
    
    updateCursor(cursorData) {
        let cursor = document.getElementById(`cursor-${cursorData.user_id}`);
        
        if (!cursor) {
            cursor = document.createElement('div');
            cursor.id = `cursor-${cursorData.user_id}`;
            cursor.className = 'fixed pointer-events-none z-50 transition-all duration-100';
            cursor.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-info" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3l18 18-6-6L3 3z"/>
                    </svg>
                    <span class="ml-2 px-2 py-1 bg-info text-white text-xs rounded">${cursorData.user_name}</span>
                </div>
            `;
            document.body.appendChild(cursor);
        }
        
        cursor.style.left = `${cursorData.x}px`;
        cursor.style.top = `${cursorData.y}px`;
    }
    
    removeCursor(userId) {
        const cursor = document.getElementById(`cursor-${userId}`);
        if (cursor) {
            cursor.remove();
        }
    }
    
    removeAllCursors() {
        const cursors = document.querySelectorAll('[id^="cursor-"]');
        cursors.forEach(cursor => cursor.remove());
    }
    
    showConnectionStatus(status) {
        let statusIndicator = document.getElementById('realtime-connection-status');
        
        if (!statusIndicator) {
            statusIndicator = document.createElement('div');
            statusIndicator.id = 'realtime-connection-status';
            statusIndicator.className = 'fixed top-4 right-4 px-3 py-2 rounded-lg text-sm font-medium z-50';
            document.body.appendChild(statusIndicator);
        }
        
        switch (status) {
            case 'connected':
                statusIndicator.className = 'fixed top-4 right-4 px-3 py-2 rounded-lg text-sm font-medium z-50 bg-success text-white';
                statusIndicator.textContent = 'Connected';
                setTimeout(() => statusIndicator.style.display = 'none', 3000);
                break;
            case 'disconnected':
                statusIndicator.className = 'fixed top-4 right-4 px-3 py-2 rounded-lg text-sm font-medium z-50 bg-warning text-white';
                statusIndicator.textContent = 'Disconnected';
                break;
            case 'error':
                statusIndicator.className = 'fixed top-4 right-4 px-3 py-2 rounded-lg text-sm font-medium z-50 bg-error text-white';
                statusIndicator.textContent = 'Connection Error';
                break;
        }
        
        statusIndicator.style.display = 'block';
    }
    
    showSessionNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-card-bg border border-border-color rounded-lg px-4 py-2 text-sm text-primary-text z-50';
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Utility methods
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
                }, delay);
            }
        };
    }
    
    processDataUpdate(update) {
        // Override this method to handle specific data updates
        console.log('Processing data update:', update);
    }
    
    updateUserStatus(userId, status) {
        // Override this method to handle user status updates
        console.log('User status update:', userId, status);
    }
    
    reconnect() {
        console.log('Real-Time Manager: Attempting to reconnect...');
        
        if (this.pusher) {
            this.pusher.disconnect();
        }
        
        setTimeout(() => {
            this.initializePusher();
        }, 1000);
    }
}

// Initialize Real-Time Manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.pusherConfig !== 'undefined') {
        window.realTimeManager = new RealTimeManager();
    }
});