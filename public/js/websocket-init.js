/**
 * WebSocket Collaboration Initializer
 * 
 * This script initializes real-time collaboration features for workspace pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're in a workspace context
    const workspaceId = document.querySelector('[data-workspace-id]')?.dataset.workspaceId;
    const authToken = document.querySelector('[data-auth-token]')?.dataset.authToken;
    const currentUser = document.querySelector('[data-current-user]')?.dataset.currentUser;
    
    if (!workspaceId || !authToken || !currentUser) {
        console.log('WebSocket: Missing required data for collaboration features');
        return;
    }
    
    const userData = JSON.parse(currentUser);
    
    // Initialize WebSocket client
    const wsClient = new WebSocketClient({
        baseUrl: 'ws://localhost:6001',
        authToken: authToken,
        workspaceId: workspaceId,
        userId: userData.id,
        userName: userData.name
    });
    
    // Join workspace automatically
    wsClient.joinWorkspace(workspaceId).then(data => {
        console.log('WebSocket: Successfully joined workspace', data);
        
        // Initialize collaboration features
        initializeCollaborationFeatures(wsClient);
        
        // Show collaboration status
        showCollaborationStatus(wsClient, data.current_users);
        
    }).catch(error => {
        console.error('WebSocket: Failed to join workspace', error);
        showOfflineMode();
    });
    
    // Handle page unload
    window.addEventListener('beforeunload', () => {
        wsClient.leaveWorkspace();
    });
    
    // Store client globally for other scripts
    window.wsClient = wsClient;
});

function initializeCollaborationFeatures(wsClient) {
    // Enable collaborative editing on form inputs
    const editableElements = document.querySelectorAll('input[type="text"], textarea, [contenteditable="true"]');
    
    editableElements.forEach(element => {
        // Add document ID for tracking
        if (!element.dataset.documentId) {
            element.dataset.documentId = generateDocumentId(element);
        }
        
        // Add visual indicator for collaborative elements
        element.classList.add('collaborative-element');
        
        // Add tooltip
        element.title = 'This field supports real-time collaboration';
    });
    
    // Add collaborative styles
    const style = document.createElement('style');
    style.textContent = `
        .collaborative-element {
            position: relative;
            border: 1px solid #e5e7eb;
            transition: border-color 0.2s;
        }
        
        .collaborative-element:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .collaborative-element::after {
            content: '👥';
            position: absolute;
            top: -8px;
            right: -8px;
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
        }
        
        .collaborative-element:hover::after {
            opacity: 1;
        }
    `;
    document.head.appendChild(style);
    
    // Initialize session management
    initializeSessionManagement(wsClient);
    
    // Initialize activity tracking
    initializeActivityTracking(wsClient);
}

function initializeSessionManagement(wsClient) {
    // Add session controls to the page
    const sessionControls = document.createElement('div');
    sessionControls.className = 'session-controls';
    sessionControls.innerHTML = `
        <button id="start-session" class="btn btn-primary">Start Collaboration Session</button>
        <button id="end-session" class="btn btn-secondary" style="display: none;">End Session</button>
        <div id="session-status" class="session-status" style="display: none;"></div>
    `;
    
    // Add to page header or designated area
    const header = document.querySelector('.page-header') || document.querySelector('header') || document.body;
    header.appendChild(sessionControls);
    
    // Handle session controls
    document.getElementById('start-session').addEventListener('click', () => {
        wsClient.startSession(wsClient.workspaceId, 'general', {
            page: window.location.pathname,
            title: document.title
        }).then(session => {
            updateSessionUI(session);
            showSessionBanner(session);
        }).catch(error => {
            console.error('Failed to start session:', error);
        });
    });
    
    document.getElementById('end-session').addEventListener('click', () => {
        wsClient.endSession(wsClient.workspaceId, wsClient.currentSessionId).then(() => {
            hideSessionBanner();
            resetSessionUI();
        }).catch(error => {
            console.error('Failed to end session:', error);
        });
    });
    
    // Listen for session events
    wsClient.on('session.started', (data) => {
        updateSessionUI(data);
        showSessionBanner(data);
    });
    
    wsClient.on('session.ended', (data) => {
        hideSessionBanner();
        resetSessionUI();
    });
}

function initializeActivityTracking(wsClient) {
    let lastActivity = Date.now();
    
    // Track user activity
    ['click', 'keypress', 'mousemove', 'scroll'].forEach(eventType => {
        document.addEventListener(eventType, () => {
            lastActivity = Date.now();
        });
    });
    
    // Send activity updates
    setInterval(() => {
        const timeSinceActivity = Date.now() - lastActivity;
        let status = 'active';
        
        if (timeSinceActivity > 300000) { // 5 minutes
            status = 'away';
        } else if (timeSinceActivity > 60000) { // 1 minute
            status = 'idle';
        }
        
        // Update user status (you can implement this endpoint)
        updateUserStatus(wsClient, status);
    }, 30000); // Check every 30 seconds
}

function showCollaborationStatus(wsClient, users) {
    const statusEl = document.createElement('div');
    statusEl.className = 'collaboration-status';
    statusEl.innerHTML = `
        <div class="status-indicator"></div>
        <div class="status-text">Connected</div>
        <div class="user-count">${users.length} online</div>
    `;
    
    document.body.appendChild(statusEl);
    
    // Update status based on connection
    wsClient.on('connection.established', () => {
        statusEl.querySelector('.status-indicator').classList.remove('disconnected', 'connecting');
        statusEl.querySelector('.status-text').textContent = 'Connected';
    });
    
    wsClient.on('connection.lost', () => {
        statusEl.querySelector('.status-indicator').classList.add('disconnected');
        statusEl.querySelector('.status-text').textContent = 'Disconnected';
    });
    
    // Show workspace users
    showWorkspaceUsers(wsClient, users);
}

function showWorkspaceUsers(wsClient, users) {
    const usersEl = document.createElement('div');
    usersEl.className = 'workspace-users';
    usersEl.innerHTML = `
        <h4>Online Users</h4>
        <div class="users-list"></div>
    `;
    
    document.body.appendChild(usersEl);
    
    function updateUsersList(usersList) {
        const listEl = usersEl.querySelector('.users-list');
        listEl.innerHTML = '';
        
        usersList.forEach(user => {
            const userEl = document.createElement('div');
            userEl.className = 'user-item';
            userEl.innerHTML = `
                <div class="user-avatar">${user.name.charAt(0).toUpperCase()}</div>
                <div class="user-info">
                    <div class="user-name">${user.name}</div>
                    <div class="user-status">Active</div>
                </div>
                <div class="user-activity"></div>
            `;
            listEl.appendChild(userEl);
        });
    }
    
    updateUsersList(users);
    
    // Update when users join/leave
    wsClient.on('user.joined', (data) => {
        // Refresh user list
        wsClient.getWorkspaceUsers(wsClient.workspaceId).then(updateUsersList);
    });
    
    wsClient.on('user.left', (data) => {
        // Refresh user list
        wsClient.getWorkspaceUsers(wsClient.workspaceId).then(updateUsersList);
    });
}

function showSessionBanner(session) {
    const banner = document.createElement('div');
    banner.className = 'session-banner';
    banner.id = 'session-banner';
    banner.innerHTML = `
        <div class="session-info">
            <div class="session-title">Collaboration Session: ${session.type}</div>
            <div class="session-participants">${session.participants.length} participants</div>
        </div>
        <div class="session-actions">
            <button onclick="copySessionLink()">Copy Link</button>
            <button onclick="hideSessionBanner()">Hide</button>
        </div>
    `;
    
    document.body.appendChild(banner);
    
    // Adjust page content to avoid overlap
    document.body.style.paddingTop = '40px';
}

function hideSessionBanner() {
    const banner = document.getElementById('session-banner');
    if (banner) {
        banner.remove();
        document.body.style.paddingTop = '0';
    }
}

function updateSessionUI(session) {
    document.getElementById('start-session').style.display = 'none';
    document.getElementById('end-session').style.display = 'block';
    
    const statusEl = document.getElementById('session-status');
    statusEl.style.display = 'block';
    statusEl.innerHTML = `
        <div class="session-info">
            <strong>Session Active:</strong> ${session.type}
            <span class="participant-count">${session.participants.length} participants</span>
        </div>
    `;
}

function resetSessionUI() {
    document.getElementById('start-session').style.display = 'block';
    document.getElementById('end-session').style.display = 'none';
    document.getElementById('session-status').style.display = 'none';
}

function showOfflineMode() {
    const statusEl = document.createElement('div');
    statusEl.className = 'collaboration-status';
    statusEl.innerHTML = `
        <div class="status-indicator disconnected"></div>
        <div class="status-text">Offline Mode</div>
    `;
    
    document.body.appendChild(statusEl);
    
    // Show offline notification
    const notification = document.createElement('div');
    notification.className = 'offline-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <strong>Collaboration Unavailable</strong>
            <p>Real-time collaboration features are currently unavailable. You can continue working, but changes won't sync with other users.</p>
            <button onclick="this.parentNode.parentNode.remove()">Dismiss</button>
        </div>
    `;
    
    document.body.appendChild(notification);
}

function generateDocumentId(element) {
    // Generate a unique ID for the document element
    const id = element.id || element.name || `doc_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    return id;
}

function updateUserStatus(wsClient, status) {
    // Implementation for updating user status
    // This would make an API call to update the user's activity status
    console.log('User status updated:', status);
}

function copySessionLink() {
    const link = `${window.location.origin}${window.location.pathname}?session=${wsClient.currentSessionId}`;
    navigator.clipboard.writeText(link).then(() => {
        alert('Session link copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy link:', err);
    });
}

// Export functions for external use
window.WebSocketCollaboration = {
    initializeCollaborationFeatures,
    showCollaborationStatus,
    showWorkspaceUsers,
    generateDocumentId
};