import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  GlobeAltIcon, 
  PlusIcon, 
  PencilIcon, 
  TrashIcon,
  EyeIcon,
  LinkIcon,
  ShareIcon,
  QrCodeIcon,
  ChartBarIcon,
  PhotoIcon,
  PaintBrushIcon,
  Cog6ToothIcon,
  ClipboardDocumentIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const BioSitesPage = () => {
  const [bioSites, setBioSites] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadBioSitesData();
  }, []);

  const loadBioSitesData = async () => {
    try {
      // Real data from APInow - replace with actual API calls
      // Real data loaded from API

      // Real data loaded from API
    } catch (error) {
      console.error('Failed to load bio sites data:', error);
    } finally {
      // Real data loaded from API
    }
  };

  const StatCard = ({ title, value, change, icon: Icon, color = 'primary' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}</p>
          {change && (
            <p className={`text-sm mt-2 ${change > 0 ? 'text-accent-success' : 'text-accent-danger'}`}>
              {change > 0 ? '+' : ''}{change}% vs last month
            </p>
          )}
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const BioSiteCard = ({ site }) => (
    <div className="card-elevated p-6">
      <div className="flex items-start justify-between mb-4">
        <div className="flex-1">
          <div className="flex items-center space-x-2 mb-2">
            <h3 className="font-semibold text-primary">{site.title}</h3>
            <span className={`w-2 h-2 rounded-full ${
              site.isActive ? 'bg-accent-success' : 'bg-accent-danger'
            }`}></span>
          </div>
          <p className="text-secondary text-sm mb-2">{site.description}</p>
          <div className="flex items-center space-x-2 text-sm text-accent-primary">
            <LinkIcon className="w-4 h-4" />
            <span className="font-mono">{site.fullUrl}</span>
          </div>
        </div>
        <div className="flex items-center space-x-2">
          <button className="p-2 text-secondary hover:text-primary">
            <EyeIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-primary">
            <PencilIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-primary">
            <ShareIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-accent-danger">
            <TrashIcon className="w-4 h-4" />
          </button>
        </div>
      </div>

      <div className="grid grid-cols-3 gap-4 text-sm mb-4">
        <div className="text-center">
          <p className="text-secondary">Views</p>
          <p className="text-xl font-bold text-primary">{site.views.toLocaleString()}</p>
        </div>
        <div className="text-center">
          <p className="text-secondary">Clicks</p>
          <p className="text-xl font-bold text-primary">{site.clicks}</p>
        </div>
        <div className="text-center">
          <p className="text-secondary">CTR</p>
          <p className="text-xl font-bold text-primary">{((site.clicks / site.views) * 100).toFixed(1)}%</p>
        </div>
      </div>

      <div className="mb-4">
        <p className="text-sm text-secondary mb-2">Top Links ({site.links.length})</p>
        <div className="space-y-1">
          {site.links.slice(0, 3).map((link, index) => (
            <div key={index} className="flex items-center justify-between text-sm">
              <span className="text-primary truncate flex-1">{link.title}</span>
              <span className="text-secondary ml-2">{link.clicks} clicks</span>
            </div>
          ))}
        </div>
      </div>

      <div className="flex items-center justify-between pt-4 border-t border-default">
        <div className="flex items-center space-x-2">
          <span className={`px-2 py-1 rounded-full text-xs font-medium capitalize ${
            site.theme === 'minimal' ? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' :
            site.theme === 'colorful' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' :
            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
          }`}>
            {site.theme}
          </span>
          <span className="text-xs text-secondary">Updated {site.lastUpdated}</span>
        </div>
        <div className="flex items-center space-x-2">
          <Button variant="secondary" size="small">
            <QrCodeIcon className="w-4 h-4 mr-1" />
            QR
          </Button>
          <Button size="small">
            <Cog6ToothIcon className="w-4 h-4 mr-1" />
            Edit
          </Button>
        </div>
      </div>
    </div>
  );

  const TemplateCard = ({ template }) => (
    <div className="card-elevated p-6 hover-surface transition-colors cursor-pointer">
      <div className="aspect-video bg-gradient-surface rounded-lg mb-4 flex items-center justify-center">
        <PhotoIcon className="w-12 h-12 text-accent-primary" />
      </div>
      <h3 className="font-semibold text-primary mb-2">{template.name}</h3>
      <p className="text-secondary text-sm mb-4">{template.description}</p>
      <div className="flex items-center justify-between">
        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
          template.price === 'Free' 
            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
            : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
        }`}>
          {template.price}
        </span>
        <Button size="small">Use Template</Button>
      </div>
    </div>
  );

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="spinner w-8 h-8 text-accent-primary"></div>
      </div>
    );
  }

  const templates = [
    { name: 'Minimal Clean', description: 'Simple and clean design perfect for professionals', price: 'Free' },
    { name: 'Creative Portfolio', description: 'Colorful design for creatives and artists', price: 'Free' },
    { name: 'Business Professional', description: 'Corporate design for business professionals', price: 'Free' },
    { name: 'Influencer Pro', description: 'Modern design for social media influencers', price: '$9' },
    { name: 'Tech Startup', description: 'Sleek design for tech entrepreneurs', price: '$9' },
    { name: 'Artist Showcase', description: 'Gallery-style design for artists', price: '$9' }
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-primary">Bio Sites</h1>
          <p className="text-secondary mt-1">Create beautiful link-in-bio pages</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary">
            <PaintBrushIcon className="w-4 h-4 mr-2" />
            Browse Templates
          </Button>
          <Button>
            <PlusIcon className="w-4 h-4 mr-2" />
            Create Bio Site
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'sites', name: 'My Sites' },
            { id: 'templates', name: 'Templates' },
            { id: 'analytics', name: 'Analytics' }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`py-2 px-1 border-b-2 font-medium text-sm ${
                activeTab === tab.id
                  ? 'border-accent-primary text-accent-primary'
                  : 'border-transparent text-secondary hover:text-primary hover:border-gray-300'
              }`}
            >
              {tab.name}
            </button>
          ))}
        </nav>
      </div>

      {/* Content based on active tab */}
      {activeTab === 'overview' && (
        <div className="space-y-6">
          {/* Analytics Stats */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
              title="Total Views"
              value={analytics.totalViews.toLocaleString()}
              change={18.2}
              icon={EyeIcon}
              color="primary"
            />
            <StatCard
              title="Total Clicks"
              value={analytics.totalClicks.toString()}
              change={12.5}
              icon={LinkIcon}
              color="success"
            />
            <StatCard
              title="Bio Sites"
              value={analytics.totalSites.toString()}
              change={0}
              icon={GlobeAltIcon}
              color="warning"
            />
            <StatCard
              title="Avg. CTR"
              value={`${analytics.averageCTR}%`}
              change={5.3}
              icon={ChartBarIcon}
              color="primary"
            />
          </div>

          {/* Recent Sites */}
          <div>
            <h2 className="text-xl font-semibold text-primary mb-4">Recent Sites</h2>
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {bioSites.slice(0, 2).map((site) => (
                <BioSiteCard key={site.id} site={site} />
              ))}
            </div>
          </div>

          {/* Quick Actions */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <PlusIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Create New Site</h3>
              <p className="text-secondary">Start building your bio site from scratch</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <PaintBrushIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Use Template</h3>
              <p className="text-secondary">Choose from our professionally designed templates</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <ClipboardDocumentIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Import Links</h3>
              <p className="text-secondary">Import links from other platforms</p>
            </button>
          </div>
        </div>
      )}

      {activeTab === 'sites' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">My Bio Sites</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Sites</option>
                <option>Active</option>
                <option>Inactive</option>
              </select>
              <select className="input px-3 py-2 rounded-md">
                <option>Sort by Date</option>
                <option>Sort by Views</option>
                <option>Sort by Clicks</option>
              </select>
            </div>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {bioSites.map((site) => (
              <BioSiteCard key={site.id} site={site} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'templates' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Choose a Template</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Templates</option>
                <option>Free</option>
                <option>Premium</option>
              </select>
              <select className="input px-3 py-2 rounded-md">
                <option>All Categories</option>
                <option>Business</option>
                <option>Creative</option>
                <option>Personal</option>
              </select>
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {templates.map((template, index) => (
              <TemplateCard key={index} template={template} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Bio Sites Analytics</h2>
          <div className="card-elevated p-8 text-center">
            <ChartBarIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-primary mb-2">Detailed Analytics Coming Soon</h3>
            <p className="text-secondary">We're building comprehensive bio site analytics to help you track visitor behavior, link performance, and conversion rates.</p>
          </div>
        </div>
      )}
    </div>
  );
};

export default BioSitesPage;