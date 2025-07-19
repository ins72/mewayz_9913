import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
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
  ShareIcon,
  CogIcon,
  TrophyIcon,
  BanknotesIcon,
  UsersIcon,
  ClockIcon,
  FireIcon,
  SparklesIcon,
  GlobeAltIcon,
  DevicePhoneMobileIcon,
  EnvelopeIcon,
  LinkIcon
} from '@heroicons/react/24/outline';
import {
  StarIcon as StarIconSolid
} from '@heroicons/react/24/solid';

const TemplateMarketplaceV2Page = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  const [activeTab, setActiveTab] = useState('browse');
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [sortBy, setSortBy] = useState('popular');
  const [priceFilter, setPriceFilter] = useState('all');
  const [showMyTemplates, setShowMyTemplates] = useState(false);

  // Enhanced template categories with icons
  const categories = [
    { id: 'all', name: 'All Categories', icon: GlobeAltIcon, count: 2847 },
    { id: 'website', name: 'Website Templates', icon: GlobeAltIcon, count: 1247 },
    { id: 'email', name: 'Email Templates', icon: EnvelopeIcon, count: 856 },
    { id: 'social', name: 'Social Media', icon: ShareIcon, count: 432 },
    { id: 'linkinbio', name: 'Link in Bio', icon: LinkIcon, count: 298 },
    { id: 'course', name: 'Course Templates', icon: DocumentTextIcon, count: 187 },
    { id: 'mobile', name: 'Mobile App', icon: DevicePhoneMobileIcon, count: 145 },
    { id: 'landing', name: 'Landing Pages', icon: DocumentTextIcon, count: 234 },
    { id: 'ecommerce', name: 'E-commerce', icon: ShoppingCartIcon, count: 189 }
  ];

  // Mock comprehensive template data
  const [templates, setTemplates] = useState([
    {
      id: '1',
      title: 'SaaS Landing Page Template',
      description: 'High-converting landing page template designed specifically for SaaS products. Includes hero sections, feature highlights, pricing tables, testimonials, and conversion optimization.',
      category: 'landing',
      type: 'premium',
      price: 89.99,
      originalPrice: 149.99,
      rating: 4.9,
      reviews: 342,
      downloads: 2847,
      favorites: 1205,
      author: {
        name: 'Digital Design Pro',
        avatar: 'https://ui-avatars.com/api/?name=Digital+Design&background=6366f1&color=fff',
        verified: true,
        totalSales: 15678,
        rating: 4.8
      },
      preview: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
      tags: ['saas', 'landing', 'conversion', 'modern', 'responsive'],
      features: ['Responsive Design', 'Dark/Light Mode', 'SEO Optimized', 'Fast Loading'],
      technologies: ['React', 'Tailwind CSS', 'Framer Motion'],
      livePreview: 'https://demo.template.com/saas-landing',
      createdAt: '2024-01-10',
      updatedAt: '2024-01-15',
      license: 'Commercial',
      support: '6 months',
      files: 23,
      size: '4.2 MB'
    },
    {
      id: '2', 
      title: 'E-commerce Email Campaign Set',
      description: 'Complete email marketing template collection for e-commerce businesses. Includes welcome series, abandoned cart, product promotions, and seasonal campaigns.',
      category: 'email',
      type: 'premium',
      price: 45.99,
      originalPrice: 75.99,
      rating: 4.7,
      reviews: 198,
      downloads: 1456,
      favorites: 678,
      author: {
        name: 'Email Marketing Experts',
        avatar: 'https://ui-avatars.com/api/?name=Email+Experts&background=10b981&color=fff',
        verified: true,
        totalSales: 8934,
        rating: 4.6
      },
      preview: 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=800',
      tags: ['email', 'ecommerce', 'marketing', 'campaigns', 'responsive'],
      features: ['20+ Templates', 'Mobile Optimized', 'A/B Test Ready', 'Mailchimp Compatible'],
      technologies: ['HTML', 'CSS', 'Mailchimp', 'Campaign Monitor'],
      livePreview: 'https://demo.template.com/email-ecommerce',
      createdAt: '2024-01-08',
      updatedAt: '2024-01-14',
      license: 'Commercial',
      support: '3 months',
      files: 45,
      size: '2.1 MB'
    },
    {
      id: '3',
      title: 'Social Media Content Pack',
      description: 'Professional social media template collection with 100+ designs for Instagram, Facebook, Twitter, and LinkedIn. Perfect for agencies and brands.',
      category: 'social',
      type: 'premium',
      price: 29.99,
      originalPrice: 59.99,
      rating: 4.8,
      reviews: 567,
      downloads: 3421,
      favorites: 1876,
      author: {
        name: 'Creative Social Studio',
        avatar: 'https://ui-avatars.com/api/?name=Creative+Social&background=ec4899&color=fff',
        verified: true,
        totalSales: 21456,
        rating: 4.9
      },
      preview: 'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=800',
      tags: ['social', 'instagram', 'facebook', 'content', 'design'],
      features: ['100+ Designs', 'Editable PSD/AI', 'Multiple Formats', 'Brand Guidelines'],
      technologies: ['Photoshop', 'Illustrator', 'Figma', 'Canva'],
      livePreview: 'https://demo.template.com/social-pack',
      createdAt: '2024-01-05',
      updatedAt: '2024-01-12',
      license: 'Commercial',
      support: '12 months',
      files: 127,
      size: '156 MB'
    },
    {
      id: '4',
      title: 'Modern Link in Bio Template',
      description: 'Stunning link in bio template with animated elements, social media integration, and analytics tracking. Perfect for influencers and content creators.',
      category: 'linkinbio',
      type: 'free',
      price: 0,
      originalPrice: 0,
      rating: 4.6,
      reviews: 234,
      downloads: 5678,
      favorites: 2341,
      author: {
        name: 'Bio Link Designs',
        avatar: 'https://ui-avatars.com/api/?name=Bio+Link&background=f59e0b&color=fff',
        verified: false,
        totalSales: 3456,
        rating: 4.5
      },
      preview: 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=800',
      tags: ['bio', 'link', 'influencer', 'modern', 'animated'],
      features: ['Responsive Design', 'Social Integration', 'Analytics Ready', 'Easy Customization'],
      technologies: ['HTML', 'CSS', 'JavaScript', 'Vue.js'],
      livePreview: 'https://demo.template.com/bio-link',
      createdAt: '2024-01-03',
      updatedAt: '2024-01-10',
      license: 'Personal',
      support: '1 month',
      files: 12,
      size: '890 KB'
    },
    {
      id: '5',
      title: 'Online Course Platform Template',
      description: 'Complete learning management system template with video player, progress tracking, quizzes, certificates, and payment integration.',
      category: 'course',
      type: 'premium',
      price: 149.99,
      originalPrice: 249.99,
      rating: 4.9,
      reviews: 89,
      downloads: 567,
      favorites: 345,
      author: {
        name: 'EduTech Solutions',
        avatar: 'https://ui-avatars.com/api/?name=EduTech&background=3b82f6&color=fff',
        verified: true,
        totalSales: 4567,
        rating: 4.8
      },
      preview: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800',
      tags: ['course', 'education', 'lms', 'learning', 'platform'],
      features: ['LMS Features', 'Video Integration', 'Progress Tracking', 'Payment Gateway'],
      technologies: ['React', 'Node.js', 'MongoDB', 'Stripe'],
      livePreview: 'https://demo.template.com/course-platform',
      createdAt: '2024-01-01',
      updatedAt: '2024-01-13',
      license: 'Commercial',
      support: '12 months',
      files: 89,
      size: '12.4 MB'
    }
  ]);

  // User's template statistics (mock data)
  const [userStats, setUserStats] = useState({
    templatesOwned: 23,
    templatesSold: 156,
    totalEarnings: 4567.89,
    thisMonthEarnings: 892.34,
    avgRating: 4.7,
    totalDownloads: 12456
  });

  const [myTemplates, setMyTemplates] = useState([
    {
      id: 'my-1',
      title: 'My Modern Dashboard Template',
      category: 'website',
      price: 79.99,
      sales: 45,
      earnings: 1234.56,
      rating: 4.6,
      reviews: 23,
      status: 'active',
      createdAt: '2023-12-15'
    },
    {
      id: 'my-2',
      title: 'Email Newsletter Template Set',
      category: 'email',
      price: 39.99,
      sales: 89,
      earnings: 2876.43,
      rating: 4.8,
      reviews: 67,
      status: 'active',
      createdAt: '2023-11-20'
    }
  ]);

  const filteredTemplates = templates.filter(template => {
    const matchesSearch = template.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         template.description.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         template.tags.some(tag => tag.toLowerCase().includes(searchQuery.toLowerCase()));
    
    const matchesCategory = selectedCategory === 'all' || template.category === selectedCategory;
    
    const matchesPrice = priceFilter === 'all' || 
                        (priceFilter === 'free' && template.price === 0) ||
                        (priceFilter === 'premium' && template.price > 0);

    return matchesSearch && matchesCategory && matchesPrice;
  });

  const sortedTemplates = [...filteredTemplates].sort((a, b) => {
    switch (sortBy) {
      case 'popular':
        return b.downloads - a.downloads;
      case 'newest':
        return new Date(b.createdAt) - new Date(a.createdAt);
      case 'rating':
        return b.rating - a.rating;
      case 'price-low':
        return a.price - b.price;
      case 'price-high':
        return b.price - a.price;
      default:
        return 0;
    }
  });

  const handlePurchaseTemplate = async (templateId) => {
    try {
      const template = templates.find(t => t.id === templateId);
      if (template.price === 0) {
        success(`Successfully downloaded "${template.title}"!`);
      } else {
        success(`Added "${template.title}" to cart. Proceeding to checkout...`);
        // Redirect to payment flow
      }
    } catch (err) {
      error('Failed to process template purchase');
    }
  };

  const handleFavoriteTemplate = (templateId) => {
    setTemplates(prev => prev.map(template => 
      template.id === templateId 
        ? { ...template, favorites: template.favorites + 1 }
        : template
    ));
    success('Template added to favorites!');
  };

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="bg-surface-elevated rounded-xl shadow-default p-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-primary mb-2">Template Marketplace</h1>
            <p className="text-secondary">Discover, purchase, and sell professional templates for your business</p>
          </div>
          <div className="flex items-center space-x-3">
            <button 
              onClick={() => setActiveTab('sell')}
              className="btn btn-secondary flex items-center space-x-2"
            >
              <PlusIcon className="h-4 w-4" />
              <span>Sell Templates</span>
            </button>
            <button 
              onClick={() => setActiveTab('my-templates')}
              className="btn btn-primary flex items-center space-x-2"
            >
              <UserIcon className="h-4 w-4" />
              <span>My Templates</span>
            </button>
          </div>
        </div>
      </div>

      {/* Template Stats Dashboard */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="bg-surface-elevated p-6 rounded-xl shadow-default"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-secondary">Total Templates</p>
              <p className="text-2xl font-bold text-primary">{templates.length.toLocaleString()}</p>
            </div>
            <div className="p-3 bg-blue-100 rounded-xl dark:bg-blue-900">
              <DocumentTextIcon className="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
          </div>
          <div className="mt-4">
            <p className="text-sm text-secondary">
              <span className="text-green-500 font-medium">+12</span> this week
            </p>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.1 }}
          className="bg-surface-elevated p-6 rounded-xl shadow-default"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-secondary">Total Downloads</p>
              <p className="text-2xl font-bold text-primary">847K</p>
            </div>
            <div className="p-3 bg-green-100 rounded-xl dark:bg-green-900">
              <ArrowDownTrayIcon className="h-6 w-6 text-green-600 dark:text-green-400" />
            </div>
          </div>
          <div className="mt-4">
            <p className="text-sm text-secondary">
              <span className="text-green-500 font-medium">+24%</span> vs last month
            </p>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="bg-surface-elevated p-6 rounded-xl shadow-default"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-secondary">Revenue Generated</p>
              <p className="text-2xl font-bold text-primary">$125.8K</p>
            </div>
            <div className="p-3 bg-purple-100 rounded-xl dark:bg-purple-900">
              <BanknotesIcon className="h-6 w-6 text-purple-600 dark:text-purple-400" />
            </div>
          </div>
          <div className="mt-4">
            <p className="text-sm text-secondary">
              <span className="text-green-500 font-medium">+18%</span> growth
            </p>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="bg-surface-elevated p-6 rounded-xl shadow-default"
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-secondary">Active Sellers</p>
              <p className="text-2xl font-bold text-primary">2,847</p>
            </div>
            <div className="p-3 bg-orange-100 rounded-xl dark:bg-orange-900">
              <UsersIcon className="h-6 w-6 text-orange-600 dark:text-orange-400" />
            </div>
          </div>
          <div className="mt-4">
            <p className="text-sm text-secondary">
              <span className="text-green-500 font-medium">+156</span> new this month
            </p>
          </div>
        </motion.div>
      </div>

      {/* Main Content Tabs */}
      <div className="bg-surface-elevated rounded-xl shadow-default">
        <div className="border-b border-default">
          <nav className="flex space-x-8 px-6">
            {[
              { id: 'browse', name: 'Browse Templates', icon: MagnifyingGlassIcon },
              { id: 'my-templates', name: 'My Templates', icon: UserIcon },
              { id: 'favorites', name: 'Favorites', icon: HeartIcon },
              { id: 'sell', name: 'Sell Templates', icon: PlusIcon },
              { id: 'earnings', name: 'Earnings', icon: ChartBarIcon }
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

        <div className="p-6">
          {activeTab === 'browse' && (
            <div className="space-y-6">
              {/* Search and Filters */}
              <div className="flex flex-col lg:flex-row gap-4">
                <div className="flex-1">
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
                
                <div className="flex gap-3">
                  <select
                    value={selectedCategory}
                    onChange={(e) => setSelectedCategory(e.target.value)}
                    className="input min-w-[150px]"
                  >
                    {categories.map((category) => (
                      <option key={category.id} value={category.id}>
                        {category.name} ({category.count})
                      </option>
                    ))}
                  </select>
                  
                  <select
                    value={priceFilter}
                    onChange={(e) => setPriceFilter(e.target.value)}
                    className="input"
                  >
                    <option value="all">All Prices</option>
                    <option value="free">Free</option>
                    <option value="premium">Premium</option>
                  </select>
                  
                  <select
                    value={sortBy}
                    onChange={(e) => setSortBy(e.target.value)}
                    className="input"
                  >
                    <option value="popular">Most Popular</option>
                    <option value="newest">Newest</option>
                    <option value="rating">Highest Rated</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                  </select>
                </div>
              </div>

              {/* Templates Grid */}
              <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                {sortedTemplates.map((template) => (
                  <motion.div
                    key={template.id}
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="bg-surface border border-default rounded-xl overflow-hidden hover:shadow-lg transition-shadow"
                  >
                    {/* Template Preview */}
                    <div className="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800">
                      <img
                        src={template.preview}
                        alt={template.title}
                        className="w-full h-full object-cover"
                      />
                      <div className="absolute top-3 right-3 flex space-x-2">
                        <button
                          onClick={() => handleFavoriteTemplate(template.id)}
                          className="p-2 bg-white dark:bg-gray-800 rounded-full shadow-default hover:scale-105 transition-transform"
                        >
                          <HeartIcon className="h-4 w-4 text-red-500" />
                        </button>
                        <button className="p-2 bg-white dark:bg-gray-800 rounded-full shadow-default hover:scale-105 transition-transform">
                          <EyeIcon className="h-4 w-4 text-secondary" />
                        </button>
                      </div>
                      {template.type === 'free' && (
                        <div className="absolute top-3 left-3">
                          <span className="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                            FREE
                          </span>
                        </div>
                      )}
                    </div>

                    {/* Template Info */}
                    <div className="p-4">
                      <div className="flex items-start justify-between mb-2">
                        <h3 className="font-semibold text-primary line-clamp-1">{template.title}</h3>
                        <div className="flex items-center ml-2">
                          <StarIconSolid className="h-4 w-4 text-yellow-400" />
                          <span className="text-sm text-secondary ml-1">{template.rating}</span>
                        </div>
                      </div>
                      
                      <p className="text-sm text-secondary mb-3 line-clamp-2">{template.description}</p>
                      
                      {/* Author Info */}
                      <div className="flex items-center mb-3">
                        <img
                          src={template.author.avatar}
                          alt={template.author.name}
                          className="w-6 h-6 rounded-full"
                        />
                        <span className="text-sm text-secondary ml-2">{template.author.name}</span>
                        {template.author.verified && (
                          <div className="ml-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                            <CheckCircleIcon className="h-3 w-3 text-white" />
                          </div>
                        )}
                      </div>

                      {/* Template Tags */}
                      <div className="flex flex-wrap gap-1 mb-4">
                        {template.tags.slice(0, 3).map((tag) => (
                          <span
                            key={tag}
                            className="px-2 py-1 bg-surface-elevated text-xs text-secondary rounded"
                          >
                            {tag}
                          </span>
                        ))}
                        {template.tags.length > 3 && (
                          <span className="px-2 py-1 bg-surface-elevated text-xs text-secondary rounded">
                            +{template.tags.length - 3}
                          </span>
                        )}
                      </div>

                      {/* Stats and Price */}
                      <div className="flex items-center justify-between">
                        <div className="flex items-center space-x-4 text-sm text-secondary">
                          <div className="flex items-center">
                            <ArrowDownTrayIcon className="h-4 w-4 mr-1" />
                            <span>{template.downloads.toLocaleString()}</span>
                          </div>
                          <div className="flex items-center">
                            <HeartIcon className="h-4 w-4 mr-1" />
                            <span>{template.favorites.toLocaleString()}</span>
                          </div>
                        </div>
                        
                        <div className="flex items-center space-x-2">
                          {template.price > 0 ? (
                            <div className="text-right">
                              <p className="font-bold text-primary">${template.price}</p>
                              {template.originalPrice > template.price && (
                                <p className="text-sm text-secondary line-through">${template.originalPrice}</p>
                              )}
                            </div>
                          ) : (
                            <p className="font-bold text-green-600">FREE</p>
                          )}
                          
                          <button
                            onClick={() => handlePurchaseTemplate(template.id)}
                            className={`p-2 rounded-lg transition-colors ${
                              template.price === 0
                                ? 'bg-green-100 text-green-600 hover:bg-green-200 dark:bg-green-900 dark:text-green-400'
                                : 'bg-blue-100 text-blue-600 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-400'
                            }`}
                          >
                            {template.price === 0 ? (
                              <ArrowDownTrayIcon className="h-4 w-4" />
                            ) : (
                              <ShoppingCartIcon className="h-4 w-4" />
                            )}
                          </button>
                        </div>
                      </div>
                    </div>
                  </motion.div>
                ))}
              </div>
            </div>
          )}

          {activeTab === 'my-templates' && (
            <div className="space-y-6">
              {/* My Templates Stats */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Templates Sold</p>
                      <p className="text-2xl font-bold text-primary">{userStats.templatesSold}</p>
                    </div>
                    <TrophyIcon className="h-8 w-8 text-yellow-500" />
                  </div>
                </div>
                
                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Total Earnings</p>
                      <p className="text-2xl font-bold text-primary">${userStats.totalEarnings.toLocaleString()}</p>
                    </div>
                    <BanknotesIcon className="h-8 w-8 text-green-500" />
                  </div>
                </div>
                
                <div className="bg-surface border border-default rounded-xl p-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-secondary">Avg Rating</p>
                      <p className="text-2xl font-bold text-primary">{userStats.avgRating}</p>
                    </div>
                    <StarIcon className="h-8 w-8 text-yellow-500" />
                  </div>
                </div>
              </div>

              {/* My Templates List */}
              <div className="space-y-4">
                {myTemplates.map((template) => (
                  <div
                    key={template.id}
                    className="bg-surface border border-default rounded-xl p-6 hover:shadow-default transition-shadow"
                  >
                    <div className="flex items-center justify-between">
                      <div className="flex-1">
                        <h3 className="font-semibold text-primary mb-2">{template.title}</h3>
                        <div className="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                          <div>
                            <p className="text-secondary">Price</p>
                            <p className="font-medium text-primary">${template.price}</p>
                          </div>
                          <div>
                            <p className="text-secondary">Sales</p>
                            <p className="font-medium text-primary">{template.sales}</p>
                          </div>
                          <div>
                            <p className="text-secondary">Earnings</p>
                            <p className="font-medium text-primary">${template.earnings}</p>
                          </div>
                          <div>
                            <p className="text-secondary">Rating</p>
                            <p className="font-medium text-primary">{template.rating} ({template.reviews} reviews)</p>
                          </div>
                          <div>
                            <p className="text-secondary">Status</p>
                            <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${
                              template.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                            }`}>
                              {template.status}
                            </span>
                          </div>
                        </div>
                      </div>
                      <div className="flex items-center space-x-2 ml-6">
                        <button className="btn btn-secondary btn-sm">
                          <CogIcon className="h-4 w-4 mr-1" />
                          Edit
                        </button>
                        <button className="btn btn-primary btn-sm">
                          <ChartBarIcon className="h-4 w-4 mr-1" />
                          Analytics
                        </button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {activeTab === 'sell' && (
            <div className="max-w-3xl mx-auto space-y-6">
              <div className="text-center">
                <h2 className="text-2xl font-bold text-primary mb-4">Sell Your Templates</h2>
                <p className="text-secondary">
                  Join thousands of creators earning money by selling their professional templates
                </p>
              </div>

              <div className="bg-surface border border-default rounded-xl p-6">
                <h3 className="text-lg font-semibold text-primary mb-4">Upload New Template</h3>
                
                <form className="space-y-4">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-primary mb-2">Template Title</label>
                      <input type="text" className="input w-full" placeholder="Enter template title..." />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-primary mb-2">Category</label>
                      <select className="input w-full">
                        {categories.filter(c => c.id !== 'all').map((category) => (
                          <option key={category.id} value={category.id}>{category.name}</option>
                        ))}
                      </select>
                    </div>
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-primary mb-2">Description</label>
                    <textarea 
                      className="input w-full h-32" 
                      placeholder="Describe your template..."
                    ></textarea>
                  </div>
                  
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-primary mb-2">Price ($)</label>
                      <input type="number" className="input w-full" placeholder="0.00" />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-primary mb-2">License Type</label>
                      <select className="input w-full">
                        <option value="personal">Personal Use</option>
                        <option value="commercial">Commercial Use</option>
                        <option value="extended">Extended License</option>
                      </select>
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-primary mb-2">Support Duration</label>
                      <select className="input w-full">
                        <option value="1">1 Month</option>
                        <option value="3">3 Months</option>
                        <option value="6">6 Months</option>
                        <option value="12">12 Months</option>
                      </select>
                    </div>
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-primary mb-2">Tags (comma-separated)</label>
                    <input type="text" className="input w-full" placeholder="modern, responsive, business..." />
                  </div>
                  
                  <div className="flex items-center justify-between pt-4">
                    <div className="text-sm text-secondary">
                      By uploading, you agree to our seller terms and conditions
                    </div>
                    <button type="submit" className="btn btn-primary">
                      Upload Template
                    </button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {activeTab === 'earnings' && (
            <div className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="bg-surface border border-default rounded-xl p-6">
                  <p className="text-sm font-medium text-secondary">This Month</p>
                  <p className="text-2xl font-bold text-primary">${userStats.thisMonthEarnings}</p>
                  <p className="text-sm text-green-500 mt-1">+23% vs last month</p>
                </div>
                
                <div className="bg-surface border border-default rounded-xl p-6">
                  <p className="text-sm font-medium text-secondary">Total Earnings</p>
                  <p className="text-2xl font-bold text-primary">${userStats.totalEarnings.toLocaleString()}</p>
                  <p className="text-sm text-secondary mt-1">All time earnings</p>
                </div>
                
                <div className="bg-surface border border-default rounded-xl p-6">
                  <p className="text-sm font-medium text-secondary">Avg. Sale Price</p>
                  <p className="text-2xl font-bold text-primary">${(userStats.totalEarnings / userStats.templatesSold).toFixed(2)}</p>
                  <p className="text-sm text-secondary mt-1">Per template sold</p>
                </div>
                
                <div className="bg-surface border border-default rounded-xl p-6">
                  <p className="text-sm font-medium text-secondary">Conversion Rate</p>
                  <p className="text-2xl font-bold text-primary">12.4%</p>
                  <p className="text-sm text-green-500 mt-1">+2.1% this month</p>
                </div>
              </div>

              <div className="bg-surface border border-default rounded-xl p-6">
                <h3 className="text-lg font-semibold text-primary mb-4">Earnings Overview</h3>
                <div className="h-64 bg-surface-elevated rounded-lg flex items-center justify-center text-secondary">
                  Earnings Chart Placeholder
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default TemplateMarketplaceV2Page;