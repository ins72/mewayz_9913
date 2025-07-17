@props([
    'type' => 'bio',
    'title' => 'Create Widget',
    'description' => 'Build your custom widget'
])

<div class="widget-creator card">
    <div class="widget-creator-header mb-6">
        <h3 class="text-xl font-semibold text-primary-text mb-2">{{ $title }}</h3>
        <p class="text-secondary-text">{{ $description }}</p>
    </div>

    <div class="widget-creator-content">
        @if($type === 'bio')
            <!-- Bio Widget Creator -->
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Widget Type</label>
                        <select class="form-input w-full" name="widget_type">
                            <option value="link">Link Button</option>
                            <option value="text">Text Block</option>
                            <option value="image">Image</option>
                            <option value="social">Social Links</option>
                            <option value="contact">Contact Info</option>
                            <option value="embed">Embed Code</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Widget Title</label>
                        <input type="text" class="form-input w-full" name="widget_title" placeholder="Enter widget title">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Content</label>
                    <textarea class="form-input w-full" name="widget_content" rows="4" placeholder="Enter widget content or URL"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Background Color</label>
                        <input type="color" class="form-input w-full" name="bg_color" value="#3B82F6">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Text Color</label>
                        <input type="color" class="form-input w-full" name="text_color" value="#FFFFFF">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Border Radius</label>
                        <select class="form-input w-full" name="border_radius">
                            <option value="none">None</option>
                            <option value="sm">Small</option>
                            <option value="md" selected>Medium</option>
                            <option value="lg">Large</option>
                            <option value="full">Full</option>
                        </select>
                    </div>
                </div>

                <div class="widget-preview border border-border-color rounded-lg p-6 bg-card-bg">
                    <h4 class="text-lg font-semibold text-primary-text mb-4">Preview</h4>
                    <div class="preview-content">
                        <div class="bg-info text-white px-4 py-2 rounded-lg text-center">
                            <span class="font-medium">Sample Widget</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button class="btn btn-secondary">Cancel</button>
                    <button class="btn btn-primary">
                        <x-icon name="plus" size="sm" class="mr-2" />
                        Add Widget
                    </button>
                </div>
            </div>

        @elseif($type === 'dashboard')
            <!-- Dashboard Widget Creator -->
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Widget Type</label>
                        <select class="form-input w-full" name="widget_type">
                            <option value="stats">Statistics Card</option>
                            <option value="chart">Chart Widget</option>
                            <option value="activity">Activity Feed</option>
                            <option value="quick-actions">Quick Actions</option>
                            <option value="custom">Custom Widget</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Widget Size</label>
                        <select class="form-input w-full" name="widget_size">
                            <option value="small">Small (1x1)</option>
                            <option value="medium" selected>Medium (2x1)</option>
                            <option value="large">Large (2x2)</option>
                            <option value="full">Full Width</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Widget Title</label>
                    <input type="text" class="form-input w-full" name="widget_title" placeholder="Enter widget title">
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Data Source</label>
                    <select class="form-input w-full" name="data_source">
                        <option value="analytics">Analytics Data</option>
                        <option value="sales">Sales Data</option>
                        <option value="users">User Data</option>
                        <option value="traffic">Traffic Data</option>
                        <option value="custom">Custom API</option>
                    </select>
                </div>

                <div class="widget-preview border border-border-color rounded-lg p-6 bg-card-bg">
                    <h4 class="text-lg font-semibold text-primary-text mb-4">Preview</h4>
                    <div class="preview-content">
                        <div class="card">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-medium text-secondary-text">Sample Widget</h3>
                                <x-icon name="chart" size="sm" class="text-info" />
                            </div>
                            <div class="text-2xl font-bold text-primary-text">1,234</div>
                            <div class="text-sm text-success">+12% from last month</div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button class="btn btn-secondary">Cancel</button>
                    <button class="btn btn-primary">
                        <x-icon name="plus" size="sm" class="mr-2" />
                        Add Widget
                    </button>
                </div>
            </div>

        @elseif($type === 'content')
            <!-- Content Widget Creator -->
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Content Type</label>
                        <select class="form-input w-full" name="content_type">
                            <option value="text">Text Content</option>
                            <option value="image">Image Gallery</option>
                            <option value="video">Video Player</option>
                            <option value="audio">Audio Player</option>
                            <option value="carousel">Image Carousel</option>
                            <option value="testimonial">Testimonial</option>
                            <option value="faq">FAQ Section</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Layout Style</label>
                        <select class="form-input w-full" name="layout_style">
                            <option value="default">Default</option>
                            <option value="minimal">Minimal</option>
                            <option value="modern">Modern</option>
                            <option value="classic">Classic</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Content Title</label>
                    <input type="text" class="form-input w-full" name="content_title" placeholder="Enter content title">
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-text mb-2">Content</label>
                    <div class="content-editor">
                        <div class="editor-toolbar bg-card-bg border border-border-color rounded-t-lg p-3 flex gap-2">
                            <button class="btn btn-sm btn-secondary">
                                <x-icon name="edit" size="xs" />
                            </button>
                            <button class="btn btn-sm btn-secondary">
                                <strong>B</strong>
                            </button>
                            <button class="btn btn-sm btn-secondary">
                                <em>I</em>
                            </button>
                            <button class="btn btn-sm btn-secondary">
                                <u>U</u>
                            </button>
                            <button class="btn btn-sm btn-secondary">
                                <x-icon name="link" size="xs" />
                            </button>
                        </div>
                        <textarea class="form-input w-full border-t-0 rounded-t-none" name="content" rows="6" placeholder="Enter your content here..."></textarea>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button class="btn btn-secondary">Cancel</button>
                    <button class="btn btn-primary">
                        <x-icon name="plus" size="sm" class="mr-2" />
                        Create Content
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const widgetCreator = document.querySelector('.widget-creator');
    const previewContent = widgetCreator.querySelector('.preview-content');
    
    // Real-time preview updates
    const inputs = widgetCreator.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });
    
    function updatePreview() {
        const type = widgetCreator.querySelector('[name="widget_type"]')?.value;
        const title = widgetCreator.querySelector('[name="widget_title"]')?.value || 'Sample Widget';
        const content = widgetCreator.querySelector('[name="widget_content"]')?.value || 'Sample content';
        const bgColor = widgetCreator.querySelector('[name="bg_color"]')?.value || '#3B82F6';
        const textColor = widgetCreator.querySelector('[name="text_color"]')?.value || '#FFFFFF';
        
        if (previewContent) {
            // Update preview based on widget type
            if (type === 'link') {
                previewContent.innerHTML = `
                    <a href="#" class="block px-4 py-2 rounded-lg text-center font-medium" style="background-color: ${bgColor}; color: ${textColor};">
                        ${title}
                    </a>
                `;
            } else if (type === 'text') {
                previewContent.innerHTML = `
                    <div class="p-4 rounded-lg" style="background-color: ${bgColor}; color: ${textColor};">
                        <h3 class="font-semibold mb-2">${title}</h3>
                        <p>${content}</p>
                    </div>
                `;
            } else {
                previewContent.innerHTML = `
                    <div class="bg-info text-white px-4 py-2 rounded-lg text-center">
                        <span class="font-medium">${title}</span>
                    </div>
                `;
            }
        }
    }
});
</script>

<style>
.widget-creator {
    max-width: 800px;
    margin: 0 auto;
}

.content-editor {
    border-radius: 0.5rem;
    overflow: hidden;
}

.editor-toolbar button {
    min-width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-content {
    min-height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.widget-preview {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
}
</style>