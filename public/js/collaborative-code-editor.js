/**
 * Collaborative Code Editor
 * 
 * Real-time collaborative code editing with syntax highlighting
 */

class CollaborativeCodeEditor {
    constructor(webSocketClient, container) {
        this.wsClient = webSocketClient;
        this.container = container;
        this.editors = new Map();
        this.cursors = new Map();
        this.languages = {
            'html': 'HTML',
            'css': 'CSS',
            'javascript': 'JavaScript',
            'python': 'Python',
            'php': 'PHP',
            'json': 'JSON',
            'xml': 'XML',
            'sql': 'SQL',
            'markdown': 'Markdown'
        };
    }
    
    create(documentId, initialContent = '', language = 'html') {
        const editorContainer = document.createElement('div');
        editorContainer.className = 'collaborative-code-editor';
        editorContainer.dataset.documentId = documentId;
        
        editorContainer.innerHTML = `
            <div class="code-editor-header">
                <div class="editor-controls">
                    <select class="language-selector" data-document-id="${documentId}">
                        ${Object.entries(this.languages).map(([key, value]) => 
                            `<option value="${key}" ${key === language ? 'selected' : ''}>${value}</option>`
                        ).join('')}
                    </select>
                    
                    <button class="code-btn" data-action="format" title="Format Code">
                        <span>Format</span>
                    </button>
                    
                    <button class="code-btn" data-action="find" title="Find & Replace">
                        <span>Find</span>
                    </button>
                    
                    <button class="code-btn" data-action="fullscreen" title="Fullscreen">
                        <span>⛶</span>
                    </button>
                </div>
                
                <div class="editor-info">
                    <span class="line-count">1 lines</span>
                    <span class="cursor-position">Line 1, Column 1</span>
                    <span class="language-indicator">${this.languages[language]}</span>
                </div>
            </div>
            
            <div class="code-editor-body">
                <div class="line-numbers">
                    <div class="line-number">1</div>
                </div>
                
                <div class="code-content">
                    <textarea 
                        class="code-textarea" 
                        data-document-id="${documentId}"
                        data-language="${language}"
                        spellcheck="false"
                        autocomplete="off"
                        placeholder="Start typing code..."
                    >${initialContent}</textarea>
                    
                    <div class="code-highlights" data-document-id="${documentId}"></div>
                    <div class="code-cursors" data-document-id="${documentId}"></div>
                </div>
            </div>
            
            <div class="code-editor-footer">
                <div class="collaborative-users">
                    <span class="users-label">Editing:</span>
                    <div class="users-list"></div>
                </div>
                
                <div class="code-status">
                    <span class="auto-save-status">Auto-saved</span>
                    <span class="collaboration-status">Ready</span>
                </div>
            </div>
        `;
        
        this.container.appendChild(editorContainer);
        
        const editor = {
            container: editorContainer,
            textarea: editorContainer.querySelector('.code-textarea'),
            highlights: editorContainer.querySelector('.code-highlights'),
            cursors: editorContainer.querySelector('.code-cursors'),
            lineNumbers: editorContainer.querySelector('.line-numbers'),
            documentId: documentId,
            language: language,
            content: initialContent,
            remoteCursors: new Map()
        };
        
        this.editors.set(documentId, editor);
        this.setupCodeEditorEvents(editor);
        this.updateLineNumbers(editor);
        this.applySyntaxHighlighting(editor);
        
        return editor;
    }
    
