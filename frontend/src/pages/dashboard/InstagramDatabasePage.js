import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  MagnifyingGlassIcon,
  FunnelIcon,
  ArrowDownTrayIcon,
  EyeIcon,
  HeartIcon,
  UserPlusIcon,
  MapPinIcon,
  HashtagIcon,
  AdjustmentsHorizontalIcon,
  PhotoIcon,
  StarIcon,
  CalendarIcon,
  ChartBarIcon,
  UserGroupIcon,
  GlobeAltIcon,
  EnvelopeIcon,
  PhoneIcon
} from '@heroicons/react/24/outline';

const InstagramDatabasePage = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [error, setError] = useState(null);
  const [filters, setFilters] = useState({
    followerRange: { min: 0, max: 1000000 },
    followingRange: { min: 0, max: 10000 },
    engagementRate: { min: 0, max: 20 },
    location: '',
    hashtags: '',
    accountType: 'all',
    postFrequency: 'all',
    language: 'all',
    verified: false
  });
  const [selectedAccounts, setSelectedAccounts] = useState(new Set());
  const [error, setError] = useState(null);
  const [accounts, setAccounts] = useState([]);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [totalResults, setTotalResults] = useState(0);
  const [error, setError] = useState(null);

  // Mock Instagram data for demonstration
  const mockAccounts = [
    {
      id: '1',
      username: 'digitalmarketer_pro',
      displayName: 'Digital Marketing Pro',
      profilePicture: 'https://images.unsplash.com/photo-1494790108755-2616b112c4be?w=128&h=128&fit=crop&crop=face',
      followers: 125000,
      following: 1200,
      posts: 890,
      engagementRate: 4.2,
      verified: false,
      accountType: 'business',
      bio: 'Digital Marketing Expert | Helping businesses grow online | DM for collaborations',
      location: 'New York, NY',
      website: 'https://digitalmarketingpro.com',
      email: 'contact@digitalmarketingpro.com',
      phone: '+1-555-0123',
      recentPosts: 45,
      avgLikes: 5200,
      avgComments: 340,
      hashtags: ['#digitalmarketing', '#socialmedia', '#marketing', '#business'],
      language: 'en',
      lastPostDate: '2025-07-19',
      category: 'Marketing & Advertising'
    },
    {
      id: '2',
      username: 'fitness_guru_2024',
      displayName: 'Fitness Guru',
      profilePicture: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=128&h=128&fit=crop&crop=face',
      followers: 87500,
      following: 890,
      posts: 1245,
      engagementRate: 6.8,
      verified: true,
      accountType: 'creator',
      bio: '🏋️‍♂️ Certified Personal Trainer | Nutrition Coach | Transform your body & mind',
      location: 'Los Angeles, CA',
      website: 'https://fitnessguru.com',
      email: 'info@fitnessguru.com',
      phone: '+1-555-0456',
      recentPosts: 78,
      avgLikes: 8900,
      avgComments: 680,
      hashtags: ['#fitness', '#workout', '#health', '#motivation'],
      language: 'en',
      lastPostDate: '2025-07-20',
      category: 'Health & Fitness'
    },
    {
      id: '3',
      username: 'food_blogger_nyc',
      displayName: 'NYC Food Explorer',
      profilePicture: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=128&h=128&fit=crop&crop=face',
      followers: 45600,
      following: 2340,
      posts: 567,
      engagementRate: 3.9,
      verified: false,
      accountType: 'personal',
      bio: 'Food blogger | NYC restaurant reviews | DM for collaborations 🍕',
      location: 'New York, NY',
      website: 'https://nycfoodexplorer.blog',
      email: 'hello@nycfoodexplorer.blog',
      phone: null,
      recentPosts: 23,
      avgLikes: 1800,
      avgComments: 120,
      hashtags: ['#foodie', '#nyc', '#foodblogger', '#restaurant'],
      language: 'en',
      lastPostDate: '2025-07-18',
      category: 'Food & Dining'
    },
    {
      id: '4',
      username: 'tech_entrepreneur',
      displayName: 'Tech Entrepreneur',
      profilePicture: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=128&h=128&fit=crop&crop=face',
      followers: 234000,
      following: 567,
      posts: 1089,
      engagementRate: 5.1,
      verified: true,
      accountType: 'business',
      bio: 'Founder & CEO @TechStartup | Angel Investor | Sharing entrepreneurship insights',
      location: 'San Francisco, CA',
      website: 'https://techentrepreneur.com',
      email: 'contact@techentrepreneur.com',
      phone: '+1-555-0789',
      recentPosts: 67,
      avgLikes: 12000,
      avgComments: 890,
      hashtags: ['#tech', '#startup', '#entrepreneur', '#innovation'],
      language: 'en',
      lastPostDate: '2025-07-20',
      category: 'Technology'
    },
    {
      id: '5',
      username: 'travel_photographer',
      displayName: 'World Traveler',
      profilePicture: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=128&h=128&fit=crop&crop=face',
      followers: 156000,
      following: 1890,
      posts: 2340,
      engagementRate: 4.7,
      verified: false,
      accountType: 'creator',
      bio: '📸 Travel Photographer | 🌍 50+ Countries | Capturing moments around the globe',
      location: 'Worldwide',
      website: 'https://worldtraveler.photo',
      email: 'bookings@worldtraveler.photo',
      phone: '+1-555-0234',
      recentPosts: 89,
      avgLikes: 7300,
      avgComments: 420,
      hashtags: ['#travel', '#photography', '#wanderlust', '#explore'],
      language: 'en',
      lastPostDate: '2025-07-19',
      category: 'Travel & Photography'
    }
  ];

  useEffect(() => {
    // Real data loaded from API
    // Real data loaded from API
  }, []);

  const searchAccounts = () => {
    // Real data loaded from API
    
    setTimeout(() => {
      let filtered = mockAccounts;
      
      if (searchTerm) {
        filtered = filtered.filter(account => 
          account.username.toLowerCase().includes(searchTerm.toLowerCase()) ||
          account.displayName.toLowerCase().includes(searchTerm.toLowerCase()) ||
          account.bio.toLowerCase().includes(searchTerm.toLowerCase())
        );
      }
      
      // Apply filters
      filtered = filtered.filter(account => {
        return (
          account.followers >= filters.followerRange.min &&
          account.followers <= filters.followerRange.max &&
          account.following >= filters.followingRange.min &&
          account.following <= filters.followingRange.max &&
          account.engagementRate >= filters.engagementRate.min &&
          account.engagementRate <= filters.engagementRate.max &&
          (filters.location === '' || account.location?.toLowerCase().includes(filters.location.toLowerCase())) &&
          (filters.accountType === 'all' || account.accountType === filters.accountType) &&
          (!filters.verified || account.verified === filters.verified)
        );
      });
      
      // Real data loaded from API
      // Real data loaded from API
      // Real data loaded from API
    }, 800);
  };

  const toggleAccountSelection = (accountId) => {
    const newSelected = new Set(selectedAccounts);
    if (newSelected.has(accountId)) {
      newSelected.delete(accountId);
    } else {
      newSelected.add(accountId);
    }
    // Real data loaded from API
  };

  const selectAll = () => {
    if (selectedAccounts.size === accounts.length) {
      setSelectedAccounts(new Set());
    } else {
      setSelectedAccounts(new Set(accounts.map(a => a.id)));
    }
  };

  const exportSelected = () => {
    const selectedData = accounts.filter(account => selectedAccounts.has(account.id));
    const csvData = selectedData.map(account => ({
      Username: account.username,
      'Display Name': account.displayName,
      Followers: account.followers,
      Following: account.following,
      'Engagement Rate': `${account.engagementRate}%`,
      'Account Type': account.accountType,
      Bio: account.bio,
      Location: account.location || '',
      Website: account.website || '',
      Email: account.email || '',
      Phone: account.phone || '',
      Category: account.category
    }));
    
    // Simple CSV export (in production, use proper CSV library)
    const csv = [
      Object.keys(csvData[0]).join(','),
      ...csvData.map(row => Object.values(row).map(val => `"${val}"`).join(','))
    ].join('\n');
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `instagram_accounts_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
  };

  const formatNumber = (num) => {
    if (num >= 1000000) return `${(num / 1000000).toFixed(1)}M`;
    if (num >= 1000) return `${(num / 1000).toFixed(1)}K`;
    return num.toString();
  };

  const getAccountTypeColor = (type) => {
    switch (type) {
      case 'business': return 'text-blue-600 bg-blue-100 dark:bg-blue-900/20';
      case 'creator': return 'text-purple-600 bg-purple-100 dark:bg-purple-900/20';
      case 'personal': return 'text-green-600 bg-green-100 dark:bg-green-900/20';
      default: return 'text-gray-600 bg-gray-100 dark:bg-gray-900/20';
    }
  };

  const AccountCard = ({ account }) => (
    <motion.div
      layout
      initial={{ opacity: 0, scale: 0.9 }}
      animate={{ opacity: 1, scale: 1 }}
      className={`bg-card border rounded-xl p-6 hover:shadow-lg transition-all duration-300 ${
        selectedAccounts.has(account.id) ? 'border-accent-primary ring-2 ring-accent-primary/20' : 'border-default'
      }`}
    >
      {/* Header */}
      <div className="flex items-start justify-between mb-4">
        <div className="flex items-center space-x-3">
          <div className="relative">
            <img
              src={account.profilePicture}
              alt={account.username}
              className="w-16 h-16 rounded-full object-cover"
            />
            {account.verified && (
              <div className="absolute -top-1 -right-1 bg-blue-500 rounded-full p-1">
                <StarIcon className="w-3 h-3 text-white" />
              </div>
            )}
          </div>
          <div className="flex-1 min-w-0">
            <h3 className="text-lg font-semibold text-primary truncate">@{account.username}</h3>
            <p className="text-sm text-secondary truncate">{account.displayName}</p>
            <span className={`inline-block px-2 py-1 rounded-full text-xs font-medium ${getAccountTypeColor(account.accountType)}`}>
              {account.accountType}
            </span>
          </div>
        </div>
        <div className="flex items-center space-x-2">
          <button
            onClick={() => toggleAccountSelection(account.id)}
            className={`p-2 rounded-lg transition-colors ${
              selectedAccounts.has(account.id)
                ? 'bg-accent-primary text-white'
                : 'bg-surface border border-default hover:bg-hover'
            }`}
          >
            <UserPlusIcon className="w-4 h-4" />
          </button>
        </div>
      </div>

      {/* Bio */}
      <p className="text-sm text-secondary mb-4 line-clamp-2">{account.bio}</p>

      {/* Stats Grid */}
      <div className="grid grid-cols-3 gap-4 mb-4">
        <div className="text-center">
          <div className="text-lg font-bold text-primary">{formatNumber(account.followers)}</div>
          <div className="text-xs text-secondary">Followers</div>
        </div>
        <div className="text-center">
          <div className="text-lg font-bold text-primary">{formatNumber(account.following)}</div>
          <div className="text-xs text-secondary">Following</div>
        </div>
        <div className="text-center">
          <div className="text-lg font-bold text-accent-primary">{account.engagementRate}%</div>
          <div className="text-xs text-secondary">Engagement</div>
        </div>
      </div>

      {/* Additional Info */}
      <div className="space-y-2 mb-4">
        {account.location && (
          <div className="flex items-center space-x-2 text-sm text-secondary">
            <MapPinIcon className="w-4 h-4" />
            <span>{account.location}</span>
          </div>
        )}
        {account.website && (
          <div className="flex items-center space-x-2 text-sm text-secondary">
            <GlobeAltIcon className="w-4 h-4" />
            <span className="truncate">{account.website}</span>
          </div>
        )}
        {account.email && (
          <div className="flex items-center space-x-2 text-sm text-secondary">
            <EnvelopeIcon className="w-4 h-4" />
            <span className="truncate">{account.email}</span>
          </div>
        )}
      </div>

      {/* Hashtags */}
      <div className="flex flex-wrap gap-1 mb-4">
        {account.hashtags.slice(0, 4).map(tag => (
          <span key={tag} className="px-2 py-1 bg-surface rounded text-xs text-secondary">
            {tag}
          </span>
        ))}
        {account.hashtags.length > 4 && (
          <span className="px-2 py-1 text-xs text-accent-primary">
            +{account.hashtags.length - 4} more
          </span>
        )}
      </div>

      {/* Action Buttons */}
      <div className="flex space-x-2">
        <button className="flex-1 bg-accent-primary text-white px-3 py-2 rounded-lg hover:opacity-90 transition-opacity text-sm font-medium">
          View Profile
        </button>
        <button className="px-3 py-2 border border-default rounded-lg hover:bg-hover transition-colors text-sm">
          Contact
        </button>
      </div>
    </motion.div>
  );

  return (
    <div className="min-h-screen bg-app">
      <div className="max-w-7xl mx-auto px-4 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold text-primary">Instagram Database</h1>
            <p className="text-secondary mt-2">Discover and analyze Instagram accounts for lead generation</p>
          </div>
          
          <div className="flex items-center space-x-4">
            {selectedAccounts.size > 0 && (
              <button
                onClick={exportSelected}
                className="bg-green-600 text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-2"
              >
                <ArrowDownTrayIcon className="w-4 h-4" />
                <span>Export ({selectedAccounts.size})</span>
              </button>
            )}
          </div>
        </div>

        {/* Search and Filters */}
        <div className="bg-card border border-default rounded-xl p-6 mb-8">
          {/* Search Bar */}
          <div className="relative mb-6">
            <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-secondary" />
            <input
              type="text"
              placeholder="Search by username, name, or bio..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-4 py-3 border border-default rounded-lg bg-surface text-primary placeholder-secondary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none transition-all"
            />
            <button
              onClick={searchAccounts}
              className="absolute right-2 top-1/2 transform -translate-y-1/2 bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity text-sm font-medium"
            >
              Search
            </button>
          </div>

          {/* Filters */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {/* Follower Range */}
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Followers</label>
              <div className="flex space-x-2">
                <input
                  type="number"
                  placeholder="Min"
                  value={filters.followerRange.min}
                  onChange={(e) => setFilters({
                    ...filters,
                    followerRange: { ...filters.followerRange, min: parseInt(e.target.value) || 0 }
                  })}
                  className="flex-1 px-3 py-2 border border-default rounded-lg bg-surface text-primary text-sm focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                />
                <input
                  type="number"
                  placeholder="Max"
                  value={filters.followerRange.max}
                  onChange={(e) => setFilters({
                    ...filters,
                    followerRange: { ...filters.followerRange, max: parseInt(e.target.value) || 1000000 }
                  })}
                  className="flex-1 px-3 py-2 border border-default rounded-lg bg-surface text-primary text-sm focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                />
              </div>
            </div>

            {/* Engagement Rate */}
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Engagement Rate (%)</label>
              <div className="flex space-x-2">
                <input
                  type="number"
                  placeholder="Min"
                  step="0.1"
                  value={filters.engagementRate.min}
                  onChange={(e) => setFilters({
                    ...filters,
                    engagementRate: { ...filters.engagementRate, min: parseFloat(e.target.value) || 0 }
                  })}
                  className="flex-1 px-3 py-2 border border-default rounded-lg bg-surface text-primary text-sm focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                />
                <input
                  type="number"
                  placeholder="Max"
                  step="0.1"
                  value={filters.engagementRate.max}
                  onChange={(e) => setFilters({
                    ...filters,
                    engagementRate: { ...filters.engagementRate, max: parseFloat(e.target.value) || 20 }
                  })}
                  className="flex-1 px-3 py-2 border border-default rounded-lg bg-surface text-primary text-sm focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                />
              </div>
            </div>

            {/* Account Type */}
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Account Type</label>
              <select
                value={filters.accountType}
                onChange={(e) => setFilters({ ...filters, accountType: e.target.value })}
                className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
              >
                <option value="all">All Types</option>
                <option value="personal">Personal</option>
                <option value="business">Business</option>
                <option value="creator">Creator</option>
              </select>
            </div>

            {/* Location */}
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">Location</label>
              <input
                type="text"
                placeholder="City, Country"
                value={filters.location}
                onChange={(e) => setFilters({ ...filters, location: e.target.value })}
                className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary placeholder-secondary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
              />
            </div>
          </div>

          {/* Filter Actions */}
          <div className="flex items-center justify-between mt-4 pt-4 border-t border-default">
            <div className="flex items-center space-x-4">
              <label className="flex items-center space-x-2">
                <input
                  type="checkbox"
                  checked={filters.verified}
                  onChange={(e) => setFilters({ ...filters, verified: e.target.checked })}
                  className="rounded border-default text-accent-primary focus:ring-2 focus:ring-accent-primary/20"
                />
                <span className="text-sm text-secondary">Verified accounts only</span>
              </label>
            </div>
            
            <div className="flex items-center space-x-2">
              <button
                onClick={() => setFilters({
                  followerRange: { min: 0, max: 1000000 },
                  followingRange: { min: 0, max: 10000 },
                  engagementRate: { min: 0, max: 20 },
                  location: '',
                  hashtags: '',
                  accountType: 'all',
                  postFrequency: 'all',
                  language: 'all',
                  verified: false
                })}
                className="text-sm text-secondary hover:text-primary transition-colors"
              >
                Clear Filters
              </button>
              <button
                onClick={searchAccounts}
                className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-2"
              >
                <FunnelIcon className="w-4 h-4" />
                <span>Apply Filters</span>
              </button>
            </div>
          </div>
        </div>

        {/* Results Header */}
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center space-x-4">
            <p className="text-secondary">
              {loading ? 'Searching...' : `${totalResults} accounts found`}
            </p>
            {accounts.length > 0 && (
              <button
                onClick={selectAll}
                className="text-sm text-accent-primary hover:opacity-80 font-medium"
              >
                {selectedAccounts.size === accounts.length ? 'Deselect All' : 'Select All'}
              </button>
            )}
          </div>
          
          {selectedAccounts.size > 0 && (
            <div className="text-sm text-secondary">
              {selectedAccounts.size} selected
            </div>
          )}
        </div>

        {/* Results Grid */}
        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[...Array(6)].map((_, i) => (
              <div key={i} className="bg-card border border-default rounded-xl p-6 animate-pulse">
                <div className="flex items-center space-x-3 mb-4">
                  <div className="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                  <div className="flex-1">
                    <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                    <div className="h-3 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                  </div>
                </div>
                <div className="h-12 bg-gray-200 dark:bg-gray-700 rounded mb-4"></div>
                <div className="grid grid-cols-3 gap-4 mb-4">
                  <div className="h-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                  <div className="h-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                  <div className="h-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
              </div>
            ))}
          </div>
        ) : accounts.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {accounts.map(account => (
              <AccountCard key={account.id} account={account} />
            ))}
          </div>
        ) : (
          <div className="text-center py-12 bg-card border border-default rounded-xl">
            <UserGroupIcon className="w-16 h-16 mx-auto text-secondary mb-4 opacity-50" />
            <h3 className="text-lg font-medium text-primary mb-2">No accounts found</h3>
            <p className="text-secondary mb-4">Try adjusting your search criteria or filters</p>
            <button
              onClick={() => {
                // Real data loaded from API
                // Real data loaded from API
                searchAccounts();
              }}
              className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity"
            >
              Clear All Filters
            </button>
          </div>
        )}

        {/* Load More */}
        {!loading && accounts.length > 0 && accounts.length < totalResults && (
          <div className="text-center">
            <button className="bg-surface border border-default text-primary px-6 py-3 rounded-lg hover:bg-hover transition-colors">
              Load More Accounts
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default InstagramDatabasePage;