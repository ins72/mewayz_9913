/**
 * Advanced Document Editing Tools for WebSocket Collaboration
 * 
 * Rich text editor, code editor, whiteboard, and other collaborative editing tools
 */

class CollaborativeDocumentEditor {
    constructor(webSocketClient, containerId) {
        this.wsClient = webSocketClient;
        this.container = document.getElementById(containerId);
        this.editors = new Map();
        this.activeUsers = new Map();
        this.changeBuffer = [];
        this.version = 0;
        
        this.initializeEditors();
        this.setupEventListeners();
    }
    
    initializeEditors() {
        // Rich Text Editor
        this.richTextEditor = new CollaborativeRichTextEditor(this.wsClient, this.container);
        
        // Code Editor
        this.codeEditor = new CollaborativeCodeEditor(this.wsClient, this.container);
        
        // Whiteboard
        this.whiteboard = new CollaborativeWhiteboard(this.wsClient, this.container);
        
        // Table Editor
        this.tableEditor = new CollaborativeTableEditor(this.wsClient, this.container);
        
        // Document Structure Editor
        this.structureEditor = new DocumentStructureEditor(this.wsClient, this.container);
        
        // Comments System
        this.commentsSystem = new CollaborativeComments(this.wsClient, this.container);
        
        // Version Control
        this.versionControl = new DocumentVersionControl(this.wsClient, this.container);
        
        // Media Collaboration
        this.mediaEditor = new CollaborativeMediaEditor(this.wsClient, this.container);
    }
    
    setupEventListeners() {
        // Listen for document changes from other users
        this.wsClient.on('document.updated', (data) => {
            this.handleRemoteDocumentChange(data);
        });
        
        // Listen for cursor and selection changes
        this.wsClient.on('cursor.moved', (data) => {
            this.handleRemoteCursorChange(data);
        });
        
        // Listen for comments
        this.wsClient.on('comment.added', (data) => {
            this.commentsSystem.handleRemoteComment(data);
        });
    }
    
    handleRemoteDocumentChange(data) {
        const editorType = data.document_type;
        const editor = this.getEditorByType(editorType);
        
        if (editor) {
            editor.applyRemoteChange(data);
        }
    }
    
    handleRemoteCursorChange(data) {
        // Update cursor positions in all active editors
        this.editors.forEach(editor => {
            editor.updateRemoteCursor(data);
        });
    }
    
    getEditorByType(type) {
        switch (type) {
            case 'rich_text': return this.richTextEditor;
            case 'code': return this.codeEditor;
            case 'whiteboard': return this.whiteboard;
            case 'table': return this.tableEditor;
            case 'structure': return this.structureEditor;
            case 'media': return this.mediaEditor;
            default: return null;
        }
    }
    
    createEditor(type, documentId, initialContent = '') {
        const editor = this.getEditorByType(type);
        if (editor) {
            return editor.create(documentId, initialContent);
        }
        return null;
    }
    
    destroyEditor(documentId) {
        this.editors.forEach(editor => {
            editor.destroy(documentId);
        });
    }
}

/**
 * Collaborative Rich Text Editor
 * 
 * WYSIWYG editor with real-time collaboration
 */
class CollaborativeRichTextEditor {
    constructor(webSocketClient, container) {
        this.wsClient = webSocketClient;
        this.container = container;
        this.editors = new Map();
        this.selectionRanges = new Map();
        this.userColors = new Map();
        this.colorIndex = 0;
        this.colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8C8'];
    }
    
