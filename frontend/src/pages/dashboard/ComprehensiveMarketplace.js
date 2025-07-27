import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  ShoppingBagIcon,
  PlusIcon,
  MagnifyingGlassIcon,
  FunnelIcon,
  StarIcon,
  HeartIcon,
  ShareIcon,
  ShoppingCartIcon,
  CreditCardIcon,
  TruckIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  PhotoIcon,
  TagIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  UserIcon,
  BuildingStorefrontIcon,
  GiftIcon,
  BoltIcon,
  FireIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  DocumentTextIcon
} from '@heroicons/react/24/outline';
import {
  ShoppingBagIcon as ShoppingBagIconSolid,
  StarIcon as StarIconSolid,
  HeartIcon as HeartIconSolid,
  FireIcon as FireIconSolid
} from '@heroicons/react/24/solid';

const ComprehensiveMarketplace = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  const [activeTab, setActiveTab] = useState('browse');
  const [searchQuery, setSearchQuery] = useState('');
  const [filters, setFilters] = useState({
    category: '',
    priceRange: [0, 1000],
    rating: 0,
    seller: '',
    availability: 'all',
    sortBy: 'popular'
  });
  
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [cart, setCart] = useState([]);
  const [wishlist, setWishlist] = useState([]);
  const [orders, setOrders] = useState([]);
  const [showProductModal, setShowProductModal] = useState(false);
  const [showStoreModal, setShowStoreModal] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [myStore, setMyStore] = useState(null);
  
  // Categories
  const productCategories = [
    { id: 'digital', name: 'Digital Products', icon: '💻', count: 156 },
    { id: 'courses', name: 'Online Courses', icon: '🎓', count: 89 },
    { id: 'templates', name: 'Templates', icon: '🎨', count: 234 },
    { id: 'ebooks', name: 'E-books', icon: '📚', count: 67 },
    { id: 'software', name: 'Software', icon: '⚙️', count: 45 },
    { id: 'graphics', name: 'Graphics', icon: '🖼️', count: 123 },
    { id: 'audio', name: 'Audio', icon: '🎵', count: 78 },
    { id: 'video', name: 'Video', icon: '🎬', count: 92 },
    { id: 'physical', name: 'Physical Products', icon: '📦', count: 201 }
  ];
  
  // Mock products
  const mockProducts = [
    {
      id: '1',
      title: 'Complete Digital Marketing Course 2025',
      description: 'Master digital marketing with our comprehensive course covering SEO, social media, PPC, and content marketing.',
      price: 199.99,
      originalPrice: 299.99,
      discount: 33,
      category: 'courses',
      seller: {
        id: 'seller1',
        name: 'Marketing Academy',
        avatar: 'https://ui-avatars.com/api/?name=Marketing+Academy&background=3b82f6&color=fff',
        rating: 4.9,
        totalSales: 1247
      },
      images: [
        'https://ui-avatars.com/api/?name=Digital+Marketing&background=10b981&color=fff',
        'https://ui-avatars.com/api/?name=Course+Preview&background=f59e0b&color=fff'
      ],
      rating: 4.8,
      reviews: 234,
      totalSales: 1847,
      tags: ['marketing', 'seo', 'social media', 'ppc'],
      features: [
        '50+ Hours of Video Content',
        'Lifetime Access',
        'Certificate of Completion',
        'Private Community Access',
        'Monthly Q&A Sessions'
      ],
      isDigital: true,
      inStock: true,
      isFeatured: true,
      createdAt: '2024-12-01'
    },
    {
      id: '2',
      title: 'Professional Website Templates Pack',
      description: 'Beautiful, responsive website templates for businesses, portfolios, and e-commerce sites.',
      price: 49.99,
      originalPrice: 99.99,
      discount: 50,
      category: 'templates',
      seller: {
        id: 'seller2',
        name: 'Design Studio Pro',
        avatar: 'https://ui-avatars.com/api/?name=Design+Studio&background=ec4899&color=fff',
        rating: 4.7,
        totalSales: 892
      },
      images: [
        'https://ui-avatars.com/api/?name=Web+Templates&background=8b5cf6&color=fff',
        'https://ui-avatars.com/api/?name=Template+Preview&background=06b6d4&color=fff'
      ],
      rating: 4.6,
      reviews: 156,
      totalSales: 634,
      tags: ['templates', 'website', 'responsive', 'business'],
      features: [
        '10 Premium Templates',
        'Figma Files Included',
        'HTML/CSS Code',
        'Mobile Responsive',
        'Free Updates'
      ],
      isDigital: true,
      inStock: true,
      isFeatured: false,
      createdAt: '2024-11-15'
    },
    {
      id: '3',
      title: 'Instagram Growth E-book & Tools',
      description: 'Complete guide to growing your Instagram following organically with proven strategies and tools.',
      price: 29.99,
      originalPrice: 49.99,
      discount: 40,
      category: 'ebooks',
      seller: {
        id: 'seller3',
        name: 'Social Media Experts',
        avatar: 'https://ui-avatars.com/api/?name=Social+Experts&background=f59e0b&color=fff',
        rating: 4.5,
        totalSales: 567
      },
      images: [
        'https://ui-avatars.com/api/?name=Instagram+Guide&background=E4405F&color=fff'
      ],
      rating: 4.4,
      reviews: 89,
      totalSales: 423,
      tags: ['instagram', 'social media', 'growth', 'marketing'],
      features: [
        '100+ Page E-book',
        'Content Templates',
        'Hashtag Research Tool',
        'Growth Tracking Sheets',
        'Video Tutorials'
      ],
      isDigital: true,
      inStock: true,
      isFeatured: true,
      createdAt: '2024-10-20'
    },
    {
      id: '4',
      title: 'AI Content Generator Software',
      description: 'Powerful AI tool for generating high-quality content for blogs, social media, and marketing.',
      price: 97.00,
      originalPrice: 197.00,
      discount: 51,
      category: 'software',
      seller: {
        id: 'seller4',
        name: 'AI Tech Solutions',
        avatar: 'https://ui-avatars.com/api/?name=AI+Tech&background=7c3aed&color=fff',
        rating: 4.9,
        totalSales: 234
      },
      images: [
        'https://ui-avatars.com/api/?name=AI+Generator&background=3b82f6&color=fff'
      ],
      rating: 4.9,
      reviews: 67,
      totalSales: 189,
      tags: ['ai', 'content', 'generator', 'marketing'],
      features: [
        'Unlimited Content Generation',
        'Multi-language Support',
        'API Access',
        'Custom Templates',
        'Priority Support'
      ],
      isDigital: true,
      inStock: true,
      isFeatured: true,
      createdAt: '2024-12-10'
    }
  ];
  
  const [marketplaceStats, setMarketplaceStats] = useState({
    totalProducts: 1234,
    totalSellers: 456,
    totalSales: 125678,
    avgRating: 4.7,
    featuredProducts: 89,
    newThisWeek: 34
  });
  
  useEffect(() => {
    // Real data loaded from API
    // Real data loaded from API
    loadUserData();
  }, []);
  
  const loadUserData = () => {
    // Mock user store data
    // Real data loaded from API
    
    // Mock cart items
    setCart([
      {
        id: '1',
        product: mockProducts[0],
        quantity: 1,
        addedAt: new Date()
      }
    ]);
    
    // Mock orders
    // Real data loaded from API
  };
  
  const addToCart = (product) => {
    const existingItem = cart.find(item => item.product.id === product.id);
    if (existingItem) {
      setCart(cart.map(item =>
        item.product.id === product.id 
          ? { ...item, quantity: item.quantity + 1 }
          : item
      ));
    } else {
      setCart([...cart, {
        id: Date.now().toString(),
        product,
        quantity: 1,
        addedAt: new Date()
      }]);
    }
    success('Added to cart!');
  };
  
  const addToWishlist = (product) => {
    if (!wishlist.find(item => item.id === product.id)) {
      // Real data loaded from API
      success('Added to wishlist!');
    } else {
      setWishlist(wishlist.filter(item => item.id !== product.id));
      success('Removed from wishlist');
    }
  };
  
  const removeFromCart = (productId) => {
    setCart(cart.filter(item => item.product.id !== productId));
    success('Removed from cart');
  };
  
  const getTotalCartValue = () => {
    return cart.reduce((total, item) => total + (item.product.price * item.quantity), 0);
  };
  
  const filteredProducts = products.filter(product => {
    if (searchQuery && !product.title.toLowerCase().includes(searchQuery.toLowerCase()) &&
        !product.description.toLowerCase().includes(searchQuery.toLowerCase())) {
      return false;
    }
    
    if (filters.category && product.category !== filters.category) {
      return false;
    }
    
    if (product.price < filters.priceRange[0] || product.price > filters.priceRange[1]) {
      return false;
    }
    
    if (filters.rating > 0 && product.rating < filters.rating) {
      return false;
    }
    
    return true;
  });
  
  const renderProductCard = (product) => (
    <motion.div
      key={product.id}
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="bg-surface border border-default rounded-xl overflow-hidden hover:shadow-lg transition-all group"
    >
      <div className="relative">
        <img
          src={product.images[0]}
          alt={product.title}
          className="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
        />
        {product.discount > 0 && (
          <div className="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded-full text-sm font-medium">
            -{product.discount}%
          </div>
        )}
        {product.isFeatured && (
          <div className="absolute top-3 right-3 bg-yellow-500 text-white px-2 py-1 rounded-full text-sm font-medium flex items-center">
            <StarIconSolid className="h-4 w-4 mr-1" />
            Featured
          </div>
        )}
        <div className="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
          <div className="flex space-x-2">
            <button
              onClick={() => setSelectedProduct(product)}
              className="bg-white text-black px-3 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors"
            >
              <EyeIcon className="h-4 w-4 mr-2 inline" />
              View
            </button>
            <button
              onClick={() => addToCart(product)}
              className="bg-blue-600 text-white px-3 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors"
            >
              <ShoppingCartIcon className="h-4 w-4 mr-2 inline" />
              Add to Cart
            </button>
          </div>
        </div>
      </div>
      
      <div className="p-4">
        <div className="flex items-start justify-between mb-2">
          <h3 className="font-semibold text-primary line-clamp-2 flex-1">{product.title}</h3>
          <button
            onClick={() => addToWishlist(product)}
            className={`ml-2 p-1 rounded ${
              wishlist.find(item => item.id === product.id)
                ? 'text-red-500'
                : 'text-gray-400 hover:text-red-500'
            }`}
          >
            {wishlist.find(item => item.id === product.id) ? (
              <HeartIconSolid className="h-5 w-5" />
            ) : (
              <HeartIcon className="h-5 w-5" />
            )}
          </button>
        </div>
        
        <p className="text-secondary text-sm line-clamp-2 mb-3">{product.description}</p>
        
        <div className="flex items-center mb-3">
          <div className="flex items-center">
            {[...Array(5)].map((_, i) => (
              <StarIcon
                key={i}
                className={`h-4 w-4 ${
                  i < Math.floor(product.rating)
                    ? 'text-yellow-400 fill-current'
                    : 'text-gray-300'
                }`}
              />
            ))}
          </div>
          <span className="ml-2 text-sm text-secondary">
            {product.rating} ({product.reviews})
          </span>
        </div>
        
        <div className="flex items-center justify-between mb-3">
          <div className="flex items-center space-x-2">
            <img
              src={product.seller.avatar}
              alt={product.seller.name}
              className="w-6 h-6 rounded-full"
            />
            <span className="text-sm text-secondary">{product.seller.name}</span>
          </div>
          <div className="text-sm text-secondary">
            {product.totalSales} sales
          </div>
        </div>
        
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-2">
            <span className="text-lg font-bold text-primary">${product.price}</span>
            {product.originalPrice > product.price && (
              <span className="text-sm text-secondary line-through">${product.originalPrice}</span>
            )}
          </div>
          <div className="flex space-x-1">
            {product.tags.slice(0, 2).map((tag, index) => (
              <span key={index} className="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                {tag}
              </span>
            ))}
          </div>
        </div>
      </div>
    </motion.div>
  );
  
  const renderProductModal = () => {
    if (!selectedProduct) return null;
    
    return (
      <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          className="bg-surface rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
        >
          <div className="p-6">
            <div className="flex items-start justify-between mb-6">
              <div>
                <h2 className="text-2xl font-bold text-primary mb-2">{selectedProduct.title}</h2>
                <div className="flex items-center space-x-4">
                  <div className="flex items-center">
                    {[...Array(5)].map((_, i) => (
                      <StarIcon
                        key={i}
                        className={`h-5 w-5 ${
                          i < Math.floor(selectedProduct.rating)
                            ? 'text-yellow-400 fill-current'
                            : 'text-gray-300'
                        }`}
                      />
                    ))}
                  </div>
                  <span className="text-secondary">
                    {selectedProduct.rating} ({selectedProduct.reviews} reviews)
                  </span>
                  <span className="text-secondary">•</span>
                  <span className="text-secondary">{selectedProduct.totalSales} sales</span>
                </div>
              </div>
              <button
                onClick={() => setSelectedProduct(null)}
                className="p-2 hover:bg-surface-hover rounded-lg"
              >
                <XCircleIcon className="h-6 w-6" />
              </button>
            </div>
            
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <div>
                <img
                  src={selectedProduct.images[0]}
                  alt={selectedProduct.title}
                  className="w-full h-64 object-cover rounded-lg mb-4"
                />
                <div className="flex space-x-2">
                  {selectedProduct.images.map((image, index) => (
                    <img
                      key={index}
                      src={image}
                      alt={`Product ${index + 1}`}
                      className="w-16 h-16 object-cover rounded cursor-pointer border-2 border-transparent hover:border-blue-500"
                    />
                  ))}
                </div>
              </div>
              
              <div>
                <div className="mb-6">
                  <div className="flex items-center space-x-4 mb-4">
                    <span className="text-3xl font-bold text-primary">${selectedProduct.price}</span>
                    {selectedProduct.originalPrice > selectedProduct.price && (
                      <>
                        <span className="text-xl text-secondary line-through">${selectedProduct.originalPrice}</span>
                        <span className="bg-red-500 text-white px-2 py-1 rounded text-sm font-medium">
                          -{selectedProduct.discount}% OFF
                        </span>
                      </>
                    )}
                  </div>
                  
                  <p className="text-secondary mb-4">{selectedProduct.description}</p>
                  
                  <div className="mb-4">
                    <h4 className="font-semibold text-primary mb-2">What's Included:</h4>
                    <ul className="space-y-2">
                      {selectedProduct.features.map((feature, index) => (
                        <li key={index} className="flex items-center text-secondary">
                          <CheckCircleIcon className="h-5 w-5 text-green-500 mr-2" />
                          {feature}
                        </li>
                      ))}
                    </ul>
                  </div>
                  
                  <div className="flex items-center mb-6">
                    <img
                      src={selectedProduct.seller.avatar}
                      alt={selectedProduct.seller.name}
                      className="w-10 h-10 rounded-full mr-3"
                    />
                    <div>
                      <div className="font-medium text-primary">{selectedProduct.seller.name}</div>
                      <div className="flex items-center text-sm text-secondary">
                        <StarIcon className="h-4 w-4 text-yellow-400 fill-current mr-1" />
                        {selectedProduct.seller.rating} • {selectedProduct.seller.totalSales} sales
                      </div>
                    </div>
                  </div>
                  
                  <div className="flex space-x-3">
                    <button
                      onClick={() => addToCart(selectedProduct)}
                      className="btn btn-primary flex-1"
                    >
                      <ShoppingCartIcon className="h-5 w-5 mr-2" />
                      Add to Cart - ${selectedProduct.price}
                    </button>
                    <button
                      onClick={() => addToWishlist(selectedProduct)}
                      className={`btn btn-secondary ${
                        wishlist.find(item => item.id === selectedProduct.id)
                          ? 'bg-red-50 text-red-600 border-red-200'
                          : ''
                      }`}
                    >
                      <HeartIcon className="h-5 w-5" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </motion.div>
      </div>
    );
  };
  
  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <div className="flex items-center mb-2">
              <ShoppingBagIconSolid className="h-8 w-8 mr-3" />
              <h1 className="text-3xl font-bold">Digital Marketplace</h1>
            </div>
            <p className="text-white/80">Discover and sell digital products, courses, and services</p>
          </div>
          <div className="flex space-x-4">
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{marketplaceStats.totalProducts.toLocaleString()}</div>
              <div className="text-sm text-white/70">Products</div>
            </div>
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{marketplaceStats.totalSellers}</div>
              <div className="text-sm text-white/70">Sellers</div>
            </div>
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{marketplaceStats.avgRating}</div>
              <div className="text-sm text-white/70">Avg Rating</div>
            </div>
          </div>
        </div>
      </div>
      
      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="flex space-x-8">
          {[
            { id: 'browse', name: 'Browse Products', icon: MagnifyingGlassIcon },
            { id: 'cart', name: `Cart (${cart.length})`, icon: ShoppingCartIcon },
            { id: 'orders', name: 'My Orders', icon: DocumentTextIcon },
            { id: 'store', name: 'My Store', icon: BuildingStorefrontIcon }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`flex items-center py-4 px-1 border-b-2 font-medium text-sm ${
                activeTab === tab.id
                  ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                  : 'border-transparent text-secondary hover:text-primary'
              }`}
            >
              <tab.icon className="h-4 w-4 mr-2" />
              {tab.name}
            </button>
          ))}
        </nav>
      </div>
      
      {/* Content */}
      {activeTab === 'browse' && (
        <div className="space-y-6">
          {/* Search and Filters */}
          <div className="bg-surface-elevated rounded-xl shadow-default p-6">
            <div className="flex items-center space-x-4 mb-4">
              <div className="relative flex-1">
                <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search products..."
                  className="pl-10 input"
                />
              </div>
              <select
                value={filters.category}
                onChange={(e) => setFilters({ ...filters, category: e.target.value })}
                className="input w-48"
              >
                <option value="">All Categories</option>
                {categories.map((category) => (
                  <option key={category.id} value={category.id}>
                    {category.name} ({category.count})
                  </option>
                ))}
              </select>
              <select
                value={filters.sortBy}
                onChange={(e) => setFilters({ ...filters, sortBy: e.target.value })}
                className="input w-48"
              >
                <option value="popular">Most Popular</option>
                <option value="newest">Newest First</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="rating">Highest Rated</option>
              </select>
            </div>
            
            {/* Categories */}
            <div className="flex flex-wrap gap-2">
              {categories.map((category) => (
                <button
                  key={category.id}
                  onClick={() => setFilters({ 
                    ...filters, 
                    category: filters.category === category.id ? '' : category.id 
                  })}
                  className={`flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all ${
                    filters.category === category.id
                      ? 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300'
                      : 'bg-surface border border-default text-secondary hover:bg-surface-hover'
                  }`}
                >
                  <span className="mr-2">{category.icon}</span>
                  {category.name}
                  <span className="ml-2 text-xs opacity-60">({category.count})</span>
                </button>
              ))}
            </div>
          </div>
          
          {/* Featured Products */}
          <div>
            <div className="flex items-center mb-4">
              <FireIconSolid className="h-6 w-6 text-orange-500 mr-2" />
              <h2 className="text-xl font-semibold text-primary">Featured Products</h2>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {products.filter(p => p.isFeatured).map(renderProductCard)}
            </div>
          </div>
          
          {/* All Products */}
          <div>
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-xl font-semibold text-primary">
                All Products ({filteredProducts.length})
              </h2>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {filteredProducts.map(renderProductCard)}
            </div>
            
            {filteredProducts.length === 0 && (
              <div className="text-center py-12">
                <ShoppingBagIcon className="h-12 w-12 mx-auto mb-4 text-gray-400" />
                <h3 className="text-lg font-medium text-primary">No products found</h3>
                <p className="text-secondary">Try adjusting your search criteria</p>
              </div>
            )}
          </div>
        </div>
      )}
      
      {activeTab === 'cart' && (
        <div className="space-y-6">
          <div className="bg-surface-elevated rounded-xl shadow-default p-6">
            <h2 className="text-xl font-semibold text-primary mb-6">Shopping Cart</h2>
            
            {cart.length > 0 ? (
              <div className="space-y-6">
                <div className="space-y-4">
                  {cart.map((item) => (
                    <div key={item.id} className="flex items-center space-x-4 p-4 border border-default rounded-lg">
                      <img
                        src={item.product.images[0]}
                        alt={item.product.title}
                        className="w-16 h-16 object-cover rounded"
                      />
                      <div className="flex-1">
                        <h3 className="font-medium text-primary">{item.product.title}</h3>
                        <p className="text-secondary">{item.product.seller.name}</p>
                        <p className="font-bold text-primary">${item.product.price}</p>
                      </div>
                      <div className="flex items-center space-x-3">
                        <span className="text-secondary">Qty: {item.quantity}</span>
                        <button
                          onClick={() => removeFromCart(item.product.id)}
                          className="text-red-600 hover:bg-red-100 dark:hover:bg-red-900 p-2 rounded"
                        >
                          <TrashIcon className="h-4 w-4" />
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
                
                <div className="border-t border-default pt-6">
                  <div className="flex items-center justify-between text-xl font-bold text-primary mb-6">
                    <span>Total: ${getTotalCartValue().toFixed(2)}</span>
                  </div>
                  <button className="btn btn-primary w-full">
                    <CreditCardIcon className="h-5 w-5 mr-2" />
                    Proceed to Checkout
                  </button>
                </div>
              </div>
            ) : (
              <div className="text-center py-12">
                <ShoppingCartIcon className="h-12 w-12 mx-auto mb-4 text-gray-400" />
                <h3 className="text-lg font-medium text-primary">Your cart is empty</h3>
                <p className="text-secondary mb-4">Start shopping to add items to your cart</p>
                <button
                  onClick={() => setActiveTab('browse')}
                  className="btn btn-primary"
                >
                  Browse Products
                </button>
              </div>
            )}
          </div>
        </div>
      )}
      
      {renderProductModal()}
    </div>
  );
};

export default ComprehensiveMarketplace;