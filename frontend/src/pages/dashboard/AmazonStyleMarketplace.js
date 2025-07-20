import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import api from '../../services/api';
import {
  ShoppingBagIcon,
  MagnifyingGlassIcon,
  FunnelIcon,
  StarIcon,
  ShoppingCartIcon,
  HeartIcon,
  EyeIcon,
  ShareIcon,
  TruckIcon,
  ShieldCheckIcon,
  CurrencyDollarIcon,
  TagIcon,
  PhotoIcon,
  PlusIcon,
  PencilIcon,
  TrashIcon,
  ChartBarIcon,
  UsersIcon,
  BuildingStorefrontIcon,
  GlobeAltIcon,
  BoltIcon,
  FireIcon,
  GiftIcon,
  CreditCardIcon,
  ClockIcon,
  CheckCircleIcon,
  XMarkIcon,
  ArrowTrendingUpIcon,
  DocumentTextIcon,
  CogIcon,
  BanknotesIcon,
  ReceiptPercentIcon
} from '@heroicons/react/24/outline';
import {
  StarIcon as StarIconSolid,
  HeartIcon as HeartIconSolid,
  ShoppingCartIcon as ShoppingCartIconSolid
} from '@heroicons/react/24/solid';

const AmazonStyleMarketplace = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  // State management
  const [activeTab, setActiveTab] = useState('browse'); // browse, my-store, orders, analytics, seller-hub
  const [loading, setLoading] = useState(false);
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [myProducts, setMyProducts] = useState([]);
  const [orders, setOrders] = useState([]);
  const [cart, setCart] = useState([]);
  const [wishlist, setWishlist] = useState([]);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [showProductModal, setShowProductModal] = useState(false);
  const [showStoreSetup, setShowStoreSetup] = useState(false);
  
  // Store management
  const [storeProfile, setStoreProfile] = useState({
    storeName: '',
    description: '',
    logo: null,
    banner: null,
    website: '',
    phone: '',
    address: '',
    businessType: '',
    taxId: '',
    bankAccount: '',
    shippingMethods: [],
    returnPolicy: '',
    isVerified: false
  });
  
  // Product creation form
  const [productForm, setProductForm] = useState({
    title: '',
    description: '',
    category: '',
    price: 0,
    compareAtPrice: 0,
    sku: '',
    inventory: 0,
    images: [],
    variants: [],
    tags: [],
    weight: 0,
    dimensions: { length: 0, width: 0, height: 0 },
    shippingClass: '',
    isDigital: false,
    downloadableFiles: [],
    specifications: {},
    seoTitle: '',
    seoDescription: ''
  });
  
  // Filters and search
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [priceRange, setPriceRange] = useState({ min: 0, max: 1000 });
  const [ratingFilter, setRatingFilter] = useState(0);
  const [sortBy, setSortBy] = useState('featured');
  const [showFilters, setShowFilters] = useState(false);
  
  // Pagination
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage] = useState(24);
  
  // Analytics and stats
  const [marketplaceStats, setMarketplaceStats] = useState({
    totalProducts: 0,
    totalSellers: 0,
    totalOrders: 0,
    totalRevenue: 0,
    averageOrderValue: 0,
    conversionRate: 0,
    myStoreStats: {
      products: 0,
      orders: 0,
      revenue: 0,
      rating: 0,
      views: 0,
      favorites: 0
    }
  });

  useEffect(() => {
    loadMarketplaceData();
  }, [activeTab, selectedCategory, searchQuery, sortBy, currentPage]);

  const loadMarketplaceData = async () => {
    setLoading(true);
    try {
      const [productsRes, categoriesRes, statsRes] = await Promise.all([
        api.get(`/marketplace/products?category=${selectedCategory}&search=${searchQuery}&sort=${sortBy}&page=${currentPage}&limit=${itemsPerPage}`),
        api.get('/marketplace/categories'),
        api.get('/marketplace/stats')
      ]);

      if (productsRes.data.success) {
        setProducts(productsRes.data.data.products);
      }
      if (categoriesRes.data.success) {
        setCategories(categoriesRes.data.data);
      }
      if (statsRes.data.success) {
        setMarketplaceStats(statsRes.data.data);
      }

      // Load additional data based on active tab
      if (activeTab === 'my-store') {
        const [myProductsRes, storeRes] = await Promise.all([
          api.get('/marketplace/my-products'),
          api.get('/marketplace/my-store')
        ]);
        
        if (myProductsRes.data.success) {
          setMyProducts(myProductsRes.data.data);
        }
        if (storeRes.data.success) {
          setStoreProfile(storeRes.data.data || storeProfile);
        }
      }

      if (activeTab === 'orders') {
        const ordersRes = await api.get('/marketplace/orders');
        if (ordersRes.data.success) {
          setOrders(ordersRes.data.data);
        }
      }

      // Load cart and wishlist
      const [cartRes, wishlistRes] = await Promise.all([
        api.get('/marketplace/cart'),
        api.get('/marketplace/wishlist')
      ]);
      
      if (cartRes.data.success) {
        setCart(cartRes.data.data);
      }
      if (wishlistRes.data.success) {
        setWishlist(wishlistRes.data.data);
      }
    } catch (err) {
      console.error('Failed to load marketplace data:', err);
      error('Failed to load marketplace data');
    } finally {
      setLoading(false);
    }
  };

  const handleProductAction = async (action, productId, additionalData = {}) => {
    setLoading(true);
    try {
      let response;
      
      switch (action) {
        case 'add-to-cart':
          response = await api.post(`/marketplace/cart/add`, {
            productId,
            quantity: additionalData.quantity || 1,
            variant: additionalData.variant
          });
          if (response.data.success) {
            success('Added to cart');
            loadMarketplaceData();
          }
          break;
          
        case 'add-to-wishlist':
          response = await api.post(`/marketplace/wishlist/add`, { productId });
          if (response.data.success) {
            success('Added to wishlist');
            loadMarketplaceData();
          }
          break;
          
        case 'rate-product':
          response = await api.post(`/marketplace/products/${productId}/rate`, {
            rating: additionalData.rating,
            review: additionalData.review
          });
          if (response.data.success) {
            success('Review submitted');
            loadMarketplaceData();
          }
          break;
          
        case 'buy-now':
          response = await api.post(`/marketplace/orders/create`, {
            items: [{ productId, quantity: additionalData.quantity || 1 }],
            paymentMethod: additionalData.paymentMethod,
            shippingAddress: additionalData.shippingAddress
          });
          if (response.data.success) {
            success('Order placed successfully');
            loadMarketplaceData();
          }
          break;
          
        case 'update-inventory':
          response = await api.put(`/marketplace/products/${productId}/inventory`, {
            inventory: additionalData.inventory
          });
          if (response.data.success) {
            success('Inventory updated');
            loadMarketplaceData();
          }
          break;
          
        case 'delete-product':
          if (window.confirm('Are you sure you want to delete this product?')) {
            response = await api.delete(`/marketplace/products/${productId}`);
            if (response.data.success) {
              success('Product deleted');
              loadMarketplaceData();
            }
          }
          break;
      }
    } catch (err) {
      console.error(`Product ${action} failed:`, err);
      error(`Failed to ${action.replace('-', ' ')} product`);
    } finally {
      setLoading(false);
    }
  };

  const handleCreateProduct = async () => {
    if (!productForm.title || !productForm.description || !productForm.category) {
      error('Please fill in all required fields');
      return;
    }

    setLoading(true);
    try {
      const formData = new FormData();
      
      Object.keys(productForm).forEach(key => {
        if (key === 'images' && productForm[key].length > 0) {
          productForm[key].forEach(file => {
            formData.append('images', file);
          });
        } else if (key !== 'images') {
          formData.append(key, JSON.stringify(productForm[key]));
        }
      });

      const response = await api.post('/marketplace/products/create', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });

      if (response.data.success) {
        success('Product created successfully');
        setShowProductModal(false);
        setProductForm({
          title: '', description: '', category: '', price: 0, compareAtPrice: 0,
          sku: '', inventory: 0, images: [], variants: [], tags: [],
          weight: 0, dimensions: { length: 0, width: 0, height: 0 },
          shippingClass: '', isDigital: false, downloadableFiles: [],
          specifications: {}, seoTitle: '', seoDescription: ''
        });
        loadMarketplaceData();
      }
    } catch (err) {
      console.error('Product creation failed:', err);
      error('Failed to create product');
    } finally {
      setLoading(false);
    }
  };

  const handleStoreSetup = async () => {
    if (!storeProfile.storeName || !storeProfile.description) {
      error('Please fill in required store information');
      return;
    }

    setLoading(true);
    try {
      const formData = new FormData();
      
      Object.keys(storeProfile).forEach(key => {
        if (key === 'logo' && storeProfile[key]) {
          formData.append('logo', storeProfile[key]);
        } else if (key === 'banner' && storeProfile[key]) {
          formData.append('banner', storeProfile[key]);
        } else if (key !== 'logo' && key !== 'banner') {
          formData.append(key, JSON.stringify(storeProfile[key]));
        }
      });

      const response = await api.post('/marketplace/store/setup', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });

      if (response.data.success) {
        success('Store setup completed');
        setShowStoreSetup(false);
        loadMarketplaceData();
      }
    } catch (err) {
      console.error('Store setup failed:', err);
      error('Failed to setup store');
    } finally {
      setLoading(false);
    }
  };

  const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(price);
  };

  const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
  };

  const getDiscountPercentage = (price, compareAtPrice) => {
    if (!compareAtPrice || compareAtPrice <= price) return 0;
    return Math.round(((compareAtPrice - price) / compareAtPrice) * 100);
  };

  const ProductCard = ({ product, isMine = false }) => {
    const discountPercentage = getDiscountPercentage(product.price, product.compareAtPrice);
    const isInCart = cart.some(item => item.productId === product.id);
    const isInWishlist = wishlist.some(item => item.productId === product.id);
    
    return (
      <motion.div
        layout
        initial={{ opacity: 0, scale: 0.9 }}
        animate={{ opacity: 1, scale: 1 }}
        exit={{ opacity: 0, scale: 0.9 }}
        className="bg-surface rounded-lg shadow-default hover:shadow-lg transition-all duration-300 overflow-hidden group"
      >
        {/* Product Image */}
        <div className="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200">
          {product.images?.length > 0 ? (
            <img
              src={product.images[0]}
              alt={product.title}
              className="w-full h-full object-cover"
            />
          ) : (
            <div className="w-full h-full flex items-center justify-center">
              <PhotoIcon className="h-16 w-16 text-secondary" />
            </div>
          )}
          
          {/* Discount Badge */}
          {discountPercentage > 0 && (
            <div className="absolute top-2 left-2">
              <span className="bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold">
                -{discountPercentage}%
              </span>
            </div>
          )}
          
          {/* Digital Product Badge */}
          {product.isDigital && (
            <div className="absolute top-2 right-2">
              <span className="bg-blue-500 text-white px-2 py-1 rounded text-xs">
                Digital
              </span>
            </div>
          )}
          
          {/* Quick Actions */}
          <div className="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center space-x-3">
            <button
              onClick={() => { setSelectedProduct(product); setShowProductModal(true); }}
              className="p-2 bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition-colors"
              title="Quick View"
            >
              <EyeIcon className="h-5 w-5" />
            </button>
            
            {!isMine && (
              <>
                <button
                  onClick={() => handleProductAction('add-to-cart', product.id)}
                  className={`p-2 backdrop-blur-sm rounded-full text-white transition-colors ${
                    isInCart ? 'bg-green-500 hover:bg-green-600' : 'bg-accent-primary hover:bg-accent-secondary'
                  }`}
                  title={isInCart ? 'In Cart' : 'Add to Cart'}
                >
                  {isInCart ? <CheckCircleIcon className="h-5 w-5" /> : <ShoppingCartIcon className="h-5 w-5" />}
                </button>
                
                <button
                  onClick={() => handleProductAction('add-to-wishlist', product.id)}
                  className={`p-2 backdrop-blur-sm rounded-full text-white transition-colors ${
                    isInWishlist ? 'bg-red-500 hover:bg-red-600' : 'bg-white/20 hover:bg-white/30'
                  }`}
                  title={isInWishlist ? 'In Wishlist' : 'Add to Wishlist'}
                >
                  {isInWishlist ? <HeartIconSolid className="h-5 w-5" /> : <HeartIcon className="h-5 w-5" />}
                </button>
              </>
            )}
            
            <button
              className="p-2 bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition-colors"
              title="Share"
            >
              <ShareIcon className="h-5 w-5" />
            </button>
          </div>
        </div>
        
        {/* Product Info */}
        <div className="p-4">
          <div className="flex items-start justify-between mb-2">
            <h3 className="font-semibold text-primary text-sm line-clamp-2 flex-1">
              {product.title}
            </h3>
            {isMine && (
              <div className="flex space-x-1 ml-2">
                <button
                  onClick={() => { setProductForm(product); setShowProductModal(true); }}
                  className="p-1 text-secondary hover:text-primary"
                  title="Edit"
                >
                  <PencilIcon className="h-4 w-4" />
                </button>
                <button
                  onClick={() => handleProductAction('delete-product', product.id)}
                  className="p-1 text-red-400 hover:text-red-600"
                  title="Delete"
                >
                  <TrashIcon className="h-4 w-4" />
                </button>
              </div>
            )}
          </div>
          
          {/* Price */}
          <div className="flex items-center space-x-2 mb-2">
            <span className="text-lg font-bold text-primary">
              {formatPrice(product.price)}
            </span>
            {product.compareAtPrice && product.compareAtPrice > product.price && (
              <span className="text-sm text-secondary line-through">
                {formatPrice(product.compareAtPrice)}
              </span>
            )}
          </div>
          
          {/* Rating and Reviews */}
          <div className="flex items-center space-x-2 mb-3">
            <div className="flex items-center">
              {[...Array(5)].map((_, i) => (
                <StarIconSolid
                  key={i}
                  className={`h-4 w-4 ${
                    i < Math.floor(product.rating) ? 'text-yellow-400' : 'text-gray-300'
                  }`}
                />
              ))}
            </div>
            <span className="text-sm text-secondary">
              {product.rating.toFixed(1)} ({product.reviewCount})
            </span>
          </div>
          
          {/* Store Info */}
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <BuildingStorefrontIcon className="h-4 w-4 text-secondary" />
              <span className="text-xs text-secondary">{product.store.name}</span>
              {product.store.verified && (
                <ShieldCheckIcon className="h-3 w-3 text-blue-500" />
              )}
            </div>
            
            {/* Stock Status */}
            <div className="text-right">
              {product.inventory > 0 ? (
                <span className="text-xs text-green-600">
                  {product.inventory} in stock
                </span>
              ) : (
                <span className="text-xs text-red-600">Out of stock</span>
              )}
            </div>
          </div>
          
          {/* Shipping Info */}
          {product.freeShipping && (
            <div className="mt-2 text-xs text-green-600 flex items-center">
              <TruckIcon className="h-3 w-3 mr-1" />
              Free shipping
            </div>
          )}
        </div>
      </motion.div>
    );
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
      >
        <div>
          <h1 className="text-3xl font-bold text-primary">Marketplace</h1>
          <p className="text-secondary mt-1">
            Buy and sell products with our comprehensive e-commerce platform
          </p>
        </div>
        <div className="flex items-center space-x-3">
          {/* Cart Icon */}
          <button className="relative p-2 text-secondary hover:text-primary">
            <ShoppingCartIcon className="h-6 w-6" />
            {cart.length > 0 && (
              <span className="absolute -top-1 -right-1 bg-accent-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {cart.length}
              </span>
            )}
          </button>
          
          <button
            onClick={() => setShowStoreSetup(true)}
            className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:bg-accent-secondary flex items-center space-x-2"
          >
            <BuildingStorefrontIcon className="h-5 w-5" />
            <span>Sell on Marketplace</span>
          </button>
        </div>
      </motion.div>

      {/* Stats Overview */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4"
      >
        {[
          { label: 'Products', value: formatNumber(marketplaceStats.totalProducts), icon: ShoppingBagIcon, color: 'bg-blue-500' },
          { label: 'Sellers', value: formatNumber(marketplaceStats.totalSellers), icon: UsersIcon, color: 'bg-green-500' },
          { label: 'Orders', value: formatNumber(marketplaceStats.totalOrders), icon: ReceiptPercentIcon, color: 'bg-purple-500' },
          { label: 'Revenue', value: formatPrice(marketplaceStats.totalRevenue), icon: CurrencyDollarIcon, color: 'bg-yellow-500' },
          { label: 'Avg Order', value: formatPrice(marketplaceStats.averageOrderValue), icon: ArrowTrendingUpIcon, color: 'bg-orange-500' },
          { label: 'Conversion', value: `${marketplaceStats.conversionRate}%`, icon: ChartBarIcon, color: 'bg-red-500' }
        ].map((stat, index) => (
          <div key={index} className="bg-surface p-4 rounded-lg">
            <div className="flex items-center">
              <div className={`flex-shrink-0 p-2 rounded-lg ${stat.color} mr-3`}>
                <stat.icon className="h-5 w-5 text-white" />
              </div>
              <div>
                <p className="text-xs font-medium text-secondary">{stat.label}</p>
                <p className="text-lg font-bold text-primary">{stat.value}</p>
              </div>
            </div>
          </div>
        ))}
      </motion.div>

      {/* Navigation Tabs */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.2 }}
        className="border-b border-default"
      >
        <nav className="flex space-x-8">
          {[
            { id: 'browse', name: 'Browse Products', icon: ShoppingBagIcon },
            { id: 'my-store', name: 'My Store', icon: BuildingStorefrontIcon },
            { id: 'orders', name: 'Orders', icon: ReceiptPercentIcon },
            { id: 'analytics', name: 'Analytics', icon: ChartBarIcon }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`flex items-center space-x-2 py-4 px-1 border-b-2 font-medium text-sm transition-colors ${
                activeTab === tab.id
                  ? 'border-accent-primary text-accent-primary'
                  : 'border-transparent text-secondary hover:text-primary hover:border-gray-300'
              }`}
            >
              <tab.icon className="h-5 w-5" />
              <span>{tab.name}</span>
            </button>
          ))}
        </nav>
      </motion.div>

      {/* Browse Products Tab */}
      {activeTab === 'browse' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="space-y-6"
        >
          {/* Search and Filters */}
          <div className="bg-surface p-6 rounded-lg">
            <div className="flex flex-col lg:flex-row gap-4">
              {/* Search */}
              <div className="flex-1">
                <div className="relative">
                  <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary" />
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    placeholder="Search products..."
                    className="w-full pl-10 pr-4 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                  />
                </div>
              </div>
              
              {/* Filters */}
              <div className="flex space-x-3">
                <select
                  value={selectedCategory}
                  onChange={(e) => setSelectedCategory(e.target.value)}
                  className="px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                >
                  <option value="all">All Categories</option>
                  {categories.map((category) => (
                    <option key={category.id} value={category.id}>
                      {category.name}
                    </option>
                  ))}
                </select>
                
                <select
                  value={sortBy}
                  onChange={(e) => setSortBy(e.target.value)}
                  className="px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                >
                  <option value="featured">Featured</option>
                  <option value="newest">Newest</option>
                  <option value="price-low">Price: Low to High</option>
                  <option value="price-high">Price: High to Low</option>
                  <option value="rating">Highest Rated</option>
                  <option value="bestselling">Best Selling</option>
                </select>
                
                <button
                  onClick={() => setShowFilters(!showFilters)}
                  className={`px-4 py-2 border rounded-lg flex items-center space-x-2 transition-colors ${
                    showFilters 
                      ? 'bg-accent-primary text-white border-accent-primary' 
                      : 'border-default text-secondary hover:text-primary'
                  }`}
                >
                  <FunnelIcon className="h-5 w-5" />
                  <span>Filters</span>
                </button>
              </div>
            </div>
            
            {/* Advanced Filters */}
            <AnimatePresence>
              {showFilters && (
                <motion.div
                  initial={{ opacity: 0, height: 0 }}
                  animate={{ opacity: 1, height: 'auto' }}
                  exit={{ opacity: 0, height: 0 }}
                  className="border-t border-default pt-4 mt-4"
                >
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {/* Price Range */}
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">
                        Price Range
                      </label>
                      <div className="flex space-x-2">
                        <input
                          type="number"
                          placeholder="Min"
                          value={priceRange.min}
                          onChange={(e) => setPriceRange(prev => ({ ...prev, min: parseFloat(e.target.value) || 0 }))}
                          className="w-1/2 px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                        />
                        <input
                          type="number"
                          placeholder="Max"
                          value={priceRange.max}
                          onChange={(e) => setPriceRange(prev => ({ ...prev, max: parseFloat(e.target.value) || 1000 }))}
                          className="w-1/2 px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                        />
                      </div>
                    </div>
                    
                    {/* Rating Filter */}
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">
                        Minimum Rating
                      </label>
                      <select
                        value={ratingFilter}
                        onChange={(e) => setRatingFilter(parseInt(e.target.value))}
                        className="w-full px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                      >
                        <option value={0}>Any Rating</option>
                        <option value={4}>4+ Stars</option>
                        <option value={3}>3+ Stars</option>
                        <option value={2}>2+ Stars</option>
                        <option value={1}>1+ Stars</option>
                      </select>
                    </div>
                    
                    {/* Additional Filters */}
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">
                        Product Type
                      </label>
                      <div className="space-y-2">
                        <label className="flex items-center">
                          <input type="checkbox" className="rounded" />
                          <span className="ml-2 text-sm text-secondary">Free Shipping</span>
                        </label>
                        <label className="flex items-center">
                          <input type="checkbox" className="rounded" />
                          <span className="ml-2 text-sm text-secondary">Digital Products</span>
                        </label>
                        <label className="flex items-center">
                          <input type="checkbox" className="rounded" />
                          <span className="ml-2 text-sm text-secondary">On Sale</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </motion.div>
              )}
            </AnimatePresence>
          </div>

          {/* Products Grid */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {products.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        </motion.div>
      )}

      {/* My Store Tab */}
      {activeTab === 'my-store' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="space-y-6"
        >
          {/* Store Stats */}
          <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            {[
              { label: 'Products', value: marketplaceStats.myStoreStats.products, icon: ShoppingBagIcon },
              { label: 'Orders', value: marketplaceStats.myStoreStats.orders, icon: ReceiptPercentIcon },
              { label: 'Revenue', value: formatPrice(marketplaceStats.myStoreStats.revenue), icon: CurrencyDollarIcon },
              { label: 'Rating', value: marketplaceStats.myStoreStats.rating.toFixed(1), icon: StarIcon },
              { label: 'Views', value: formatNumber(marketplaceStats.myStoreStats.views), icon: EyeIcon },
              { label: 'Favorites', value: formatNumber(marketplaceStats.myStoreStats.favorites), icon: HeartIcon }
            ].map((stat, index) => (
              <div key={index} className="bg-surface p-4 rounded-lg">
                <div className="flex items-center">
                  <stat.icon className="h-8 w-8 text-accent-primary mr-3" />
                  <div>
                    <p className="text-sm font-medium text-secondary">{stat.label}</p>
                    <p className="text-xl font-bold text-primary">{stat.value}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>

          {/* Add Product Button */}
          <div className="flex justify-end">
            <button
              onClick={() => setShowProductModal(true)}
              className="bg-accent-primary text-white px-6 py-2 rounded-lg hover:bg-accent-secondary flex items-center space-x-2"
            >
              <PlusIcon className="h-5 w-5" />
              <span>Add Product</span>
            </button>
          </div>

          {/* My Products Grid */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {myProducts.map((product) => (
              <ProductCard key={product.id} product={product} isMine={true} />
            ))}
          </div>
        </motion.div>
      )}

      {/* Orders Tab */}
      {activeTab === 'orders' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="space-y-6"
        >
          <div className="bg-surface p-6 rounded-lg">
            <h2 className="text-xl font-semibold text-primary mb-6">Recent Orders</h2>
            <div className="space-y-4">
              {orders.map((order) => (
                <div key={order.id} className="border border-default rounded-lg p-4">
                  <div className="flex justify-between items-start mb-3">
                    <div>
                      <h3 className="font-semibold text-primary">Order #{order.id}</h3>
                      <p className="text-sm text-secondary">{order.date}</p>
                    </div>
                    <span className={`px-3 py-1 rounded-full text-sm font-medium ${
                      order.status === 'delivered' ? 'bg-green-100 text-green-800' :
                      order.status === 'shipped' ? 'bg-blue-100 text-blue-800' :
                      order.status === 'processing' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-gray-100 text-gray-800'
                    }`}>
                      {order.status}
                    </span>
                  </div>
                  <div className="flex justify-between items-center">
                    <div>
                      <p className="text-sm text-secondary">
                        {order.itemCount} items • {formatPrice(order.total)}
                      </p>
                    </div>
                    <button className="text-accent-primary hover:text-accent-secondary text-sm font-medium">
                      View Details
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </motion.div>
      )}

      {/* Analytics Tab */}
      {activeTab === 'analytics' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="space-y-6"
        >
          <div className="bg-surface p-6 rounded-lg">
            <h2 className="text-xl font-semibold text-primary mb-6">Marketplace Analytics</h2>
            <div className="text-center py-12">
              <ChartBarIcon className="h-16 w-16 text-secondary mx-auto mb-4" />
              <p className="text-secondary">Comprehensive analytics dashboard coming soon...</p>
            </div>
          </div>
        </motion.div>
      )}

      {/* Store Setup Modal */}
      <AnimatePresence>
        {showStoreSetup && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 flex items-center justify-center p-4"
          >
            <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowStoreSetup(false)} />
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-surface rounded-lg shadow-xl max-w-2xl max-h-[90vh] overflow-auto relative z-10 w-full"
            >
              <div className="p-6">
                <div className="flex justify-between items-center mb-6">
                  <h2 className="text-2xl font-bold text-primary">Setup Your Store</h2>
                  <button
                    onClick={() => setShowStoreSetup(false)}
                    className="p-2 text-secondary hover:text-primary rounded-lg"
                  >
                    <XMarkIcon className="h-6 w-6" />
                  </button>
                </div>

                <div className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Store Name *</label>
                      <input
                        type="text"
                        value={storeProfile.storeName}
                        onChange={(e) => setStoreProfile(prev => ({ ...prev, storeName: e.target.value }))}
                        className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                        placeholder="Enter store name"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Business Type</label>
                      <select
                        value={storeProfile.businessType}
                        onChange={(e) => setStoreProfile(prev => ({ ...prev, businessType: e.target.value }))}
                        className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                      >
                        <option value="">Select business type</option>
                        <option value="individual">Individual</option>
                        <option value="business">Business</option>
                        <option value="corporation">Corporation</option>
                      </select>
                    </div>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Store Description *</label>
                    <textarea
                      value={storeProfile.description}
                      onChange={(e) => setStoreProfile(prev => ({ ...prev, description: e.target.value }))}
                      rows={4}
                      className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                      placeholder="Describe your store and products"
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Phone</label>
                      <input
                        type="tel"
                        value={storeProfile.phone}
                        onChange={(e) => setStoreProfile(prev => ({ ...prev, phone: e.target.value }))}
                        className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                        placeholder="Store phone number"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Website</label>
                      <input
                        type="url"
                        value={storeProfile.website}
                        onChange={(e) => setStoreProfile(prev => ({ ...prev, website: e.target.value }))}
                        className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                        placeholder="https://yourstore.com"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Business Address</label>
                    <textarea
                      value={storeProfile.address}
                      onChange={(e) => setStoreProfile(prev => ({ ...prev, address: e.target.value }))}
                      rows={3}
                      className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                      placeholder="Full business address"
                    />
                  </div>

                  <div className="flex justify-end space-x-3 pt-6 border-t border-default">
                    <button
                      onClick={() => setShowStoreSetup(false)}
                      className="px-4 py-2 text-secondary hover:text-primary"
                    >
                      Cancel
                    </button>
                    <button
                      onClick={handleStoreSetup}
                      disabled={loading}
                      className="bg-accent-primary text-white px-6 py-2 rounded-lg hover:bg-accent-secondary disabled:opacity-50"
                    >
                      {loading ? 'Setting up...' : 'Setup Store'}
                    </button>
                  </div>
                </div>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Product Creation/Edit Modal */}
      <AnimatePresence>
        {showProductModal && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 flex items-center justify-center p-4"
          >
            <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowProductModal(false)} />
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-surface rounded-lg shadow-xl max-w-4xl max-h-[90vh] overflow-auto relative z-10 w-full"
            >
              <div className="p-6">
                <div className="flex justify-between items-center mb-6">
                  <h2 className="text-2xl font-bold text-primary">
                    {selectedProduct ? 'Product Details' : 'Add New Product'}
                  </h2>
                  <button
                    onClick={() => setShowProductModal(false)}
                    className="p-2 text-secondary hover:text-primary rounded-lg"
                  >
                    <XMarkIcon className="h-6 w-6" />
                  </button>
                </div>

                {selectedProduct ? (
                  <div className="space-y-6">
                    {/* Product details view */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div className="aspect-square bg-gray-200 rounded-lg flex items-center justify-center">
                        {selectedProduct.images?.length > 0 ? (
                          <img
                            src={selectedProduct.images[0]}
                            alt={selectedProduct.title}
                            className="w-full h-full object-cover rounded-lg"
                          />
                        ) : (
                          <PhotoIcon className="h-24 w-24 text-secondary" />
                        )}
                      </div>
                      <div>
                        <h3 className="text-2xl font-bold text-primary mb-2">{selectedProduct.title}</h3>
                        <p className="text-secondary mb-4">{selectedProduct.description}</p>
                        <div className="space-y-2">
                          <div className="flex items-center space-x-2">
                            <span className="text-3xl font-bold text-primary">
                              {formatPrice(selectedProduct.price)}
                            </span>
                            {selectedProduct.compareAtPrice && (
                              <span className="text-lg text-secondary line-through">
                                {formatPrice(selectedProduct.compareAtPrice)}
                              </span>
                            )}
                          </div>
                          <div className="flex items-center space-x-2">
                            <div className="flex items-center">
                              {[...Array(5)].map((_, i) => (
                                <StarIconSolid
                                  key={i}
                                  className={`h-5 w-5 ${
                                    i < Math.floor(selectedProduct.rating) 
                                      ? 'text-yellow-400' 
                                      : 'text-gray-300'
                                  }`}
                                />
                              ))}
                            </div>
                            <span className="text-secondary">
                              {selectedProduct.rating.toFixed(1)} ({selectedProduct.reviewCount} reviews)
                            </span>
                          </div>
                        </div>
                        <div className="flex space-x-3 mt-6">
                          <button
                            onClick={() => handleProductAction('add-to-cart', selectedProduct.id)}
                            className="bg-accent-primary text-white px-6 py-2 rounded-lg hover:bg-accent-secondary flex items-center space-x-2"
                          >
                            <ShoppingCartIcon className="h-5 w-5" />
                            <span>Add to Cart</span>
                          </button>
                          <button
                            onClick={() => handleProductAction('buy-now', selectedProduct.id)}
                            className="bg-accent-secondary text-white px-6 py-2 rounded-lg hover:bg-accent-primary"
                          >
                            Buy Now
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                ) : (
                  <div className="space-y-4">
                    {/* Product creation form */}
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Product Title *</label>
                        <input
                          type="text"
                          value={productForm.title}
                          onChange={(e) => setProductForm(prev => ({ ...prev, title: e.target.value }))}
                          className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                          placeholder="Enter product title"
                        />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Category *</label>
                        <select
                          value={productForm.category}
                          onChange={(e) => setProductForm(prev => ({ ...prev, category: e.target.value }))}
                          className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                        >
                          <option value="">Select category</option>
                          {categories.map((category) => (
                            <option key={category.id} value={category.id}>
                              {category.name}
                            </option>
                          ))}
                        </select>
                      </div>
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Description *</label>
                      <textarea
                        value={productForm.description}
                        onChange={(e) => setProductForm(prev => ({ ...prev, description: e.target.value }))}
                        rows={4}
                        className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                        placeholder="Describe your product"
                      />
                    </div>

                    <div className="grid grid-cols-4 gap-4">
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Price ($)</label>
                        <input
                          type="number"
                          value={productForm.price}
                          onChange={(e) => setProductForm(prev => ({ ...prev, price: parseFloat(e.target.value) || 0 }))}
                          className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                          min="0"
                          step="0.01"
                        />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Compare Price ($)</label>
                        <input
                          type="number"
                          value={productForm.compareAtPrice}
                          onChange={(e) => setProductForm(prev => ({ ...prev, compareAtPrice: parseFloat(e.target.value) || 0 }))}
                          className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                          min="0"
                          step="0.01"
                        />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">SKU</label>
                        <input
                          type="text"
                          value={productForm.sku}
                          onChange={(e) => setProductForm(prev => ({ ...prev, sku: e.target.value }))}
                          className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                          placeholder="Product SKU"
                        />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Inventory</label>
                        <input
                          type="number"
                          value={productForm.inventory}
                          onChange={(e) => setProductForm(prev => ({ ...prev, inventory: parseInt(e.target.value) || 0 }))}
                          className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                          min="0"
                        />
                      </div>
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Product Images</label>
                      <div className="border-2 border-dashed border-default rounded-lg p-6 text-center">
                        <PhotoIcon className="h-8 w-8 text-secondary mx-auto mb-2" />
                        <p className="text-secondary">Drag & drop images here or click to browse</p>
                        <input
                          type="file"
                          multiple
                          accept="image/*"
                          onChange={(e) => setProductForm(prev => ({ 
                            ...prev, 
                            images: Array.from(e.target.files)
                          }))}
                          className="hidden"
                          id="product-images"
                        />
                        <label
                          htmlFor="product-images"
                          className="mt-2 inline-block px-4 py-2 bg-accent-primary text-white rounded-lg cursor-pointer hover:bg-accent-secondary"
                        >
                          Choose Files
                        </label>
                      </div>
                    </div>

                    <div className="flex items-center space-x-2">
                      <input
                        type="checkbox"
                        id="digital-product"
                        checked={productForm.isDigital}
                        onChange={(e) => setProductForm(prev => ({ ...prev, isDigital: e.target.checked }))}
                        className="rounded"
                      />
                      <label htmlFor="digital-product" className="text-sm text-secondary">
                        This is a digital product
                      </label>
                    </div>

                    <div className="flex justify-end space-x-3 pt-6 border-t border-default">
                      <button
                        onClick={() => setShowProductModal(false)}
                        className="px-4 py-2 text-secondary hover:text-primary"
                      >
                        Cancel
                      </button>
                      <button
                        onClick={handleCreateProduct}
                        disabled={loading}
                        className="bg-accent-primary text-white px-6 py-2 rounded-lg hover:bg-accent-secondary disabled:opacity-50"
                      >
                        {loading ? 'Creating...' : 'Add Product'}
                      </button>
                    </div>
                  </div>
                )}
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default AmazonStyleMarketplace;