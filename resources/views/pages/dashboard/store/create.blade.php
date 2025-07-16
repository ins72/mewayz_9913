<x-layouts.dashboard title="Add Product - Mewayz" page-title="Add Product">
    <div class="fade-in">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Add New Product</h1>
                <p class="text-secondary-text">Add a new product to your store</p>
            </div>
            <a href="{{ route('dashboard.store.index') }}" class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Store
            </a>
        </div>

        <!-- Product Creation Form -->
        <div class="max-w-4xl mx-auto">
            <form id="productForm" class="space-y-8">
                <!-- Basic Information -->
                <div class="card">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-primary-text mb-2">Product Information</h2>
                        <p class="text-secondary-text">Enter the basic details of your product</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Product Name *</label>
                            <input type="text" name="name" required class="form-input w-full" placeholder="e.g., Premium Web Template">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Category</label>
                            <input type="text" name="category" class="form-input w-full" placeholder="e.g., Digital Product, Physical Product">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-primary-text mb-2">Product Description</label>
                        <textarea name="description" rows="4" class="form-input w-full" placeholder="Describe your product, its features, and benefits..."></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Price (USD) *</label>
                            <input type="number" name="price" step="0.01" min="0" required class="form-input w-full" placeholder="0.00">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Stock Quantity *</label>
                            <input type="number" name="stock_quantity" min="0" required class="form-input w-full" placeholder="0">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">SKU</label>
                            <input type="text" name="sku" class="form-input w-full" placeholder="e.g., WT-001">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_digital" value="true" class="form-checkbox">
                            <span class="ml-2 text-sm text-primary-text">This is a digital product</span>
                        </label>
                        <p class="text-sm text-secondary-text mt-1">Digital products don't require shipping</p>
                    </div>
                </div>

                <!-- Product Images -->
                <div class="card">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-primary-text mb-2">Product Images</h2>
                        <p class="text-secondary-text">Add images to showcase your product</p>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Product Image URLs</label>
                            <div id="imageInputs">
                                <div class="flex gap-2 mb-2">
                                    <input type="url" name="images[]" class="form-input flex-1" placeholder="https://example.com/image1.jpg">
                                    <button type="button" onclick="addImageInput()" class="btn btn-secondary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Preview -->
                <div class="card">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-primary-text mb-2">Product Preview</h2>
                        <p class="text-secondary-text">This is how your product will appear in your store</p>
                    </div>
                    
                    <div class="border border-border-color rounded-lg p-6 bg-card-bg">
                        <div class="flex items-start gap-4">
                            <div class="w-24 h-24 bg-gradient-to-br from-warning/20 to-error/20 rounded-lg flex items-center justify-center">
                                <svg class="w-12 h-12 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-primary-text mb-2" id="preview-name">Product Name</h3>
                                <p class="text-secondary-text mb-3" id="preview-description">Product description will appear here...</p>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="text-success font-medium" id="preview-price">$0.00</span>
                                    <span class="text-secondary-text" id="preview-category">Category</span>
                                    <span class="text-info" id="preview-stock">Stock: 0</span>
                                    <span class="text-warning text-xs" id="preview-type">Physical</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-4">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add image input functionality
        function addImageInput() {
            const container = document.getElementById('imageInputs');
            const div = document.createElement('div');
            div.className = 'flex gap-2 mb-2';
            div.innerHTML = `
                <input type="url" name="images[]" class="form-input flex-1" placeholder="https://example.com/image.jpg">
                <button type="button" onclick="removeImageInput(this)" class="btn btn-secondary text-red-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(div);
        }
        
        function removeImageInput(button) {
            button.parentElement.remove();
        }

        // Real-time preview updates
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('productForm');
            const inputs = form.querySelectorAll('input, textarea, select');
            
            // Update preview in real-time
            inputs.forEach(input => {
                input.addEventListener('input', updatePreview);
            });
            
            function updatePreview() {
                const name = form.querySelector('[name="name"]').value || 'Product Name';
                const description = form.querySelector('[name="description"]').value || 'Product description will appear here...';
                const category = form.querySelector('[name="category"]').value || 'Category';
                const price = form.querySelector('[name="price"]').value || '0.00';
                const stock = form.querySelector('[name="stock_quantity"]').value || '0';
                const isDigital = form.querySelector('[name="is_digital"]').checked;
                
                document.getElementById('preview-name').textContent = name;
                document.getElementById('preview-description').textContent = description;
                document.getElementById('preview-category').textContent = category;
                document.getElementById('preview-price').textContent = `$${price}`;
                document.getElementById('preview-stock').textContent = `Stock: ${stock}`;
                document.getElementById('preview-type').textContent = isDigital ? 'Digital' : 'Physical';
            }
            
            // Form submission
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);
                
                // Handle images array
                const imageInputs = form.querySelectorAll('[name="images[]"]');
                data.images = Array.from(imageInputs).map(input => input.value).filter(url => url);
                
                try {
                    const response = await fetch('/api/ecommerce/products', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        alert('Product added successfully!');
                        window.location.href = '/dashboard/store';
                    } else {
                        const error = await response.json();
                        alert('Error: ' + (error.message || 'Failed to add product'));
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            });
        });
    </script>
</x-layouts.dashboard>