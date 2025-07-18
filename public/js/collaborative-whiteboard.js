/**
 * Collaborative Whiteboard
 * 
 * Real-time collaborative drawing and whiteboard tool
 */

class CollaborativeWhiteboard {
    constructor(webSocketClient, container) {
        this.wsClient = webSocketClient;
        this.container = container;
        this.whiteboards = new Map();
        this.tools = {
            pen: 'Pen',
            eraser: 'Eraser',
            rectangle: 'Rectangle',
            circle: 'Circle',
            line: 'Line',
            text: 'Text',
            arrow: 'Arrow',
            select: 'Select'
        };
        this.colors = ['#000000', '#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF', '#00FFFF', '#FFA500', '#800080', '#FFC0CB'];
        this.strokeSizes = [2, 4, 6, 8, 10, 12, 16, 20];
    }
    
    create(documentId, initialData = null) {
        const whiteboardContainer = document.createElement('div');
        whiteboardContainer.className = 'collaborative-whiteboard';
        whiteboardContainer.dataset.documentId = documentId;
        
        whiteboardContainer.innerHTML = `
            <div class="whiteboard-toolbar">
                <div class="tool-group">
                    <h4>Tools</h4>
                    <div class="tool-buttons">
                        ${Object.entries(this.tools).map(([key, value]) => 
                            `<button class="tool-btn ${key === 'pen' ? 'active' : ''}" data-tool="${key}" title="${value}">
                                <span class="tool-icon">${this.getToolIcon(key)}</span>
                            </button>`
                        ).join('')}
                    </div>
                </div>
                
                <div class="tool-group">
                    <h4>Colors</h4>
                    <div class="color-palette">
                        ${this.colors.map(color => 
                            `<div class="color-swatch ${color === '#000000' ? 'active' : ''}" 
                                  style="background-color: ${color}" 
                                  data-color="${color}"></div>`
                        ).join('')}
                    </div>
                </div>
                
                <div class="tool-group">
                    <h4>Stroke</h4>
                    <div class="stroke-sizes">
                        ${this.strokeSizes.map(size => 
                            `<button class="stroke-btn ${size === 2 ? 'active' : ''}" data-size="${size}">
                                <div class="stroke-preview" style="width: ${size}px; height: ${size}px;"></div>
                            </button>`
                        ).join('')}
                    </div>
                </div>
                
                <div class="tool-group">
                    <h4>Actions</h4>
                    <div class="action-buttons">
                        <button class="action-btn" data-action="clear" title="Clear All">
                            <span>🗑️</span>
                        </button>
                        <button class="action-btn" data-action="undo" title="Undo">
                            <span>↶</span>
                        </button>
                        <button class="action-btn" data-action="redo" title="Redo">
                            <span>↷</span>
                        </button>
                        <button class="action-btn" data-action="save" title="Save as Image">
                            <span>💾</span>
                        </button>
                        <button class="action-btn" data-action="fullscreen" title="Fullscreen">
                            <span>⛶</span>
                        </button>
                    </div>
                </div>
                
                <div class="tool-group">
                    <h4>Collaboration</h4>
                    <div class="collaboration-info">
                        <span class="active-users-count">1 user</span>
                        <div class="user-cursors-indicator">
                            <span class="cursor-legend">User cursors:</span>
                            <div class="cursor-colors"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="whiteboard-canvas-container">
                <canvas class="whiteboard-canvas" 
                        width="1200" 
                        height="800" 
                        data-document-id="${documentId}">
                </canvas>
                
                <div class="remote-cursors"></div>
                
                <div class="canvas-overlay">
                    <div class="zoom-controls">
                        <button class="zoom-btn" data-zoom="in">+</button>
                        <span class="zoom-level">100%</span>
                        <button class="zoom-btn" data-zoom="out">-</button>
                        <button class="zoom-btn" data-zoom="reset">Reset</button>
                    </div>
                    
                    <div class="canvas-info">
                        <span class="canvas-size">1200 × 800</span>
                        <span class="cursor-position">0, 0</span>
                    </div>
                </div>
            </div>
        `;
        
        this.container.appendChild(whiteboardContainer);
        
        const canvas = whiteboardContainer.querySelector('.whiteboard-canvas');
        const ctx = canvas.getContext('2d');
        
        const whiteboard = {
            container: whiteboardContainer,
            canvas: canvas,
            ctx: ctx,
            documentId: documentId,
            currentTool: 'pen',
            currentColor: '#000000',
            currentStrokeSize: 2,
            isDrawing: false,
            startX: 0,
            startY: 0,
            history: [],
            historyIndex: -1,
            zoomLevel: 1,
            panX: 0,
            panY: 0,
            remoteCursors: new Map(),
            drawingData: []
        };
        
        this.whiteboards.set(documentId, whiteboard);
        this.setupWhiteboardEvents(whiteboard);
        
        // Load initial data if provided
        if (initialData) {
            this.loadWhiteboardData(whiteboard, initialData);
        }
        
        return whiteboard;
    }
    
