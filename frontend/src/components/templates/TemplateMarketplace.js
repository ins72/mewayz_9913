import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  MagnifyingGlassIcon,
  FunnelIcon,
  StarIcon,
  EyeIcon,
  HeartIcon,
  ArrowDownTrayIcon,
  TagIcon,
  UserIcon,
  ClockIcon,
  FireIcon,
  TrophyIcon,
  SparklesIcon
} from '@heroicons/react/24/outline';
import { HeartIcon as HeartIconSolid } from '@heroicons/react/24/solid';

const TemplateMarketplace = () => {
  const [templates, setTemplates] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [sortBy, setSortBy] = useState('popular');
  const [favorites, setFavorites] = useState(new Set());

  const categories = [
    { id: 'all', name: 'All Templates', count: 156 },
    { id: 'business', name: 'Business', count: 45 },
    { id: 'creative', name: 'Creative', count: 38 },
    { id: 'ecommerce', name: 'E-commerce', count: 29 },
    { id: 'portfolio', name: 'Portfolio', count: 24 },
    { id: 'blog', name: 'Blog', count: 20 }
  ];

  const sortOptions = [
    { id: 'popular', name: 'Most Popular' },
    { id: 'newest', name: 'Newest' },
    { id: 'rating', name: 'Highest Rated' },
    { id: 'downloads', name: 'Most Downloaded' },
    { id: 'price-low', name: 'Price: Low to High' },
    { id: 'price-high', name: 'Price: High to Low' }
  ];

  // Mock templates data
  const mockTemplates = [
    {
      id: '1',
      name: 'Modern Business Landing',
      description: 'Clean and professional landing page perfect for modern businesses',
      category: 'business',
      price: 29.99,
      rating: 4.8,
      downloads: 1234,
      author: 'DesignPro',
      thumbnail: '/api/placeholder/400/300',
      tags: ['responsive', 'modern', 'clean'],
      isPro: false,
      isFeatured: true,
      createdAt: '2024-01-15'
    },
    {
      id: '2', 
      name: 'Creative Portfolio Pro',
      description: 'Stunning portfolio template for creative professionals',
      category: 'creative',
      price: 49.99,
      rating: 4.9,
      downloads: 856,
      author: 'CreativeStudio',
      thumbnail: '/api/placeholder/400/300',
      tags: ['portfolio', 'creative', 'animation'],
      isPro: true,
      isFeatured: true,
      createdAt: '2024-01-20'
    },
    {
      id: '3',
      name: 'E-commerce Starter',
      description: 'Complete e-commerce template with all essential features',
      category: 'ecommerce',
      price: 0,
      rating: 4.6,
      downloads: 2156,
      author: 'WebTemplates',
      thumbnail: '/api/placeholder/400/300',
      tags: ['ecommerce', 'shop', 'free'],
      isPro: false,
      isFeatured: false,
      createdAt: '2024-01-10'
    },
    {
      id: '4',
      name: 'Minimalist Blog',
      description: 'Beautiful minimalist blog template with clean typography',
      category: 'blog',
      price: 19.99,
      rating: 4.7,
      downloads: 743,
      author: 'MinimalDesign',
      thumbnail: '/api/placeholder/400/300',
      tags: ['blog', 'minimal', 'typography'],
      isPro: false,
      isFeatured: false,
      createdAt: '2024-01-25'
    },
    {
      id: '5',
      name: 'Agency Premium',
      description: 'Premium agency template with advanced features',
      category: 'business',
      price: 79.99,
      rating: 5.0,
      downloads: 467,
      author: 'AgencyThemes',
      thumbnail: '/api/placeholder/400/300',
      tags: ['agency', 'premium', 'corporate'],
      isPro: true,
      isFeatured: true,
      createdAt: '2024-01-30'
    },
    {
      id: '6',
      name: 'Artist Showcase',
      description: 'Perfect template for artists to showcase their work',
      category: 'portfolio',
      price: 34.99,
      rating: 4.8,
      downloads: 612,
      author: 'ArtisticWeb',
      thumbnail: '/api/placeholder/400/300',
      tags: ['artist', 'gallery', 'showcase'],
      isPro: false,
      isFeatured: false,
      createdAt: '2024-01-18'
    }
  ];

  useEffect(() => {
    // Simulate API call
    setTimeout(() => {
      setTemplates(mockTemplates);
      setLoading(false);
    }, 1000);
  }, []);

  const filteredTemplates = templates.filter(template => {
    const matchesSearch = template.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         template.description.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         template.tags.some(tag => tag.toLowerCase().includes(searchQuery.toLowerCase()));
    
    const matchesCategory = selectedCategory === 'all' || template.category === selectedCategory;
    
    return matchesSearch && matchesCategory;
  }).sort((a, b) => {
    switch (sortBy) {
      case 'popular':
        return b.downloads - a.downloads;
      case 'newest':
        return new Date(b.createdAt) - new Date(a.createdAt);
      case 'rating':
        return b.rating - a.rating;
      case 'downloads':
        return b.downloads - a.downloads;
      case 'price-low':
        return a.price - b.price;
      case 'price-high':
        return b.price - a.price;
      default:
        return 0;
    }
  });

  const toggleFavorite = (templateId) => {
    setFavorites(prev => {
      const newFavorites = new Set(prev);
      if (newFavorites.has(templateId)) {
        newFavorites.delete(templateId);
      } else {
        newFavorites.add(templateId);
      }
      return newFavorites;
    });
  };

  const handleDownload = (template) => {
    // Handle template download/purchase
    console.log('Downloading template:', template.name);
    alert(`${template.price === 0 ? 'Downloading' : 'Purchasing'}: ${template.name}`);
  };

  const handlePreview = (template) => {
    // Handle template preview
    console.log('Previewing template:', template.name);
    alert(`Opening preview for: ${template.name}`);
  };

  if (loading) {
    return (
      <div className="max-w-7xl mx-auto p-6">
        <div className="animate-pulse">
          <div className="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-8"></div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3, 4, 5, 6].map(i => (
              <div key={i} className="h-80 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            ))}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto p-6">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 dark:text-white mb-4">
          Template Marketplace
        </h1>
        <p className="text-gray-600 dark:text-gray-300">
          Discover and download professional templates for your projects
        </p>
      </div>

      {/* Search and Filters */}
      <div className="mb-8 space-y-4">
        {/* Search Bar */}
        <div className="relative">
          <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
          <input
            type="text"
            placeholder="Search templates, tags, or authors..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        {/* Categories and Sort */}
        <div className="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
          {/* Categories */}
          <div className="flex flex-wrap gap-2">
            {categories.map((category) => (
              <button
                key={category.id}
                onClick={() => setSelectedCategory(category.id)}
                className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
                  selectedCategory === category.id
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
                }`}
              >
                {category.name} ({category.count})
              </button>
            ))}
          </div>

          {/* Sort */}
          <div className="flex items-center space-x-4">
            <div className="flex items-center">
              <FunnelIcon className="h-5 w-5 text-gray-500 mr-2" />
              <select
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value)}
                className="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
              >
                {sortOptions.map((option) => (
                  <option key={option.id} value={option.id}>
                    {option.name}
                  </option>
                ))}
              </select>
            </div>
          </div>
        </div>
      </div>

      {/* Featured Templates */}
      {selectedCategory === 'all' && (
        <div className="mb-8">
          <h2 className="text-2xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <FireIcon className="h-6 w-6 text-orange-500 mr-2" />
            Featured Templates
          </h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {templates.filter(t => t.isFeatured).slice(0, 3).map((template) => (
              <motion.div
                key={template.id}
                whileHover={{ y: -5 }}
                className="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border-2 border-yellow-200 dark:border-yellow-800"
              >
                {/* Template Image */}
                <div className="relative">
                  <img
                    src={`https://picsum.photos/400/300?random=${template.id}`}
                    alt={template.name}
                    className="w-full h-48 object-cover"
                  />
                  <div className="absolute top-3 left-3">
                    <span className="bg-yellow-500 text-white px-2 py-1 rounded text-xs font-medium flex items-center">
                      <TrophyIcon className="h-3 w-3 mr-1" />
                      Featured
                    </span>
                  </div>
                  <div className="absolute top-3 right-3 flex space-x-2">
                    <button
                      onClick={() => toggleFavorite(template.id)}
                      className="p-2 bg-black bg-opacity-50 rounded-full text-white hover:bg-opacity-70 transition-opacity"
                    >
                      {favorites.has(template.id) ? (
                        <HeartIconSolid className="h-4 w-4 text-red-500" />
                      ) : (
                        <HeartIcon className="h-4 w-4" />
                      )}
                    </button>
                    <button
                      onClick={() => handlePreview(template)}
                      className="p-2 bg-black bg-opacity-50 rounded-full text-white hover:bg-opacity-70 transition-opacity"
                    >
                      <EyeIcon className="h-4 w-4" />
                    </button>
                  </div>
                </div>

                {/* Template Info */}
                <div className="p-6">
                  <div className="flex items-start justify-between mb-2">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                      {template.name}
                    </h3>
                    {template.isPro && (
                      <span className="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-2 py-1 rounded text-xs font-medium">
                        PRO
                      </span>
                    )}
                  </div>

                  <p className="text-gray-600 dark:text-gray-300 text-sm mb-4">
                    {template.description}
                  </p>

                  {/* Tags */}
                  <div className="flex flex-wrap gap-1 mb-4">
                    {template.tags.slice(0, 3).map((tag) => (
                      <span
                        key={tag}
                        className="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded text-xs"
                      >
                        #{tag}
                      </span>
                    ))}
                  </div>

                  {/* Stats */}
                  <div className="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                    <div className="flex items-center space-x-4">
                      <div className="flex items-center">
                        <StarIcon className="h-4 w-4 text-yellow-500 mr-1" />
                        {template.rating}
                      </div>
                      <div className="flex items-center">
                        <ArrowDownTrayIcon className="h-4 w-4 mr-1" />
                        {template.downloads.toLocaleString()}
                      </div>
                    </div>
                    <div className="flex items-center">
                      <UserIcon className="h-4 w-4 mr-1" />
                      {template.author}
                    </div>
                  </div>

                  {/* Price and Action */}
                  <div className="flex items-center justify-between">
                    <div className="text-2xl font-bold text-gray-900 dark:text-white">
                      {template.price === 0 ? 'Free' : `$${template.price}`}
                    </div>
                    <button
                      onClick={() => handleDownload(template)}
                      className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                      {template.price === 0 ? 'Download' : 'Purchase'}
                    </button>
                  </div>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      )}

      {/* All Templates */}
      <div>
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
          {selectedCategory === 'all' ? 'All Templates' : categories.find(c => c.id === selectedCategory)?.name}
          <span className="text-gray-500 text-base ml-2">({filteredTemplates.length} templates)</span>
        </h2>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredTemplates.map((template) => (
            <motion.div
              key={template.id}
              whileHover={{ y: -3 }}
              className="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
            >
              {/* Template Image */}
              <div className="relative">
                <img
                  src={`https://picsum.photos/400/300?random=${template.id}`}
                  alt={template.name}
                  className="w-full h-48 object-cover"
                />
                <div className="absolute top-3 right-3 flex space-x-2">
                  <button
                    onClick={() => toggleFavorite(template.id)}
                    className="p-2 bg-black bg-opacity-50 rounded-full text-white hover:bg-opacity-70 transition-opacity"
                  >
                    {favorites.has(template.id) ? (
                      <HeartIconSolid className="h-4 w-4 text-red-500" />
                    ) : (
                      <HeartIcon className="h-4 w-4" />
                    )}
                  </button>
                  <button
                    onClick={() => handlePreview(template)}
                    className="p-2 bg-black bg-opacity-50 rounded-full text-white hover:bg-opacity-70 transition-opacity"
                  >
                    <EyeIcon className="h-4 w-4" />
                  </button>
                </div>
                {template.isPro && (
                  <div className="absolute top-3 left-3">
                    <span className="bg-purple-600 text-white px-2 py-1 rounded text-xs font-medium flex items-center">
                      <SparklesIcon className="h-3 w-3 mr-1" />
                      PRO
                    </span>
                  </div>
                )}
              </div>

              {/* Template Info */}
              <div className="p-6">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  {template.name}
                </h3>

                <p className="text-gray-600 dark:text-gray-300 text-sm mb-4">
                  {template.description}
                </p>

                {/* Tags */}
                <div className="flex flex-wrap gap-1 mb-4">
                  {template.tags.slice(0, 3).map((tag) => (
                    <span
                      key={tag}
                      className="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded text-xs"
                    >
                      #{tag}
                    </span>
                  ))}
                </div>

                {/* Stats */}
                <div className="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                  <div className="flex items-center space-x-3">
                    <div className="flex items-center">
                      <StarIcon className="h-4 w-4 text-yellow-500 mr-1" />
                      {template.rating}
                    </div>
                    <div className="flex items-center">
                      <ArrowDownTrayIcon className="h-4 w-4 mr-1" />
                      {template.downloads.toLocaleString()}
                    </div>
                  </div>
                  <div className="text-xs text-gray-400">
                    by {template.author}
                  </div>
                </div>

                {/* Price and Action */}
                <div className="flex items-center justify-between">
                  <div className="text-xl font-bold text-gray-900 dark:text-white">
                    {template.price === 0 ? 'Free' : `$${template.price}`}
                  </div>
                  <button
                    onClick={() => handleDownload(template)}
                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
                  >
                    {template.price === 0 ? 'Download' : 'Purchase'}
                  </button>
                </div>
              </div>
            </motion.div>
          ))}
        </div>

        {/* Empty State */}
        {filteredTemplates.length === 0 && (
          <div className="text-center py-12">
            <TagIcon className="h-12 w-12 text-gray-400 mx-auto mb-4" />
            <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
              No templates found
            </h3>
            <p className="text-gray-600 dark:text-gray-300">
              Try adjusting your search or filter criteria
            </p>
          </div>
        )}
      </div>
    </div>
  );
};

export default TemplateMarketplace;