    setupCodeEditorEvents(editor) {
        // Content changes
        editor.textarea.addEventListener('input', (e) => {
            this.handleCodeChange(editor, e);
        });
        
        // Cursor/selection changes
        editor.textarea.addEventListener('selectionchange', (e) => {
            this.handleSelectionChange(editor, e);
        });
        
        // Key events for special handling
        editor.textarea.addEventListener('keydown', (e) => {
            this.handleKeyDown(editor, e);
        });
        
        // Language change
        const languageSelector = editor.container.querySelector('.language-selector');
        languageSelector.addEventListener('change', (e) => {
            this.changeLanguage(editor, e.target.value);
        });
        
        // Control buttons
        editor.container.addEventListener('click', (e) => {
            if (e.target.matches('.code-btn')) {
                const action = e.target.dataset.action;
                this.executeCodeAction(editor, action);
            }
        });
        
        // Scroll sync
        editor.textarea.addEventListener('scroll', (e) => {
            this.syncScroll(editor);
        });
        
        // Auto-save
        let autoSaveTimer;
        editor.textarea.addEventListener('input', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                this.autoSave(editor);
            }, 2000);
        });
    }
    
    handleCodeChange(editor, event) {
        const content = editor.textarea.value;
        const cursorPosition = editor.textarea.selectionStart;
        
        // Update line numbers
        this.updateLineNumbers(editor);
        
        // Update syntax highlighting
        this.applySyntaxHighlighting(editor);
        
        // Update cursor position display
        this.updateCursorPosition(editor);
        
        // Broadcast change
        const change = {
            type: 'code_change',
            content: content,
            cursor_position: cursorPosition,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastCodeChange(editor, change);
    }
    
    handleSelectionChange(editor, event) {
        const start = editor.textarea.selectionStart;
        const end = editor.textarea.selectionEnd;
        
        this.updateCursorPosition(editor);
        
        // Broadcast cursor change
        const cursorData = {
            start: start,
            end: end,
            text: editor.textarea.value.substring(start, end)
        };
        
        this.broadcastCursorChange(editor, cursorData);
    }
    
    handleKeyDown(editor, event) {
        const textarea = editor.textarea;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        
        // Auto-indent
        if (event.key === 'Enter') {
            const lines = textarea.value.split('\n');
            const currentLineIndex = textarea.value.substring(0, start).split('\n').length - 1;
            const currentLine = lines[currentLineIndex];
            const indent = currentLine.match(/^\s*/)[0];
            
            // Add extra indent for certain characters
            if (currentLine.trim().endsWith('{') || currentLine.trim().endsWith(':')) {
                event.preventDefault();
                const newContent = 
                    textarea.value.substring(0, start) + 
                    '\n' + indent + '    ' + 
                    textarea.value.substring(end);
                textarea.value = newContent;
                textarea.selectionStart = textarea.selectionEnd = start + indent.length + 5;
                this.handleCodeChange(editor, event);
            }
        }
        
        // Auto-close brackets
        if (event.key === '{') {
            event.preventDefault();
            const newContent = 
                textarea.value.substring(0, start) + 
                '{}' + 
                textarea.value.substring(end);
            textarea.value = newContent;
            textarea.selectionStart = textarea.selectionEnd = start + 1;
            this.handleCodeChange(editor, event);
        }
        
        // Auto-close quotes
        if (event.key === '"' || event.key === "'") {
            const quote = event.key;
            event.preventDefault();
            const newContent = 
                textarea.value.substring(0, start) + 
                quote + quote + 
                textarea.value.substring(end);
            textarea.value = newContent;
            textarea.selectionStart = textarea.selectionEnd = start + 1;
            this.handleCodeChange(editor, event);
        }
        
        // Tab handling
        if (event.key === 'Tab') {
            event.preventDefault();
            const newContent = 
                textarea.value.substring(0, start) + 
                '    ' + 
                textarea.value.substring(end);
            textarea.value = newContent;
            textarea.selectionStart = textarea.selectionEnd = start + 4;
            this.handleCodeChange(editor, event);
        }
    }
    
    changeLanguage(editor, language) {
        editor.language = language;
        editor.textarea.dataset.language = language;
        
        // Update language indicator
        const indicator = editor.container.querySelector('.language-indicator');
        if (indicator) {
            indicator.textContent = this.languages[language];
        }
        
        // Re-apply syntax highlighting
        this.applySyntaxHighlighting(editor);
        
        // Broadcast language change
        const change = {
            type: 'language_change',
            language: language,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastCodeChange(editor, change);
    }
    
    executeCodeAction(editor, action) {
        switch (action) {
            case 'format':
                this.formatCode(editor);
                break;
            case 'find':
                this.showFindReplace(editor);
                break;
            case 'fullscreen':
                this.toggleFullscreen(editor);
                break;
        }
    }
    
    formatCode(editor) {
        const content = editor.textarea.value;
        const language = editor.language;
        
        // Basic formatting based on language
        let formatted = content;
        
        if (language === 'json') {
            try {
                const parsed = JSON.parse(content);
                formatted = JSON.stringify(parsed, null, 2);
            } catch (e) {
                console.log('Invalid JSON for formatting');
            }
        } else if (language === 'html' || language === 'xml') {
            formatted = this.formatHTML(content);
        } else if (language === 'css') {
            formatted = this.formatCSS(content);
        } else if (language === 'javascript') {
            formatted = this.formatJavaScript(content);
        }
        
        if (formatted !== content) {
            editor.textarea.value = formatted;
            this.handleCodeChange(editor, null);
        }
    }
    
    formatHTML(html) {
        // Basic HTML formatting
        return html.replace(/></g, '>\n<')
                  .replace(/^\s*\n/gm, '')
                  .split('\n')
                  .map(line => line.trim())
                  .filter(line => line.length > 0)
                  .join('\n');
    }
    
    formatCSS(css) {
        // Basic CSS formatting
        return css.replace(/\{/g, ' {\n')
                  .replace(/\}/g, '\n}\n')
                  .replace(/;/g, ';\n')
                  .replace(/,/g, ',\n')
                  .replace(/\n\s*\n/g, '\n');
    }
    
    formatJavaScript(js) {
        // Basic JavaScript formatting
        return js.replace(/\{/g, ' {\n')
                 .replace(/\}/g, '\n}\n')
                 .replace(/;/g, ';\n')
                 .replace(/,/g, ',\n');
    }
    
    showFindReplace(editor) {
        // Create find/replace dialog
        const dialog = document.createElement('div');
        dialog.className = 'find-replace-dialog';
        dialog.innerHTML = `
            <div class="dialog-header">
                <h3>Find & Replace</h3>
                <button class="close-btn" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
            <div class="dialog-body">
                <div class="find-group">
                    <input type="text" class="find-input" placeholder="Find">
                    <button class="find-btn">Find Next</button>
                    <button class="find-all-btn">Find All</button>
                </div>
                <div class="replace-group">
                    <input type="text" class="replace-input" placeholder="Replace">
                    <button class="replace-btn">Replace</button>
                    <button class="replace-all-btn">Replace All</button>
                </div>
                <div class="options-group">
                    <label><input type="checkbox" class="case-sensitive"> Case sensitive</label>
                    <label><input type="checkbox" class="regex"> Regular expression</label>
                </div>
            </div>
        `;
        
        editor.container.appendChild(dialog);
        
        // Focus find input
        dialog.querySelector('.find-input').focus();
        
        // Setup find/replace functionality
        this.setupFindReplace(editor, dialog);
    }
    
    setupFindReplace(editor, dialog) {
        const findInput = dialog.querySelector('.find-input');
        const replaceInput = dialog.querySelector('.replace-input');
        const caseSensitive = dialog.querySelector('.case-sensitive');
        const regex = dialog.querySelector('.regex');
        
        dialog.querySelector('.find-btn').addEventListener('click', () => {
            this.findNext(editor, findInput.value, caseSensitive.checked, regex.checked);
        });
        
        dialog.querySelector('.find-all-btn').addEventListener('click', () => {
            this.findAll(editor, findInput.value, caseSensitive.checked, regex.checked);
        });
        
        dialog.querySelector('.replace-btn').addEventListener('click', () => {
            this.replace(editor, findInput.value, replaceInput.value, caseSensitive.checked, regex.checked);
        });
        
        dialog.querySelector('.replace-all-btn').addEventListener('click', () => {
            this.replaceAll(editor, findInput.value, replaceInput.value, caseSensitive.checked, regex.checked);
        });
        
        // Find on Enter
        findInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.findNext(editor, findInput.value, caseSensitive.checked, regex.checked);
            }
        });
    }
    
    findNext(editor, searchText, caseSensitive, isRegex) {
        if (!searchText) return;
        
        const content = editor.textarea.value;
        const currentPos = editor.textarea.selectionStart;
        
        let searchRegex;
        if (isRegex) {
            searchRegex = new RegExp(searchText, caseSensitive ? 'g' : 'gi');
        } else {
            searchRegex = new RegExp(searchText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), caseSensitive ? 'g' : 'gi');
        }
        
        searchRegex.lastIndex = currentPos;
        const match = searchRegex.exec(content);
        
        if (match) {
            editor.textarea.selectionStart = match.index;
            editor.textarea.selectionEnd = match.index + match[0].length;
            editor.textarea.focus();
        } else {
            // Search from beginning
            searchRegex.lastIndex = 0;
            const firstMatch = searchRegex.exec(content);
            if (firstMatch) {
                editor.textarea.selectionStart = firstMatch.index;
                editor.textarea.selectionEnd = firstMatch.index + firstMatch[0].length;
                editor.textarea.focus();
            }
        }
    }
    
    replaceAll(editor, searchText, replaceText, caseSensitive, isRegex) {
        if (!searchText) return;
        
        const content = editor.textarea.value;
        let searchRegex;
        
        if (isRegex) {
            searchRegex = new RegExp(searchText, caseSensitive ? 'g' : 'gi');
        } else {
            searchRegex = new RegExp(searchText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), caseSensitive ? 'g' : 'gi');
        }
        
        const newContent = content.replace(searchRegex, replaceText);
        
        if (newContent !== content) {
            editor.textarea.value = newContent;
            this.handleCodeChange(editor, null);
        }
    }
    
    toggleFullscreen(editor) {
        editor.container.classList.toggle('fullscreen');
        
        if (editor.container.classList.contains('fullscreen')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
    
    updateLineNumbers(editor) {
        const lines = editor.textarea.value.split('\n');
        const lineCount = lines.length;
        
        let lineNumbersHTML = '';
        for (let i = 1; i <= lineCount; i++) {
            lineNumbersHTML += `<div class="line-number">${i}</div>`;
        }
        
        editor.lineNumbers.innerHTML = lineNumbersHTML;
        
        // Update line count display
        const lineCountEl = editor.container.querySelector('.line-count');
        if (lineCountEl) {
            lineCountEl.textContent = `${lineCount} lines`;
        }
    }
    
    updateCursorPosition(editor) {
        const textarea = editor.textarea;
        const cursorPos = textarea.selectionStart;
        const content = textarea.value;
        
        // Calculate line and column
        const beforeCursor = content.substring(0, cursorPos);
        const lines = beforeCursor.split('\n');
        const line = lines.length;
        const column = lines[lines.length - 1].length + 1;
        
        // Update cursor position display
        const positionEl = editor.container.querySelector('.cursor-position');
        if (positionEl) {
            positionEl.textContent = `Line ${line}, Column ${column}`;
        }
    }
    
    applySyntaxHighlighting(editor) {
        const content = editor.textarea.value;
        const language = editor.language;
        
        // Basic syntax highlighting
        let highlighted = this.highlightSyntax(content, language);
        
        editor.highlights.innerHTML = highlighted;
        
        // Sync scroll
        this.syncScroll(editor);
    }
    
    highlightSyntax(content, language) {
        // Basic syntax highlighting for different languages
        let highlighted = content;
        
        if (language === 'html' || language === 'xml') {
            highlighted = this.highlightHTML(content);
        } else if (language === 'css') {
            highlighted = this.highlightCSS(content);
        } else if (language === 'javascript') {
            highlighted = this.highlightJavaScript(content);
        } else if (language === 'json') {
            highlighted = this.highlightJSON(content);
        } else if (language === 'python') {
            highlighted = this.highlightPython(content);
        } else if (language === 'php') {
            highlighted = this.highlightPHP(content);
        }
        
        return highlighted;
    }
    
    highlightHTML(html) {
        return html
            .replace(/(&lt;\/?)([a-zA-Z][a-zA-Z0-9]*)(.*?)(&gt;)/g, '$1<span class="tag">$2</span>$3$4')
            .replace(/(\w+)(\s*=\s*)(["'])(.*?)\3/g, '<span class="attr">$1</span>$2<span class="string">$3$4$3</span>')
            .replace(/&lt;!--[\s\S]*?--&gt;/g, '<span class="comment">$&</span>');
    }
    
    highlightCSS(css) {
        return css
            .replace(/([a-zA-Z-]+)(\s*:\s*)/g, '<span class="property">$1</span>$2')
            .replace(/(#[a-fA-F0-9]{3,6})/g, '<span class="color">$1</span>')
            .replace(/(\d+)(px|em|rem|%|vh|vw)/g, '<span class="number">$1</span><span class="unit">$2</span>')
            .replace(/\/\*[\s\S]*?\*\//g, '<span class="comment">$&</span>');
    }
    
    highlightJavaScript(js) {
        const keywords = ['var', 'let', 'const', 'function', 'if', 'else', 'for', 'while', 'return', 'class', 'extends', 'import', 'export'];
        
        let highlighted = js;
        
        // Keywords
        keywords.forEach(keyword => {
            const regex = new RegExp(`\\b${keyword}\\b`, 'g');
            highlighted = highlighted.replace(regex, `<span class="keyword">${keyword}</span>`);
        });
        
        // Strings
        highlighted = highlighted.replace(/(["'])(.*?)\1/g, '<span class="string">$1$2$1</span>');
        
        // Numbers
        highlighted = highlighted.replace(/\b\d+\.?\d*\b/g, '<span class="number">$&</span>');
        
        // Comments
        highlighted = highlighted.replace(/\/\/.*$/gm, '<span class="comment">$&</span>');
        highlighted = highlighted.replace(/\/\*[\s\S]*?\*\//g, '<span class="comment">$&</span>');
        
        return highlighted;
    }
    
    highlightJSON(json) {
        return json
            .replace(/"([^"]*)"(\s*:\s*)/g, '<span class="key">"$1"</span>$2')
            .replace(/:\s*"([^"]*)"/g, ': <span class="string">"$1"</span>')
            .replace(/:\s*(\d+\.?\d*)/g, ': <span class="number">$1</span>')
            .replace(/:\s*(true|false|null)/g, ': <span class="keyword">$1</span>');
    }
    
    highlightPython(python) {
        const keywords = ['def', 'class', 'if', 'elif', 'else', 'for', 'while', 'try', 'except', 'import', 'from', 'return', 'yield', 'lambda', 'with', 'as'];
        
        let highlighted = python;
        
        // Keywords
        keywords.forEach(keyword => {
            const regex = new RegExp(`\\b${keyword}\\b`, 'g');
            highlighted = highlighted.replace(regex, `<span class="keyword">${keyword}</span>`);
        });
        
        // Strings
        highlighted = highlighted.replace(/(["'])(.*?)\1/g, '<span class="string">$1$2$1</span>');
        
        // Comments
        highlighted = highlighted.replace(/#.*$/gm, '<span class="comment">$&</span>');
        
        return highlighted;
    }
    
    highlightPHP(php) {
        const keywords = ['<?php', '<?=', '?>', 'function', 'class', 'if', 'else', 'elseif', 'for', 'foreach', 'while', 'return', 'public', 'private', 'protected', 'static'];
        
        let highlighted = php;
        
        // Keywords
        keywords.forEach(keyword => {
            const regex = new RegExp(`\\b${keyword}\\b`, 'g');
            highlighted = highlighted.replace(regex, `<span class="keyword">${keyword}</span>`);
        });
        
        // Variables
        highlighted = highlighted.replace(/\$[a-zA-Z_][a-zA-Z0-9_]*/g, '<span class="variable">$&</span>');
        
        // Strings
        highlighted = highlighted.replace(/(["'])(.*?)\1/g, '<span class="string">$1$2$1</span>');
        
        // Comments
        highlighted = highlighted.replace(/\/\/.*$/gm, '<span class="comment">$&</span>');
        highlighted = highlighted.replace(/\/\*[\s\S]*?\*\//g, '<span class="comment">$&</span>');
        
        return highlighted;
    }
    
    syncScroll(editor) {
        const scrollTop = editor.textarea.scrollTop;
        const scrollLeft = editor.textarea.scrollLeft;
        
        editor.highlights.scrollTop = scrollTop;
        editor.highlights.scrollLeft = scrollLeft;
        editor.lineNumbers.scrollTop = scrollTop;
    }
    
    autoSave(editor) {
        const statusEl = editor.container.querySelector('.auto-save-status');
        if (statusEl) {
            statusEl.textContent = 'Saving...';
            
            // Simulate auto-save
            setTimeout(() => {
                statusEl.textContent = 'Auto-saved';
            }, 1000);
        }
    }
    
    broadcastCodeChange(editor, change) {
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
                document_type: 'code'
            })
        });
    }
    
    broadcastCursorChange(editor, cursorData) {
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
    
    applyRemoteChange(data) {
        const editor = this.editors.get(data.document_id);
        if (!editor) return;
        
        const change = data.changes;
        
        if (change.type === 'code_change' && change.user_id !== this.wsClient.userId) {
            // Apply code change
            const currentPos = editor.textarea.selectionStart;
            editor.textarea.value = change.content;
            editor.textarea.selectionStart = editor.textarea.selectionEnd = currentPos;
            
            this.updateLineNumbers(editor);
            this.applySyntaxHighlighting(editor);
        } else if (change.type === 'language_change' && change.user_id !== this.wsClient.userId) {
            // Apply language change
            this.changeLanguage(editor, change.language);
        }
        
        // Update collaboration status
        const statusEl = editor.container.querySelector('.collaboration-status');
        if (statusEl) {
            statusEl.textContent = `Updated by ${data.user_name}`;
            setTimeout(() => {
                statusEl.textContent = 'Ready';
            }, 3000);
        }
    }
    
    updateRemoteCursor(data) {
        if (data.user_id === this.wsClient.userId) return;
        
        this.editors.forEach(editor => {
            this.showRemoteCodeCursor(editor, data);
        });
    }
    
    showRemoteCodeCursor(editor, data) {
        const cursorId = `cursor_${data.user_id}`;
        let cursor = editor.cursors.querySelector(`[data-cursor-id="${cursorId}"]`);
        
        if (!cursor) {
            cursor = document.createElement('div');
            cursor.className = 'remote-code-cursor';
            cursor.dataset.cursorId = cursorId;
            cursor.innerHTML = `
                <div class="cursor-line"></div>
                <div class="cursor-label">${data.user_name}</div>
            `;
            editor.cursors.appendChild(cursor);
        }
        
        // Position cursor (simplified - would need more complex positioning)
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
window.CollaborativeCodeEditor = CollaborativeCodeEditor;