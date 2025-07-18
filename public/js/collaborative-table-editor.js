/**
 * Collaborative Table Editor
 * 
 * Real-time collaborative table editing with spreadsheet-like functionality
 */

class CollaborativeTableEditor {
    constructor(webSocketClient, container) {
        this.wsClient = webSocketClient;
        this.container = container;
        this.tables = new Map();
        this.selectedCells = new Map();
        this.columnTypes = {
            'text': 'Text',
            'number': 'Number',
            'date': 'Date',
            'boolean': 'Boolean',
            'select': 'Select',
            'formula': 'Formula'
        };
    }
    
    create(documentId, initialData = null) {
        const tableContainer = document.createElement('div');
        tableContainer.className = 'collaborative-table-editor';
        tableContainer.dataset.documentId = documentId;
        
        const rows = initialData?.rows || 10;
        const cols = initialData?.cols || 6;
        
        tableContainer.innerHTML = `
            <div class="table-toolbar">
                <div class="toolbar-section">
                    <h4>Table Actions</h4>
                    <div class="action-buttons">
                        <button class="table-btn" data-action="add-row" title="Add Row">
                            <span>+ Row</span>
                        </button>
                        <button class="table-btn" data-action="add-column" title="Add Column">
                            <span>+ Column</span>
                        </button>
                        <button class="table-btn" data-action="delete-row" title="Delete Row">
                            <span>- Row</span>
                        </button>
                        <button class="table-btn" data-action="delete-column" title="Delete Column">
                            <span>- Column</span>
                        </button>
                    </div>
                </div>
                
                <div class="toolbar-section">
                    <h4>Formatting</h4>
                    <div class="format-buttons">
                        <button class="format-btn" data-format="bold" title="Bold">
                            <strong>B</strong>
                        </button>
                        <button class="format-btn" data-format="italic" title="Italic">
                            <em>I</em>
                        </button>
                        <button class="format-btn" data-format="underline" title="Underline">
                            <u>U</u>
                        </button>
                        <select class="format-select" data-format="align">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                        <input type="color" class="format-color" data-format="color" value="#000000" title="Text Color">
                        <input type="color" class="format-color" data-format="background" value="#ffffff" title="Background Color">
                    </div>
                </div>
                
                <div class="toolbar-section">
                    <h4>Data</h4>
                    <div class="data-buttons">
                        <button class="data-btn" data-action="sort-asc" title="Sort Ascending">
                            <span>↑ Sort</span>
                        </button>
                        <button class="data-btn" data-action="sort-desc" title="Sort Descending">
                            <span>↓ Sort</span>
                        </button>
                        <button class="data-btn" data-action="filter" title="Filter">
                            <span>🔍 Filter</span>
                        </button>
                        <button class="data-btn" data-action="export" title="Export CSV">
                            <span>📤 Export</span>
                        </button>
                        <input type="file" class="data-import" accept=".csv" style="display: none;">
                        <button class="data-btn" data-action="import" title="Import CSV">
                            <span>📥 Import</span>
                        </button>
                    </div>
                </div>
                
                <div class="toolbar-section">
                    <h4>Collaboration</h4>
                    <div class="collaboration-status">
                        <span class="active-editors">1 editor</span>
                        <div class="user-selections"></div>
                    </div>
                </div>
            </div>
            
            <div class="table-container">
                <div class="table-header">
                    <div class="formula-bar">
                        <span class="cell-reference">A1</span>
                        <input type="text" class="formula-input" placeholder="Enter formula or value...">
                        <button class="formula-btn">✓</button>
                    </div>
                </div>
                
                <div class="table-body">
                    <table class="editable-table" data-document-id="${documentId}">
                        <thead>
                            <tr>
                                <th class="row-header"></th>
                                ${this.generateColumnHeaders(cols)}
                            </tr>
                        </thead>
                        <tbody>
                            ${this.generateTableRows(rows, cols)}
                        </tbody>
                    </table>
                </div>
                
                <div class="table-footer">
                    <div class="table-info">
                        <span class="table-size">${rows} rows × ${cols} columns</span>
                        <span class="selection-info">No selection</span>
                    </div>
                    
                    <div class="table-status">
                        <span class="auto-save">Auto-saved</span>
                        <span class="collaboration-indicator">Ready for collaboration</span>
                    </div>
                </div>
            </div>
        `;
        
        this.container.appendChild(tableContainer);
        
        const table = {
            container: tableContainer,
            table: tableContainer.querySelector('.editable-table'),
            formulaInput: tableContainer.querySelector('.formula-input'),
            cellReference: tableContainer.querySelector('.cell-reference'),
            documentId: documentId,
            rows: rows,
            cols: cols,
            data: this.initializeData(rows, cols),
            selectedCell: null,
            selectedRange: null,
            editingCell: null,
            remoteSelections: new Map(),
            formulas: new Map(),
            columnTypes: new Map()
        };
        
        this.tables.set(documentId, table);
        this.setupTableEvents(table);
        
        // Load initial data if provided
        if (initialData && initialData.data) {
            this.loadTableData(table, initialData.data);
        }
        
        return table;
    }
    
