import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
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
  const [longUrl, setLongUrl] = useState('');
  const [shortCode, setShortCode] = useState('');
  const [shortLinks, setShortLinks] = useState([]);
  const [loading, setLoading] = useState(false);

  // Mock data for demonstration
  const mockLinks = [
    {
      id: '1',
      originalUrl: 'https://example.com/very-long-url-that-needs-shortening',
      shortCode: 'abc123',
      shortUrl: 'https://mwz.to/abc123',
      clicks: 245,
      created: '2 days ago',
      status: 'active'
    },
    {
      id: '2',
      originalUrl: 'https://mystore.com/product/amazing-course',
      shortCode: 'course1',
      shortUrl: 'https://mwz.to/course1',
      clicks: 89,
      created: '1 week ago',
      status: 'active'
    }
  ];

  useEffect(() => {
    setShortLinks(mockLinks);
  }, []);

  const handleCreateShortLink = async (e) => {
    e.preventDefault();
    setLoading(true);
    
    // Simulate API call
    setTimeout(() => {
      const newLink = {
        id: Date.now().toString(),
        originalUrl: longUrl,
        shortCode: shortCode || Math.random().toString(36).substr(2, 8),
        shortUrl: `https://mwz.to/${shortCode || Math.random().toString(36).substr(2, 8)}`,
        clicks: 0,
        created: 'Just now',
        status: 'active'
      };
      
      setShortLinks([newLink, ...shortLinks]);
      setLongUrl('');
      setShortCode('');
      setLoading(false);
    }, 1000);
  };

  const copyToClipboard = (url) => {
    navigator.clipboard.writeText(url);
    // You could add a toast notification here
  };

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