    create(documentId, initialContent = '') {
        const editorContainer = document.createElement('div');
        editorContainer.className = 'collaborative-rich-text-editor';
        editorContainer.dataset.documentId = documentId;
        
        editorContainer.innerHTML = `
            <div class="editor-toolbar">
                <div class="toolbar-group">
                    <button class="toolbar-btn" data-action="bold" title="Bold">
                        <i class="icon-bold">B</i>
                    </button>
                    <button class="toolbar-btn" data-action="italic" title="Italic">
                        <i class="icon-italic">I</i>
                    </button>
                    <button class="toolbar-btn" data-action="underline" title="Underline">
                        <i class="icon-underline">U</i>
                    </button>
                    <button class="toolbar-btn" data-action="strikethrough" title="Strikethrough">
                        <i class="icon-strikethrough">S</i>
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <select class="toolbar-select" data-action="fontSize">
                        <option value="12">12px</option>
                        <option value="14">14px</option>
                        <option value="16" selected>16px</option>
                        <option value="18">18px</option>
                        <option value="20">20px</option>
                        <option value="24">24px</option>
                        <option value="32">32px</option>
                    </select>
                    
                    <input type="color" class="toolbar-color" data-action="color" value="#000000" title="Text Color">
                    <input type="color" class="toolbar-color" data-action="backgroundColor" value="#ffffff" title="Background Color">
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" data-action="alignLeft" title="Align Left">
                        <i class="icon-align-left">←</i>
                    </button>
                    <button class="toolbar-btn" data-action="alignCenter" title="Align Center">
                        <i class="icon-align-center">⟷</i>
                    </button>
                    <button class="toolbar-btn" data-action="alignRight" title="Align Right">
                        <i class="icon-align-right">→</i>
                    </button>
                    <button class="toolbar-btn" data-action="justify" title="Justify">
                        <i class="icon-justify">⟼</i>
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" data-action="insertOrderedList" title="Numbered List">
                        <i class="icon-ol">1.</i>
                    </button>
                    <button class="toolbar-btn" data-action="insertUnorderedList" title="Bullet List">
                        <i class="icon-ul">•</i>
                    </button>
                    <button class="toolbar-btn" data-action="indent" title="Indent">
                        <i class="icon-indent">→</i>
                    </button>
                    <button class="toolbar-btn" data-action="outdent" title="Outdent">
                        <i class="icon-outdent">←</i>
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" data-action="createLink" title="Insert Link">
                        <i class="icon-link">🔗</i>
                    </button>
                    <button class="toolbar-btn" data-action="insertImage" title="Insert Image">
                        <i class="icon-image">🖼</i>
                    </button>
                    <button class="toolbar-btn" data-action="insertTable" title="Insert Table">
                        <i class="icon-table">⊞</i>
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" data-action="undo" title="Undo">
                        <i class="icon-undo">↶</i>
                    </button>
                    <button class="toolbar-btn" data-action="redo" title="Redo">
                        <i class="icon-redo">↷</i>
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button class="toolbar-btn" data-action="addComment" title="Add Comment">
                        <i class="icon-comment">💬</i>
                    </button>
                    <button class="toolbar-btn" data-action="showVersions" title="Version History">
                        <i class="icon-versions">📚</i>
                    </button>
                </div>
                
                <div class="active-users">
                    <span class="active-users-label">Active:</span>
                    <div class="active-users-list"></div>
                </div>
            </div>
            
            <div class="editor-content" contenteditable="true" data-document-id="${documentId}">
                ${initialContent}
            </div>
            
            <div class="editor-status">
                <span class="word-count">0 words</span>
                <span class="character-count">0 characters</span>
                <span class="collaboration-status">Ready for collaboration</span>
            </div>
        `;
        
        this.container.appendChild(editorContainer);
        
        // Initialize editor
        const editor = {
            container: editorContainer,
            content: editorContainer.querySelector('.editor-content'),
            toolbar: editorContainer.querySelector('.editor-toolbar'),
            documentId: documentId,
            cursors: new Map(),
            selections: new Map()
        };
        
        this.editors.set(documentId, editor);
        this.setupEditorEvents(editor);
        
        return editor;
    }
    
    setupEditorEvents(editor) {
        // Toolbar events
        editor.toolbar.addEventListener('click', (e) => {
            if (e.target.matches('.toolbar-btn')) {
                const action = e.target.dataset.action;
                this.executeCommand(editor, action);
            }
        });
        
        editor.toolbar.addEventListener('change', (e) => {
            if (e.target.matches('.toolbar-select') || e.target.matches('.toolbar-color')) {
                const action = e.target.dataset.action;
                const value = e.target.value;
                this.executeCommand(editor, action, value);
            }
        });
        
        // Content events
        editor.content.addEventListener('input', (e) => {
            this.handleContentChange(editor, e);
        });
        
        editor.content.addEventListener('selectionchange', (e) => {
            this.handleSelectionChange(editor, e);
        });
        
        editor.content.addEventListener('paste', (e) => {
            this.handlePaste(editor, e);
        });
        
        // Update word count
        editor.content.addEventListener('input', () => {
            this.updateWordCount(editor);
        });
        
        // Initial word count
        this.updateWordCount(editor);
    }
    