    generateColumnHeaders(cols) {
        let headers = '';
        for (let i = 0; i < cols; i++) {
            const letter = String.fromCharCode(65 + i);
            headers += `<th class="column-header" data-column="${i}">${letter}</th>`;
        }
        return headers;
    }
    
    generateTableRows(rows, cols) {
        let tableRows = '';
        for (let i = 0; i < rows; i++) {
            tableRows += `<tr>`;
            tableRows += `<td class="row-header" data-row="${i}">${i + 1}</td>`;
            for (let j = 0; j < cols; j++) {
                const cellId = this.getCellId(i, j);
                tableRows += `<td class="table-cell" data-row="${i}" data-col="${j}" data-cell-id="${cellId}" contenteditable="true"></td>`;
            }
            tableRows += `</tr>`;
        }
        return tableRows;
    }
    
    getCellId(row, col) {
        const letter = String.fromCharCode(65 + col);
        return `${letter}${row + 1}`;
    }
    
    initializeData(rows, cols) {
        const data = [];
        for (let i = 0; i < rows; i++) {
            data[i] = [];
            for (let j = 0; j < cols; j++) {
                data[i][j] = '';
            }
        }
        return data;
    }
    
    setupTableEvents(table) {
        // Cell selection and editing
        table.table.addEventListener('click', (e) => {
            if (e.target.matches('.table-cell')) {
                this.selectCell(table, e.target);
            } else if (e.target.matches('.column-header')) {
                this.selectColumn(table, e.target);
            } else if (e.target.matches('.row-header')) {
                this.selectRow(table, e.target);
            }
        });
        
        // Cell editing
        table.table.addEventListener('dblclick', (e) => {
            if (e.target.matches('.table-cell')) {
                this.startCellEdit(table, e.target);
            }
        });
        
        // Cell content changes
        table.table.addEventListener('input', (e) => {
            if (e.target.matches('.table-cell')) {
                this.handleCellChange(table, e.target);
            }
        });
        
        // Formula bar
        table.formulaInput.addEventListener('input', (e) => {
            this.updateFormulaBar(table, e.target.value);
        });
        
        table.formulaInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.applyFormulaValue(table);
            }
        });
        
        table.container.querySelector('.formula-btn').addEventListener('click', () => {
            this.applyFormulaValue(table);
        });
        
        // Toolbar actions
        table.container.addEventListener('click', (e) => {
            if (e.target.matches('.table-btn')) {
                this.executeTableAction(table, e.target.dataset.action);
            } else if (e.target.matches('.format-btn')) {
                this.applyFormatting(table, e.target.dataset.format);
            } else if (e.target.matches('.data-btn')) {
                this.executeDataAction(table, e.target.dataset.action);
            }
        });
        
        // Formatting changes
        table.container.addEventListener('change', (e) => {
            if (e.target.matches('.format-select') || e.target.matches('.format-color')) {
                this.applyFormatting(table, e.target.dataset.format, e.target.value);
            }
        });
        
        // Keyboard shortcuts
        table.container.addEventListener('keydown', (e) => {
            this.handleKeyboard(table, e);
        });
        
        // Range selection
        table.table.addEventListener('mousedown', (e) => {
            if (e.target.matches('.table-cell')) {
                this.startRangeSelection(table, e);
            }
        });
    }
    
    selectCell(table, cell) {
        // Clear previous selection
        this.clearSelection(table);
        
        // Select new cell
        cell.classList.add('selected');
        table.selectedCell = cell;
        
        const row = parseInt(cell.dataset.row);
        const col = parseInt(cell.dataset.col);
        const cellId = this.getCellId(row, col);
        
        // Update cell reference
        table.cellReference.textContent = cellId;
        
        // Update formula bar
        table.formulaInput.value = table.data[row][col] || '';
        
        // Update selection info
        this.updateSelectionInfo(table, `Cell ${cellId}`);
        
        // Broadcast selection
        this.broadcastSelection(table, { type: 'cell', row: row, col: col });
    }
    
    selectColumn(table, header) {
        this.clearSelection(table);
        
        const col = parseInt(header.dataset.column);
        const cells = table.table.querySelectorAll(`[data-col="${col}"]`);
        
        cells.forEach(cell => {
            if (cell.classList.contains('table-cell')) {
                cell.classList.add('selected');
            }
        });
        
        header.classList.add('selected');
        table.selectedRange = { type: 'column', col: col };
        
        // Update selection info
        const letter = String.fromCharCode(65 + col);
        this.updateSelectionInfo(table, `Column ${letter}`);
        
        // Broadcast selection
        this.broadcastSelection(table, { type: 'column', col: col });
    }
    
    selectRow(table, header) {
        this.clearSelection(table);
        
        const row = parseInt(header.dataset.row);
        const cells = table.table.querySelectorAll(`[data-row="${row}"]`);
        
        cells.forEach(cell => {
            if (cell.classList.contains('table-cell')) {
                cell.classList.add('selected');
            }
        });
        
        header.classList.add('selected');
        table.selectedRange = { type: 'row', row: row };
        
        // Update selection info
        this.updateSelectionInfo(table, `Row ${row + 1}`);
        
        // Broadcast selection
        this.broadcastSelection(table, { type: 'row', row: row });
    }
    
    clearSelection(table) {
        table.table.querySelectorAll('.selected').forEach(cell => {
            cell.classList.remove('selected');
        });
        table.selectedCell = null;
        table.selectedRange = null;
    }
    
    startCellEdit(table, cell) {
        table.editingCell = cell;
        cell.focus();
        
        // Select all text
        const range = document.createRange();
        range.selectNodeContents(cell);
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);
        
        // Broadcast edit start
        const row = parseInt(cell.dataset.row);
        const col = parseInt(cell.dataset.col);
        this.broadcastEdit(table, { type: 'start', row: row, col: col });
    }
    
    handleCellChange(table, cell) {
        const row = parseInt(cell.dataset.row);
        const col = parseInt(cell.dataset.col);
        const value = cell.textContent || '';
        
        // Update data
        table.data[row][col] = value;
        
        // Update formula bar if this is the selected cell
        if (table.selectedCell === cell) {
            table.formulaInput.value = value;
        }
        
        // Process formulas
        if (value.startsWith('=')) {
            this.processFormula(table, cell, value);
        }
        
        // Auto-save
        this.autoSave(table);
        
        // Broadcast change
        this.broadcastCellChange(table, { row: row, col: col, value: value });
    }
    
    processFormula(table, cell, formula) {
        const row = parseInt(cell.dataset.row);
        const col = parseInt(cell.dataset.col);
        
        try {
            const result = this.evaluateFormula(table, formula);
            cell.textContent = result;
            table.data[row][col] = result;
            
            // Store formula for reference
            table.formulas.set(`${row},${col}`, formula);
            
            // Mark as formula cell
            cell.classList.add('formula-cell');
            cell.title = `Formula: ${formula}`;
        } catch (error) {
            cell.textContent = '#ERROR';
            cell.classList.add('error-cell');
            cell.title = `Error: ${error.message}`;
        }
    }
    
    evaluateFormula(table, formula) {
        // Remove the = sign
        const expression = formula.substring(1);
        
        // Replace cell references with values
        const processedExpression = expression.replace(/([A-Z]+)(\d+)/g, (match, col, row) => {
            const colIndex = col.charCodeAt(0) - 65;
            const rowIndex = parseInt(row) - 1;
            
            if (rowIndex >= 0 && rowIndex < table.rows && colIndex >= 0 && colIndex < table.cols) {
                const value = table.data[rowIndex][colIndex];
                return isNaN(value) ? `"${value}"` : parseFloat(value) || 0;
            }
            return 0;
        });
        
        // Basic formula functions
        const functions = {
            SUM: (range) => {
                // Simple implementation for SUM
                return range.reduce((sum, val) => sum + (parseFloat(val) || 0), 0);
            },
            AVERAGE: (range) => {
                const sum = range.reduce((sum, val) => sum + (parseFloat(val) || 0), 0);
                return sum / range.length;
            },
            COUNT: (range) => {
                return range.filter(val => val !== '').length;
            },
            MAX: (range) => {
                return Math.max(...range.map(val => parseFloat(val) || 0));
            },
            MIN: (range) => {
                return Math.min(...range.map(val => parseFloat(val) || 0));
            }
        };
        
        // Process functions
        let result = processedExpression;
        for (const [func, handler] of Object.entries(functions)) {
            const regex = new RegExp(`${func}\\(([^)]+)\\)`, 'gi');
            result = result.replace(regex, (match, args) => {
                // Simple range processing - would need more sophisticated parsing
                const range = []; // Extract range values
                return handler(range);
            });
        }
        
        // Evaluate the expression (simplified - in production use a safe evaluator)
        try {
            return eval(result);
        } catch (error) {
            throw new Error('Invalid formula');
        }
    }
    
    updateFormulaBar(table, value) {
        if (table.selectedCell) {
            const row = parseInt(table.selectedCell.dataset.row);
            const col = parseInt(table.selectedCell.dataset.col);
            table.data[row][col] = value;
        }
    }
    
    applyFormulaValue(table) {
        if (table.selectedCell) {
            const value = table.formulaInput.value;
            table.selectedCell.textContent = value;
            
            // Trigger change event
            const event = new Event('input', { bubbles: true });
            table.selectedCell.dispatchEvent(event);
        }
    }
    
    executeTableAction(table, action) {
        switch (action) {
            case 'add-row':
                this.addRow(table);
                break;
            case 'add-column':
                this.addColumn(table);
                break;
            case 'delete-row':
                this.deleteRow(table);
                break;
            case 'delete-column':
                this.deleteColumn(table);
                break;
        }
    }
    
    addRow(table) {
        const tbody = table.table.querySelector('tbody');
        const newRow = document.createElement('tr');
        const rowIndex = table.rows;
        
        newRow.innerHTML = `
            <td class="row-header" data-row="${rowIndex}">${rowIndex + 1}</td>
            ${Array(table.cols).fill().map((_, colIndex) => {
                const cellId = this.getCellId(rowIndex, colIndex);
                return `<td class="table-cell" data-row="${rowIndex}" data-col="${colIndex}" data-cell-id="${cellId}" contenteditable="true"></td>`;
            }).join('')}
        `;
        
        tbody.appendChild(newRow);
        
        // Update data
        table.data.push(Array(table.cols).fill(''));
        table.rows++;
        
        // Update info
        this.updateTableInfo(table);
        
        // Broadcast change
        this.broadcastStructureChange(table, { type: 'add-row', row: rowIndex });
    }
    
    addColumn(table) {
        const colIndex = table.cols;
        const letter = String.fromCharCode(65 + colIndex);
        
        // Add header
        const headerRow = table.table.querySelector('thead tr');
        const newHeader = document.createElement('th');
        newHeader.className = 'column-header';
        newHeader.dataset.column = colIndex;
        newHeader.textContent = letter;
        headerRow.appendChild(newHeader);
        
        // Add cells to each row
        const rows = table.table.querySelectorAll('tbody tr');
        rows.forEach((row, rowIndex) => {
            const newCell = document.createElement('td');
            newCell.className = 'table-cell';
            newCell.dataset.row = rowIndex;
            newCell.dataset.col = colIndex;
            newCell.dataset.cellId = this.getCellId(rowIndex, colIndex);
            newCell.contentEditable = true;
            row.appendChild(newCell);
            
            // Update data
            if (!table.data[rowIndex]) table.data[rowIndex] = [];
            table.data[rowIndex][colIndex] = '';
        });
        
        table.cols++;
        
        // Update info
        this.updateTableInfo(table);
        
        // Broadcast change
        this.broadcastStructureChange(table, { type: 'add-column', col: colIndex });
    }
    
    deleteRow(table) {
        if (table.selectedRange && table.selectedRange.type === 'row') {
            const rowIndex = table.selectedRange.row;
            
            // Remove row from DOM
            const row = table.table.querySelector(`tbody tr:nth-child(${rowIndex + 1})`);
            if (row) {
                row.remove();
                
                // Update data
                table.data.splice(rowIndex, 1);
                table.rows--;
                
                // Update row numbers
                this.updateRowNumbers(table);
                
                // Update info
                this.updateTableInfo(table);
                
                // Broadcast change
                this.broadcastStructureChange(table, { type: 'delete-row', row: rowIndex });
            }
        }
    }
    
    deleteColumn(table) {
        if (table.selectedRange && table.selectedRange.type === 'column') {
            const colIndex = table.selectedRange.col;
            
            // Remove header
            const header = table.table.querySelector(`thead th:nth-child(${colIndex + 2})`);
            if (header) header.remove();
            
            // Remove cells from each row
            const rows = table.table.querySelectorAll('tbody tr');
            rows.forEach((row, rowIndex) => {
                const cell = row.querySelector(`td:nth-child(${colIndex + 2})`);
                if (cell) cell.remove();
                
                // Update data
                if (table.data[rowIndex]) {
                    table.data[rowIndex].splice(colIndex, 1);
                }
            });
            
            table.cols--;
            
            // Update column headers
            this.updateColumnHeaders(table);
            
            // Update info
            this.updateTableInfo(table);
            
            // Broadcast change
            this.broadcastStructureChange(table, { type: 'delete-column', col: colIndex });
        }
    }
    
    updateRowNumbers(table) {
        const rowHeaders = table.table.querySelectorAll('.row-header');
        rowHeaders.forEach((header, index) => {
            header.textContent = index + 1;
            header.dataset.row = index;
        });
        
        // Update cell row attributes
        const cells = table.table.querySelectorAll('.table-cell');
        cells.forEach(cell => {
            const row = cell.closest('tr');
            const rowIndex = Array.from(row.parentElement.children).indexOf(row);
            cell.dataset.row = rowIndex;
        });
    }
    
    updateColumnHeaders(table) {
        const columnHeaders = table.table.querySelectorAll('.column-header');
        columnHeaders.forEach((header, index) => {
            const letter = String.fromCharCode(65 + index);
            header.textContent = letter;
            header.dataset.column = index;
        });
        
        // Update cell column attributes
        const rows = table.table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('.table-cell');
            cells.forEach((cell, colIndex) => {
                cell.dataset.col = colIndex;
                cell.dataset.cellId = this.getCellId(parseInt(cell.dataset.row), colIndex);
            });
        });
    }
    
    applyFormatting(table, format, value) {
        if (!table.selectedCell && !table.selectedRange) return;
        
        const cells = this.getSelectedCells(table);
        
        cells.forEach(cell => {
            switch (format) {
                case 'bold':
                    cell.style.fontWeight = cell.style.fontWeight === 'bold' ? 'normal' : 'bold';
                    break;
                case 'italic':
                    cell.style.fontStyle = cell.style.fontStyle === 'italic' ? 'normal' : 'italic';
                    break;
                case 'underline':
                    cell.style.textDecoration = cell.style.textDecoration === 'underline' ? 'none' : 'underline';
                    break;
                case 'align':
                    cell.style.textAlign = value;
                    break;
                case 'color':
                    cell.style.color = value;
                    break;
                case 'background':
                    cell.style.backgroundColor = value;
                    break;
            }
        });
        
        // Broadcast formatting change
        this.broadcastFormatting(table, { format: format, value: value });
    }
    
    getSelectedCells(table) {
        if (table.selectedCell) {
            return [table.selectedCell];
        } else if (table.selectedRange) {
            return table.table.querySelectorAll('.selected.table-cell');
        }
        return [];
    }
    
    executeDataAction(table, action) {
        switch (action) {
            case 'sort-asc':
                this.sortTable(table, 'asc');
                break;
            case 'sort-desc':
                this.sortTable(table, 'desc');
                break;
            case 'filter':
                this.showFilterDialog(table);
                break;
            case 'export':
                this.exportToCSV(table);
                break;
            case 'import':
                this.importFromCSV(table);
                break;
        }
    }
    
    sortTable(table, order) {
        if (!table.selectedRange || table.selectedRange.type !== 'column') return;
        
        const colIndex = table.selectedRange.col;
        const data = table.data;
        
        // Sort data
        const sortedData = [...data].sort((a, b) => {
            const aVal = a[colIndex] || '';
            const bVal = b[colIndex] || '';
            
            if (order === 'asc') {
                return aVal.localeCompare(bVal);
            } else {
                return bVal.localeCompare(aVal);
            }
        });
        
        // Update table
        this.updateTableWithData(table, sortedData);
        
        // Broadcast sort
        this.broadcastDataOperation(table, { type: 'sort', column: colIndex, order: order });
    }
    
    exportToCSV(table) {
        const data = table.data;
        const csv = data.map(row => 
            row.map(cell => `"${cell.replace(/"/g, '""')}"`).join(',')
        ).join('\n');
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `table_${table.documentId}_${Date.now()}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    }
    
    updateTableInfo(table) {
        const infoEl = table.container.querySelector('.table-size');
        if (infoEl) {
            infoEl.textContent = `${table.rows} rows × ${table.cols} columns`;
        }
    }
    
    updateSelectionInfo(table, info) {
        const selectionEl = table.container.querySelector('.selection-info');
        if (selectionEl) {
            selectionEl.textContent = info;
        }
    }
    
    autoSave(table) {
        const statusEl = table.container.querySelector('.auto-save');
        if (statusEl) {
            statusEl.textContent = 'Saving...';
            setTimeout(() => {
                statusEl.textContent = 'Auto-saved';
            }, 1000);
        }
    }
    
    handleKeyboard(table, event) {
        // Handle keyboard shortcuts
        if (event.ctrlKey || event.metaKey) {
            switch (event.key) {
                case 'c':
                    event.preventDefault();
                    this.copySelection(table);
                    break;
                case 'v':
                    event.preventDefault();
                    this.pasteSelection(table);
                    break;
                case 'z':
                    event.preventDefault();
                    this.undo(table);
                    break;
            }
        }
    }
    
    broadcastSelection(table, selection) {
        const change = {
            type: 'selection',
            selection: selection,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastTableChange(table, change);
    }
    
    broadcastEdit(table, editData) {
        const change = {
            type: 'edit',
            edit: editData,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastTableChange(table, change);
    }
    
    broadcastCellChange(table, cellData) {
        const change = {
            type: 'cell_change',
            cell: cellData,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastTableChange(table, change);
    }
    
    broadcastStructureChange(table, structureData) {
        const change = {
            type: 'structure_change',
            structure: structureData,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastTableChange(table, change);
    }
    
    broadcastFormatting(table, formatData) {
        const change = {
            type: 'formatting',
            format: formatData,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastTableChange(table, change);
    }
    
    broadcastDataOperation(table, operationData) {
        const change = {
            type: 'data_operation',
            operation: operationData,
            timestamp: Date.now(),
            user_id: this.wsClient.userId
        };
        
        this.broadcastTableChange(table, change);
    }
    
    broadcastTableChange(table, change) {
        // Use existing WebSocket controller
        fetch('/api/websocket/update-document', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.wsClient.authToken}`
            },
            body: JSON.stringify({
                workspace_id: this.wsClient.workspaceId,
                document_id: table.documentId,
                changes: change,
                document_type: 'table'
            })
        });
    }
    
    applyRemoteChange(data) {
        const table = this.tables.get(data.document_id);
        if (!table) return;
        
        const change = data.changes;
        
        if (change.user_id === this.wsClient.userId) return;
        
        switch (change.type) {
            case 'cell_change':
                this.applyRemoteCellChange(table, change.cell);
                break;
            case 'selection':
                this.showRemoteSelection(table, change.selection, data.user_name);
                break;
            case 'structure_change':
                this.applyRemoteStructureChange(table, change.structure);
                break;
            case 'formatting':
                this.applyRemoteFormatting(table, change.format);
                break;
        }
        
        // Update collaboration status
        const statusEl = table.container.querySelector('.collaboration-indicator');
        if (statusEl) {
            statusEl.textContent = `Updated by ${data.user_name}`;
            setTimeout(() => {
                statusEl.textContent = 'Ready for collaboration';
            }, 3000);
        }
    }
    
    applyRemoteCellChange(table, cellData) {
        const cell = table.table.querySelector(`[data-row="${cellData.row}"][data-col="${cellData.col}"]`);
        if (cell) {
            cell.textContent = cellData.value;
            table.data[cellData.row][cellData.col] = cellData.value;
        }
    }
    
    showRemoteSelection(table, selection, userName) {
        // Show remote user selection
        const selectionEl = table.container.querySelector('.user-selections');
        
        let userSelection = selectionEl.querySelector(`[data-user-id="${selection.user_id}"]`);
        if (!userSelection) {
            userSelection = document.createElement('div');
            userSelection.className = 'user-selection';
            userSelection.dataset.userId = selection.user_id;
            selectionEl.appendChild(userSelection);
        }
        
        userSelection.textContent = `${userName}: ${selection.type}`;
    }
    
    loadTableData(table, data) {
        table.data = data;
        
        // Update table cells
        data.forEach((row, rowIndex) => {
            row.forEach((cellValue, colIndex) => {
                const cell = table.table.querySelector(`[data-row="${rowIndex}"][data-col="${colIndex}"]`);
                if (cell) {
                    cell.textContent = cellValue;
                }
            });
        });
    }
    
    updateTableWithData(table, data) {
        table.data = data;
        this.loadTableData(table, data);
    }
    
    destroy(documentId) {
        const table = this.tables.get(documentId);
        if (table) {
            table.container.remove();
            this.tables.delete(documentId);
        }
    }
}

// Export for global use
window.CollaborativeTableEditor = CollaborativeTableEditor;