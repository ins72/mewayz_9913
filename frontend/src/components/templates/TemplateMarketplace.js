import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import {
  DocumentTextIcon,
  PlusIcon,
  MagnifyingGlassIcon,
  FunnelIcon,
  StarIcon,
  EyeIcon,
  ArrowDownTrayIcon,
  ShoppingCartIcon,
  TagIcon,
  UserIcon,
  ChartBarIcon,
  CreditCardIcon,
  HeartIcon,
  ShareIcon
} from '@heroicons/react/24/outline';
import {
  StarIcon as StarIconSolid
} from '@heroicons/react/24/solid';

const TemplateMarketplace = () => {
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('browse');
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [sortBy, setSortBy] = useState('popular');

  // Mock template data
  const [templates, setTemplates] = useState([
    {
      id: '1',
      title: 'Modern Business Website Template',
      description: 'A clean and professional website template perfect for small businesses and startups.',
      category: 'website',
      type: 'premium',
      price: 29.99,
      originalPrice: 49.99,
      rating: 4.8,
      reviews: 156,
      downloads: 1240,
      author: {
        name: 'Sarah Design Studio',
        avatar: null,
        verified: true
      },
      preview: '/templates/business-website-preview.jpg',
      tags: ['business', 'modern', 'responsive', 'clean'],
      features: ['Responsive Design', '10+ Pages', 'Dark/Light Mode', 'Contact Forms'],
      lastUpdated: '2025-07-15',
      isFavorited: false,
      discount: 40
    },
    {
      id: '2',
      title: 'Email Marketing Campaign Kit',
      description: 'Complete email marketing templates for newsletters, promotions, and automated campaigns.',
      category: 'email',
      type: 'premium',
      price: 19.99,
      rating: 4.9,
      reviews: 89,
      downloads: 892,
      author: {
        name: 'Marketing Pro',
        avatar: null,
        verified: true
      },
      preview: '/templates/email-kit-preview.jpg',
      tags: ['email', 'marketing', 'newsletter', 'campaigns'],
      features: ['20+ Templates', 'Mobile Optimized', 'All Platforms', 'A/B Test Ready'],
      lastUpdated: '2025-07-18',
      isFavorited: true,
      discount: 0
    },
    {
      id: '3',
      title: 'Social Media Content Pack',
      description: 'Instagram story and post templates with professional graphics and layouts.',
      category: 'social',
      type: 'free',
      price: 0,
      rating: 4.6,
      reviews: 234,
      downloads: 3456,
      author: {
        name: 'Creative Collective',
        avatar: null,
        verified: false
      },
      preview: '/templates/social-pack-preview.jpg',
      tags: ['instagram', 'stories', 'posts', 'graphics'],
      features: ['50+ Templates', 'Editable Graphics', 'Story & Post Size', 'PSD Files'],
      lastUpdated: '2025-07-10',
      isFavorited: false,
      discount: 0
    },
    {
      id: '4',
      title: 'Course Landing Page Template',
      description: 'High-converting landing page template designed specifically for online courses.',
      category: 'landing',
      type: 'premium',
      price: 39.99,
      rating: 4.7,
      reviews: 67,
      downloads: 445,
      author: {
        name: 'Conversion Kings',
        avatar: null,
        verified: true
      },
      preview: '/templates/course-landing-preview.jpg',
      tags: ['course', 'landing', 'conversion', 'education'],
      features: ['High Conversion', 'Payment Integration', 'Mobile Ready', 'SEO Optimized'],
      lastUpdated: '2025-07-12',
      isFavorited: false,
      discount: 20
    }
  ]);

  const categories = [
    { id: 'all', name: 'All Categories', count: templates.length },
    { id: 'website', name: 'Website Templates', count: templates.filter(t => t.category === 'website').length },
    { id: 'email', name: 'Email Templates', count: templates.filter(t => t.category === 'email').length },
    { id: 'social', name: 'Social Media', count: templates.filter(t => t.category === 'social').length },
    { id: 'landing', name: 'Landing Pages', count: templates.filter(t => t.category === 'landing').length },
    { id: 'bio', name: 'Link in Bio', count: 0 },
    { id: 'form', name: 'Form Templates', count: 0 }
  ];

  const myTemplates = [
    {
      id: 'my1',
      title: 'My Custom Newsletter Template',
      category: 'email',
      status: 'published',
      sales: 23,
      revenue: 459.77,
      rating: 4.3,
      reviews: 12,
      lastSale: '2025-07-19'
    },
    {
      id: 'my2',
      title: 'Personal Brand Website',
      category: 'website',
      status: 'draft',
      sales: 0,
      revenue: 0,
      rating: 0,
      reviews: 0,
      lastSale: null
    }
  ];

  const filteredTemplates = templates.filter(template => {
    const matchesSearch = template.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         template.description.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         template.tags.some(tag => tag.toLowerCase().includes(searchQuery.toLowerCase()));
    const matchesCategory = selectedCategory === 'all' || template.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  const sortedTemplates = [...filteredTemplates].sort((a, b) => {
    switch (sortBy) {
      case 'popular':
        return b.downloads - a.downloads;
      case 'rating':
        return b.rating - a.rating;
      case 'price_low':
        return a.price - b.price;
      case 'price_high':
        return b.price - a.price;
      case 'newest':
        return new Date(b.lastUpdated) - new Date(a.lastUpdated);
      default:
        return 0;
    }
  });

  const renderStars = (rating) => {
    return [...Array(5)].map((_, i) => (
      <StarIconSolid
        key={i}
        className={`h-4 w-4 ${i < Math.floor(rating) ? 'text-yellow-500' : 'text-gray-300'}`}
      />
    ));
  };

  const renderBrowseTemplates = () => (
    <div className="space-y-6">
      {/* Search and Filters */}
      <div className="bg-surface p-6 rounded-lg shadow-default">
        <div className="grid grid-cols-1 lg:grid-cols-5 gap-4">
          <div className="lg:col-span-2">
            <div className="relative">
              <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search templates..."
                className="input w-full pl-10"
              />
            </div>
          </div>
          
          <select
            value={selectedCategory}
            onChange={(e) => setSelectedCategory(e.target.value)}
            className="input"
          >
            {categories.map(category => (
              <option key={category.id} value={category.id}>
                {category.name} ({category.count})
              </option>
            ))}
          </select>
          
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
            className="input"
          >
            <option value="popular">Most Popular</option>
            <option value="rating">Highest Rated</option>
            <option value="newest">Newest</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
          </select>
          
          <button className="btn btn-secondary flex items-center justify-center">
            <FunnelIcon className="h-4 w-4 mr-2" />
            Filters
          </button>
        </div>
      </div>

      {/* Results Summary */}
      <div className="flex items-center justify-between">
        <p className="text-secondary">
          Showing {sortedTemplates.length} of {templates.length} templates
        </p>
        <div className="flex items-center space-x-2">
          <span className="text-sm text-secondary">View:</span>
          <button className="p-1 text-blue-500">
            <div className="grid grid-cols-2 gap-1">
              <div className="w-2 h-2 bg-current rounded"></div>
              <div className="w-2 h-2 bg-current rounded"></div>
              <div className="w-2 h-2 bg-current rounded"></div>
              <div className="w-2 h-2 bg-current rounded"></div>
            </div>
          </button>
          <button className="p-1 text-secondary">
            <div className="space-y-1">
              <div className="w-6 h-1 bg-current rounded"></div>
              <div className="w-6 h-1 bg-current rounded"></div>
              <div className="w-6 h-1 bg-current rounded"></div>
            </div>
          </button>
        </div>
      </div>

      {/* Templates Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {sortedTemplates.map((template) => (
          <motion.div
            key={template.id}
            whileHover={{ y: -5 }}
            className="bg-surface rounded-lg shadow-default overflow-hidden hover:shadow-lg transition-all duration-200"
          >
            {/* Template Preview */}
            <div className="relative">
              <div className="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center">
                <DocumentTextIcon className="h-16 w-16 text-white opacity-50" />
              </div>
              
              {/* Badges */}
              <div className="absolute top-3 left-3 flex items-center space-x-2">
                {template.type === 'free' && (
                  <span className="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                    FREE
                  </span>
                )}
                {template.discount > 0 && (
                  <span className="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                    -{template.discount}%
                  </span>
                )}
              </div>
              
              {/* Action Buttons */}
              <div className="absolute top-3 right-3 flex items-center space-x-2">
                <button 
                  className={`p-2 rounded-full transition-colors ${
                    template.isFavorited 
                      ? 'bg-red-500 text-white' 
                      : 'bg-white/90 text-gray-600 hover:bg-red-500 hover:text-white'
                  }`}
                >
                  <HeartIcon className="h-4 w-4" />
                </button>
                <button className="p-2 bg-white/90 text-gray-600 hover:bg-blue-500 hover:text-white rounded-full transition-colors">
                  <ShareIcon className="h-4 w-4" />
                </button>
              </div>
            </div>
            
            {/* Template Info */}
            <div className="p-6">
              <div className="flex items-start justify-between mb-3">
                <h3 className="font-semibold text-primary line-clamp-2 flex-1">{template.title}</h3>
                <div className="ml-2">
                  {template.price === 0 ? (
                    <span className="text-lg font-bold text-green-500">FREE</span>
                  ) : (
                    <div className="text-right">
                      <span className="text-lg font-bold text-primary">${template.price}</span>
                      {template.originalPrice && (
                        <div className="text-sm text-secondary line-through">
                          ${template.originalPrice}
                        </div>
                      )}
                    </div>
                  )}
                </div>
              </div>
              
              <p className="text-sm text-secondary line-clamp-2 mb-4">
                {template.description}
              </p>
              
              {/* Rating and Stats */}
              <div className="flex items-center space-x-4 mb-4 text-sm text-secondary">
                <div className="flex items-center space-x-1">
                  {renderStars(template.rating)}
                  <span className="ml-1">{template.rating}</span>
                  <span>({template.reviews})</span>
                </div>
                <div className="flex items-center space-x-1">
                  <DownloadIcon className="h-4 w-4" />
                  <span>{template.downloads}</span>
                </div>
              </div>
              
              {/* Author */}
              <div className="flex items-center space-x-2 mb-4">
                <div className="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                  <UserIcon className="h-4 w-4 text-white" />
                </div>
                <span className="text-sm text-secondary">{template.author.name}</span>
                {template.author.verified && (
                  <div className="w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                    <span className="text-white text-xs">✓</span>
                  </div>
                )}
              </div>
              
              {/* Features */}
              <div className="flex flex-wrap gap-1 mb-4">
                {template.features.slice(0, 3).map((feature) => (
                  <span key={feature} className="px-2 py-1 bg-surface-hover text-xs text-secondary rounded-full">
                    {feature}
                  </span>
                ))}
                {template.features.length > 3 && (
                  <span className="px-2 py-1 bg-surface-hover text-xs text-secondary rounded-full">
                    +{template.features.length - 3} more
                  </span>
                )}
              </div>
              
              {/* Action Buttons */}
              <div className="grid grid-cols-2 gap-2">
                <button className="btn btn-secondary text-sm py-2 flex items-center justify-center">
                  <EyeIcon className="h-4 w-4 mr-1" />
                  Preview
                </button>
                <button className="btn btn-primary text-sm py-2 flex items-center justify-center">
                  {template.price === 0 ? (
                    <>
                      <DownloadIcon className="h-4 w-4 mr-1" />
                      Download
                    </>
                  ) : (
                    <>
                      <ShoppingCartIcon className="h-4 w-4 mr-1" />
                      Buy Now
                    </>
                  )}
                </button>
              </div>
            </div>
          </motion.div>
        ))}
      </div>
    </div>
  );

  const renderMyTemplates = () => (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h3 className="text-xl font-semibold text-primary">My Templates</h3>
          <p className="text-secondary">Manage and track your template sales</p>
        </div>
        <button className="btn btn-primary flex items-center space-x-2">
          <PlusIcon className="h-4 w-4" />
          <span>Upload New Template</span>
        </button>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {[
          { label: 'Total Templates', value: myTemplates.length.toString(), icon: DocumentTextIcon, color: 'bg-blue-500' },
          { label: 'Total Sales', value: myTemplates.reduce((sum, t) => sum + t.sales, 0).toString(), icon: ShoppingCartIcon, color: 'bg-green-500' },
          { label: 'Revenue', value: `$${myTemplates.reduce((sum, t) => sum + t.revenue, 0).toFixed(2)}`, icon: CreditCardIcon, color: 'bg-purple-500' },
          { label: 'Avg Rating', value: '4.3', icon: StarIcon, color: 'bg-orange-500' }
        ].map((stat, index) => (
          <div key={index} className="bg-surface p-6 rounded-lg shadow-default">
            <div className="flex items-center">
              <div className={`p-3 rounded-lg ${stat.color} mr-4`}>
                <stat.icon className="h-6 w-6 text-white" />
              </div>
              <div>
                <p className="text-sm font-medium text-secondary">{stat.label}</p>
                <p className="text-2xl font-bold text-primary">{stat.value}</p>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Templates List */}
      <div className="bg-surface rounded-lg shadow-default overflow-hidden">
        <div className="divide-y divide-default">
          {myTemplates.map((template) => (
            <div key={template.id} className="p-6 hover:bg-surface-hover transition-colors">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-4">
                  <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <DocumentTextIcon className="h-8 w-8 text-white" />
                  </div>
                  
                  <div>
                    <h4 className="font-semibold text-primary">{template.title}</h4>
                    <div className="flex items-center space-x-4 mt-1">
                      <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                        template.status === 'published' 
                          ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                          : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                      }`}>
                        {template.status}
                      </span>
                      <span className="text-sm text-secondary capitalize">{template.category}</span>
                    </div>
                  </div>
                </div>
                
                <div className="grid grid-cols-3 gap-6 text-center">
                  <div>
                    <p className="text-lg font-bold text-primary">{template.sales}</p>
                    <p className="text-xs text-secondary">Sales</p>
                  </div>
                  <div>
                    <p className="text-lg font-bold text-primary">${template.revenue.toFixed(2)}</p>
                    <p className="text-xs text-secondary">Revenue</p>
                  </div>
                  <div>
                    <p className="text-lg font-bold text-primary">
                      {template.rating > 0 ? template.rating.toFixed(1) : '-'}
                    </p>
                    <p className="text-xs text-secondary">Rating</p>
                  </div>
                </div>
                
                <div className="flex items-center space-x-2">
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <EyeIcon className="h-5 w-5" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <ChartBarIcon className="h-5 w-5" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );

  return (
    <div className="space-y-6">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="mb-8"
      >
        <h1 className="text-3xl font-bold text-primary mb-2">Template Marketplace</h1>
        <p className="text-secondary">Discover, buy, and sell professional templates</p>
      </motion.div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="flex space-x-8">
          {[
            { id: 'browse', name: 'Browse Templates', icon: MagnifyingGlassIcon },
            { id: 'my-templates', name: 'My Templates', icon: DocumentTextIcon },
            { id: 'favorites', name: 'Favorites', icon: HeartIcon }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`flex items-center space-x-2 py-4 px-1 border-b-2 font-medium text-sm transition-colors ${
                activeTab === tab.id
                  ? 'border-blue-500 text-blue-500'
                  : 'border-transparent text-secondary hover:text-primary hover:border-gray-300'
              }`}
            >
              <tab.icon className="h-4 w-4" />
              <span>{tab.name}</span>
            </button>
          ))}
        </nav>
      </div>

      {/* Tab Content */}
      <motion.div
        key={activeTab}
        initial={{ opacity: 0, x: 20 }}
        animate={{ opacity: 1, x: 0 }}
        transition={{ duration: 0.3 }}
      >
        {activeTab === 'browse' && renderBrowseTemplates()}
        {activeTab === 'my-templates' && renderMyTemplates()}
        {activeTab === 'favorites' && (
          <div className="text-center py-12">
            <HeartIcon className="mx-auto h-12 w-12 text-secondary mb-4" />
            <h3 className="text-lg font-medium text-primary mb-2">No favorites yet</h3>
            <p className="text-secondary">Start browsing templates and add your favorites!</p>
          </div>
        )}
      </motion.div>
    </div>
  );
};

export default TemplateMarketplace;