    executeCommand(editor, action, value = null) {
        const selection = window.getSelection();
        const range = selection.getRangeAt(0);
        
        // Store selection for collaboration
        this.storeSelection(editor, range);
        
        switch (action) {
            case 'bold':
            case 'italic':
            case 'underline':
            case 'strikethrough':
                document.execCommand(action, false, null);
                break;
                
            case 'fontSize':
                document.execCommand('fontSize', false, '7');
                // Replace font tags with span tags
                const fontTags = editor.content.querySelectorAll('font[size="7"]');
                fontTags.forEach(font => {
                    const span = document.createElement('span');
                    span.style.fontSize = value + 'px';
                    span.innerHTML = font.innerHTML;
                    font.replaceWith(span);
                });
                break;
                
            case 'color':
                document.execCommand('foreColor', false, value);
                break;
                
            case 'backgroundColor':
                document.execCommand('backColor', false, value);
                break;
                
            case 'alignLeft':
                document.execCommand('justifyLeft', false, null);
                break;
                
            case 'alignCenter':
                document.execCommand('justifyCenter', false, null);
                break;
                
            case 'alignRight':
                document.execCommand('justifyRight', false, null);
                break;
                
            case 'justify':
                document.execCommand('justifyFull', false, null);
                break;
                
            case 'insertOrderedList':
                document.execCommand('insertOrderedList', false, null);
                break;
                
            case 'insertUnorderedList':
                document.execCommand('insertUnorderedList', false, null);
                break;
                
            case 'indent':
                document.execCommand('indent', false, null);
                break;
                
            case 'outdent':
                document.execCommand('outdent', false, null);
                break;
                
            case 'createLink':
                const url = prompt('Enter URL:');
                if (url) {
                    document.execCommand('createLink', false, url);
                }
                break;
                
            case 'insertImage':
                const imgUrl = prompt('Enter image URL:');
                if (imgUrl) {
                    document.execCommand('insertImage', false, imgUrl);
                }
                break;
                
            case 'insertTable':
                this.insertTable(editor);
                break;
                
            case 'undo':
                document.execCommand('undo', false, null);
                break;
                
            case 'redo':
                document.execCommand('redo', false, null);
                break;
                
            case 'addComment':
                this.addComment(editor, range);
                break;
                
            case 'showVersions':
                this.showVersionHistory(editor);
                break;
        }
        
        // Broadcast change to other users
        this.broadcastChange(editor, action, value);
    }
    
