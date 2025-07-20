import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  MagnifyingGlassIcon, 
  FunnelIcon, 
  HeartIcon,
  StarIcon,
  EyeIcon,
  ArrowDownTrayIcon,
  CreditCardIcon,
  TagIcon,
  UserIcon,
  CalendarIcon,
  ShareIcon,
  PlusIcon
} from '@heroicons/react/24/outline';
import { HeartIcon as HeartSolidIcon } from '@heroicons/react/24/solid';

const EnhancedTemplateMarketplace = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [selectedType, setSelectedType] = useState('all');
  const [sortBy, setSortBy] = useState('popular');
  const [priceFilter, setPriceFilter] = useState('all');
  const [templates, setTemplates] = useState([]);
  const [loading, setLoading] = useState(true);
  const [favorites, setFavorites] = useState(new Set());
  const [showCreateModal, setShowCreateModal] = useState(false);

  const categories = [
    { id: 'all', name: 'All Categories', count: 0 },
    { id: 'link-in-bio', name: 'Link in Bio', count: 45 },
    { id: 'social-media', name: 'Social Media', count: 38 },
    { id: 'email-marketing', name: 'Email Marketing', count: 52 },
    { id: 'websites', name: 'Websites', count: 34 },
    { id: 'courses', name: 'Courses', count: 28 },
    { id: 'e-commerce', name: 'E-commerce', count: 41 }
  ];

  const templateTypes = [
    { id: 'all', name: 'All Types' },
    { id: 'free', name: 'Free Templates' },
    { id: 'premium', name: 'Premium Templates' },
    { id: 'exclusive', name: 'Exclusive Templates' }
  ];

  useEffect(() => {
    fetchTemplates();
  }, [selectedCategory, selectedType, sortBy, priceFilter]);

  const fetchTemplates = async () => {
    setLoading(true);
    // Simulate API call
    setTimeout(() => {
      const mockTemplates = [
        {
          id: 1,
          title: 'Modern Social Media Kit',
          description: 'Complete social media template kit with Instagram posts, stories, and covers',
          category: 'social-media',
          type: 'premium',
          price: 29.99,
          rating: 4.8,
          downloads: 1247,
          views: 8934,
          author: 'DesignPro Studio',
          authorAvatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=32&h=32&fit=crop&crop=face',
          thumbnail: 'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=300&h=200&fit=crop',
          tags: ['social-media', 'instagram', 'modern', 'clean'],
          createdAt: '2025-01-15',
          featured: true
        },
        {
          id: 2,
          title: 'Minimalist Link in Bio',
          description: 'Clean and professional link in bio template perfect for creators',
          category: 'link-in-bio',
          type: 'free',
          price: 0,
          rating: 4.6,
          downloads: 2134,
          views: 12456,
          author: 'CreativeHub',
          authorAvatar: 'https://images.unsplash.com/photo-1494790108755-2616b112c4be?w=32&h=32&fit=crop&crop=face',
          thumbnail: 'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?w=300&h=200&fit=crop',
          tags: ['link-in-bio', 'minimalist', 'creator', 'mobile'],
          createdAt: '2025-01-12',
          featured: false
        },
        {
          id: 3,
          title: 'E-commerce Email Series',
          description: 'Complete email marketing templates for e-commerce businesses',
          category: 'email-marketing',
          type: 'premium',
          price: 49.99,
          rating: 4.9,
          downloads: 845,
          views: 5623,
          author: 'EmailMaster',
          authorAvatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=32&h=32&fit=crop&crop=face',
          thumbnail: 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=300&h=200&fit=crop',
          tags: ['email', 'e-commerce', 'marketing', 'conversion'],
          createdAt: '2025-01-10',
          featured: true
        },
        {
          id: 4,
          title: 'Course Landing Page',
          description: 'High-converting landing page template for online courses',
          category: 'courses',
          type: 'premium',
          price: 39.99,
          rating: 4.7,
          downloads: 567,
          views: 3421,
          author: 'EduDesign',
          authorAvatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=32&h=32&fit=crop&crop=face',
          thumbnail: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=300&h=200&fit=crop',
          tags: ['course', 'landing-page', 'education', 'conversion'],
          createdAt: '2025-01-08',
          featured: false
        },
        {
          id: 5,
          title: 'Business Website Template',
          description: 'Professional website template for small businesses',
          category: 'websites',
          type: 'free',
          price: 0,
          rating: 4.5,
          downloads: 1876,
          views: 9834,
          author: 'WebCrafters',
          authorAvatar: 'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=32&h=32&fit=crop&crop=face',
          thumbnail: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=300&h=200&fit=crop',
          tags: ['website', 'business', 'professional', 'responsive'],
          createdAt: '2025-01-05',
          featured: false
        },
        {
          id: 6,
          title: 'Premium E-commerce Store',
          description: 'Complete e-commerce store template with shopping cart and checkout',
          category: 'e-commerce',
          type: 'exclusive',
          price: 99.99,
          rating: 4.9,
          downloads: 234,
          views: 2134,
          author: 'ShopDesign Pro',
          authorAvatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=32&h=32&fit=crop&crop=face',
          thumbnail: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=300&h=200&fit=crop',
          tags: ['e-commerce', 'store', 'shopping', 'premium'],
          createdAt: '2025-01-03',
          featured: true
        }
      ];

      // Apply filters
      let filtered = mockTemplates;

      if (selectedCategory !== 'all') {
        filtered = filtered.filter(template => template.category === selectedCategory);
      }

      if (selectedType !== 'all') {
        filtered = filtered.filter(template => template.type === selectedType);
      }

      if (priceFilter !== 'all') {
        if (priceFilter === 'free') {
          filtered = filtered.filter(template => template.price === 0);
        } else if (priceFilter === 'paid') {
          filtered = filtered.filter(template => template.price > 0);
        }
      }

      if (searchTerm) {
        filtered = filtered.filter(template => 
          template.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
          template.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
          template.tags.some(tag => tag.toLowerCase().includes(searchTerm.toLowerCase()))
        );
      }

      // Apply sorting
      if (sortBy === 'popular') {
        filtered.sort((a, b) => b.downloads - a.downloads);
      } else if (sortBy === 'rating') {
        filtered.sort((a, b) => b.rating - a.rating);
      } else if (sortBy === 'newest') {
        filtered.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
      } else if (sortBy === 'price-low') {
        filtered.sort((a, b) => a.price - b.price);
      } else if (sortBy === 'price-high') {
        filtered.sort((a, b) => b.price - a.price);
      }

      setTemplates(filtered);
      setLoading(false);
    }, 800);
  };

  const toggleFavorite = (templateId) => {
    const newFavorites = new Set(favorites);
    if (newFavorites.has(templateId)) {
      newFavorites.delete(templateId);
    } else {
      newFavorites.add(templateId);
    }
    setFavorites(newFavorites);
  };

  const getTypeColor = (type) => {
    switch (type) {
      case 'free': return 'text-green-600 bg-green-100 dark:bg-green-900/20';
      case 'premium': return 'text-blue-600 bg-blue-100 dark:bg-blue-900/20';
      case 'exclusive': return 'text-purple-600 bg-purple-100 dark:bg-purple-900/20';
      default: return 'text-gray-600 bg-gray-100 dark:bg-gray-900/20';
    }
  };

  const formatPrice = (price) => {
    return price === 0 ? 'Free' : `$${price.toFixed(2)}`;
  };

  const TemplateCard = ({ template }) => (
    <motion.div
      layout
      initial={{ opacity: 0, scale: 0.9 }}
      animate={{ opacity: 1, scale: 1 }}
      exit={{ opacity: 0, scale: 0.9 }}
      whileHover={{ y: -5 }}
      className={`bg-card border rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 ${
        template.featured ? 'border-accent-primary ring-2 ring-accent-primary/20' : 'border-default'
      }`}
    >
      {/* Template Thumbnail */}
      <div className="relative">
        <img
          src={template.thumbnail}
          alt={template.title}
          className="w-full h-48 object-cover"
        />
        {template.featured && (
          <div className="absolute top-2 left-2 bg-accent-primary text-white px-2 py-1 rounded text-xs font-medium">
            Featured
          </div>
        )}
        <div className={`absolute top-2 right-2 px-2 py-1 rounded text-xs font-medium ${getTypeColor(template.type)}`}>
          {template.type.charAt(0).toUpperCase() + template.type.slice(1)}
        </div>
        
        {/* Quick Actions */}
        <div className="absolute bottom-2 right-2 flex space-x-1">
          <button
            onClick={() => toggleFavorite(template.id)}
            className="p-1.5 bg-black/50 backdrop-blur-sm rounded-full text-white hover:bg-black/70 transition-colors"
          >
            {favorites.has(template.id) ? (
              <HeartSolidIcon className="w-4 h-4 text-red-500" />
            ) : (
              <HeartIcon className="w-4 h-4" />
            )}
          </button>
          <button className="p-1.5 bg-black/50 backdrop-blur-sm rounded-full text-white hover:bg-black/70 transition-colors">
            <ShareIcon className="w-4 h-4" />
          </button>
        </div>
      </div>

      {/* Template Info */}
      <div className="p-4">
        <div className="flex items-start justify-between mb-2">
          <h3 className="text-lg font-semibold text-primary truncate flex-1">
            {template.title}
          </h3>
          <div className="text-lg font-bold text-accent-primary ml-2">
            {formatPrice(template.price)}
          </div>
        </div>
        
        <p className="text-secondary text-sm mb-3 line-clamp-2">
          {template.description}
        </p>

        {/* Tags */}
        <div className="flex flex-wrap gap-1 mb-3">
          {template.tags.slice(0, 3).map(tag => (
            <span
              key={tag}
              className="px-2 py-1 bg-gray-100 dark:bg-gray-800 text-xs text-secondary rounded"
            >
              #{tag}
            </span>
          ))}
          {template.tags.length > 3 && (
            <span className="px-2 py-1 text-xs text-accent-primary">
              +{template.tags.length - 3}
            </span>
          )}
        </div>

        {/* Author & Stats */}
        <div className="flex items-center justify-between text-xs text-secondary mb-3">
          <div className="flex items-center space-x-2">
            <img
              src={template.authorAvatar}
              alt={template.author}
              className="w-5 h-5 rounded-full"
            />
            <span>{template.author}</span>
          </div>
          <div className="flex items-center space-x-1">
            <StarIcon className="w-3 h-3 text-yellow-500" />
            <span>{template.rating}</span>
          </div>
        </div>

        {/* Stats */}
        <div className="flex items-center justify-between text-xs text-secondary mb-4">
          <div className="flex items-center space-x-1">
            <ArrowDownTrayIcon className="w-3 h-3" />
            <span>{template.downloads.toLocaleString()}</span>
          </div>
          <div className="flex items-center space-x-1">
            <EyeIcon className="w-3 h-3" />
            <span>{template.views.toLocaleString()}</span>
          </div>
          <div className="flex items-center space-x-1">
            <CalendarIcon className="w-3 h-3" />
            <span>{new Date(template.createdAt).toLocaleDateString()}</span>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="flex space-x-2">
          <button className="flex-1 bg-accent-primary text-white px-3 py-2 rounded-lg hover:opacity-90 transition-opacity text-sm font-medium">
            {template.price > 0 ? 'Purchase' : 'Download'}
          </button>
          <button className="px-3 py-2 border border-default rounded-lg hover:bg-hover transition-colors text-sm">
            Preview
          </button>
        </div>
      </div>
    </motion.div>
  );

  return (
    <div className="min-h-screen bg-app">
      <div className="max-w-7xl mx-auto px-4 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold text-primary">Template Marketplace</h1>
            <p className="text-secondary mt-2">Discover and purchase professional templates for your business</p>
          </div>
          <button 
            onClick={() => setShowCreateModal(true)}
            className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-2"
          >
            <PlusIcon className="w-4 h-4" />
            <span>Upload Template</span>
          </button>
        </div>

        {/* Search and Filters */}
        <div className="bg-card border border-default rounded-xl p-6 mb-8">
          {/* Search Bar */}
          <div className="relative mb-4">
            <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-secondary" />
            <input
              type="text"
              placeholder="Search templates..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-4 py-3 border border-default rounded-lg bg-surface text-primary placeholder-secondary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none transition-all"
            />
          </div>

          {/* Filter Row */}
          <div className="flex flex-wrap gap-4">
            {/* Category Filter */}
            <select
              value={selectedCategory}
              onChange={(e) => setSelectedCategory(e.target.value)}
              className="px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
            >
              {categories.map(category => (
                <option key={category.id} value={category.id}>
                  {category.name} {category.count > 0 && `(${category.count})`}
                </option>
              ))}
            </select>

            {/* Type Filter */}
            <select
              value={selectedType}
              onChange={(e) => setSelectedType(e.target.value)}
              className="px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
            >
              {templateTypes.map(type => (
                <option key={type.id} value={type.id}>
                  {type.name}
                </option>
              ))}
            </select>

            {/* Price Filter */}
            <select
              value={priceFilter}
              onChange={(e) => setPriceFilter(e.target.value)}
              className="px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
            >
              <option value="all">All Prices</option>
              <option value="free">Free Only</option>
              <option value="paid">Paid Only</option>
            </select>

            {/* Sort By */}
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value)}
              className="px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
            >
              <option value="popular">Most Popular</option>
              <option value="rating">Highest Rated</option>
              <option value="newest">Newest First</option>
              <option value="price-low">Price: Low to High</option>
              <option value="price-high">Price: High to Low</option>
            </select>
          </div>
        </div>

        {/* Results Count */}
        <div className="flex items-center justify-between mb-6">
          <p className="text-secondary">
            {loading ? 'Loading...' : `${templates.length} templates found`}
          </p>
        </div>

        {/* Templates Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          {loading ? (
            // Loading skeletons
            [...Array(6)].map((_, i) => (
              <div key={i} className="bg-card border border-default rounded-xl overflow-hidden animate-pulse">
                <div className="h-48 bg-gray-200 dark:bg-gray-700"></div>
                <div className="p-4">
                  <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                  <div className="h-3 bg-gray-200 dark:bg-gray-700 rounded mb-3 w-3/4"></div>
                  <div className="h-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
              </div>
            ))
          ) : templates.length > 0 ? (
            templates.map(template => (
              <TemplateCard key={template.id} template={template} />
            ))
          ) : (
            <div className="col-span-full text-center py-12">
              <p className="text-secondary">No templates found matching your criteria.</p>
              <button 
                onClick={() => {
                  setSearchTerm('');
                  setSelectedCategory('all');
                  setSelectedType('all');
                  setPriceFilter('all');
                }}
                className="mt-4 text-accent-primary hover:opacity-80 font-medium"
              >
                Clear all filters
              </button>
            </div>
          )}
        </div>

        {/* Load More */}
        {!loading && templates.length > 0 && (
          <div className="text-center">
            <button className="bg-surface border border-default text-primary px-6 py-3 rounded-lg hover:bg-hover transition-colors">
              Load More Templates
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default EnhancedTemplateMarketplace;