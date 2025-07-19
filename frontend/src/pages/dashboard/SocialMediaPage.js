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
      {/* Create New Post */}
      <div className="bg-surface p-6 rounded-lg shadow-default">
        <div className="flex items-center justify-between mb-4">
          <h3 className="text-lg font-semibold text-primary">Schedule New Post</h3>
          <button className="btn btn-primary flex items-center space-x-2">
            <PlusIcon className="h-4 w-4" />
            <span>Create Post</span>
          </button>
        </div>
        
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div className="lg:col-span-2">
            <label className="block text-sm font-medium text-secondary mb-2">Caption</label>
            <textarea
              rows={4}
              placeholder="What's happening? Write your post caption here..."
              className="input w-full resize-none"
            />
          </div>
          
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Platforms</label>
              <div className="space-y-2">
                {['Instagram', 'Facebook', 'Twitter', 'LinkedIn'].map((platform) => (
                  <label key={platform} className="flex items-center">
                    <input type="checkbox" className="mr-2" />
                    <span className="text-sm text-primary">{platform}</span>
                  </label>
                ))}
              </div>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Schedule Date</label>
              <input type="datetime-local" className="input w-full" />
            </div>
          </div>
        </div>
      </div>

      {/* Scheduled Posts */}
      <div className="bg-surface rounded-lg shadow-default overflow-hidden">
        <div className="p-6 border-b border-default">
          <h3 className="text-xl font-semibold text-primary">Scheduled Posts</h3>
        </div>
        
        <div className="divide-y divide-default">
          {scheduledPosts.map((post) => (
            <div key={post.id} className="p-6 hover:bg-surface-hover transition-colors">
              <div className="flex items-start justify-between">
                <div className="flex-1">
                  <p className="text-primary mb-2 line-clamp-2">{post.caption}</p>
                  
                  <div className="flex items-center space-x-4 mb-4">
                    <div className="flex items-center space-x-2">
                      <CalendarIcon className="h-4 w-4 text-secondary" />
                      <span className="text-sm text-secondary">
                        {new Date(post.scheduledDate).toLocaleString()}
                      </span>
                    </div>
                    
                    <div className="flex items-center space-x-2">
                      {post.platforms.map((platform) => (
                        <span key={platform} className="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 rounded-full text-xs">
                          {platform}
                        </span>
                      ))}
                    </div>
                    
                    <span className="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded-full text-xs">
                      {post.status}
                    </span>
                  </div>
                  
                  <div className="flex items-center space-x-2 text-sm text-secondary">
                    {post.hashtags.map((hashtag) => (
                      <span key={hashtag} className="text-blue-500">{hashtag}</span>
                    ))}
                  </div>
                </div>
                
                <div className="flex items-center space-x-2 ml-4">
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <EyeIcon className="h-5 w-5" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <FilterIcon className="h-5 w-5" />
                  </button>
                </div>
              </div>
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