import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import api from '../../services/api';
import {
  LinkIcon,
  ClipboardDocumentIcon,
  ChartBarIcon,
  EyeIcon,
  CalendarIcon,
  GlobeAltIcon,
  QrCodeIcon
} from '@heroicons/react/24/outline';

const LinkShortenerPage = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  const [longUrl, setLongUrl] = useState('');
  const [shortCode, setShortCode] = useState('');
  const [shortLinks, setShortLinks] = useState([]);
  const [loading, setLoading] = useState(false);
  const [stats, setStats] = useState({
    total_links: 0,
    active_links: 0,
    total_clicks: 0,
    click_rate: 0
  });
  const [initialLoading, setInitialLoading] = useState(true);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      await Promise.all([loadLinks(), loadStats()]);
    } catch (err) {
      console.error('Failed to load data:', err);
      error('Failed to load link shortener data');
    } finally {
      setInitialLoading(false);
    }
  };

  const loadLinks = async () => {
    try {
      const response = await api.get('/link-shortener/links');
      if (response.data.success) {
        setShortLinks(response.data.data.links);
      }
    } catch (err) {
      console.error('Failed to load links:', err);
    }
  };

  const loadStats = async () => {
    try {
      const response = await api.get('/link-shortener/stats');
      if (response.data.success) {
        setStats(response.data.data.stats);
      }
    } catch (err) {
      console.error('Failed to load stats:', err);
    }
  };

  const handleCreateShortLink = async (e) => {
    e.preventDefault();
    setLoading(true);
    
    try {
      const response = await api.post('/link-shortener/create', {
        original_url: longUrl,
        custom_code: shortCode || null
      });
      
      if (response.data.success) {
        success('Short link created successfully!');
        setLongUrl('');
        setShortCode('');
        await loadData(); // Reload both links and stats
      } else {
        error('Failed to create short link');
      }
    } catch (err) {
      console.error('Failed to create short link:', err);
      error(err.response?.data?.detail || 'Failed to create short link');
    } finally {
      setLoading(false);
    }
  };

  const copyToClipboard = async (url) => {
    try {
      await navigator.clipboard.writeText(url);
      success('Link copied to clipboard!');
    } catch (err) {
      console.error('Failed to copy to clipboard:', err);
      error('Failed to copy link');
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    return date.toLocaleDateString();
  };

  if (initialLoading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="mb-8"
      >
        <h1 className="text-3xl font-bold text-primary mb-2">Link Shortener</h1>
        <p className="text-secondary">Create short, trackable links for your content</p>
      </motion.div>

      {/* Stats Cards */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8"
      >
        {[
          { label: 'Total Links', value: '127', icon: LinkIcon, color: 'bg-blue-500' },
          { label: 'Total Clicks', value: '5,834', icon: EyeIcon, color: 'bg-green-500' },
          { label: 'Click Rate', value: '78.5%', icon: ChartBarIcon, color: 'bg-purple-500' },
          { label: 'Active Links', value: '95', icon: GlobeAltIcon, color: 'bg-orange-500' }
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
      </motion.div>

      {/* Create Short Link Form */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.2 }}
        className="bg-surface p-6 rounded-lg shadow-default"
      >
        <h2 className="text-xl font-semibold text-primary mb-4">Create New Short Link</h2>
        <form onSubmit={handleCreateShortLink} className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">
                Long URL *
              </label>
              <input
                type="url"
                value={longUrl}
                onChange={(e) => setLongUrl(e.target.value)}
                placeholder="https://example.com/your-very-long-url"
                className="input w-full"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">
                Custom Code (Optional)
              </label>
              <input
                type="text"
                value={shortCode}
                onChange={(e) => setShortCode(e.target.value)}
                placeholder="my-custom-code"
                className="input w-full"
              />
            </div>
          </div>
          <button
            type="submit"
            disabled={loading}
            className="btn btn-primary"
          >
            {loading ? 'Creating...' : 'Create Short Link'}
          </button>
        </form>
      </motion.div>

      {/* Links List */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.3 }}
        className="bg-surface rounded-lg shadow-default overflow-hidden"
      >
        <div className="p-6 border-b border-default">
          <h2 className="text-xl font-semibold text-primary">Your Short Links</h2>
        </div>
        <div className="divide-y divide-default">
          {shortLinks.map((link) => (
            <div key={link.id} className="p-6 hover:bg-surface-hover transition-colors">
              <div className="flex items-start justify-between">
                <div className="flex-1 min-w-0">
                  <div className="flex items-center space-x-2 mb-2">
                    <h3 className="text-lg font-medium text-primary truncate">
                      {link.shortUrl}
                    </h3>
                    <button
                      onClick={() => copyToClipboard(link.shortUrl)}
                      className="p-1 text-secondary hover:text-primary"
                      title="Copy to clipboard"
                    >
                      <ClipboardDocumentIcon className="h-4 w-4" />
                    </button>
                  </div>
                  <p className="text-sm text-secondary mb-2 truncate">
                    {link.originalUrl}
                  </p>
                  <div className="flex items-center space-x-4 text-sm text-secondary">
                    <span className="flex items-center">
                      <EyeIcon className="h-4 w-4 mr-1" />
                      {link.clicks} clicks
                    </span>
                    <span className="flex items-center">
                      <CalendarIcon className="h-4 w-4 mr-1" />
                      {link.created}
                    </span>
                    <span className={`px-2 py-1 rounded-full text-xs ${
                      link.status === 'active' 
                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                        : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
                    }`}>
                      {link.status}
                    </span>
                  </div>
                </div>
                <div className="flex items-center space-x-2 ml-4">
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <ChartBarIcon className="h-5 w-5" />
                  </button>
                  <button className="p-2 text-secondary hover:text-primary hover:bg-surface-hover rounded-lg">
                    <QrCodeIcon className="h-5 w-5" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </motion.div>
    </div>
  );
};

export default LinkShortenerPage;