import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  MagnifyingGlassIcon,
  FunnelIcon,
  ArrowDownTrayIcon,
  PlusIcon,
  EyeIcon,
  HeartIcon,
  ShareIcon,
  CheckCircleIcon,
  ChartBarIcon,
  UsersIcon,
  CalendarIcon,
  SparklesIcon,
  ClockIcon,
  MapPinIcon,
  GlobeAltIcon,
  AtSymbolIcon,
  HashtagIcon,
  CameraIcon,
  VideoIcon
} from '@heroicons/react/24/outline';

const InstagramManagementPage = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  const [activeTab, setActiveTab] = useState('database');
  const [searchQuery, setSearchQuery] = useState('');
  const [loading, setLoading] = useState(false);
  const [selectedAccounts, setSelectedAccounts] = useState([]);

  // Advanced filtering state
  const [filters, setFilters] = useState({
    followerMin: '',
    followerMax: '',
    engagementMin: '',
    engagementMax: '',
    accountType: 'all',
    verified: 'all',
    location: '',
    hashtags: '',
    niche: 'all',
    lastActiveHours: ''
  });

  // Advanced feature states
  const [automationEnabled, setAutomationEnabled] = useState(false);
  const [aiRecommendations, setAiRecommendations] = useState([]);
  const [exportFormat, setExportFormat] = useState('csv');
  const [contentAnalysis, setContentAnalysis] = useState({});

  // Mock Instagram database with comprehensive data
  const [instagramAccounts, setInstagramAccounts] = useState([
    {
      id: '1',
      username: 'fashion_influencer_nyc',
      displayName: 'NYC Fashion Queen',
      followers: 125000,
      following: 1580,
      posts: 1247,
      engagementRate: 6.8,
      avgLikes: 8500,
      avgComments: 245,
      accountType: 'creator',
      verified: true,
      profilePicture: 'https://ui-avatars.com/api/?name=NYC+Fashion&background=ec4899&color=fff',
      bio: '✨ Fashion & Lifestyle | NYC | Brand collaborations: info@fashionqueen.com',
      location: 'New York, NY',
      email: 'collabs@fashionqueen.com',
      website: 'www.fashionqueennyc.com',
      phone: '+1-555-0123',
      lastActive: '1 hour ago',
      contentCategories: ['Fashion', 'Lifestyle', 'Beauty'],
      bestPostingTimes: ['9:00 AM', '12:00 PM', '6:00 PM'],
      audienceDemographics: {
        ageGroups: { '18-24': 35, '25-34': 45, '35-44': 15, '45+': 5 },
        gender: { female: 78, male: 22 },
        topLocations: ['New York', 'Los Angeles', 'Miami', 'Chicago', 'Atlanta']
      },
      recentPosts: [
        { date: '2024-01-15', likes: 9200, comments: 287, reach: 45000, impressions: 62000 },
        { date: '2024-01-14', likes: 7800, comments: 198, reach: 38000, impressions: 51000 }
      ],
      collaborationStatus: 'open',
      averageRate: 2500,
      language: 'English',
      niche: 'Fashion & Lifestyle'
    },
    {
      id: '2', 
      username: 'tech_reviewer_sf',
      displayName: 'Silicon Valley Tech Reviews',
      followers: 89000,
      following: 890,
      posts: 567,
      engagementRate: 5.2,
      avgLikes: 4600,
      avgComments: 189,
      accountType: 'business',
      verified: false,
      profilePicture: 'https://ui-avatars.com/api/?name=Tech+Reviews&background=3b82f6&color=fff',
      bio: '🚀 Latest tech reviews & unboxing | SF Bay Area | Contact: hello@techreviews.io',
      location: 'San Francisco, CA',
      email: 'partnerships@techreviews.io',
      website: 'www.sftechreviews.com',
      phone: '+1-555-0456',
      lastActive: '3 hours ago',
      contentCategories: ['Technology', 'Reviews', 'Gadgets'],
      bestPostingTimes: ['8:00 AM', '2:00 PM', '7:00 PM'],
      audienceDemographics: {
        ageGroups: { '18-24': 25, '25-34': 55, '35-44': 15, '45+': 5 },
        gender: { female: 32, male: 68 },
        topLocations: ['San Francisco', 'Seattle', 'Austin', 'Boston', 'Denver']
      },
      recentPosts: [
        { date: '2024-01-15', likes: 5200, comments: 203, reach: 28000, impressions: 39000 },
        { date: '2024-01-13', likes: 4100, comments: 156, reach: 25000, impressions: 34000 }
      ],
      collaborationStatus: 'selective',
      averageRate: 1800,
      language: 'English',
      niche: 'Technology'
    },
    {
      id: '3',
      username: 'fitness_coach_miami',
      displayName: 'Miami Fitness Pro',
      followers: 67000,
      following: 2100,
      posts: 892,
      engagementRate: 7.1,
      avgLikes: 4750,
      avgComments: 298,
      accountType: 'creator',
      verified: false,
      profilePicture: 'https://ui-avatars.com/api/?name=Fitness+Pro&background=10b981&color=fff',
      bio: '💪 Certified personal trainer | Nutrition expert | Miami Beach | Transform your body!',
      location: 'Miami, FL',
      email: 'coach@miamifit.com',
      website: 'www.miamifitnesspro.com',
      phone: '+1-555-0789',
      lastActive: '30 minutes ago',
      contentCategories: ['Fitness', 'Nutrition', 'Wellness'],
      bestPostingTimes: ['6:00 AM', '12:00 PM', '8:00 PM'],
      audienceDemographics: {
        ageGroups: { '18-24': 30, '25-34': 40, '35-44': 25, '45+': 5 },
        gender: { female: 55, male: 45 },
        topLocations: ['Miami', 'Fort Lauderdale', 'Orlando', 'Tampa', 'Jacksonville']
      },
      recentPosts: [
        { date: '2024-01-15', likes: 5400, comments: 312, reach: 32000, impressions: 41000 },
        { date: '2024-01-14', likes: 4200, comments: 267, reach: 28000, impressions: 36000 }
      ],
      collaborationStatus: 'open',
      averageRate: 1200,
      language: 'English',
      niche: 'Fitness & Health'
    },
    {
      id: '4',
      username: 'food_blogger_la',
      displayName: 'LA Foodie Adventures',
      followers: 145000,
      following: 3200,
      posts: 1456,
      engagementRate: 4.9,
      avgLikes: 7100,
      avgComments: 234,
      accountType: 'creator',
      verified: true,
      profilePicture: 'https://ui-avatars.com/api/?name=LA+Foodie&background=f59e0b&color=fff',
      bio: '🍕 Food blogger & restaurant critic | LA dining scene | Food photography | 📧: hello@lafoodie.com',
      location: 'Los Angeles, CA',
      email: 'collabs@lafoodie.com',
      website: 'www.lafoodieadventures.com',
      phone: '+1-555-0321',
      lastActive: '2 hours ago',
      contentCategories: ['Food', 'Restaurants', 'Cooking'],
      bestPostingTimes: ['11:00 AM', '1:00 PM', '7:00 PM'],
      audienceDemographics: {
        ageGroups: { '18-24': 20, '25-34': 50, '35-44': 25, '45+': 5 },
        gender: { female: 65, male: 35 },
        topLocations: ['Los Angeles', 'San Diego', 'San Francisco', 'Las Vegas', 'Phoenix']
      },
      recentPosts: [
        { date: '2024-01-15', likes: 8200, comments: 276, reach: 52000, impressions: 68000 },
        { date: '2024-01-14', likes: 6800, comments: 198, reach: 45000, impressions: 59000 }
      ],
      collaborationStatus: 'open',
      averageRate: 3200,
      language: 'English',
      niche: 'Food & Dining'
    },
    {
      id: '5',
      username: 'travel_couple_world',
      displayName: 'World Wanderers',
      followers: 198000,
      following: 1890,
      posts: 2134,
      engagementRate: 5.8,
      avgLikes: 11500,
      avgComments: 445,
      accountType: 'creator',
      verified: true,
      profilePicture: 'https://ui-avatars.com/api/?name=World+Wanderers&background=6366f1&color=fff',
      bio: '✈️ Travel couple | 60+ countries | Adventure & culture | Travel guides & tips | 📧: hello@worldwanderers.com',
      location: 'Worldwide',
      email: 'partnerships@worldwanderers.com',
      website: 'www.worldwanderers.travel',
      phone: '+1-555-0654',
      lastActive: '4 hours ago',
      contentCategories: ['Travel', 'Adventure', 'Culture'],
      bestPostingTimes: ['9:00 AM', '3:00 PM', '8:00 PM'],
      audienceDemographics: {
        ageGroups: { '18-24': 25, '25-34': 45, '35-44': 20, '45+': 10 },
        gender: { female: 58, male: 42 },
        topLocations: ['New York', 'Los Angeles', 'London', 'Toronto', 'Sydney']
      },
      recentPosts: [
        { date: '2024-01-15', likes: 13200, comments: 487, reach: 78000, impressions: 95000 },
        { date: '2024-01-13', likes: 10800, comments: 356, reach: 65000, impressions: 82000 }
      ],
      collaborationStatus: 'selective',
      averageRate: 4500,
      language: 'English',
      niche: 'Travel & Adventure'
    }
  ]);

  const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
  };

  const getAccountTypeColor = (type) => {
    switch (type) {
      case 'business':
        return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
      case 'creator':
        return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
      default:
        return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
  };

  const getCollaborationInterestColor = (interest) => {
    switch (interest) {
      case 'open':
        return 'bg-green-500';
      case 'selective':
        return 'bg-yellow-500';
      default:
        return 'bg-red-500';
    }
  };

  const handleAccountSelect = (accountId) => {
    setSelectedAccounts(prev => 
      prev.includes(accountId) 
        ? prev.filter(id => id !== accountId)
        : [...prev, accountId]
    );
  };

  const handleBulkExport = async () => {
    if (selectedAccounts.length === 0) {
      error('Please select at least one account to export');
      return;
    }
    
    setLoading(true);
    try {
      const selectedAccountsData = instagramAccounts.filter(acc => 
        selectedAccounts.includes(acc.id)
      );
      
      // Create CSV data
      const csvHeaders = [
        'Username', 'Display Name', 'Followers', 'Following', 'Posts',
        'Engagement Rate', 'Account Type', 'Verified', 'Location', 
        'Email', 'Website', 'Phone', 'Avg Likes', 'Avg Comments',
        'Best Posting Time', 'Niche', 'Collaboration Status'
      ];
      
      const csvData = selectedAccountsData.map(acc => [
        acc.username, acc.displayName, acc.followers, acc.following, acc.posts,
        acc.engagementRate + '%', acc.accountType, acc.verified ? 'Yes' : 'No',
        acc.location, acc.email, acc.website, acc.phone, acc.avgLikes,
        acc.avgComments, acc.bestPostingTimes?.[0] || 'N/A', acc.niche,
        acc.collaborationStatus
      ]);
      
      const csvContent = [
        csvHeaders.join(','),
        ...csvData.map(row => row.join(','))
      ].join('\n');
      
      const blob = new Blob([csvContent], { type: 'text/csv' });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `instagram-accounts-${new Date().toISOString().split('T')[0]}.csv`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);
      
      success(`Successfully exported ${selectedAccounts.length} accounts`);
      setSelectedAccounts([]);
    } catch (err) {
      error('Failed to export accounts');
    }
    setLoading(false);
  };

  const generateAIRecommendations = async () => {
    setLoading(true);
    try {
      // Simulate AI analysis
      const recommendations = [
        {
          id: 1,
          type: 'collaboration',
          title: 'High-Value Collaboration Opportunity',
          description: 'NYC Fashion Queen shows 6.8% engagement with 125K followers in fashion niche - excellent ROI potential',
          priority: 'high',
          estimatedROI: '320%'
        },
        {
          id: 2,
          type: 'timing',
          title: 'Optimal Posting Schedule',
          description: 'Analysis shows 9 AM, 12 PM, and 6 PM are peak engagement times across selected accounts',
          priority: 'medium',
          estimatedROI: '85%'
        },
        {
          id: 3,
          type: 'content',
          title: 'Content Strategy Insight',
          description: 'Travel and lifestyle content performs 40% better than product-focused posts for your target audience',
          priority: 'high',
          estimatedROI: '210%'
        }
      ];
      
      setAiRecommendations(recommendations);
      success('AI recommendations generated successfully');
    } catch (err) {
      error('Failed to generate AI recommendations');
    }
    setLoading(false);
  };

  const renderInstagramDatabase = () => (
    <div className="space-y-6">
      {/* Advanced Search & Filters */}
      <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
        <div className="flex items-center justify-between mb-6">
          <h3 className="text-xl font-semibold text-primary">Instagram Database Search</h3>
          <div className="text-sm text-secondary">
            <span className="font-medium">{instagramAccounts.length.toLocaleString()}</span> accounts in database
          </div>
        </div>
        
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
          <div className="lg:col-span-2">
            <div className="relative">
              <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search by username, bio, location, hashtags..."
                className="input w-full pl-10"
              />
            </div>
          </div>
          
          <button 
            className="btn btn-secondary flex items-center justify-center space-x-2"
            onClick={() => setActiveTab('filters')}
          >
            <FunnelIcon className="h-4 w-4" />
            <span>Advanced Filters</span>
          </button>
        </div>

        {/* Quick Filter Buttons */}
        <div className="flex flex-wrap gap-3 mb-4">
          {[
            { label: 'Verified Only', filter: 'verified', value: true },
            { label: 'Business Accounts', filter: 'accountType', value: 'business' },
            { label: 'High Engagement (5%+)', filter: 'engagementHigh', value: true },
            { label: 'Recently Active (24h)', filter: 'recentlyActive', value: true }
          ].map((quickFilter) => (
            <button
              key={quickFilter.label}
              className="px-3 py-1 text-sm rounded-full border border-default hover:bg-surface-hover transition-colors"
            >
              {quickFilter.label}
            </button>
          ))}
        </div>

        {/* Export & Actions */}
        <div className="flex items-center justify-between pt-4 border-t border-default">
          <div className="flex items-center space-x-4">
            <span className="text-sm text-secondary">
              {selectedAccounts.length} account{selectedAccounts.length !== 1 ? 's' : ''} selected
            </span>
          </div>
          
          <div className="flex items-center space-x-3">
            <button 
              onClick={handleBulkExport}
              disabled={selectedAccounts.length === 0 || loading}
              className="btn btn-secondary flex items-center space-x-2 disabled:opacity-50"
            >
              <ArrowDownTrayIcon className="h-4 w-4" />
              <span>{loading ? 'Exporting...' : 'Export CSV'}</span>
            </button>
            <button className="btn btn-primary flex items-center space-x-2">
              <PlusIcon className="h-4 w-4" />
              <span>Create Campaign</span>
            </button>
          </div>
        </div>
      </div>

      {/* Instagram Accounts Grid */}
      <div className="grid grid-cols-1 gap-6">
        {instagramAccounts.map((account) => (
          <motion.div
            key={account.id}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="bg-surface-elevated p-6 rounded-xl shadow-default hover:shadow-lg transition-shadow"
          >
            <div className="flex items-start justify-between mb-4">
              <div className="flex items-start space-x-4">
                <label className="flex items-center">
                  <input
                    type="checkbox"
                    checked={selectedAccounts.includes(account.id)}
                    onChange={() => handleAccountSelect(account.id)}
                    className="mr-3 rounded"
                  />
                </label>
                
                <div className="w-16 h-16 bg-gradient-to-br from-pink-500 via-red-500 to-yellow-500 rounded-full flex items-center justify-center shadow-default">
                  <span className="text-white font-bold text-xl">
                    {account.displayName.charAt(0)}
                  </span>
                </div>
                
                <div className="flex-1">
                  <div className="flex items-center space-x-2 mb-2">
                    <h4 className="text-xl font-bold text-primary">@{account.username}</h4>
                    {account.verified && (
                      <CheckCircleIcon className="h-5 w-5 text-blue-500" />
                    )}
                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${getAccountTypeColor(account.accountType)}`}>
                      {account.accountType}
                    </span>
                    <div className={`w-3 h-3 rounded-full ${getCollaborationInterestColor(account.collaborationInterest)}`} 
                         title={`Collaboration Interest: ${account.collaborationInterest}`} />
                  </div>
                  
                  <p className="text-lg font-semibold text-primary mb-2">{account.displayName}</p>
                  <p className="text-sm text-secondary mb-4 line-clamp-2">{account.bio}</p>
                </div>
              </div>
              
              <div className="flex items-center space-x-2">
                <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors">
                  <EyeIcon className="h-5 w-5" />
                </button>
                <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors">
                  <HeartIcon className="h-5 w-5" />
                </button>
                <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg transition-colors">
                  <ShareIcon className="h-5 w-5" />
                </button>
              </div>
            </div>
            
            {/* Stats Grid */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
              <div className="text-center p-3 bg-surface rounded-lg">
                <p className="text-lg font-bold text-primary">{formatNumber(account.followers)}</p>
                <p className="text-xs text-secondary">Followers</p>
              </div>
              <div className="text-center p-3 bg-surface rounded-lg">
                <p className="text-lg font-bold text-primary">{account.engagementRate}%</p>
                <p className="text-xs text-secondary">Engagement</p>
              </div>
              <div className="text-center p-3 bg-surface rounded-lg">
                <p className="text-lg font-bold text-primary">{formatNumber(account.avgLikes)}</p>
                <p className="text-xs text-secondary">Avg Likes</p>
              </div>
              <div className="text-center p-3 bg-surface rounded-lg">
                <p className="text-lg font-bold text-primary">{account.posts}</p>
                <p className="text-xs text-secondary">Posts</p>
              </div>
            </div>

            {/* Detailed Information */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div className="space-y-2">
                <div className="flex items-center space-x-2 text-sm">
                  <MapPinIcon className="h-4 w-4 text-secondary" />
                  <span className="text-secondary">{account.location}</span>
                </div>
                <div className="flex items-center space-x-2 text-sm">
                  <GlobeAltIcon className="h-4 w-4 text-secondary" />
                  <span className="text-secondary">{account.website}</span>
                </div>
                <div className="flex items-center space-x-2 text-sm">
                  <AtSymbolIcon className="h-4 w-4 text-secondary" />
                  <span className="text-secondary">{account.email}</span>
                </div>
                <div className="flex items-center space-x-2 text-sm">
                  <ClockIcon className="h-4 w-4 text-secondary" />
                  <span className="text-secondary">Active {account.lastActive}</span>
                </div>
              </div>
              
              <div className="space-y-2">
                <div className="flex items-center space-x-2 text-sm">
                  <ChartBarIcon className="h-4 w-4 text-secondary" />
                  <span className="text-secondary">{account.avgPostsPerWeek} posts/week</span>
                </div>
                <div className="flex items-center space-x-2 text-sm">
                  <ClockIcon className="h-4 w-4 text-secondary" />
                  <span className="text-secondary">Best time: {account.bestPostingTime}</span>
                </div>
                <div className="flex items-center space-x-2 text-sm">
                  <UsersIcon className="h-4 w-4 text-secondary" />
                  <span className="text-secondary">{account.audienceGender}</span>
                </div>
                <div className="flex items-center space-x-2 text-sm">
                  <UsersIcon className="h-4 w-4 text-secondary" />
                  <span className="text-secondary">Age: {account.audienceAgeRange}</span>
                </div>
              </div>
            </div>

            {/* Hashtags */}
            <div className="mb-4">
              <p className="text-sm font-medium text-secondary mb-2">Recent Hashtags:</p>
              <div className="flex flex-wrap gap-2">
                {account.recentHashtags.map((hashtag) => (
                  <span key={hashtag} className="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 rounded-full text-xs">
                    {hashtag}
                  </span>
                ))}
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex items-center space-x-3">
              <button className="btn btn-sm btn-primary">Add to Campaign</button>
              <button className="btn btn-sm btn-secondary">View Profile</button>
              <button className="btn btn-sm btn-secondary">Contact Info</button>
              <button className="btn btn-sm btn-secondary">Analytics</button>
            </div>
          </motion.div>
        ))}
      </div>
    </div>
  );

  const renderContentScheduler = () => (
    <div className="space-y-6">
      <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
        <h3 className="text-xl font-semibold text-primary mb-6">Content Scheduler</h3>
        
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-medium text-secondary mb-2">Post Caption</label>
            <textarea
              rows={6}
              placeholder="Write your Instagram post caption here... ✨"
              className="input w-full resize-none"
            />
          </div>
          
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Media Upload</label>
              <div className="border-2 border-dashed border-default rounded-lg p-6 text-center">
                <CameraIcon className="h-12 w-12 text-secondary mx-auto mb-2" />
                <p className="text-secondary">Drop images or videos here</p>
                <button className="btn btn-sm btn-secondary mt-2">Browse Files</button>
              </div>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Schedule Time</label>
              <input type="datetime-local" className="input w-full" />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Hashtags</label>
              <input 
                type="text" 
                placeholder="#hashtag1 #hashtag2 #hashtag3"
                className="input w-full" 
              />
            </div>
          </div>
        </div>
        
        <div className="flex items-center justify-between mt-6 pt-4 border-t border-default">
          <div className="flex items-center space-x-4">
            <button className="btn btn-secondary">Save as Draft</button>
            <button className="btn btn-secondary">Preview</button>
          </div>
          <button className="btn btn-primary">Schedule Post</button>
        </div>
      </div>
    </div>
  );

  const renderAnalytics = () => (
    <div className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {[
          { label: 'Total Reach', value: '2.4M', change: '+18.2%', icon: ChartBarIcon },
          { label: 'Engagement Rate', value: '6.8%', change: '+2.1%', icon: HeartIcon },
          { label: 'New Followers', value: '12.5K', change: '+24.6%', icon: UsersIcon },
          { label: 'Profile Visits', value: '89.2K', change: '+15.3%', icon: EyeIcon }
        ].map((stat, index) => (
          <div key={index} className="bg-surface-elevated p-6 rounded-xl shadow-default">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-secondary">{stat.label}</p>
                <p className="text-2xl font-bold text-primary mt-1">{stat.value}</p>
              </div>
              <div className="text-right">
                <stat.icon className="h-6 w-6 text-secondary mb-1" />
                <p className="text-sm font-medium text-green-600">{stat.change}</p>
              </div>
            </div>
          </div>
        ))}
      </div>

      <div className="bg-surface-elevated p-6 rounded-xl shadow-default">
        <h3 className="text-xl font-semibold text-primary mb-6">Performance Analytics</h3>
        <div className="h-64 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg flex items-center justify-center">
          <p className="text-secondary">Advanced analytics charts would be rendered here</p>
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
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-primary mb-2">Instagram Management</h1>
            <p className="text-secondary">Advanced Instagram database, lead generation, and content management</p>
          </div>
          <div className="flex items-center space-x-3">
            <button className="btn btn-secondary flex items-center space-x-2">
              <SparklesIcon className="h-4 w-4" />
              <span>AI Assistant</span>
            </button>
            <button className="btn btn-primary flex items-center space-x-2">
              <PlusIcon className="h-4 w-4" />
              <span>New Campaign</span>
            </button>
          </div>
        </div>
      </motion.div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="flex space-x-8">
          {[
            { id: 'database', name: 'Instagram Database', icon: UsersIcon },
            { id: 'scheduler', name: 'Content Scheduler', icon: CalendarIcon },
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
        {activeTab === 'scheduler' && renderContentScheduler()}
        {activeTab === 'analytics' && renderAnalytics()}
      </motion.div>
    </div>
  );
};

export default InstagramManagementPage;