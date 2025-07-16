<x-layouts.dashboard title="Store - Mewayz" page-title="Store">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Your Store</h1>
                <p class="text-secondary-text">Manage your products and track sales performance</p>
            </div>
            <a href="{{ route('dashboard.store.create') }}" class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Product
            </a>
        </div>

        <!-- Store Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Sales</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="total-sales">-</div>
                <div class="text-sm text-success" id="sales-growth">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Orders</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="total-orders">-</div>
                <div class="text-sm text-info" id="orders-growth">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Products</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="total-products">-</div>
                <div class="text-sm text-warning" id="products-status">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Conversion</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="conversion-rate">-</div>
                <div class="text-sm text-success" id="conversion-growth">Loading...</div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-primary-text">Products</h2>
                <div class="flex items-center gap-3">
                    <input type="text" id="search-input" placeholder="Search products..." class="form-input w-64" onkeyup="filterProducts()">
                    <select id="category-filter" class="form-input" onchange="filterProducts()">
                        <option value="">All Categories</option>
                        <option value="Digital">Digital Products</option>
                        <option value="Physical">Physical Products</option>
                        <option value="Services">Services</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border-color">
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Product</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Category</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Price</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Stock</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Type</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Status</th>
                            <th class="text-left py-3 px-4 text-secondary-text font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-table-body">
                        <!-- Loading State -->
                        <tr>
                            <td colspan="7" class="text-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-info mx-auto mb-4"></div>
                                <p class="text-secondary-text">Loading products...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-12">
            <div class="w-24 h-24 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-primary-text mb-2">No products in your store</h3>
            <p class="text-secondary-text mb-6">Add your first product to start selling online</p>
            <a href="/dashboard/store/create" class="btn btn-primary">Add Your First Product</a>
        </div>
    </div>

    <script>
        let allProducts = [];

        document.addEventListener('DOMContentLoaded', function() {
            loadProductsData();
            loadStoreStats();
        });

        async function loadProductsData() {
            try {
                const response = await fetch('/api/ecommerce/products', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    allProducts = data.data?.data || data.data || [];
                    displayProducts(allProducts);
                } else {
                    console.error('Failed to load products');
                    showEmptyState();
                }
            } catch (error) {
                console.error('Error loading products:', error);
                showEmptyState();
            }
        }

        async function loadStoreStats() {
            try {
                // Mock stats for now - you can replace with actual API calls
                document.getElementById('total-sales').textContent = '$8,456';
                document.getElementById('sales-growth').textContent = '+12.3% from last month';
                document.getElementById('total-orders').textContent = '142';
                document.getElementById('orders-growth').textContent = '+8.1% from last month';
                document.getElementById('total-products').textContent = allProducts.length.toString();
                document.getElementById('products-status').textContent = '3 out of stock';
                document.getElementById('conversion-rate').textContent = '3.2%';
                document.getElementById('conversion-growth').textContent = '+0.4% from last month';
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        function displayProducts(products) {
            const tableBody = document.getElementById('products-table-body');
            const emptyState = document.getElementById('empty-state');
            
            if (products.length === 0) {
                showEmptyState();
                return;
            }

            tableBody.innerHTML = products.map(product => createProductRow(product)).join('');
            emptyState.classList.add('hidden');
        }

        function createProductRow(product) {
            const statusClass = product.status === 1 ? 'success' : 'warning';
            const statusText = product.status === 1 ? 'Active' : 'Draft';
            const productType = product.is_digital ? 'Digital' : 'Physical';
            const stockDisplay = product.is_digital ? 'Unlimited' : (product.stock_quantity || 0);
            
            return `
                <tr class="border-b border-border-color hover:bg-hover-bg">
                    <td class="py-4 px-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-warning/20 to-error/20 rounded-lg mr-3"></div>
                            <div>
                                <div class="font-medium text-primary-text">${product.name}</div>
                                <div class="text-sm text-secondary-text">${product.description || 'No description'}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-secondary-text">${product.category || 'Uncategorized'}</td>
                    <td class="py-4 px-4 text-primary-text font-medium">$${product.price || '0.00'}</td>
                    <td class="py-4 px-4 text-secondary-text">${stockDisplay}</td>
                    <td class="py-4 px-4 text-secondary-text">${productType}</td>
                    <td class="py-4 px-4">
                        <span class="px-2 py-1 text-xs font-medium bg-${statusClass}/10 text-${statusClass} rounded">${statusText}</span>
                    </td>
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-2">
                            <button onclick="editProduct(${product.id})" class="text-info hover:text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="viewProduct(${product.id})" class="text-secondary-text hover:text-primary-text">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteProduct(${product.id})" class="text-error hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        function filterProducts() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const categoryFilter = document.getElementById('category-filter').value;
            
            const filtered = allProducts.filter(product => {
                const matchesSearch = product.name.toLowerCase().includes(searchTerm) || 
                                    (product.description && product.description.toLowerCase().includes(searchTerm));
                const matchesCategory = !categoryFilter || product.category === categoryFilter;
                
                return matchesSearch && matchesCategory;
            });
            
            displayProducts(filtered);
        }

        function showEmptyState() {
            document.getElementById('products-table-body').innerHTML = '';
            document.getElementById('empty-state').classList.remove('hidden');
        }

        function editProduct(productId) {
            window.location.href = `/dashboard/store/${productId}/edit`;
        }

        function viewProduct(productId) {
            window.location.href = `/dashboard/store/${productId}`;
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                // Implement delete functionality
                console.log('Delete product:', productId);
            }
        }
    </script>
</x-layouts.dashboard>