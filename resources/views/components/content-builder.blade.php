@props([
    'type' => 'page',
    'title' => 'Content Builder',
    'allowedBlocks' => ['text', 'image', 'video', 'button', 'gallery', 'testimonial', 'faq']
])

<div class="content-builder">
    <div class="content-builder-header mb-6">
        <h3 class="text-xl font-semibold text-primary-text mb-2">{{ $title }}</h3>
        <p class="text-secondary-text">Drag and drop blocks to build your content</p>
    </div>

    <div class="content-builder-container grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Block Palette -->
        <div class="content-blocks-palette">
            <h4 class="text-lg font-semibold text-primary-text mb-4">Content Blocks</h4>
            <div class="space-y-3">
                @if(in_array('text', $allowedBlocks))
                <div class="block-item" data-block-type="text">
                    <div class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:bg-hover-bg cursor-pointer">
                        <div class="w-10 h-10 bg-info/10 rounded-lg flex items-center justify-center mr-3">
                            <x-icon name="edit" size="sm" class="text-info" />
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">Text Block</div>
                            <div class="text-sm text-secondary-text">Add text content</div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('image', $allowedBlocks))
                <div class="block-item" data-block-type="image">
                    <div class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:bg-hover-bg cursor-pointer">
                        <div class="w-10 h-10 bg-success/10 rounded-lg flex items-center justify-center mr-3">
                            <x-icon name="image" size="sm" class="text-success" alt="Image block" />
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">Image</div>
                            <div class="text-sm text-secondary-text">Add images</div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('video', $allowedBlocks))
                <div class="block-item" data-block-type="video">
                    <div class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:bg-hover-bg cursor-pointer">
                        <div class="w-10 h-10 bg-warning/10 rounded-lg flex items-center justify-center mr-3">
                            <x-icon name="video" size="sm" class="text-warning" alt="Video block" />
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">Video</div>
                            <div class="text-sm text-secondary-text">Embed videos</div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('button', $allowedBlocks))
                <div class="block-item" data-block-type="button">
                    <div class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:bg-hover-bg cursor-pointer">
                        <div class="w-10 h-10 bg-error/10 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">Button</div>
                            <div class="text-sm text-secondary-text">Call-to-action</div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('gallery', $allowedBlocks))
                <div class="block-item" data-block-type="gallery">
                    <div class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:bg-hover-bg cursor-pointer">
                        <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">Gallery</div>
                            <div class="text-sm text-secondary-text">Image gallery</div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('testimonial', $allowedBlocks))
                <div class="block-item" data-block-type="testimonial">
                    <div class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:bg-hover-bg cursor-pointer">
                        <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center mr-3">
                            <x-icon name="chat" size="sm" class="text-green-500" alt="Testimonial block" />
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">Testimonial</div>
                            <div class="text-sm text-secondary-text">Customer reviews</div>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array('faq', $allowedBlocks))
                <div class="block-item" data-block-type="faq">
                    <div class="flex items-center p-3 bg-card-bg rounded-lg border border-border-color hover:bg-hover-bg cursor-pointer">
                        <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-primary-text">FAQ</div>
                            <div class="text-sm text-secondary-text">Q&A section</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Content Canvas -->
        <div class="lg:col-span-3">
            <div class="content-canvas">
                <div class="content-canvas-header mb-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-semibold text-primary-text">Content Canvas</h4>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-secondary" id="preview-btn">
                                <x-icon name="eye" size="sm" class="mr-2" />
                                Preview
                            </button>
                            <button class="btn btn-sm btn-primary" id="save-btn">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save
                            </button>
                        </div>
                    </div>
                </div>

                <div class="content-canvas-area min-h-96 bg-card-bg border-2 border-dashed border-border-color rounded-lg p-6" id="canvas-area">
                    <div class="canvas-placeholder text-center text-secondary-text">
                        <svg class="w-16 h-16 mx-auto mb-4 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <p>Drop content blocks here to start building</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Block Editor Modal -->
    <div class="block-editor-modal hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-card-bg rounded-lg p-6 max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-primary-text">Edit Block</h3>
                <button class="text-secondary-text hover:text-primary-text" id="close-modal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="block-editor-content" id="block-editor-content">
                <!-- Dynamic content based on block type -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentBuilder = document.querySelector('.content-builder');
    const canvasArea = document.getElementById('canvas-area');
    const blockItems = contentBuilder.querySelectorAll('.block-item');
    const modal = contentBuilder.querySelector('.block-editor-modal');
    const modalContent = document.getElementById('block-editor-content');
    let currentBlock = null;
    let blockCounter = 0;

    // Make blocks draggable
    blockItems.forEach(item => {
        item.draggable = true;
        item.addEventListener('dragstart', handleDragStart);
    });

    // Make canvas droppable
    canvasArea.addEventListener('dragover', handleDragOver);
    canvasArea.addEventListener('drop', handleDrop);

    function handleDragStart(e) {
        e.dataTransfer.setData('text/plain', e.target.dataset.blockType);
    }

    function handleDragOver(e) {
        e.preventDefault();
        canvasArea.classList.add('border-info');
    }

    function handleDrop(e) {
        e.preventDefault();
        canvasArea.classList.remove('border-info');
        
        const blockType = e.dataTransfer.getData('text/plain');
        addBlock(blockType);
    }

    function addBlock(blockType) {
        blockCounter++;
        const blockId = `block-${blockCounter}`;
        
        // Remove placeholder if it exists
        const placeholder = canvasArea.querySelector('.canvas-placeholder');
        if (placeholder) {
            placeholder.remove();
        }

        const blockElement = createBlockElement(blockType, blockId);
        canvasArea.appendChild(blockElement);
    }

    function createBlockElement(blockType, blockId) {
        const blockDiv = document.createElement('div');
        blockDiv.className = 'content-block mb-4 border border-border-color rounded-lg p-4 bg-app-bg';
        blockDiv.dataset.blockType = blockType;
        blockDiv.dataset.blockId = blockId;

        let blockContent = '';
        switch(blockType) {
            case 'text':
                blockContent = `
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-primary-text">Text Block</h4>
                        <div class="flex gap-2">
                            <button class="edit-block text-info hover:text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button class="delete-block text-error hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <p class="text-secondary-text">Click edit to add your text content...</p>
                    </div>
                `;
                break;
            case 'image':
                blockContent = `
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-primary-text">Image Block</h4>
                        <div class="flex gap-2">
                            <button class="edit-block text-info hover:text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button class="delete-block text-error hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="w-full h-32 bg-secondary-bg rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                `;
                break;
            case 'button':
                blockContent = `
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-primary-text">Button Block</h4>
                        <div class="flex gap-2">
                            <button class="edit-block text-info hover:text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button class="delete-block text-error hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <button class="btn btn-primary">Click Me</button>
                    </div>
                `;
                break;
            default:
                blockContent = `
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-primary-text">${blockType.charAt(0).toUpperCase() + blockType.slice(1)} Block</h4>
                        <div class="flex gap-2">
                            <button class="edit-block text-info hover:text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button class="delete-block text-error hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <p class="text-secondary-text">Configure this ${blockType} block...</p>
                    </div>
                `;
        }

        blockDiv.innerHTML = blockContent;

        // Add event listeners for edit and delete
        blockDiv.querySelector('.edit-block').addEventListener('click', () => editBlock(blockDiv));
        blockDiv.querySelector('.delete-block').addEventListener('click', () => deleteBlock(blockDiv));

        return blockDiv;
    }

    function editBlock(blockElement) {
        currentBlock = blockElement;
        const blockType = blockElement.dataset.blockType;
        
        // Show modal with appropriate editor
        showBlockEditor(blockType);
    }

    function deleteBlock(blockElement) {
        if (confirm('Are you sure you want to delete this block?')) {
            blockElement.remove();
            
            // Show placeholder if no blocks left
            if (canvasArea.children.length === 0) {
                canvasArea.innerHTML = `
                    <div class="canvas-placeholder text-center text-secondary-text">
                        <svg class="w-16 h-16 mx-auto mb-4 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <p>Drop content blocks here to start building</p>
                    </div>
                `;
            }
        }
    }

    function showBlockEditor(blockType) {
        let editorContent = '';
        
        switch(blockType) {
            case 'text':
                editorContent = `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Text Content</label>
                            <textarea class="form-input w-full" rows="4" placeholder="Enter your text content..."></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-primary-text mb-2">Text Size</label>
                                <select class="form-input w-full">
                                    <option value="sm">Small</option>
                                    <option value="md" selected>Medium</option>
                                    <option value="lg">Large</option>
                                    <option value="xl">Extra Large</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary-text mb-2">Text Color</label>
                                <input type="color" class="form-input w-full" value="#F1F1F1">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                            <button class="btn btn-primary" onclick="saveBlock()">Save</button>
                        </div>
                    </div>
                `;
                break;
            case 'image':
                editorContent = `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Image URL</label>
                            <input type="url" class="form-input w-full" placeholder="https://example.com/image.jpg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Alt Text</label>
                            <input type="text" class="form-input w-full" placeholder="Describe the image">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-primary-text mb-2">Width</label>
                                <select class="form-input w-full">
                                    <option value="auto">Auto</option>
                                    <option value="full" selected>Full Width</option>
                                    <option value="half">Half Width</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary-text mb-2">Alignment</label>
                                <select class="form-input w-full">
                                    <option value="left">Left</option>
                                    <option value="center" selected>Center</option>
                                    <option value="right">Right</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                            <button class="btn btn-primary" onclick="saveBlock()">Save</button>
                        </div>
                    </div>
                `;
                break;
            case 'button':
                editorContent = `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Button Text</label>
                            <input type="text" class="form-input w-full" placeholder="Click Me" value="Click Me">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Button URL</label>
                            <input type="url" class="form-input w-full" placeholder="https://example.com">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-primary-text mb-2">Button Style</label>
                                <select class="form-input w-full">
                                    <option value="primary" selected>Primary</option>
                                    <option value="secondary">Secondary</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary-text mb-2">Button Size</label>
                                <select class="form-input w-full">
                                    <option value="sm">Small</option>
                                    <option value="md" selected>Medium</option>
                                    <option value="lg">Large</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                            <button class="btn btn-primary" onclick="saveBlock()">Save</button>
                        </div>
                    </div>
                `;
                break;
            default:
                editorContent = `
                    <div class="space-y-4">
                        <p class="text-secondary-text">Editor for ${blockType} block coming soon...</p>
                        <div class="flex justify-end gap-3">
                            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                            <button class="btn btn-primary" onclick="saveBlock()">Save</button>
                        </div>
                    </div>
                `;
        }
        
        modalContent.innerHTML = editorContent;
        modal.classList.remove('hidden');
    }

    // Global functions for modal
    window.closeModal = function() {
        modal.classList.add('hidden');
        currentBlock = null;
    };

    window.saveBlock = function() {
        // Save logic here
        console.log('Saving block...');
        closeModal();
    };

    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close modal button
    document.getElementById('close-modal').addEventListener('click', closeModal);

    // Save and preview buttons
    document.getElementById('save-btn').addEventListener('click', function() {
        // Save content logic
        console.log('Saving content...');
        alert('Content saved successfully!');
    });

    document.getElementById('preview-btn').addEventListener('click', function() {
        // Preview logic
        console.log('Previewing content...');
        alert('Preview functionality coming soon!');
    });
});
</script>

<style>
.content-builder {
    max-width: 1200px;
    margin: 0 auto;
}

.content-blocks-palette {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1.5rem;
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.content-canvas-area {
    min-height: 600px;
    transition: border-color 0.3s ease;
}

.content-canvas-area.border-info {
    border-color: var(--info) !important;
}

.content-block {
    position: relative;
    transition: all 0.3s ease;
}

.content-block:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.block-item {
    transition: all 0.3s ease;
}

.block-item:hover {
    transform: translateY(-2px);
}

.block-editor-modal {
    z-index: 1000;
}

.canvas-placeholder {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>