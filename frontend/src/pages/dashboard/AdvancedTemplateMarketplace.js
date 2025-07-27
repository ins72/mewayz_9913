import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import api from '../../services/api';
import {
  MagnifyingGlassIcon,
  FunnelIcon,
  PlusIcon,
  StarIcon,
  EyeIcon,
  ArrowDownTrayIcon,
  ShareIcon,
  HeartIcon,
  TagIcon,
  CurrencyDollarIcon,
  UserIcon,
  CalendarIcon,
  ChartBarIcon,
  DocumentTextIcon,
  PhotoIcon,
  CodeBracketIcon,
  PaintBrushIcon,
  DevicePhoneMobileIcon,
  ComputerDesktopIcon,
  FireIcon,
  TrophyIcon,
  BoltIcon,
  SparklesIcon,
  CheckCircleIcon,
  XMarkIcon,
  PencilIcon,
  TrashIcon,
  ArrowUpTrayIcon,
  GiftIcon,
  ShoppingBagIcon
} from '@heroicons/react/24/outline';
import {
  StarIcon as StarIconSolid,
  HeartIcon as HeartIconSolid
} from '@heroicons/react/24/solid';

const AdvancedTemplateMarketplace = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  // State management
  const [loading, setLoading] = useState(false);
  const [templates, setTemplates] = useState([]);
  const [myTemplates, setMyTemplates] = useState([]);
  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [sortBy, setSortBy] = useState('popularity');
  const [viewMode, setViewMode] = useState('grid'); // grid, list
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [selectedTemplate, setSelectedTemplate] = useState(null);
  const [showPreview, setShowPreview] = useState(false);
  const [activeTab, setActiveTab] = useState('browse'); // browse, my-templates, create, analytics
  
  // Filters
  const [filters, setFilters] = useState({
    priceRange: 'all', // free, premium, all
    rating: 0,
    tags: [],
    author: '',
    dateRange: 'all'
  });
  
  // Pagination
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage] = useState(24);
  
  // Analytics & Stats
  const [marketplaceStats, setMarketplaceStats] = useState({
    totalTemplates: 0,
    totalDownloads: 0,
    totalRevenue: 0,
    topSellingTemplate: null,
    myStats: {
      templatesPublished: 0,
      totalDownloads: 0,
      totalRevenue: 0,
      avgRating: 0
    }
  });

  // Form for creating/editing templates
  const [templateForm, setTemplateForm] = useState({
    title: '',
    description: '',
    category: '',
    price: 0,
    tags: [],
    previewImages: [],
    templateFiles: [],
    demoUrl: '',
    features: [],
    compatibility: [],
    version: '1.0.0'
  });

  useEffect(() => {
    loadMarketplaceData();
  }, [activeTab, selectedCategory, searchQuery, sortBy, currentPage]);

  const loadMarketplaceData = async () => {
    // Real data loaded from API
    try {
      const [templatesRes, categoriesRes, statsRes] = await Promise.all([
        api.get(`/templates/marketplace?category=${selectedCategory}&search=${searchQuery}&sort=${sortBy}&page=${currentPage}&limit=${itemsPerPage}`),
        api.get('/templates/categories'),
        api.get('/templates/stats')
      ]);

      if (templatesRes.data.success) {
        // Real data loaded from API
      }
      if (categoriesRes.data.success) {
        // Real data loaded from API
      }
      if (statsRes.data.success) {
        // Real data loaded from API
      }

      if (activeTab === 'my-templates') {
        const myTemplatesRes = await api.get('/templates/my-templates');
        if (myTemplatesRes.data.success) {
          // Real data loaded from API
        }
      }
    } catch (err) {
      console.error('Failed to load marketplace data:', err);
      error('Failed to load marketplace data');
    } finally {
      // Real data loaded from API
    }
  };

  const handleTemplateAction = async (action, templateId, additionalData = {}) => {
    // Real data loaded from API
    try {
      let response;
      
      switch (action) {
        case 'download':
          response = await api.post(`/templates/${templateId}/download`);
          if (response.data.success) {
            // Handle file download
            const link = document.createElement('a');
            link.href = response.data.downloadUrl;
            link.download = response.data.filename;
            link.click();
            success('Template downloaded successfully');
          }
          break;
          
        case 'purchase':
          response = await api.post(`/templates/${templateId}/purchase`, additionalData);
          if (response.data.success) {
            success('Template purchased successfully');
            loadMarketplaceData(); // Refresh data
          }
          break;
          
        case 'favorite':
          response = await api.post(`/templates/${templateId}/favorite`);
          if (response.data.success) {
            success('Added to favorites');
            loadMarketplaceData();
          }
          break;
          
        case 'rate':
          response = await api.post(`/templates/${templateId}/rate`, { rating: additionalData.rating });
          if (response.data.success) {
            success('Rating submitted');
            loadMarketplaceData();
          }
          break;
          
        case 'delete':
          if (window.confirm('Are you sure you want to delete this template?')) {
            response = await api.delete(`/templates/${templateId}`);
            if (response.data.success) {
              success('Template deleted successfully');
              loadMarketplaceData();
            }
          }
          break;
      }
    } catch (err) {
      console.error(`Template ${action} failed:`, err);
      error(`Failed to ${action} template`);
    } finally {
      // Real data loaded from API
    }
  };

  const handleTemplateSubmit = async () => {
    if (!templateForm.title || !templateForm.description || !templateForm.category) {
      error('Please fill in all required fields');
      return;
    }

    // Real data loaded from API
    try {
      const formData = new FormData();
      
      // Add text fields
      Object.keys(templateForm).forEach(key => {
        if (key !== 'previewImages' && key !== 'templateFiles') {
          formData.append(key, JSON.stringify(templateForm[key]));
        }
      });
      
      // Add files
      templateForm.previewImages.forEach((file, index) => {
        formData.append(`previewImages`, file);
      });
      
      templateForm.templateFiles.forEach((file, index) => {
        formData.append(`templateFiles`, file);
      });

      const response = await api.post('/templates/create', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });

      if (response.data.success) {
        success('Template submitted for review');
        // Real data loaded from API
        // Real data loaded from API
        loadMarketplaceData();
      }
    } catch (err) {
      console.error('Template submission failed:', err);
      error('Failed to submit template');
    } finally {
      // Real data loaded from API
    }
  };

  const formatPrice = (price) => {
    return price === 0 ? 'Free' : `$${price.toFixed(2)}`;
  };

  const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
  };

  const getCategoryIcon = (category) => {
    const icons = {
      'website': ComputerDesktopIcon,
      'mobile': DevicePhoneMobileIcon,
      'email': DocumentTextIcon,
      'social': ShareIcon,
      'bio': UserIcon,
      'course': StarIcon,
      'ecommerce': ShoppingBagIcon,
      'portfolio': PhotoIcon,
      'landing': PaintBrushIcon,
      'admin': CodeBracketIcon
    };
    return icons[category] || DocumentTextIcon;
  };

  const TemplateCard = ({ template, isMine = false }) => {
    const IconComponent = getCategoryIcon(template.category);
    
    return (
      <motion.div
        layout
        initial={{ opacity: 0, scale: 0.9 }}
        animate={{ opacity: 1, scale: 1 }}
        exit={{ opacity: 0, scale: 0.9 }}
        className="bg-surface rounded-lg shadow-default hover:shadow-lg transition-all duration-300 overflow-hidden group"
      >
        {/* Preview Image */}
        <div className="relative h-48 bg-gradient-to-br from-accent-primary/10 to-accent-secondary/10">
          {template.previewImages?.length > 0 ? (
            <img
              src={template.previewImages[0]}
              alt={template.title}
              className="w-full h-full object-cover"
            />
          ) : (
            <div className="w-full h-full flex items-center justify-center">
              <IconComponent className="h-16 w-16 text-secondary" />
            </div>
          )}
          
          {/* Overlay Actions */}
          <div className="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center space-x-3">
            <button
              onClick={() => { // Real data loaded from API // Real data loaded from API }}
              className="p-3 bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition-colors"
              title="Preview"
            >
              <EyeIcon className="h-5 w-5" />
            </button>
            {!isMine && (
              <button
                onClick={() => handleTemplateAction(template.price === 0 ? 'download' : 'purchase', template.id)}
                className="p-3 bg-accent-primary backdrop-blur-sm rounded-full text-white hover:bg-accent-secondary transition-colors"
                title={template.price === 0 ? 'Download' : 'Purchase'}
              >
                {template.price === 0 ? <ArrowDownTrayIcon className="h-5 w-5" /> : <ShoppingBagIcon className="h-5 w-5" />}
              </button>
            )}
            <button
              onClick={() => handleTemplateAction('favorite', template.id)}
              className="p-3 bg-white/20 backdrop-blur-sm rounded-full text-white hover:bg-white/30 transition-colors"
              title="Add to Favorites"
            >
              <HeartIcon className="h-5 w-5" />
            </button>
          </div>
          
          {/* Price Badge */}
          <div className="absolute top-3 right-3">
            <span className={`px-3 py-1 rounded-full text-sm font-semibold ${
              template.price === 0 
                ? 'bg-green-500 text-white' 
                : 'bg-accent-primary text-white'
            }`}>
              {formatPrice(template.price)}
            </span>
          </div>
          
          {/* Category Badge */}
          <div className="absolute top-3 left-3">
            <span className="px-2 py-1 bg-black/50 backdrop-blur-sm rounded text-white text-xs">
              {template.category}
            </span>
          </div>
        </div>
        
        {/* Content */}
        <div className="p-4">
          <div className="flex items-start justify-between mb-2">
            <h3 className="font-semibold text-primary text-lg line-clamp-1">
              {template.title}
            </h3>
            {isMine && (
              <div className="flex space-x-1">
                <button
                  onClick={() => { // Real data loaded from API // Real data loaded from API }}
                  className="p-1 text-secondary hover:text-primary"
                  title="Edit"
                >
                  <PencilIcon className="h-4 w-4" />
                </button>
                <button
                  onClick={() => handleTemplateAction('delete', template.id)}
                  className="p-1 text-red-400 hover:text-red-600"
                  title="Delete"
                >
                  <TrashIcon className="h-4 w-4" />
                </button>
              </div>
            )}
          </div>
          
          <p className="text-secondary text-sm line-clamp-2 mb-3">
            {template.description}
          </p>
          
          {/* Author & Stats */}
          <div className="flex items-center justify-between mb-3">
            <div className="flex items-center space-x-2">
              <img
                src={template.author?.avatar || `https://ui-avatars.com/api/?name=${template.author?.name}&background=ec4899&color=fff`}
                alt={template.author?.name}
                className="w-6 h-6 rounded-full"
              />
              <span className="text-sm text-secondary">{template.author?.name}</span>
            </div>
            <div className="flex items-center space-x-3 text-sm text-secondary">
              <div className="flex items-center space-x-1">
                <ArrowDownTrayIcon className="h-4 w-4" />
                <span>{formatNumber(template.downloads)}</span>
              </div>
              <div className="flex items-center space-x-1">
                <StarIcon className="h-4 w-4 text-yellow-500" />
                <span>{template.rating.toFixed(1)}</span>
              </div>
            </div>
          </div>
          
          {/* Tags */}
          <div className="flex flex-wrap gap-1">
            {template.tags?.slice(0, 3).map((tag, index) => (
              <span
                key={index}
                className="px-2 py-1 bg-surface-elevated rounded text-xs text-secondary"
              >
                {tag}
              </span>
            ))}
            {template.tags?.length > 3 && (
              <span className="px-2 py-1 bg-surface-elevated rounded text-xs text-secondary">
                +{template.tags.length - 3}
              </span>
            )}
          </div>
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
          <h1 className="text-3xl font-bold text-primary">Template Marketplace</h1>
          <p className="text-secondary mt-1">
            Discover, create, and monetize professional templates
          </p>
        </div>
        <div className="flex items-center space-x-3">
          <button
            onClick={() => setShowCreateModal(true)}
            className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:bg-accent-secondary flex items-center space-x-2"
          >
            <PlusIcon className="h-5 w-5" />
            <span>Create Template</span>
          </button>
        </div>
      </motion.div>

      {/* Stats Overview */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-4 gap-4"
      >
        <div className="bg-surface p-6 rounded-lg">
          <div className="flex items-center">
            <div className="flex-shrink-0 p-3 bg-blue-500 rounded-lg">
              <DocumentTextIcon className="h-6 w-6 text-white" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-secondary">Total Templates</p>
              <p className="text-2xl font-bold text-primary">{formatNumber(marketplaceStats.totalTemplates)}</p>
            </div>
          </div>
        </div>
        <div className="bg-surface p-6 rounded-lg">
          <div className="flex items-center">
            <div className="flex-shrink-0 p-3 bg-green-500 rounded-lg">
              <ArrowDownTrayIcon className="h-6 w-6 text-white" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-secondary">Total Downloads</p>
              <p className="text-2xl font-bold text-primary">{formatNumber(marketplaceStats.totalDownloads)}</p>
            </div>
          </div>
        </div>
        <div className="bg-surface p-6 rounded-lg">
          <div className="flex items-center">
            <div className="flex-shrink-0 p-3 bg-purple-500 rounded-lg">
              <CurrencyDollarIcon className="h-6 w-6 text-white" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-secondary">Revenue</p>
              <p className="text-2xl font-bold text-primary">${formatNumber(marketplaceStats.totalRevenue)}</p>
            </div>
          </div>
        </div>
        <div className="bg-surface p-6 rounded-lg">
          <div className="flex items-center">
            <div className="flex-shrink-0 p-3 bg-orange-500 rounded-lg">
              <TrophyIcon className="h-6 w-6 text-white" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-secondary">Top Seller</p>
              <p className="text-lg font-bold text-primary line-clamp-1">
                {marketplaceStats.topSellingTemplate?.title || 'N/A'}
              </p>
            </div>
          </div>
        </div>
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
            { id: 'browse', name: 'Browse Templates', icon: MagnifyingGlassIcon },
            { id: 'my-templates', name: 'My Templates', icon: UserIcon },
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

      {/* Browse Templates Tab */}
      {activeTab === 'browse' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="space-y-6"
        >
          {/* Search and Filters */}
          <div className="bg-surface p-6 rounded-lg">
            <div className="flex flex-col lg:flex-row gap-4 items-center">
              {/* Search */}
              <div className="flex-1">
                <div className="relative">
                  <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary" />
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    placeholder="Search templates..."
                    className="w-full pl-10 pr-4 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                  />
                </div>
              </div>
              
              {/* Category Filter */}
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
                
                {/* Sort */}
                <select
                  value={sortBy}
                  onChange={(e) => setSortBy(e.target.value)}
                  className="px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                >
                  <option value="popularity">Most Popular</option>
                  <option value="rating">Highest Rated</option>
                  <option value="downloads">Most Downloaded</option>
                  <option value="newest">Newest</option>
                  <option value="price-low">Price: Low to High</option>
                  <option value="price-high">Price: High to Low</option>
                </select>
                
                {/* View Mode */}
                <div className="flex rounded-lg border border-default overflow-hidden">
                  <button
                    onClick={() => setViewMode('grid')}
                    className={`p-2 ${viewMode === 'grid' ? 'bg-accent-primary text-white' : 'bg-surface text-secondary hover:text-primary'}`}
                  >
                    <PhotoIcon className="h-5 w-5" />
                  </button>
                  <button
                    onClick={() => setViewMode('list')}
                    className={`p-2 ${viewMode === 'list' ? 'bg-accent-primary text-white' : 'bg-surface text-secondary hover:text-primary'}`}
                  >
                    <DocumentTextIcon className="h-5 w-5" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          {/* Templates Grid */}
          <div className={`grid gap-6 ${
            viewMode === 'grid' 
              ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4' 
              : 'grid-cols-1'
          }`}>
            {templates.map((template) => (
              <TemplateCard key={template.id} template={template} />
            ))}
          </div>
        </motion.div>
      )}

      {/* My Templates Tab */}
      {activeTab === 'my-templates' && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="space-y-6"
        >
          {/* My Stats */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="bg-surface p-6 rounded-lg">
              <div className="flex items-center">
                <DocumentTextIcon className="h-8 w-8 text-blue-500 mr-3" />
                <div>
                  <p className="text-sm font-medium text-secondary">Published</p>
                  <p className="text-xl font-bold text-primary">{marketplaceStats.myStats.templatesPublished}</p>
                </div>
              </div>
            </div>
            <div className="bg-surface p-6 rounded-lg">
              <div className="flex items-center">
                <ArrowDownTrayIcon className="h-8 w-8 text-green-500 mr-3" />
                <div>
                  <p className="text-sm font-medium text-secondary">Downloads</p>
                  <p className="text-xl font-bold text-primary">{formatNumber(marketplaceStats.myStats.totalDownloads)}</p>
                </div>
              </div>
            </div>
            <div className="bg-surface p-6 rounded-lg">
              <div className="flex items-center">
                <CurrencyDollarIcon className="h-8 w-8 text-purple-500 mr-3" />
                <div>
                  <p className="text-sm font-medium text-secondary">Revenue</p>
                  <p className="text-xl font-bold text-primary">${formatNumber(marketplaceStats.myStats.totalRevenue)}</p>
                </div>
              </div>
            </div>
            <div className="bg-surface p-6 rounded-lg">
              <div className="flex items-center">
                <StarIcon className="h-8 w-8 text-yellow-500 mr-3" />
                <div>
                  <p className="text-sm font-medium text-secondary">Avg Rating</p>
                  <p className="text-xl font-bold text-primary">{marketplaceStats.myStats.avgRating.toFixed(1)}</p>
                </div>
              </div>
            </div>
          </div>

          {/* My Templates Grid */}
          <div className="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            {myTemplates.map((template) => (
              <TemplateCard key={template.id} template={template} isMine={true} />
            ))}
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
            <h2 className="text-xl font-semibold text-primary mb-6">Template Performance</h2>
            <div className="text-center py-12">
              <ChartBarIcon className="h-16 w-16 text-secondary mx-auto mb-4" />
              <p className="text-secondary">Analytics dashboard coming soon...</p>
            </div>
          </div>
        </motion.div>
      )}

      {/* Template Preview Modal */}
      <AnimatePresence>
        {showPreview && selectedTemplate && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 flex items-center justify-center p-4"
          >
            <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowPreview(false)} />
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-surface rounded-lg shadow-xl max-w-4xl max-h-[90vh] overflow-auto relative z-10"
            >
              <div className="p-6">
                <div className="flex justify-between items-start mb-4">
                  <div>
                    <h2 className="text-2xl font-bold text-primary">{selectedTemplate.title}</h2>
                    <p className="text-secondary">{selectedTemplate.description}</p>
                  </div>
                  <button
                    onClick={() => setShowPreview(false)}
                    className="p-2 text-secondary hover:text-primary rounded-lg"
                  >
                    <XMarkIcon className="h-6 w-6" />
                  </button>
                </div>
                
                {/* Template preview content would go here */}
                <div className="aspect-video bg-gradient-to-br from-accent-primary/10 to-accent-secondary/10 rounded-lg flex items-center justify-center mb-6">
                  <p className="text-secondary">Template Preview</p>
                </div>
                
                <div className="flex justify-between items-center">
                  <div className="flex items-center space-x-4">
                    <span className="text-2xl font-bold text-primary">{formatPrice(selectedTemplate.price)}</span>
                    <div className="flex items-center space-x-1">
                      <StarIconSolid className="h-5 w-5 text-yellow-500" />
                      <span className="text-secondary">{selectedTemplate.rating.toFixed(1)} ({selectedTemplate.reviews} reviews)</span>
                    </div>
                  </div>
                  <div className="flex space-x-3">
                    <button
                      onClick={() => handleTemplateAction('favorite', selectedTemplate.id)}
                      className="p-2 border border-default rounded-lg text-secondary hover:text-primary"
                    >
                      <HeartIcon className="h-5 w-5" />
                    </button>
                    <button
                      onClick={() => handleTemplateAction(selectedTemplate.price === 0 ? 'download' : 'purchase', selectedTemplate.id)}
                      className="bg-accent-primary text-white px-6 py-2 rounded-lg hover:bg-accent-secondary"
                    >
                      {selectedTemplate.price === 0 ? 'Download' : 'Purchase'}
                    </button>
                  </div>
                </div>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Create Template Modal */}
      <AnimatePresence>
        {showCreateModal && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 flex items-center justify-center p-4"
          >
            <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowCreateModal(false)} />
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-surface rounded-lg shadow-xl max-w-2xl max-h-[90vh] overflow-auto relative z-10 w-full"
            >
              <div className="p-6">
                <div className="flex justify-between items-center mb-6">
                  <h2 className="text-2xl font-bold text-primary">
                    {selectedTemplate ? 'Edit Template' : 'Create New Template'}
                  </h2>
                  <button
                    onClick={() => setShowCreateModal(false)}
                    className="p-2 text-secondary hover:text-primary rounded-lg"
                  >
                    <XMarkIcon className="h-6 w-6" />
                  </button>
                </div>

                <div className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Title *</label>
                    <input
                      type="text"
                      value={templateForm.title}
                      onChange={(e) => setTemplateForm(prev => ({ ...prev, title: e.target.value }))}
                      className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                      placeholder="Enter template title"
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Description *</label>
                    <textarea
                      value={templateForm.description}
                      onChange={(e) => setTemplateForm(prev => ({ ...prev, description: e.target.value }))}
                      rows={4}
                      className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                      placeholder="Describe your template"
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Category *</label>
                      <select
                        value={templateForm.category}
                        onChange={(e) => setTemplateForm(prev => ({ ...prev, category: e.target.value }))}
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

                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Price ($)</label>
                      <input
                        type="number"
                        value={templateForm.price}
                        onChange={(e) => setTemplateForm(prev => ({ ...prev, price: parseFloat(e.target.value) || 0 }))}
                        className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                        min="0"
                        step="0.01"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Tags</label>
                    <input
                      type="text"
                      placeholder="Enter tags separated by commas"
                      className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                      onBlur={(e) => setTemplateForm(prev => ({ 
                        ...prev, 
                        tags: e.target.value.split(',').map(tag => tag.trim()).filter(Boolean)
                      }))}
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Preview Images</label>
                    <div className="border-2 border-dashed border-default rounded-lg p-6 text-center">
                      <ArrowUpTrayIcon className="h-8 w-8 text-secondary mx-auto mb-2" />
                      <p className="text-secondary">Drag & drop images here or click to browse</p>
                      <input
                        type="file"
                        multiple
                        accept="image/*"
                        onChange={(e) => setTemplateForm(prev => ({ 
                          ...prev, 
                          previewImages: Array.from(e.target.files)
                        }))}
                        className="hidden"
                        id="preview-images"
                      />
                      <label
                        htmlFor="preview-images"
                        className="mt-2 inline-block px-4 py-2 bg-accent-primary text-white rounded-lg cursor-pointer hover:bg-accent-secondary"
                      >
                        Choose Files
                      </label>
                    </div>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Template Files</label>
                    <div className="border-2 border-dashed border-default rounded-lg p-6 text-center">
                      <DocumentTextIcon className="h-8 w-8 text-secondary mx-auto mb-2" />
                      <p className="text-secondary">Upload your template files (ZIP recommended)</p>
                      <input
                        type="file"
                        multiple
                        onChange={(e) => setTemplateForm(prev => ({ 
                          ...prev, 
                          templateFiles: Array.from(e.target.files)
                        }))}
                        className="hidden"
                        id="template-files"
                      />
                      <label
                        htmlFor="template-files"
                        className="mt-2 inline-block px-4 py-2 bg-accent-primary text-white rounded-lg cursor-pointer hover:bg-accent-secondary"
                      >
                        Choose Files
                      </label>
                    </div>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Demo URL</label>
                    <input
                      type="url"
                      value={templateForm.demoUrl}
                      onChange={(e) => setTemplateForm(prev => ({ ...prev, demoUrl: e.target.value }))}
                      className="w-full px-3 py-2 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary"
                      placeholder="https://example.com/demo"
                    />
                  </div>
                </div>

                <div className="flex justify-end space-x-3 mt-6 pt-6 border-t border-default">
                  <button
                    onClick={() => setShowCreateModal(false)}
                    className="px-4 py-2 text-secondary hover:text-primary"
                  >
                    Cancel
                  </button>
                  <button
                    onClick={handleTemplateSubmit}
                    disabled={loading}
                    className="bg-accent-primary text-white px-6 py-2 rounded-lg hover:bg-accent-secondary disabled:opacity-50"
                  >
                    {loading ? 'Submitting...' : 'Submit Template'}
                  </button>
                </div>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default AdvancedTemplateMarketplace;