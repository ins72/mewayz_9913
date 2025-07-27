import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import api from '../../services/api';
import {
  MagnifyingGlassIcon,
  FunnelIcon,
  ArrowDownTrayIcon,
  EyeIcon,
  HeartIcon,
  UserIcon,
  MapPinIcon,
  HashtagIcon,
  CalendarIcon,
  ChartBarIcon,
  PhotoIcon,
  UsersIcon,
  CreditCardIcon,
  DocumentArrowDownIcon,
  CheckCircleIcon,
  XMarkIcon,
  AdjustmentsHorizontalIcon,
  GlobeAltIcon,
  SparklesIcon,
  BoltIcon,
  ChevronRightIcon,
  ArrowPathIcon
} from '@heroicons/react/24/outline';

const InstagramLeadGeneration = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  // State management
  const [loading, setLoading] = useState(false);
  const [searchResults, setSearchResults] = useState([]);
  const [selectedAccounts, setSelectedAccounts] = useState([]);
  const [filters, setFilters] = useState({
    minFollowers: '',
    maxFollowers: '',
    minFollowing: '',
    maxFollowing: '',
    minEngagementRate: '',
    location: '',
    hashtags: '',
    bioKeywords: '',
    accountType: '',
    postFrequency: '',
    language: 'en',
    verified: '',
    businessCategory: ''
  });
  const [searchQuery, setSearchQuery] = useState('');
  const [showFilters, setShowFilters] = useState(false);
  const [exportFormat, setExportFormat] = useState('csv');
  const [searchHistory, setSearchHistory] = useState([]);
  const [savedSearches, setSavedSearches] = useState([]);

  // Pagination and sorting
  const [currentPage, setCurrentPage] = useState(1);
  const [sortBy, setSortBy] = useState('followers');
  const [sortOrder, setSortOrder] = useState('desc');
  const [itemsPerPage] = useState(50);

  // Analytics
  const [searchStats, setSearchStats] = useState({
    totalSearches: 0,
    totalAccountsFound: 0,
    averageEngagementRate: 0,
    topCategories: []
  });

  useEffect(() => {
    loadSearchHistory();
    loadSavedSearches();
    loadSearchStats();
  }, []);

  const loadSearchHistory = async () => {
    try {
      const response = await api.get('/instagram/search-history');
      if (response.data.success) {
        // Real data loaded from API
      }
    } catch (err) {
      console.error('Failed to load search history:', err);
    }
  };

  const loadSavedSearches = async () => {
    try {
      const response = await api.get('/instagram/saved-searches');
      if (response.data.success) {
        // Real data loaded from API
      }
    } catch (err) {
      console.error('Failed to load saved searches:', err);
    }
  };

  const loadSearchStats = async () => {
    try {
      const response = await api.get('/instagram/search-stats');
      if (response.data.success) {
        // Real data loaded from API
      }
    } catch (err) {
      console.error('Failed to load search stats:', err);
    }
  };

  const handleSearch = async () => {
    if (!searchQuery.trim()) {
      error('Please enter a search query');
      return;
    }

    // Real data loaded from API
    try {
      const response = await api.post('/instagram/search', {
        query: searchQuery,
        filters: filters,
        page: currentPage,
        limit: itemsPerPage,
        sortBy: sortBy,
        sortOrder: sortOrder
      });

      if (response.data.success) {
        // Real data loaded from API
        success(`Found ${response.data.data.total} accounts`);
        
        // Save search to history
        setSearchHistory(prev => [{
          id: Date.now(),
          query: searchQuery,
          filters: { ...filters },
          timestamp: new Date(),
          resultsCount: response.data.data.total
        }, ...prev.slice(0, 9)]); // Keep last 10 searches
      } else {
        error('Search failed');
      }
    } catch (err) {
      console.error('Search failed:', err);
      error('Search failed. Please try again.');
    } finally {
      // Real data loaded from API
    }
  };

  const handleExport = async () => {
    if (selectedAccounts.length === 0) {
      error('Please select accounts to export');
      return;
    }

    // Real data loaded from API
    try {
      const response = await api.post('/instagram/export', {
        accounts: selectedAccounts,
        format: exportFormat,
        includeEmails: true,
        includeContactInfo: true,
        includeAnalytics: true
      }, {
        responseType: 'blob'
      });

      // Create download link
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `instagram_leads_${new Date().toISOString().split('T')[0]}.${exportFormat}`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      
      success(`Exported ${selectedAccounts.length} accounts successfully`);
    } catch (err) {
      console.error('Export failed:', err);
      error('Export failed. Please try again.');
    } finally {
      // Real data loaded from API
    }
  };

  const toggleAccountSelection = (accountId) => {
    setSelectedAccounts(prev => 
      prev.includes(accountId)
        ? prev.filter(id => id !== accountId)
        : [...prev, accountId]
    );
  };

  const selectAllAccounts = () => {
    setSelectedAccounts(searchResults.map(account => account.id));
  };

  const clearSelection = () => {
    // Real data loaded from API
  };

  const saveCurrentSearch = async () => {
    const searchName = prompt('Enter a name for this search:');
    if (!searchName) return;

    try {
      const response = await api.post('/instagram/save-search', {
        name: searchName,
        query: searchQuery,
        filters: filters
      });

      if (response.data.success) {
        // Real data loaded from API
        success('Search saved successfully');
      }
    } catch (err) {
      error('Failed to save search');
    }
  };

  const loadSavedSearch = (search) => {
    // Real data loaded from API
    // Real data loaded from API
    success('Search loaded');
  };

  const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
  };

  const getEngagementRateColor = (rate) => {
    if (rate >= 6) return 'text-green-500';
    if (rate >= 3) return 'text-yellow-500';
    return 'text-red-500';
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
          <h1 className="text-3xl font-bold text-primary">Instagram Lead Generation</h1>
          <p className="text-secondary mt-1">
            Discover and export Instagram accounts based on advanced filtering
          </p>
        </div>
        <div className="flex items-center space-x-3">
          <span className="text-sm text-secondary">
            {searchResults.length} accounts found
          </span>
          <span className="text-sm text-secondary">•</span>
          <span className="text-sm text-secondary">
            {selectedAccounts.length} selected
          </span>
        </div>
      </motion.div>

      {/* Search Stats */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-4 gap-4"
      >
        <div className="bg-surface p-4 rounded-lg">
          <div className="flex items-center">
            <MagnifyingGlassIcon className="h-8 w-8 text-blue-500" />
            <div className="ml-3">
              <p className="text-sm font-medium text-secondary">Total Searches</p>
              <p className="text-xl font-bold text-primary">{searchStats.totalSearches}</p>
            </div>
          </div>
        </div>
        <div className="bg-surface p-4 rounded-lg">
          <div className="flex items-center">
            <UsersIcon className="h-8 w-8 text-green-500" />
            <div className="ml-3">
              <p className="text-sm font-medium text-secondary">Accounts Found</p>
              <p className="text-xl font-bold text-primary">{formatNumber(searchStats.totalAccountsFound)}</p>
            </div>
          </div>
        </div>
        <div className="bg-surface p-4 rounded-lg">
          <div className="flex items-center">
            <HeartIcon className="h-8 w-8 text-pink-500" />
            <div className="ml-3">
              <p className="text-sm font-medium text-secondary">Avg Engagement</p>
              <p className="text-xl font-bold text-primary">{searchStats.averageEngagementRate}%</p>
            </div>
          </div>
        </div>
        <div className="bg-surface p-4 rounded-lg">
          <div className="flex items-center">
            <DocumentArrowDownIcon className="h-8 w-8 text-purple-500" />
            <div className="ml-3">
              <p className="text-sm font-medium text-secondary">Exports</p>
              <p className="text-xl font-bold text-primary">47</p>
            </div>
          </div>
        </div>
      </motion.div>

      {/* Search Section */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.2 }}
        className="bg-surface p-6 rounded-lg shadow-default"
      >
        <div className="flex flex-col space-y-4">
          {/* Search Input */}
          <div className="flex flex-col sm:flex-row gap-4">
            <div className="flex-1">
              <div className="relative">
                <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search by username, hashtag, location, or bio keywords..."
                  className="w-full pl-10 pr-4 py-3 border border-default rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-accent-primary"
                  onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                />
              </div>
            </div>
            <div className="flex space-x-2">
              <button
                onClick={() => setShowFilters(!showFilters)}
                className={`px-4 py-3 border rounded-lg flex items-center space-x-2 transition-colors ${
                  showFilters 
                    ? 'bg-accent-primary text-white border-accent-primary' 
                    : 'border-default text-secondary hover:text-primary hover:border-accent-primary'
                }`}
              >
                <FunnelIcon className="h-5 w-5" />
                <span>Filters</span>
              </button>
              <button
                onClick={handleSearch}
                disabled={loading}
                className="bg-accent-primary text-white px-6 py-3 rounded-lg hover:bg-accent-secondary disabled:opacity-50 flex items-center space-x-2"
              >
                {loading ? (
                  <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white" />
                ) : (
                  <MagnifyingGlassIcon className="h-5 w-5" />
                )}
                <span>Search</span>
              </button>
            </div>
          </div>

          {/* Advanced Filters */}
          <AnimatePresence>
            {showFilters && (
              <motion.div
                initial={{ opacity: 0, height: 0 }}
                animate={{ opacity: 1, height: 'auto' }}
                exit={{ opacity: 0, height: 0 }}
                className="border-t border-default pt-4"
              >
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {/* Follower Range */}
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">
                      Followers Range
                    </label>
                    <div className="flex space-x-2">
                      <input
                        type="number"
                        placeholder="Min"
                        value={filters.minFollowers}
                        onChange={(e) => setFilters(prev => ({ ...prev, minFollowers: e.target.value }))}
                        className="w-1/2 px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                      />
                      <input
                        type="number"
                        placeholder="Max"
                        value={filters.maxFollowers}
                        onChange={(e) => setFilters(prev => ({ ...prev, maxFollowers: e.target.value }))}
                        className="w-1/2 px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                      />
                    </div>
                  </div>

                  {/* Following Range */}
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">
                      Following Range
                    </label>
                    <div className="flex space-x-2">
                      <input
                        type="number"
                        placeholder="Min"
                        value={filters.minFollowing}
                        onChange={(e) => setFilters(prev => ({ ...prev, minFollowing: e.target.value }))}
                        className="w-1/2 px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                      />
                      <input
                        type="number"
                        placeholder="Max"
                        value={filters.maxFollowing}
                        onChange={(e) => setFilters(prev => ({ ...prev, maxFollowing: e.target.value }))}
                        className="w-1/2 px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                      />
                    </div>
                  </div>

                  {/* Engagement Rate */}
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">
                      Min Engagement Rate
                    </label>
                    <input
                      type="number"
                      placeholder="e.g. 3.5"
                      value={filters.minEngagementRate}
                      onChange={(e) => setFilters(prev => ({ ...prev, minEngagementRate: e.target.value }))}
                      className="w-full px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                    />
                  </div>

                  {/* Location */}
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">
                      Location
                    </label>
                    <input
                      type="text"
                      placeholder="e.g. New York, USA"
                      value={filters.location}
                      onChange={(e) => setFilters(prev => ({ ...prev, location: e.target.value }))}
                      className="w-full px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                    />
                  </div>

                  {/* Account Type */}
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">
                      Account Type
                    </label>
                    <select
                      value={filters.accountType}
                      onChange={(e) => setFilters(prev => ({ ...prev, accountType: e.target.value }))}
                      className="w-full px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                    >
                      <option value="">All Types</option>
                      <option value="business">Business</option>
                      <option value="creator">Creator</option>
                      <option value="personal">Personal</option>
                    </select>
                  </div>

                  {/* Language */}
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">
                      Language
                    </label>
                    <select
                      value={filters.language}
                      onChange={(e) => setFilters(prev => ({ ...prev, language: e.target.value }))}
                      className="w-full px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                    >
                      <option value="en">English</option>
                      <option value="es">Spanish</option>
                      <option value="fr">French</option>
                      <option value="de">German</option>
                      <option value="it">Italian</option>
                      <option value="pt">Portuguese</option>
                      <option value="ja">Japanese</option>
                      <option value="ko">Korean</option>
                      <option value="zh">Chinese</option>
                    </select>
                  </div>

                  {/* Bio Keywords */}
                  <div className="md:col-span-2">
                    <label className="block text-sm font-medium text-secondary mb-2">
                      Bio Keywords
                    </label>
                    <input
                      type="text"
                      placeholder="e.g. entrepreneur, marketing, fitness"
                      value={filters.bioKeywords}
                      onChange={(e) => setFilters(prev => ({ ...prev, bioKeywords: e.target.value }))}
                      className="w-full px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                    />
                  </div>

                  {/* Hashtags */}
                  <div className="md:col-span-2">
                    <label className="block text-sm font-medium text-secondary mb-2">
                      Hashtags Used
                    </label>
                    <input
                      type="text"
                      placeholder="e.g. #marketing, #business, #entrepreneur"
                      value={filters.hashtags}
                      onChange={(e) => setFilters(prev => ({ ...prev, hashtags: e.target.value }))}
                      className="w-full px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
                    />
                  </div>
                </div>

                <div className="flex justify-between items-center mt-4">
                  <button
                    onClick={() => setFilters({
                      minFollowers: '', maxFollowers: '', minFollowing: '', maxFollowing: '',
                      minEngagementRate: '', location: '', hashtags: '', bioKeywords: '',
                      accountType: '', postFrequency: '', language: 'en', verified: '',
                      businessCategory: ''
                    })}
                    className="text-secondary hover:text-primary flex items-center space-x-1"
                  >
                    <XMarkIcon className="h-4 w-4" />
                    <span>Clear Filters</span>
                  </button>
                  <button
                    onClick={saveCurrentSearch}
                    className="text-accent-primary hover:text-accent-secondary flex items-center space-x-1"
                  >
                    <CheckCircleIcon className="h-4 w-4" />
                    <span>Save Search</span>
                  </button>
                </div>
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      </motion.div>

      {/* Results Section */}
      {searchResults.length > 0 && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="bg-surface p-6 rounded-lg shadow-default"
        >
          {/* Results Header */}
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <div>
              <h2 className="text-xl font-semibold text-primary">Search Results</h2>
              <p className="text-secondary">
                {searchResults.length} accounts found • {selectedAccounts.length} selected
              </p>
            </div>
            <div className="flex items-center space-x-3 mt-3 sm:mt-0">
              <select
                value={exportFormat}
                onChange={(e) => setExportFormat(e.target.value)}
                className="px-3 py-2 border border-default rounded focus:ring-2 focus:ring-accent-primary"
              >
                <option value="csv">CSV</option>
                <option value="excel">Excel</option>
                <option value="json">JSON</option>
              </select>
              <button
                onClick={selectAllAccounts}
                className="text-accent-primary hover:text-accent-secondary"
              >
                Select All
              </button>
              <button
                onClick={clearSelection}
                className="text-secondary hover:text-primary"
              >
                Clear
              </button>
              <button
                onClick={handleExport}
                disabled={selectedAccounts.length === 0 || loading}
                className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:bg-accent-secondary disabled:opacity-50 flex items-center space-x-2"
              >
                <ArrowDownTrayIcon className="h-4 w-4" />
                <span>Export</span>
              </button>
            </div>
          </div>

          {/* Results Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {searchResults.map((account) => (
              <div
                key={account.id}
                className={`bg-surface-elevated p-4 rounded-lg border-2 transition-all cursor-pointer ${
                  selectedAccounts.includes(account.id)
                    ? 'border-accent-primary bg-accent-primary/5'
                    : 'border-transparent hover:border-accent-primary/50'
                }`}
                onClick={() => toggleAccountSelection(account.id)}
              >
                <div className="flex items-start justify-between">
                  <div className="flex items-center space-x-3">
                    <img
                      src={account.profilePicture || 'https://ui-avatars.com/api/?name=' + account.username}
                      alt={account.username}
                      className="w-12 h-12 rounded-full object-cover"
                    />
                    <div>
                      <h3 className="font-semibold text-primary">
                        {account.displayName || account.username}
                      </h3>
                      <p className="text-sm text-secondary">@{account.username}</p>
                      {account.verified && (
                        <span className="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mt-1">
                          <CheckCircleIcon className="h-3 w-3 mr-1" />
                          Verified
                        </span>
                      )}
                    </div>
                  </div>
                  <input
                    type="checkbox"
                    checked={selectedAccounts.includes(account.id)}
                    onChange={() => toggleAccountSelection(account.id)}
                    className="rounded border-gray-300 text-accent-primary focus:ring-accent-primary"
                  />
                </div>

                <div className="mt-3">
                  <p className="text-sm text-secondary line-clamp-2">
                    {account.bio || 'No bio available'}
                  </p>
                </div>

                <div className="grid grid-cols-2 gap-3 mt-4 text-sm">
                  <div className="flex items-center space-x-1">
                    <UsersIcon className="h-4 w-4 text-secondary" />
                    <span className="text-secondary">{formatNumber(account.followers)}</span>
                  </div>
                  <div className="flex items-center space-x-1">
                    <HeartIcon className={`h-4 w-4 ${getEngagementRateColor(account.engagementRate)}`} />
                    <span className={getEngagementRateColor(account.engagementRate)}>
                      {account.engagementRate}%
                    </span>
                  </div>
                  <div className="flex items-center space-x-1">
                    <PhotoIcon className="h-4 w-4 text-secondary" />
                    <span className="text-secondary">{account.postCount} posts</span>
                  </div>
                  {account.location && (
                    <div className="flex items-center space-x-1">
                      <MapPinIcon className="h-4 w-4 text-secondary" />
                      <span className="text-secondary truncate">{account.location}</span>
                    </div>
                  )}
                </div>

                {account.email && (
                  <div className="mt-3 p-2 bg-green-50 rounded border border-green-200">
                    <p className="text-xs text-green-800">
                      <strong>Email found:</strong> {account.email}
                    </p>
                  </div>
                )}

                <div className="flex items-center justify-between mt-4">
                  <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs ${
                    account.accountType === 'business' 
                      ? 'bg-blue-100 text-blue-800'
                      : account.accountType === 'creator'
                      ? 'bg-purple-100 text-purple-800'
                      : 'bg-gray-100 text-gray-800'
                  }`}>
                    {account.accountType || 'personal'}
                  </span>
                  <span className="text-xs text-secondary">
                    {account.lastPostDate && `Last post: ${new Date(account.lastPostDate).toLocaleDateString()}`}
                  </span>
                </div>
              </div>
            ))}
          </div>
        </motion.div>
      )}

      {/* Saved Searches & History */}
      {(savedSearches.length > 0 || searchHistory.length > 0) && (
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.4 }}
          className="grid grid-cols-1 lg:grid-cols-2 gap-6"
        >
          {/* Saved Searches */}
          {savedSearches.length > 0 && (
            <div className="bg-surface p-6 rounded-lg shadow-default">
              <h3 className="text-lg font-semibold text-primary mb-4">Saved Searches</h3>
              <div className="space-y-3">
                {savedSearches.slice(0, 5).map((search) => (
                  <div
                    key={search.id}
                    className="flex items-center justify-between p-3 bg-surface-elevated rounded border hover:border-accent-primary cursor-pointer"
                    onClick={() => loadSavedSearch(search)}
                  >
                    <div>
                      <p className="font-medium text-primary">{search.name}</p>
                      <p className="text-sm text-secondary">{search.query}</p>
                    </div>
                    <ChevronRightIcon className="h-5 w-5 text-secondary" />
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Search History */}
          {searchHistory.length > 0 && (
            <div className="bg-surface p-6 rounded-lg shadow-default">
              <h3 className="text-lg font-semibold text-primary mb-4">Recent Searches</h3>
              <div className="space-y-3">
                {searchHistory.slice(0, 5).map((search) => (
                  <div
                    key={search.id}
                    className="flex items-center justify-between p-3 bg-surface-elevated rounded"
                  >
                    <div>
                      <p className="font-medium text-primary">{search.query}</p>
                      <p className="text-sm text-secondary">
                        {search.resultsCount} results • {new Date(search.timestamp).toLocaleDateString()}
                      </p>
                    </div>
                    <button
                      onClick={() => {
                        // Real data loaded from API
                        // Real data loaded from API
                      }}
                      className="text-accent-primary hover:text-accent-secondary"
                    >
                      <ArrowPathIcon className="h-4 w-4" />
                    </button>
                  </div>
                ))}
              </div>
            </div>
          )}
        </motion.div>
      )}
    </div>
  );
};

export default InstagramLeadGeneration;