    handleContentChange(editor, event) {
        const content = editor.content.innerHTML;
        const change = {
            type: 'content_change',
            content: content,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastDocumentChange(editor, change);
        this.updateWordCount(editor);
    }
    
    handleSelectionChange(editor, event) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            this.storeSelection(editor, range);
            this.broadcastSelectionChange(editor, range);
        }
    }
    
    handlePaste(editor, event) {
        event.preventDefault();
        
        const clipboardData = event.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('text/html') || clipboardData.getData('text/plain');
        
        // Clean pasted content
        const cleanContent = this.cleanPastedContent(pastedData);
        
        // Insert at cursor position
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(document.createRange().createContextualFragment(cleanContent));
        }
        
        // Broadcast change
        this.handleContentChange(editor, event);
    }
    
    cleanPastedContent(html) {
        // Remove dangerous tags and attributes
        const div = document.createElement('div');
        div.innerHTML = html;
        
        // Remove script tags
        const scripts = div.querySelectorAll('script');
        scripts.forEach(script => script.remove());
        
        // Remove dangerous attributes
        const allElements = div.querySelectorAll('*');
        allElements.forEach(element => {
            const allowedAttributes = ['style', 'class', 'href', 'src', 'alt', 'title'];
            Array.from(element.attributes).forEach(attr => {
                if (!allowedAttributes.includes(attr.name)) {
                    element.removeAttribute(attr.name);
                }
            });
        });
        
        return div.innerHTML;
    }
    
    insertTable(editor) {
        const rows = prompt('Number of rows:', '3');
        const cols = prompt('Number of columns:', '3');
        
        if (rows && cols) {
            let tableHtml = '<table class="collaborative-table" border="1" cellpadding="5" cellspacing="0">';
            
            for (let i = 0; i < parseInt(rows); i++) {
                tableHtml += '<tr>';
                for (let j = 0; j < parseInt(cols); j++) {
                    tableHtml += '<td contenteditable="true">&nbsp;</td>';
                }
                tableHtml += '</tr>';
            }
            
            tableHtml += '</table>';
            
            // Insert table at cursor position
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                range.deleteContents();
                range.insertNode(document.createRange().createContextualFragment(tableHtml));
            }
            
            this.handleContentChange(editor, null);
        }
    }
    
    addComment(editor, range) {
        const comment = prompt('Add comment:');
        if (comment) {
            const commentId = 'comment_' + Date.now();
            const selectedText = range.toString();
            
            // Wrap selection in comment span
            const commentSpan = document.createElement('span');
            commentSpan.className = 'comment-highlight';
            commentSpan.dataset.commentId = commentId;
            commentSpan.title = comment;
            
            try {
                range.surroundContents(commentSpan);
            } catch (e) {
                // Fallback for complex selections
                const contents = range.extractContents();
                commentSpan.appendChild(contents);
                range.insertNode(commentSpan);
            }
            
            // Broadcast comment
            this.wsClient.sendNotification('comment', `Comment added: ${comment}`, {
                comment_id: commentId,
                text: selectedText,
                comment: comment,
                document_id: editor.documentId
            });
            
            this.handleContentChange(editor, null);
        }
    }
    
    showVersionHistory(editor) {
        // This would integrate with the version control system
        console.log('Showing version history for', editor.documentId);
    }
    
    storeSelection(editor, range) {
        const selection = {
            startContainer: range.startContainer,
            startOffset: range.startOffset,
            endContainer: range.endContainer,
            endOffset: range.endOffset
        };
        
        this.selectionRanges.set(editor.documentId, selection);
    }
    
    broadcastChange(editor, action, value) {
        const change = {
            type: 'formatting_change',
            action: action,
            value: value,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastDocumentChange(editor, change);
    }
    
    broadcastDocumentChange(editor, change) {
        // Use existing WebSocket controller
        fetch('/api/websocket/update-document', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.wsClient.authToken}`
            },
            body: JSON.stringify({
                workspace_id: this.wsClient.workspaceId,
                document_id: editor.documentId,
                changes: change,
                document_type: 'rich_text'
            })
        });
    }
    
    broadcastSelectionChange(editor, range) {
        const selection = {
            startOffset: range.startOffset,
            endOffset: range.endOffset,
            text: range.toString()
        };
        
        // Use existing cursor update endpoint
        fetch('/api/websocket/update-cursor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.wsClient.authToken}`
            },
            body: JSON.stringify({
                workspace_id: this.wsClient.workspaceId,
                cursor_position: selection,
                page_url: window.location.href
            })
        });
    }
    
    updateWordCount(editor) {
        const content = editor.content.textContent || '';
        const words = content.trim().split(/\s+/).filter(word => word.length > 0);
        const wordCount = words.length;
        const charCount = content.length;
        
        const wordCountEl = editor.container.querySelector('.word-count');
        const charCountEl = editor.container.querySelector('.character-count');
        
        if (wordCountEl) wordCountEl.textContent = `${wordCount} words`;
        if (charCountEl) charCountEl.textContent = `${charCount} characters`;
    }
    
    applyRemoteChange(data) {
        const editor = this.editors.get(data.document_id);
        if (!editor) return;
        
        const change = data.changes;
        
        if (change.type === 'content_change') {
            // Apply content change with conflict resolution
            this.applyContentChange(editor, change);
        } else if (change.type === 'formatting_change') {
            // Apply formatting change
            this.applyFormattingChange(editor, change);
        }
        
        // Update collaboration status
        const statusEl = editor.container.querySelector('.collaboration-status');
        if (statusEl) {
            statusEl.textContent = `Updated by ${data.user_name}`;
            setTimeout(() => {
                statusEl.textContent = 'Ready for collaboration';
            }, 3000);
        }
    }
    
    applyContentChange(editor, change) {
        // Simple content replacement - in production, use operational transformation
        if (change.user_id !== this.wsClient.userId) {
            editor.content.innerHTML = change.content;
            this.updateWordCount(editor);
        }
    }
    
    applyFormattingChange(editor, change) {
        // Apply formatting changes from other users
        if (change.user_id !== this.wsClient.userId) {
            console.log('Applying formatting change:', change);
        }
    }
    
    updateRemoteCursor(data) {
        // Update cursor positions from other users
        this.editors.forEach(editor => {
            this.showRemoteCursor(editor, data);
        });
    }
    
    showRemoteCursor(editor, data) {
        if (data.user_id === this.wsClient.userId) return;
        
        const cursorId = `cursor_${data.user_id}`;
        let cursor = editor.container.querySelector(`[data-cursor-id="${cursorId}"]`);
        
        if (!cursor) {
            cursor = document.createElement('div');
            cursor.className = 'remote-cursor';
            cursor.dataset.cursorId = cursorId;
            cursor.innerHTML = `
                <div class="cursor-line"></div>
                <div class="cursor-label">${data.user_name}</div>
            `;
            
            // Assign color
            if (!this.userColors.has(data.user_id)) {
                this.userColors.set(data.user_id, this.colors[this.colorIndex % this.colors.length]);
                this.colorIndex++;
            }
            
            const color = this.userColors.get(data.user_id);
            cursor.style.borderColor = color;
            cursor.querySelector('.cursor-label').style.backgroundColor = color;
            
            editor.container.appendChild(cursor);
        }
        
        // Update cursor position (this would need more sophisticated positioning)
        cursor.style.display = 'block';
        
        // Hide cursor after inactivity
        setTimeout(() => {
            cursor.style.display = 'none';
        }, 5000);
    }
    
    destroy(documentId) {
        const editor = this.editors.get(documentId);
        if (editor) {
            editor.container.remove();
            this.editors.delete(documentId);
        }
    }
}

// Export for global use
window.CollaborativeDocumentEditor = CollaborativeDocumentEditor;
window.CollaborativeRichTextEditor = CollaborativeRichTextEditor;