    getToolIcon(tool) {
        const icons = {
            pen: '✏️',
            eraser: '🧹',
            rectangle: '⬜',
            circle: '⭕',
            line: '📏',
            text: '📝',
            arrow: '↗️',
            select: '👆'
        };
        return icons[tool] || '🔧';
    }
    
    setupWhiteboardEvents(whiteboard) {
        // Tool selection
        whiteboard.container.addEventListener('click', (e) => {
            if (e.target.matches('.tool-btn')) {
                this.selectTool(whiteboard, e.target.dataset.tool);
            } else if (e.target.matches('.color-swatch')) {
                this.selectColor(whiteboard, e.target.dataset.color);
            } else if (e.target.matches('.stroke-btn')) {
                this.selectStrokeSize(whiteboard, parseInt(e.target.dataset.size));
            } else if (e.target.matches('.action-btn')) {
                this.executeAction(whiteboard, e.target.dataset.action);
            } else if (e.target.matches('.zoom-btn')) {
                this.handleZoom(whiteboard, e.target.dataset.zoom);
            }
        });
        
        // Drawing events
        whiteboard.canvas.addEventListener('mousedown', (e) => {
            this.startDrawing(whiteboard, e);
        });
        
        whiteboard.canvas.addEventListener('mousemove', (e) => {
            this.draw(whiteboard, e);
            this.updateCursorPosition(whiteboard, e);
        });
        
        whiteboard.canvas.addEventListener('mouseup', (e) => {
            this.stopDrawing(whiteboard, e);
        });
        
        whiteboard.canvas.addEventListener('mouseleave', (e) => {
            this.stopDrawing(whiteboard, e);
        });
        
        // Touch events for mobile
        whiteboard.canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousedown', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            whiteboard.canvas.dispatchEvent(mouseEvent);
        });
        
        whiteboard.canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousemove', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            whiteboard.canvas.dispatchEvent(mouseEvent);
        });
        
        whiteboard.canvas.addEventListener('touchend', (e) => {
            e.preventDefault();
            const mouseEvent = new MouseEvent('mouseup', {});
            whiteboard.canvas.dispatchEvent(mouseEvent);
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.target.closest('.collaborative-whiteboard') === whiteboard.container) {
                this.handleKeyboard(whiteboard, e);
            }
        });
    }
    
    selectTool(whiteboard, tool) {
        whiteboard.currentTool = tool;
        
        // Update UI
        whiteboard.container.querySelectorAll('.tool-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        whiteboard.container.querySelector(`[data-tool="${tool}"]`).classList.add('active');
        
        // Update cursor
        this.updateCanvasCursor(whiteboard);
        
        // Broadcast tool change
        this.broadcastToolChange(whiteboard, { tool: tool });
    }
    
    selectColor(whiteboard, color) {
        whiteboard.currentColor = color;
        
        // Update UI
        whiteboard.container.querySelectorAll('.color-swatch').forEach(swatch => {
            swatch.classList.remove('active');
        });
        whiteboard.container.querySelector(`[data-color="${color}"]`).classList.add('active');
        
        // Broadcast color change
        this.broadcastToolChange(whiteboard, { color: color });
    }
    
    selectStrokeSize(whiteboard, size) {
        whiteboard.currentStrokeSize = size;
        
        // Update UI
        whiteboard.container.querySelectorAll('.stroke-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        whiteboard.container.querySelector(`[data-size="${size}"]`).classList.add('active');
        
        // Broadcast stroke size change
        this.broadcastToolChange(whiteboard, { strokeSize: size });
    }
    
    executeAction(whiteboard, action) {
        switch (action) {
            case 'clear':
                this.clearCanvas(whiteboard);
                break;
            case 'undo':
                this.undo(whiteboard);
                break;
            case 'redo':
                this.redo(whiteboard);
                break;
            case 'save':
                this.saveAsImage(whiteboard);
                break;
            case 'fullscreen':
                this.toggleFullscreen(whiteboard);
                break;
        }
    }
    
    handleZoom(whiteboard, zoomAction) {
        switch (zoomAction) {
            case 'in':
                whiteboard.zoomLevel = Math.min(whiteboard.zoomLevel * 1.2, 5);
                break;
            case 'out':
                whiteboard.zoomLevel = Math.max(whiteboard.zoomLevel / 1.2, 0.2);
                break;
            case 'reset':
                whiteboard.zoomLevel = 1;
                whiteboard.panX = 0;
                whiteboard.panY = 0;
                break;
        }
        
        this.applyZoom(whiteboard);
    }
    
    applyZoom(whiteboard) {
        const canvas = whiteboard.canvas;
        const transform = `scale(${whiteboard.zoomLevel}) translate(${whiteboard.panX}px, ${whiteboard.panY}px)`;
        canvas.style.transform = transform;
        
        // Update zoom display
        const zoomDisplay = whiteboard.container.querySelector('.zoom-level');
        if (zoomDisplay) {
            zoomDisplay.textContent = Math.round(whiteboard.zoomLevel * 100) + '%';
        }
    }
    
    updateCanvasCursor(whiteboard) {
        const canvas = whiteboard.canvas;
        const tool = whiteboard.currentTool;
        
        switch (tool) {
            case 'pen':
                canvas.style.cursor = 'crosshair';
                break;
            case 'eraser':
                canvas.style.cursor = 'grab';
                break;
            case 'text':
                canvas.style.cursor = 'text';
                break;
            case 'select':
                canvas.style.cursor = 'default';
                break;
            default:
                canvas.style.cursor = 'crosshair';
                break;
        }
    }
    
    startDrawing(whiteboard, event) {
        const rect = whiteboard.canvas.getBoundingClientRect();
        const x = (event.clientX - rect.left) / whiteboard.zoomLevel;
        const y = (event.clientY - rect.top) / whiteboard.zoomLevel;
        
        whiteboard.isDrawing = true;
        whiteboard.startX = x;
        whiteboard.startY = y;
        
        const ctx = whiteboard.ctx;
        ctx.strokeStyle = whiteboard.currentColor;
        ctx.lineWidth = whiteboard.currentStrokeSize;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        
        if (whiteboard.currentTool === 'pen') {
            ctx.beginPath();
            ctx.moveTo(x, y);
        } else if (whiteboard.currentTool === 'eraser') {
            ctx.globalCompositeOperation = 'destination-out';
            ctx.beginPath();
            ctx.moveTo(x, y);
        }
        
        // Save state for undo
        this.saveState(whiteboard);
        
        // Broadcast drawing start
        this.broadcastDrawingEvent(whiteboard, {
            type: 'start',
            tool: whiteboard.currentTool,
            x: x,
            y: y,
            color: whiteboard.currentColor,
            strokeSize: whiteboard.currentStrokeSize
        });
    }
    
    draw(whiteboard, event) {
        if (!whiteboard.isDrawing) return;
        
        const rect = whiteboard.canvas.getBoundingClientRect();
        const x = (event.clientX - rect.left) / whiteboard.zoomLevel;
        const y = (event.clientY - rect.top) / whiteboard.zoomLevel;
        
        const ctx = whiteboard.ctx;
        
        if (whiteboard.currentTool === 'pen' || whiteboard.currentTool === 'eraser') {
            ctx.lineTo(x, y);
            ctx.stroke();
        } else if (whiteboard.currentTool === 'rectangle') {
            this.drawRectangle(whiteboard, whiteboard.startX, whiteboard.startY, x, y);
        } else if (whiteboard.currentTool === 'circle') {
            this.drawCircle(whiteboard, whiteboard.startX, whiteboard.startY, x, y);
        } else if (whiteboard.currentTool === 'line') {
            this.drawLine(whiteboard, whiteboard.startX, whiteboard.startY, x, y);
        } else if (whiteboard.currentTool === 'arrow') {
            this.drawArrow(whiteboard, whiteboard.startX, whiteboard.startY, x, y);
        }
        
        // Broadcast drawing continue
        this.broadcastDrawingEvent(whiteboard, {
            type: 'continue',
            tool: whiteboard.currentTool,
            x: x,
            y: y
        });
    }
    
    stopDrawing(whiteboard, event) {
        if (!whiteboard.isDrawing) return;
        
        whiteboard.isDrawing = false;
        
        const rect = whiteboard.canvas.getBoundingClientRect();
        const x = (event.clientX - rect.left) / whiteboard.zoomLevel;
        const y = (event.clientY - rect.top) / whiteboard.zoomLevel;
        
        // Reset composite operation
        whiteboard.ctx.globalCompositeOperation = 'source-over';
        
        // Handle text tool
        if (whiteboard.currentTool === 'text') {
            this.addText(whiteboard, x, y);
        }
        
        // Broadcast drawing end
        this.broadcastDrawingEvent(whiteboard, {
            type: 'end',
            tool: whiteboard.currentTool,
            x: x,
            y: y
        });
    }
    
    drawRectangle(whiteboard, startX, startY, endX, endY) {
        const ctx = whiteboard.ctx;
        
        // Clear and redraw
        this.restoreState(whiteboard);
        
        ctx.strokeStyle = whiteboard.currentColor;
        ctx.lineWidth = whiteboard.currentStrokeSize;
        ctx.strokeRect(startX, startY, endX - startX, endY - startY);
    }
    
    drawCircle(whiteboard, startX, startY, endX, endY) {
        const ctx = whiteboard.ctx;
        
        // Clear and redraw
        this.restoreState(whiteboard);
        
        const radius = Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2));
        
        ctx.strokeStyle = whiteboard.currentColor;
        ctx.lineWidth = whiteboard.currentStrokeSize;
        ctx.beginPath();
        ctx.arc(startX, startY, radius, 0, 2 * Math.PI);
        ctx.stroke();
    }
    
    drawLine(whiteboard, startX, startY, endX, endY) {
        const ctx = whiteboard.ctx;
        
        // Clear and redraw
        this.restoreState(whiteboard);
        
        ctx.strokeStyle = whiteboard.currentColor;
        ctx.lineWidth = whiteboard.currentStrokeSize;
        ctx.beginPath();
        ctx.moveTo(startX, startY);
        ctx.lineTo(endX, endY);
        ctx.stroke();
    }
    
    drawArrow(whiteboard, startX, startY, endX, endY) {
        const ctx = whiteboard.ctx;
        
        // Clear and redraw
        this.restoreState(whiteboard);
        
        const headlen = 10;
        const dx = endX - startX;
        const dy = endY - startY;
        const angle = Math.atan2(dy, dx);
        
        ctx.strokeStyle = whiteboard.currentColor;
        ctx.lineWidth = whiteboard.currentStrokeSize;
        
        // Draw line
        ctx.beginPath();
        ctx.moveTo(startX, startY);
        ctx.lineTo(endX, endY);
        ctx.stroke();
        
        // Draw arrow head
        ctx.beginPath();
        ctx.moveTo(endX, endY);
        ctx.lineTo(endX - headlen * Math.cos(angle - Math.PI/6), endY - headlen * Math.sin(angle - Math.PI/6));
        ctx.moveTo(endX, endY);
        ctx.lineTo(endX - headlen * Math.cos(angle + Math.PI/6), endY - headlen * Math.sin(angle + Math.PI/6));
        ctx.stroke();
    }
    
    addText(whiteboard, x, y) {
        const text = prompt('Enter text:');
        if (text) {
            const ctx = whiteboard.ctx;
            ctx.fillStyle = whiteboard.currentColor;
            ctx.font = `${whiteboard.currentStrokeSize * 4}px Arial`;
            ctx.fillText(text, x, y);
            
            // Broadcast text
            this.broadcastDrawingEvent(whiteboard, {
                type: 'text',
                text: text,
                x: x,
                y: y,
                color: whiteboard.currentColor,
                fontSize: whiteboard.currentStrokeSize * 4
            });
        }
    }
    
    clearCanvas(whiteboard) {
        const ctx = whiteboard.ctx;
        ctx.clearRect(0, 0, whiteboard.canvas.width, whiteboard.canvas.height);
        
        // Save state
        this.saveState(whiteboard);
        
        // Broadcast clear
        this.broadcastDrawingEvent(whiteboard, {
            type: 'clear'
        });
    }
    
    saveState(whiteboard) {
        const ctx = whiteboard.ctx;
        const imageData = ctx.getImageData(0, 0, whiteboard.canvas.width, whiteboard.canvas.height);
        
        // Remove future history if we're not at the end
        if (whiteboard.historyIndex < whiteboard.history.length - 1) {
            whiteboard.history = whiteboard.history.slice(0, whiteboard.historyIndex + 1);
        }
        
        whiteboard.history.push(imageData);
        whiteboard.historyIndex++;
        
        // Limit history size
        if (whiteboard.history.length > 50) {
            whiteboard.history.shift();
            whiteboard.historyIndex--;
        }
    }
    
    restoreState(whiteboard) {
        if (whiteboard.history.length > 0 && whiteboard.historyIndex >= 0) {
            const ctx = whiteboard.ctx;
            const imageData = whiteboard.history[whiteboard.historyIndex];
            ctx.putImageData(imageData, 0, 0);
        }
    }
    
    undo(whiteboard) {
        if (whiteboard.historyIndex > 0) {
            whiteboard.historyIndex--;
            this.restoreState(whiteboard);
            
            // Broadcast undo
            this.broadcastDrawingEvent(whiteboard, {
                type: 'undo'
            });
        }
    }
    
    redo(whiteboard) {
        if (whiteboard.historyIndex < whiteboard.history.length - 1) {
            whiteboard.historyIndex++;
            this.restoreState(whiteboard);
            
            // Broadcast redo
            this.broadcastDrawingEvent(whiteboard, {
                type: 'redo'
            });
        }
    }
    
    saveAsImage(whiteboard) {
        const canvas = whiteboard.canvas;
        const link = document.createElement('a');
        link.download = `whiteboard_${whiteboard.documentId}_${Date.now()}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }
    
    toggleFullscreen(whiteboard) {
        whiteboard.container.classList.toggle('fullscreen');
        
        if (whiteboard.container.classList.contains('fullscreen')) {
            document.body.style.overflow = 'hidden';
            // Resize canvas for fullscreen
            const canvas = whiteboard.canvas;
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        } else {
            document.body.style.overflow = '';
            // Restore original size
            const canvas = whiteboard.canvas;
            canvas.width = 1200;
            canvas.height = 800;
        }
    }
    
    updateCursorPosition(whiteboard, event) {
        const rect = whiteboard.canvas.getBoundingClientRect();
        const x = Math.round((event.clientX - rect.left) / whiteboard.zoomLevel);
        const y = Math.round((event.clientY - rect.top) / whiteboard.zoomLevel);
        
        // Update cursor position display
        const positionEl = whiteboard.container.querySelector('.cursor-position');
        if (positionEl) {
            positionEl.textContent = `${x}, ${y}`;
        }
        
        // Broadcast cursor position
        this.broadcastCursorUpdate(whiteboard, { x: x, y: y });
    }
    
    handleKeyboard(whiteboard, event) {
        if (event.ctrlKey || event.metaKey) {
            switch (event.key) {
                case 'z':
                    event.preventDefault();
                    if (event.shiftKey) {
                        this.redo(whiteboard);
                    } else {
                        this.undo(whiteboard);
                    }
                    break;
                case 'y':
                    event.preventDefault();
                    this.redo(whiteboard);
                    break;
                case 's':
                    event.preventDefault();
                    this.saveAsImage(whiteboard);
                    break;
            }
        }
    }
    
    broadcastToolChange(whiteboard, toolData) {
        const change = {
            type: 'tool_change',
            ...toolData,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastWhiteboardChange(whiteboard, change);
    }
    
    broadcastDrawingEvent(whiteboard, drawingData) {
        const change = {
            type: 'drawing_event',
            ...drawingData,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastWhiteboardChange(whiteboard, change);
    }
    
    broadcastCursorUpdate(whiteboard, cursorData) {
        // Use existing cursor update endpoint
        fetch('/api/websocket/update-cursor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.wsClient.authToken}`
            },
            body: JSON.stringify({
                workspace_id: this.wsClient.workspaceId,
                cursor_position: cursorData,
                page_url: window.location.href
            })
        });
    }
    
    broadcastWhiteboardChange(whiteboard, change) {
        // Use existing WebSocket controller
        fetch('/api/websocket/update-document', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.wsClient.authToken}`
            },
            body: JSON.stringify({
                workspace_id: this.wsClient.workspaceId,
                document_id: whiteboard.documentId,
                changes: change,
                document_type: 'whiteboard'
            })
        });
    }
    
    applyRemoteChange(data) {
        const whiteboard = this.whiteboards.get(data.document_id);
        if (!whiteboard) return;
        
        const change = data.changes;
        
        if (change.user_id === this.wsClient.userId) return;
        
        if (change.type === 'drawing_event') {
            this.applyRemoteDrawing(whiteboard, change);
        } else if (change.type === 'tool_change') {
            this.showRemoteToolChange(whiteboard, change, data.user_name);
        }
    }
    
    applyRemoteDrawing(whiteboard, change) {
        const ctx = whiteboard.ctx;
        
        // Set tool properties
        ctx.strokeStyle = change.color || '#000000';
        ctx.lineWidth = change.strokeSize || 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        
        switch (change.type) {
            case 'clear':
                ctx.clearRect(0, 0, whiteboard.canvas.width, whiteboard.canvas.height);
                break;
                
            case 'text':
                ctx.fillStyle = change.color || '#000000';
                ctx.font = `${change.fontSize || 16}px Arial`;
                ctx.fillText(change.text, change.x, change.y);
                break;
                
            // Add more drawing operations as needed
        }
    }
    
    showRemoteToolChange(whiteboard, change, userName) {
        // Show notification of tool change
        this.wsClient.showNotification({
            notification_type: 'info',
            message: `${userName} switched to ${change.tool || 'tool'}`,
            user_name: userName
        });
    }
    
    updateRemoteCursor(data) {
        if (data.user_id === this.wsClient.userId) return;
        
        this.whiteboards.forEach(whiteboard => {
            this.showRemoteWhiteboardCursor(whiteboard, data);
        });
    }
    
    showRemoteWhiteboardCursor(whiteboard, data) {
        const cursorContainer = whiteboard.container.querySelector('.remote-cursors');
        const cursorId = `cursor_${data.user_id}`;
        
        let cursor = cursorContainer.querySelector(`[data-cursor-id="${cursorId}"]`);
        
        if (!cursor) {
            cursor = document.createElement('div');
            cursor.className = 'remote-whiteboard-cursor';
            cursor.dataset.cursorId = cursorId;
            cursor.innerHTML = `
                <div class="cursor-dot"></div>
                <div class="cursor-label">${data.user_name}</div>
            `;
            cursorContainer.appendChild(cursor);
        }
        
        // Position cursor
        if (data.cursor_position && data.cursor_position.x !== undefined) {
            cursor.style.left = (data.cursor_position.x * whiteboard.zoomLevel) + 'px';
            cursor.style.top = (data.cursor_position.y * whiteboard.zoomLevel) + 'px';
            cursor.style.display = 'block';
        }
        
        // Hide cursor after inactivity
        setTimeout(() => {
            cursor.style.display = 'none';
        }, 3000);
    }
    
    loadWhiteboardData(whiteboard, data) {
        // Load existing whiteboard data
        if (data.imageData) {
            const img = new Image();
            img.onload = () => {
                whiteboard.ctx.drawImage(img, 0, 0);
                this.saveState(whiteboard);
            };
            img.src = data.imageData;
        }
    }
    
    destroy(documentId) {
        const whiteboard = this.whiteboards.get(documentId);
        if (whiteboard) {
            whiteboard.container.remove();
            this.whiteboards.delete(documentId);
        }
    }
}

// Export for global use
window.CollaborativeWhiteboard = CollaborativeWhiteboard;