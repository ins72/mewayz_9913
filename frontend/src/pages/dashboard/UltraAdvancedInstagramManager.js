import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  MagnifyingGlassIcon,
  FunnelIcon,
  ArrowDownTrayIcon,
  SparklesIcon,
  UserGroupIcon,
  HeartIcon,
  ChatBubbleLeftIcon,
  ShareIcon,
  BookmarkIcon,
  EyeIcon,
  MapPinIcon,
  CalendarIcon,
  ClockIcon,
  ArrowTrendingUpIcon,
  HashtagIcon,
  PhotoIcon,
  VideoCameraIcon,
  ChartBarIcon,
  DocumentArrowDownIcon,
  PlusIcon,
  StarIcon,
  FireIcon,
  GlobeAltIcon,
  TagIcon,
  UserIcon,
  CameraIcon,
  MicrophoneIcon,
  PlayIcon
} from '@heroicons/react/24/outline';
import {
  HeartIcon as HeartIconSolid,
  StarIcon as StarIconSolid,
  FireIcon as FireIconSolid
} from '@heroicons/react/24/solid';

const UltraAdvancedInstagramManager = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  const [activeTab, setActiveTab] = useState('database');
  const [searchQuery, setSearchQuery] = useState('');
  const [filters, setFilters] = useState({
    followerRange: [1000, 1000000],
    engagementRate: [1, 10],
    location: '',
    accountType: 'all',
    verified: false,
    hasEmail: false,
    recentPost: 7
  });
  
  const [accounts, setAccounts] = useState([]);
  const [loading, setLoading] = useState(false);
  const [selectedAccounts, setSelectedAccounts] = useState([]);
  const [showFilters, setShowFilters] = useState(false);
  const [aiInsights, setAiInsights] = useState(null);
  
  // Mock Instagram data
  const mockAccounts = [
    {
      id: 1,
      username: 'techinfluencer',
      displayName: 'Tech Influencer',
      followers: 125000,
      following: 890,
      posts: 1247,
      profilePicture: 'https://ui-avatars.com/api/?name=Tech+Influencer&background=3b82f6&color=fff',
      bio: '🚀 Tech entrepreneur | AI enthusiast | Building the future',
      website: 'techinfluencer.com',
      email: 'hello@techinfluencer.com',
      verified: true,
      accountType: 'creator',
      location: 'San Francisco, CA',
      engagementRate: 4.2,
      avgLikes: 3500,
      avgComments: 180,
      lastPost: '2025-01-18',
      topHashtags: ['#tech', '#AI', '#innovation', '#startup'],
      category: 'Technology',
      contentTypes: ['photos', 'reels', 'stories'],
      postingFrequency: 'daily',
      bestPostTime: '6:00 PM',
      collaborationHistory: [],
      brand_safety_score: 9.2
    },
    {
      id: 2,
      username: 'lifestyle_guru',
      displayName: 'Lifestyle Guru',
      followers: 89500,
      following: 1234,
      posts: 892,
      profilePicture: 'https://ui-avatars.com/api/?name=Lifestyle+Guru&background=ec4899&color=fff',
      bio: '✨ Living my best life | Wellness coach | 📍 Los Angeles',
      website: 'lifestyleguru.co',
      email: null,
      verified: false,
      accountType: 'personal',
      location: 'Los Angeles, CA',
      engagementRate: 6.8,
      avgLikes: 2800,
      avgComments: 145,
      lastPost: '2025-01-19',
      topHashtags: ['#lifestyle', '#wellness', '#selflove', '#motivation'],
      category: 'Lifestyle',
      contentTypes: ['photos', 'reels', 'igtv'],
      postingFrequency: '5x/week',
      bestPostTime: '7:00 PM',
      collaborationHistory: ['@brandname1', '@brandname2'],
      brand_safety_score: 8.7
    },
    {
      id: 3,
      username: 'foodie_adventures',
      displayName: 'Foodie Adventures',
      followers: 245000,
      following: 567,
      posts: 2156,
      profilePicture: 'https://ui-avatars.com/api/?name=Foodie+Adventures&background=10b981&color=fff',
      bio: '🍕 Food lover | Restaurant reviews | NYC based foodie',
      website: null,
      email: 'partnerships@foodieadventures.com',
      verified: true,
      accountType: 'creator',
      location: 'New York, NY',
      engagementRate: 3.5,
      avgLikes: 8500,
      avgComments: 420,
      lastPost: '2025-01-19',
      topHashtags: ['#food', '#foodie', '#nyceats', '#restaurant'],
      category: 'Food & Beverage',
      contentTypes: ['photos', 'reels', 'stories'],
      postingFrequency: 'daily',
      bestPostTime: '12:00 PM',
      collaborationHistory: ['@restaurant1', '@foodbrand'],
      brand_safety_score: 9.5
    },
    {
      id: 4,
      username: 'fitness_journey',
      displayName: 'Fitness Journey',
      followers: 67800,
      following: 234,
      posts: 743,
      profilePicture: 'https://ui-avatars.com/api/?name=Fitness+Journey&background=f59e0b&color=fff',
      bio: '💪 Personal trainer | Fitness motivation | Transform your life',
      website: 'fitnessjourney.app',
      email: 'contact@fitnessjourney.app',
      verified: false,
      accountType: 'business',
      location: 'Miami, FL',
      engagementRate: 5.9,
      avgLikes: 2200,
      avgComments: 89,
      lastPost: '2025-01-19',
      topHashtags: ['#fitness', '#workout', '#motivation', '#health'],
      category: 'Health & Fitness',
      contentTypes: ['reels', 'igtv', 'photos'],
      postingFrequency: '6x/week',
      bestPostTime: '6:00 AM',
      collaborationHistory: ['@supplementbrand'],
      brand_safety_score: 9.1
    }
  ];
  
  const [contentAnalytics, setContentAnalytics] = useState({
    topPerformingPosts: [
      {
        id: 1,
        type: 'reel',
        thumbnail: 'https://ui-avatars.com/api/?name=Reel+1&background=3b82f6&color=fff',
        likes: 15420,
        comments: 234,
        shares: 89,
        saves: 567,
        caption: '5 AI tools that will change your business forever 🚀',
        hashtags: ['#AI', '#business', '#productivity'],
        posted: '2025-01-18'
      },
      {
        id: 2,
        type: 'carousel',
        thumbnail: 'https://ui-avatars.com/api/?name=Carousel+2&background=ec4899&color=fff',
        likes: 12890,
        comments: 178,
        shares: 45,
        saves: 432,
        caption: 'Morning routine that changed my life ✨',
        hashtags: ['#morningroutine', '#lifestyle', '#wellness'],
        posted: '2025-01-17'
      }
    ],
    engagementTrends: [
      { date: '2025-01-13', likes: 8500, comments: 234, shares: 45 },
      { date: '2025-01-14', likes: 9200, comments: 267, shares: 52 },
      { date: '2025-01-15', likes: 11300, comments: 298, shares: 67 },
      { date: '2025-01-16', likes: 10800, comments: 245, shares: 58 },
      { date: '2025-01-17', likes: 12890, comments: 356, shares: 78 },
      { date: '2025-01-18', likes: 15420, comments: 423, shares: 89 },
      { date: '2025-01-19', likes: 13650, comments: 387, shares: 72 }
    ],
    hashtagPerformance: [
      { hashtag: '#AI', posts: 45, avgLikes: 8500, avgComments: 234 },
      { hashtag: '#tech', posts: 67, avgLikes: 7200, avgComments: 189 },
      { hashtag: '#innovation', posts: 34, avgLikes: 6800, avgComments: 156 },
      { hashtag: '#startup', posts: 23, avgLikes: 5900, avgComments: 123 }
    ]
  });
  
  useEffect(() => {
    setAccounts(mockAccounts);
    generateAIInsights();
  }, []);
  
  const generateAIInsights = async () => {
    // Mock AI insights generation
    setTimeout(() => {
      setAiInsights({
        recommendations: [
          {
            type: 'content_opportunity',
            title: 'Trending Content Gap',
            description: 'AI automation content is trending 340% higher than your current topics',
            action: 'Create content about AI automation tools',
            priority: 'high'
          },
          {
            type: 'collaboration',
            title: 'High-Value Collaboration',
            description: '@techinfluencer has 89% audience overlap and 4.2% engagement rate',
            action: 'Reach out for collaboration opportunity',
            priority: 'medium'
          },
          {
            type: 'posting_time',
            title: 'Optimal Posting Time',
            description: 'Your audience is 23% more active at 7:30 PM on weekdays',
            action: 'Adjust posting schedule for better engagement',
            priority: 'low'
          }
        ],
        insights: {
          growth_prediction: '+15.2%',
          engagement_forecast: '+8.7%',
          optimal_content_mix: 'Reels: 60%, Carousels: 30%, Photos: 10%'
        }
      });
    }, 1000);
  };
  
  const filterAccounts = () => {
    return accounts.filter(account => {
      if (searchQuery && !account.username.toLowerCase().includes(searchQuery.toLowerCase()) && 
          !account.displayName.toLowerCase().includes(searchQuery.toLowerCase())) {
        return false;
      }
      
      if (account.followers < filters.followerRange[0] || account.followers > filters.followerRange[1]) {
        return false;
      }
      
      if (account.engagementRate < filters.engagementRate[0] || account.engagementRate > filters.engagementRate[1]) {
        return false;
      }
      
      if (filters.location && !account.location.toLowerCase().includes(filters.location.toLowerCase())) {
        return false;
      }
      
      if (filters.accountType !== 'all' && account.accountType !== filters.accountType) {
        return false;
      }
      
      if (filters.verified && !account.verified) {
        return false;
      }
      
      if (filters.hasEmail && !account.email) {
        return false;
      }
      
      return true;
    });
  };
  
  const exportAccounts = () => {
    const dataToExport = selectedAccounts.length > 0 
      ? accounts.filter(acc => selectedAccounts.includes(acc.id))
      : filterAccounts();
    
    const csvContent = [
      ['Username', 'Display Name', 'Followers', 'Engagement Rate', 'Email', 'Location', 'Category'],
      ...dataToExport.map(account => [
        account.username,
        account.displayName,
        account.followers,
        account.engagementRate + '%',
        account.email || 'N/A',
        account.location,
        account.category
      ])
    ].map(row => row.join(',')).join('\\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'instagram_accounts.csv';
    a.click();
    URL.revokeObjectURL(url);
    
    success(`Exported ${dataToExport.length} accounts successfully!`);
  };
  
  const renderAccountCard = (account) => (
    <motion.div
      key={account.id}
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="bg-surface border border-default rounded-xl p-6 hover:shadow-lg transition-all"
    >
      <div className="flex items-start justify-between mb-4">
        <div className="flex items-center">
          <input
            type="checkbox"
            checked={selectedAccounts.includes(account.id)}
            onChange={(e) => {
              if (e.target.checked) {
                setSelectedAccounts([...selectedAccounts, account.id]);
              } else {
                setSelectedAccounts(selectedAccounts.filter(id => id !== account.id));
              }
            }}
            className="mr-3"
          />
          <img
            src={account.profilePicture}
            alt={account.displayName}
            className="w-16 h-16 rounded-full mr-4"
          />
          <div>
            <div className="flex items-center">
              <h3 className="font-semibold text-primary">{account.displayName}</h3>
              {account.verified && (
                <div className="ml-2 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                  <svg className="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                  </svg>
                </div>
              )}
            </div>
            <p className="text-secondary">@{account.username}</p>
            <p className="text-sm text-secondary flex items-center mt-1">
              <MapPinIcon className="h-4 w-4 mr-1" />
              {account.location}
            </p>
          </div>
        </div>
        <div className="text-right">
          <div className={`inline-flex px-3 py-1 rounded-full text-xs font-medium ${
            account.brand_safety_score >= 9 ? 'bg-green-100 text-green-800' :
            account.brand_safety_score >= 7 ? 'bg-yellow-100 text-yellow-800' :
            'bg-red-100 text-red-800'
          }`}>
            Safety: {account.brand_safety_score}/10
          </div>
        </div>
      </div>
      
      <p className="text-secondary mb-4 line-clamp-2">{account.bio}</p>
      
      <div className="grid grid-cols-3 gap-4 mb-4">
        <div className="text-center">
          <div className="text-lg font-bold text-primary">{account.followers.toLocaleString()}</div>
          <div className="text-sm text-secondary">Followers</div>
        </div>
        <div className="text-center">
          <div className="text-lg font-bold text-primary">{account.engagementRate}%</div>
          <div className="text-sm text-secondary">Engagement</div>
        </div>
        <div className="text-center">
          <div className="text-lg font-bold text-primary">{account.posts}</div>
          <div className="text-sm text-secondary">Posts</div>
        </div>
      </div>
      
      <div className="flex items-center justify-between mb-4">
        <div className="flex space-x-3 text-sm text-secondary">
          <span className="flex items-center">
            <HeartIcon className="h-4 w-4 mr-1" />
            {account.avgLikes.toLocaleString()}
          </span>
          <span className="flex items-center">
            <ChatBubbleLeftIcon className="h-4 w-4 mr-1" />
            {account.avgComments}
          </span>
        </div>
        <div className="flex items-center text-sm text-secondary">
          <ClockIcon className="h-4 w-4 mr-1" />
          {account.postingFrequency}
        </div>
      </div>
      
      <div className="mb-4">
        <div className="text-sm text-secondary mb-2">Top Hashtags:</div>
        <div className="flex flex-wrap gap-1">
          {account.topHashtags.slice(0, 4).map((tag, index) => (
            <span key={index} className="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
              {tag}
            </span>
          ))}
        </div>
      </div>
      
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-2">
          {account.email && (
            <span className="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
              📧 Email Available
            </span>
          )}
          {account.website && (
            <span className="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">
              🌐 Website
            </span>
          )}
        </div>
        <button className="text-blue-600 hover:text-blue-800 text-sm font-medium">
          View Profile
        </button>
      </div>
    </motion.div>
  );
  
  const renderAIInsights = () => (
    <div className="bg-surface-elevated rounded-xl shadow-default p-6">
      <div className="flex items-center mb-6">
        <SparklesIcon className="h-6 w-6 text-yellow-500 mr-2" />
        <h3 className="text-xl font-semibold text-primary">AI-Powered Insights</h3>
      </div>
      
      {aiInsights ? (
        <div className="space-y-6">
          {/* Key Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="text-center p-4 rounded-lg bg-green-50 dark:bg-green-900/20">
              <TrendingUpIcon className="h-8 w-8 mx-auto mb-2 text-green-600" />
              <div className="text-2xl font-bold text-primary">{aiInsights.insights.growth_prediction}</div>
              <div className="text-sm text-secondary">Growth Forecast</div>
            </div>
            <div className="text-center p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20">
              <HeartIconSolid className="h-8 w-8 mx-auto mb-2 text-blue-600" />
              <div className="text-2xl font-bold text-primary">{aiInsights.insights.engagement_forecast}</div>
              <div className="text-sm text-secondary">Engagement Boost</div>
            </div>
            <div className="text-center p-4 rounded-lg bg-purple-50 dark:bg-purple-900/20">
              <PlayIcon className="h-8 w-8 mx-auto mb-2 text-purple-600" />
              <div className="text-sm font-bold text-primary">{aiInsights.insights.optimal_content_mix}</div>
              <div className="text-sm text-secondary">Optimal Mix</div>
            </div>
          </div>
          
          {/* Recommendations */}
          <div>
            <h4 className="text-lg font-semibold text-primary mb-4">AI Recommendations</h4>
            <div className="space-y-3">
              {aiInsights.recommendations.map((rec, index) => (
                <div key={index} className={`p-4 rounded-lg border-l-4 ${
                  rec.priority === 'high' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' :
                  rec.priority === 'medium' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' :
                  'border-green-500 bg-green-50 dark:bg-green-900/20'
                }`}>
                  <div className="flex items-start justify-between">
                    <div>
                      <h5 className="font-semibold text-primary mb-1">{rec.title}</h5>
                      <p className="text-secondary text-sm mb-2">{rec.description}</p>
                      <p className="text-blue-600 text-sm font-medium">{rec.action}</p>
                    </div>
                    <span className={`px-2 py-1 rounded text-xs font-medium ${
                      rec.priority === 'high' ? 'bg-red-100 text-red-800' :
                      rec.priority === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-green-100 text-green-800'
                    }`}>
                      {rec.priority}
                    </span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      ) : (
        <div className="text-center py-8">
          <SparklesIcon className="h-12 w-12 mx-auto mb-4 text-gray-400 animate-pulse" />
          <div className="text-secondary">Generating AI insights...</div>
        </div>
      )}
    </div>
  );
  
  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <div className="flex items-center mb-2">
              <CameraIcon className="h-8 w-8 mr-3" />
              <h1 className="text-3xl font-bold">Instagram Management Pro</h1>
            </div>
            <p className="text-white/80">Advanced Instagram database, analytics, and AI-powered insights</p>
          </div>
          <div className="flex space-x-4">
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{accounts.length.toLocaleString()}</div>
              <div className="text-sm text-white/70">Accounts</div>
            </div>
            <div className="bg-white/20 rounded-xl p-4 text-center">
              <div className="text-2xl font-bold mb-1">{selectedAccounts.length}</div>
              <div className="text-sm text-white/70">Selected</div>
            </div>
          </div>
        </div>
      </div>
      
      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="flex space-x-8">
          {[
            { id: 'database', name: 'Account Database', icon: UserGroupIcon },
            { id: 'analytics', name: 'Content Analytics', icon: ChartBarIcon },
            { id: 'insights', name: 'AI Insights', icon: SparklesIcon },
            { id: 'hashtags', name: 'Hashtag Research', icon: HashtagIcon }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`flex items-center py-4 px-1 border-b-2 font-medium text-sm ${
                activeTab === tab.id
                  ? 'border-pink-500 text-pink-600 dark:text-pink-400'
                  : 'border-transparent text-secondary hover:text-primary'
              }`}
            >
              <tab.icon className="h-4 w-4 mr-2" />
              {tab.name}
            </button>
          ))}
        </nav>
      </div>
      
      {/* Content based on active tab */}
      {activeTab === 'database' && (
        <div className="space-y-6">
          {/* Search and Filters */}
          <div className="bg-surface-elevated rounded-xl shadow-default p-6">
            <div className="flex items-center justify-between mb-4">
              <div className="flex items-center space-x-4 flex-1">
                <div className="relative flex-1 max-w-lg">
                  <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary" />
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    placeholder="Search accounts by username or name..."
                    className="pl-10 input"
                  />
                </div>
                <button
                  onClick={() => setShowFilters(!showFilters)}
                  className="btn btn-secondary"
                >
                  <FunnelIcon className="h-4 w-4 mr-2" />
                  Filters
                </button>
                <button
                  onClick={exportAccounts}
                  className="btn btn-primary"
                  disabled={filterAccounts().length === 0}
                >
                  <ArrowDownTrayIcon className="h-4 w-4 mr-2" />
                  Export ({selectedAccounts.length > 0 ? selectedAccounts.length : filterAccounts().length})
                </button>
              </div>
            </div>
            
            {showFilters && (
              <motion.div
                initial={{ opacity: 0, height: 0 }}
                animate={{ opacity: 1, height: 'auto' }}
                className="border-t border-default pt-4 grid grid-cols-1 md:grid-cols-3 gap-4"
              >
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Follower Range</label>
                  <div className="flex items-center space-x-2">
                    <input
                      type="number"
                      value={filters.followerRange[0]}
                      onChange={(e) => setFilters({
                        ...filters,
                        followerRange: [parseInt(e.target.value), filters.followerRange[1]]
                      })}
                      className="input text-sm"
                      placeholder="Min"
                    />
                    <span className="text-secondary">to</span>
                    <input
                      type="number"
                      value={filters.followerRange[1]}
                      onChange={(e) => setFilters({
                        ...filters,
                        followerRange: [filters.followerRange[0], parseInt(e.target.value)]
                      })}
                      className="input text-sm"
                      placeholder="Max"
                    />
                  </div>
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Account Type</label>
                  <select
                    value={filters.accountType}
                    onChange={(e) => setFilters({...filters, accountType: e.target.value})}
                    className="input text-sm"
                  >
                    <option value="all">All Types</option>
                    <option value="personal">Personal</option>
                    <option value="business">Business</option>
                    <option value="creator">Creator</option>
                  </select>
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Location</label>
                  <input
                    type="text"
                    value={filters.location}
                    onChange={(e) => setFilters({...filters, location: e.target.value})}
                    placeholder="e.g. Los Angeles"
                    className="input text-sm"
                  />
                </div>
                
                <div className="flex items-center space-x-4">
                  <label className="flex items-center">
                    <input
                      type="checkbox"
                      checked={filters.verified}
                      onChange={(e) => setFilters({...filters, verified: e.target.checked})}
                      className="mr-2"
                    />
                    <span className="text-sm text-secondary">Verified only</span>
                  </label>
                  <label className="flex items-center">
                    <input
                      type="checkbox"
                      checked={filters.hasEmail}
                      onChange={(e) => setFilters({...filters, hasEmail: e.target.checked})}
                      className="mr-2"
                    />
                    <span className="text-sm text-secondary">Has email</span>
                  </label>
                </div>
              </motion.div>
            )}
          </div>
          
          {/* Accounts Grid */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {filterAccounts().map(renderAccountCard)}
          </div>
          
          {filterAccounts().length === 0 && (
            <div className="text-center py-12">
              <UserGroupIcon className="h-12 w-12 mx-auto mb-4 text-gray-400" />
              <h3 className="text-lg font-medium text-primary">No accounts found</h3>
              <p className="text-secondary">Try adjusting your search criteria or filters</p>
            </div>
          )}
        </div>
      )}
      
      {activeTab === 'insights' && renderAIInsights()}
      
      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* Top Performing Posts */}
            <div className="bg-surface-elevated rounded-xl shadow-default p-6">
              <h3 className="text-xl font-semibold text-primary mb-6">Top Performing Content</h3>
              <div className="space-y-4">
                {contentAnalytics.topPerformingPosts.map((post) => (
                  <div key={post.id} className="flex items-start p-4 rounded-lg bg-surface border border-default">
                    <img
                      src={post.thumbnail}
                      alt="Post thumbnail"
                      className="w-16 h-16 rounded-lg mr-4"
                    />
                    <div className="flex-1">
                      <p className="font-medium text-primary mb-2">{post.caption}</p>
                      <div className="flex items-center space-x-4 text-sm text-secondary">
                        <span className="flex items-center">
                          <HeartIcon className="h-4 w-4 mr-1" />
                          {post.likes.toLocaleString()}
                        </span>
                        <span className="flex items-center">
                          <ChatBubbleLeftIcon className="h-4 w-4 mr-1" />
                          {post.comments}
                        </span>
                        <span className="flex items-center">
                          <BookmarkIcon className="h-4 w-4 mr-1" />
                          {post.saves}
                        </span>
                      </div>
                      <div className="flex space-x-2 mt-2">
                        {post.hashtags.slice(0, 3).map((tag, index) => (
                          <span key={index} className="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                            {tag}
                          </span>
                        ))}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
            
            {/* Hashtag Performance */}
            <div className="bg-surface-elevated rounded-xl shadow-default p-6">
              <h3 className="text-xl font-semibold text-primary mb-6">Hashtag Performance</h3>
              <div className="space-y-4">
                {contentAnalytics.hashtagPerformance.map((hashtag, index) => (
                  <div key={index} className="flex items-center justify-between p-3 rounded-lg bg-surface border border-default">
                    <div>
                      <div className="font-medium text-primary">{hashtag.hashtag}</div>
                      <div className="text-sm text-secondary">{hashtag.posts} posts</div>
                    </div>
                    <div className="text-right">
                      <div className="font-medium text-primary">{hashtag.avgLikes.toLocaleString()}</div>
                      <div className="text-sm text-secondary">avg likes</div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default UltraAdvancedInstagramManager;