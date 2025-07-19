import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import {
  ChartBarIcon,
  UserGroupIcon,
  CalendarIcon,
  MagnifyingGlassIcon,
  PlusIcon,
  FilterIcon,
  ArrowDownTrayIcon,
  EyeIcon,
  HeartIcon,
  ChatBubbleOvalLeftIcon,
  ShareIcon,
  AdjustmentsHorizontalIcon,
  CheckIcon,
  ClockIcon
} from '@heroicons/react/24/outline';

const SocialMediaPage = () => {
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('database');
  const [searchQuery, setSearchQuery] = useState('');
  const [filters, setFilters] = useState({
    followerRange: 'all',
    engagementRate: 'all',
    accountType: 'all',
    location: '',
    hashtags: ''
  });

  // Mock Instagram database data
  const [instagramAccounts, setInstagramAccounts] = useState([
    {
      id: '1',
      username: 'fitness_guru_miami',
      displayName: 'Miami Fitness Guru',
      followers: 45000,
      following: 2300,
      posts: 892,
      engagementRate: 4.2,
      accountType: 'business',
      verified: false,
      profilePicture: null,
      bio: '💪 Fitness coach in Miami | Personal training | Nutrition tips | DM for coaching',
      location: 'Miami, FL',
      email: 'contact@fitnessguru.com',
      website: 'www.fitnessguru.com',
      lastActive: '2 hours ago'
    },
    {
      id: '2',
      username: 'tech_startup_nyc',
      displayName: 'TechStartup NYC',
      followers: 15000,
      following: 1200,
      posts: 456,
      engagementRate: 6.8,
      accountType: 'business',
      verified: true,
      profilePicture: null,
      bio: '🚀 Building the future of tech | NYC-based startup | Join our journey',
      location: 'New York, NY',
      email: 'hello@techstartup.com',
      website: 'www.techstartup.com',
      lastActive: '1 day ago'
    },
    {
      id: '3',
      username: 'food_blogger_la',
      displayName: 'LA Food Explorer',
      followers: 85000,
      following: 3400,
      posts: 1240,
      engagementRate: 3.9,
      accountType: 'creator',
      verified: true,
      profilePicture: null,
      bio: '🍕 Exploring LA\'s best eats | Food reviews | Restaurant recommendations',
      location: 'Los Angeles, CA',
      email: 'collab@lafoodie.com',
      website: 'www.lafoodexplorer.com',
      lastActive: '30 minutes ago'
    }
  ]);

  const [scheduledPosts, setScheduledPosts] = useState([
    {
      id: '1',
      caption: 'New product launch coming soon! 🚀 Stay tuned for something amazing...',
      platforms: ['instagram', 'facebook', 'twitter'],
      media: ['image1.jpg'],
      scheduledDate: '2025-07-21T14:00:00Z',
      status: 'scheduled',
      hashtags: ['#launch', '#product', '#amazing']
    },
    {
      id: '2',
      caption: 'Behind the scenes of our latest project. The team is working hard! 💪',
      platforms: ['instagram', 'linkedin'],
      media: ['video1.mp4'],
      scheduledDate: '2025-07-22T10:30:00Z',
      status: 'scheduled',
      hashtags: ['#behindthescenes', '#team', '#work']
    }
  ]);

  const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
  };

  const getAccountTypeColor = (type) => {
    switch (type) {
      case 'business': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
      case 'creator': return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300';
      case 'personal': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
      default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
  };

  const renderInstagramDatabase = () => (
    <div className="space-y-6">
      {/* Search and Filters */}
      <div className="bg-surface p-6 rounded-lg shadow-default">
        <h3 className="text-lg font-semibold text-primary mb-4">Instagram Database Search</h3>
        
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-4">
          <div className="lg:col-span-2">
            <div className="relative">
              <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search by username, hashtags, or keywords..."
                className="input w-full pl-10"
              />
            </div>
          </div>
          
          <select
            value={filters.followerRange}
            onChange={(e) => setFilters(prev => ({ ...prev, followerRange: e.target.value }))}
            className="input"
          >
            <option value="all">All Followers</option>
            <option value="1k-10k">1K - 10K</option>
            <option value="10k-50k">10K - 50K</option>
            <option value="50k-100k">50K - 100K</option>
            <option value="100k+">100K+</option>
          </select>
          
          <select
            value={filters.accountType}
            onChange={(e) => setFilters(prev => ({ ...prev, accountType: e.target.value }))}
            className="input"
          >
            <option value="all">All Account Types</option>
            <option value="business">Business</option>
            <option value="creator">Creator</option>
            <option value="personal">Personal</option>
          </select>
        </div>
        
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-4">
            <button className="btn btn-secondary flex items-center space-x-2">
              <AdjustmentsHorizontalIcon className="h-4 w-4" />
              <span>Advanced Filters</span>
            </button>
            <span className="text-sm text-secondary">
              {instagramAccounts.length} accounts found
            </span>
          </div>
          
          <div className="flex items-center space-x-2">
            <button className="btn btn-secondary flex items-center space-x-2">
              <ArrowDownTrayIcon className="h-4 w-4" />
              <span>Export CSV</span>
            </button>
            <button className="btn btn-primary flex items-center space-x-2">
              <PlusIcon className="h-4 w-4" />
              <span>Add to Campaign</span>
            </button>
          </div>
        </div>
      </div>

      {/* Instagram Accounts List */}
      <div className="bg-surface rounded-lg shadow-default overflow-hidden">
        <div className="p-6 border-b border-default">
          <h3 className="text-xl font-semibold text-primary">Instagram Accounts Database</h3>
        </div>
        
        <div className="divide-y divide-default">
          {instagramAccounts.map((account) => (
            <div key={account.id} className="p-6 hover:bg-surface-hover transition-colors">
              <div className="flex items-start justify-between">
                <div className="flex items-start space-x-4">
                  <div className="w-16 h-16 bg-gradient-to-br from-pink-500 to-orange-500 rounded-full flex items-center justify-center">
                    <span className="text-white font-semibold text-lg">
                      {account.displayName.charAt(0)}
                    </span>
                  </div>
                  
                  <div className="flex-1">
                    <div className="flex items-center space-x-2 mb-2">
                      <h4 className="text-lg font-semibold text-primary">@{account.username}</h4>
                      {account.verified && (
                        <div className="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                          <CheckIcon className="h-3 w-3 text-white" />
                        </div>
                      )}
                      <span className={`px-2 py-1 rounded-full text-xs font-medium ${getAccountTypeColor(account.accountType)}`}>
                        {account.accountType}
                      </span>
                    </div>
                    
                    <p className="text-lg font-medium text-primary mb-2">{account.displayName}</p>
                    <p className="text-sm text-secondary mb-4 line-clamp-2">{account.bio}</p>
                    
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                      <div className="text-center">
                        <p className="text-lg font-bold text-primary">{formatNumber(account.followers)}</p>
                        <p className="text-xs text-secondary">Followers</p>
                      </div>
                      <div className="text-center">
                        <p className="text-lg font-bold text-primary">{formatNumber(account.following)}</p>
                        <p className="text-xs text-secondary">Following</p>
                      </div>
                      <div className="text-center">
                        <p className="text-lg font-bold text-primary">{account.posts}</p>
                        <p className="text-xs text-secondary">Posts</p>
                      </div>
                      <div className="text-center">
                        <p className="text-lg font-bold text-primary">{account.engagementRate}%</p>
                        <p className="text-xs text-secondary">Engagement</p>
                      </div>
                    </div>
                    
                    <div className="flex items-center space-x-4 text-sm text-secondary">
                      <span>📍 {account.location}</span>
                      <span>🌐 {account.website}</span>
                      <span>📧 {account.email}</span>
                      <span>🕐 {account.lastActive}</span>
                    </div>
                  </div>
                </div>
                
                <div className="flex items-center space-x-2">
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <EyeIcon className="h-5 w-5" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <HeartIcon className="h-5 w-5" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <ChatBubbleOvalLeftIcon className="h-5 w-5" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <ShareIcon className="h-5 w-5" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );

  const renderPostScheduling = () => (
    <div className="space-y-6">
      {/* Platform Selection & Quick Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-surface-elevated p-4 rounded-xl shadow-default">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-secondary">Instagram</p>
              <p className="text-2xl font-bold text-primary">47</p>
              <p className="text-xs text-green-500">+12 this week</p>
            </div>
            <div className="w-12 h-12 bg-gradient-to-br from-pink-500 via-red-500 to-yellow-500 rounded-xl flex items-center justify-center">
              <span className="text-white font-bold">IG</span>
            </div>
          </div>
        </div>
        
        <div className="bg-surface-elevated p-4 rounded-xl shadow-default">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-secondary">Facebook</p>
              <p className="text-2xl font-bold text-primary">23</p>
              <p className="text-xs text-blue-500">+5 this week</p>
            </div>
            <div className="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
              <span className="text-white font-bold">FB</span>
            </div>
          </div>
        </div>
        
        <div className="bg-surface-elevated p-4 rounded-xl shadow-default">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-secondary">Twitter</p>
              <p className="text-2xl font-bold text-primary">89</p>
              <p className="text-xs text-green-500">+34 this week</p>
            </div>
            <div className="w-12 h-12 bg-sky-400 rounded-xl flex items-center justify-center">
              <span className="text-white font-bold">TW</span>
            </div>
          </div>
        </div>
        
        <div className="bg-surface-elevated p-4 rounded-xl shadow-default">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-secondary">LinkedIn</p>
              <p className="text-2xl font-bold text-primary">12</p>
              <p className="text-xs text-blue-500">+3 this week</p>
            </div>
            <div className="w-12 h-12 bg-blue-700 rounded-xl flex items-center justify-center">
              <span className="text-white font-bold">LI</span>
            </div>
          </div>
        </div>
      </div>

      {/* Create New Post Section */}
      <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
        <div className="flex items-center justify-between mb-6">
          <h3 className="text-xl font-semibold text-primary">Create New Post</h3>
          <div className="flex space-x-3">
            <button className="btn btn-secondary flex items-center space-x-2">
              <ClockIcon className="h-4 w-4" />
              <span>Schedule Later</span>
            </button>
            <button className="btn btn-primary flex items-center space-x-2">
              <PlusIcon className="h-4 w-4" />
              <span>Post Now</span>
            </button>
          </div>
        </div>
        
        {/* Platform Selection */}
        <div className="mb-6">
          <p className="text-sm font-medium text-primary mb-3">Select Platforms</p>
          <div className="flex flex-wrap gap-3">
            {[
              { name: 'Instagram', color: 'from-pink-500 to-yellow-500', checked: true },
              { name: 'Facebook', color: 'from-blue-600 to-blue-700', checked: false },
              { name: 'Twitter', color: 'from-sky-400 to-sky-500', checked: true },
              { name: 'LinkedIn', color: 'from-blue-700 to-blue-800', checked: false },
              { name: 'TikTok', color: 'from-black to-gray-800', checked: false },
              { name: 'YouTube', color: 'from-red-600 to-red-700', checked: false }
            ].map((platform) => (
              <label key={platform.name} className="flex items-center cursor-pointer">
                <input type="checkbox" defaultChecked={platform.checked} className="sr-only" />
                <div className={`w-4 h-4 bg-gradient-to-r ${platform.color} rounded mr-2 ${platform.checked ? 'opacity-100' : 'opacity-50'}`}></div>
                <span className="text-sm text-primary">{platform.name}</span>
              </label>
            ))}
          </div>
        </div>
        
        {/* Content Creation */}
        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-primary mb-2">Post Content</label>
            <textarea
              className="input w-full h-32 resize-none"
              placeholder="What would you like to share?"
            ></textarea>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-primary mb-2">Media Upload</label>
              <div className="border-2 border-dashed border-default rounded-lg p-6 text-center hover:bg-surface-hover transition-colors">
                <div className="mx-auto w-12 h-12 bg-surface-elevated rounded-full flex items-center justify-center mb-4">
                  <PlusIcon className="h-6 w-6 text-secondary" />
                </div>
                <p className="text-sm text-secondary mb-2">Drop images or videos here</p>
                <button className="text-sm text-blue-500 hover:text-blue-600">Browse files</button>
              </div>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-primary mb-2">Schedule Settings</label>
              <div className="space-y-3">
                <div className="flex items-center space-x-3">
                  <input type="radio" id="now" name="schedule" className="text-blue-500" defaultChecked />
                  <label htmlFor="now" className="text-sm text-primary">Post now</label>
                </div>
                <div className="flex items-center space-x-3">
                  <input type="radio" id="schedule" name="schedule" className="text-blue-500" />
                  <label htmlFor="schedule" className="text-sm text-primary">Schedule for later</label>
                </div>
                <div className="flex items-center space-x-3">
                  <input type="radio" id="optimal" name="schedule" className="text-blue-500" />
                  <label htmlFor="optimal" className="text-sm text-primary">AI optimal time</label>
                </div>
              </div>
            </div>
          </div>
          
          {/* Hashtag Suggestions */}
          <div>
            <label className="block text-sm font-medium text-primary mb-2">Hashtag Suggestions</label>
            <div className="flex flex-wrap gap-2">
              {[
                '#socialmedia', '#marketing', '#business', '#growth', '#content',
                '#engagement', '#brand', '#digital', '#strategy', '#trending'
              ].map((hashtag) => (
                <button
                  key={hashtag}
                  className="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-sm rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                >
                  {hashtag}
                </button>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Scheduled Posts */}
      <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
        <div className="flex items-center justify-between mb-6">
          <h3 className="text-xl font-semibold text-primary">Scheduled Posts</h3>
          <div className="flex items-center space-x-3">
            <div className="text-sm text-secondary">
              <span className="font-medium">23</span> posts scheduled this week
            </div>
            <button className="btn btn-secondary btn-sm">
              <CalendarIcon className="h-4 w-4 mr-1" />
              Calendar View
            </button>
          </div>
        </div>
        
        <div className="space-y-4">
          {[
            {
              id: 1,
              content: "🚀 Excited to announce our new product launch! Check out the amazing features we've built for you...",
              platforms: ['Instagram', 'Facebook', 'Twitter'],
              scheduledFor: 'Today, 3:00 PM',
              status: 'scheduled',
              image: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=100'
            },
            {
              id: 2,
              content: "Behind the scenes: Our team working hard to bring you the best experience. #teamwork #innovation",
              platforms: ['Instagram', 'LinkedIn'],
              scheduledFor: 'Tomorrow, 9:00 AM',
              status: 'scheduled',
              image: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=100'
            },
            {
              id: 3,
              content: "Customer success story: How Sarah increased her business revenue by 300% using our platform! 📈",
              platforms: ['Facebook', 'LinkedIn', 'Twitter'],
              scheduledFor: 'Jan 20, 2:30 PM',
              status: 'draft',
              image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=100'
            }
          ].map((post) => (
            <div key={post.id} className="border border-default rounded-lg p-4 hover:shadow-default transition-shadow">
              <div className="flex items-start space-x-4">
                <img
                  src={post.image}
                  alt="Post preview"
                  className="w-16 h-16 rounded-lg object-cover"
                />
                <div className="flex-1 min-w-0">
                  <p className="text-sm text-primary mb-2 line-clamp-2">{post.content}</p>
                  <div className="flex items-center space-x-4 text-sm text-secondary mb-2">
                    <div className="flex items-center space-x-1">
                      <CalendarIcon className="h-4 w-4" />
                      <span>{post.scheduledFor}</span>
                    </div>
                    <div className="flex items-center space-x-2">
                      {post.platforms.map((platform, idx) => (
                        <span key={idx} className="px-2 py-1 bg-surface rounded text-xs">
                          {platform}
                        </span>
                      ))}
                    </div>
                    <div className={`px-2 py-1 rounded-full text-xs font-medium ${
                      post.status === 'scheduled' 
                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                    }`}>
                      {post.status}
                    </div>
                  </div>
                </div>
                <div className="flex items-center space-x-2">
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors">
                    <EyeIcon className="h-4 w-4" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors">
                    <AdjustmentsHorizontalIcon className="h-4 w-4" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors">
                    <ShareIcon className="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* AI Content Suggestions */}
      <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
        <h3 className="text-xl font-semibold text-primary mb-4">AI Content Suggestions</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {[
            {
              type: 'Trending Topic',
              title: 'AI in Social Media Marketing',
              description: 'Create content about the latest AI trends in marketing',
              engagement: '+45% expected engagement'
            },
            {
              type: 'User-Generated Content',
              title: 'Customer Success Stories',
              description: 'Share testimonials and success stories from your customers',
              engagement: '+32% expected engagement'
            },
            {
              type: 'Educational Content',
              title: 'How-to Guide Series',
              description: 'Create step-by-step tutorials for your audience',
              engagement: '+28% expected engagement'
            },
            {
              type: 'Behind the Scenes',
              title: 'Team & Culture Content',
              description: 'Show your company culture and team dynamics',
              engagement: '+51% expected engagement'
            }
          ].map((suggestion, idx) => (
            <div key={idx} className="border border-default rounded-lg p-4 hover:shadow-default transition-shadow">
              <div className="flex items-start justify-between mb-3">
                <div>
                  <span className="text-xs text-blue-500 font-medium">{suggestion.type}</span>
                  <h4 className="font-semibold text-primary">{suggestion.title}</h4>
                </div>
                <button className="btn btn-primary btn-sm">Use</button>
              </div>
              <p className="text-sm text-secondary mb-2">{suggestion.description}</p>
              <p className="text-sm text-green-500 font-medium">{suggestion.engagement}</p>
            </div>
          ))}
        </div>
      </div>
    </div>
  );

  const renderAnalytics = () => (
    <div className="space-y-6">
      {/* Analytics Overview */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {[
          { label: 'Total Reach', value: '127K', change: '+12.5%', color: 'text-green-500' },
          { label: 'Engagement', value: '8.9K', change: '+5.2%', color: 'text-blue-500' },
          { label: 'New Followers', value: '2.1K', change: '+8.7%', color: 'text-purple-500' },
          { label: 'Post Performance', value: '94%', change: '+3.1%', color: 'text-orange-500' }
        ].map((stat, index) => (
          <div key={index} className="bg-surface p-6 rounded-lg shadow-default">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary">{stat.label}</p>
                <p className="text-2xl font-bold text-primary">{stat.value}</p>
              </div>
              <span className={`text-sm font-medium ${stat.color}`}>{stat.change}</span>
            </div>
          </div>
        ))}
      </div>

      {/* Charts would go here */}
      <div className="bg-surface p-6 rounded-lg shadow-default">
        <h3 className="text-lg font-semibold text-primary mb-4">Performance Analytics</h3>
        <div className="h-64 bg-surface-hover rounded-lg flex items-center justify-center">
          <p className="text-secondary">Analytics charts would be implemented here</p>
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
        <h1 className="text-3xl font-bold text-primary mb-2">Social Media Management</h1>
        <p className="text-secondary">Manage your social media presence with advanced tools and analytics</p>
      </motion.div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="flex space-x-8">
          {[
            { id: 'database', name: 'Instagram Database', icon: UserGroupIcon },
            { id: 'scheduling', name: 'Post Scheduling', icon: CalendarIcon },
            { id: 'analytics', name: 'Analytics', icon: ChartBarIcon }
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
        {activeTab === 'database' && renderInstagramDatabase()}
        {activeTab === 'scheduling' && renderPostScheduling()}
        {activeTab === 'analytics' && renderAnalytics()}
      </motion.div>
    </div>
  );
};

export default SocialMediaPage;