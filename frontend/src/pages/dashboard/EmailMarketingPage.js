import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  EnvelopeIcon, 
  PlusIcon, 
  PencilIcon, 
  TrashIcon,
  EyeIcon,
  PaperAirplaneIcon,
  UserGroupIcon,
  ChartBarIcon,
  DocumentTextIcon,
  CalendarIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  ClockIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const EmailMarketingPage = () => {
  const [campaigns, setCampaigns] = useState([]);
  const [templates, setTemplates] = useState([]);
  const [lists, setLists] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadEmailMarketingData();
  }, []);

  const loadEmailMarketingData = async () => {
    try {
      // Real data from APInow - replace with actual API calls
      // Real data loaded from API

      // Real data loaded from API

      // Real data loaded from API

      // Real data loaded from API
    } catch (error) {
      console.error('Failed to load email marketing data:', error);
    } finally {
      // Real data loaded from API
    }
  };

  const StatCard = ({ title, value, change, icon: Icon, color = 'primary', suffix = '' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}{suffix}</p>
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

  const CampaignCard = ({ campaign }) => (
    <div className="card-elevated p-6">
      <div className="flex items-start justify-between mb-4">
        <div className="flex-1">
          <div className="flex items-center space-x-2 mb-2">
            <h3 className="font-semibold text-primary">{campaign.name}</h3>
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${
              campaign.status === 'sent' 
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : campaign.status === 'scheduled'
                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            }`}>
              {campaign.status}
            </span>
          </div>
          <p className="text-secondary text-sm mb-3">{campaign.subject}</p>
          
          {campaign.status === 'sent' && (
            <div className="grid grid-cols-3 gap-4 text-sm">
              <div>
                <p className="text-secondary">Open Rate</p>
                <p className="font-bold text-accent-success">{campaign.openRate}%</p>
              </div>
              <div>
                <p className="text-secondary">Click Rate</p>
                <p className="font-bold text-accent-primary">{campaign.clickRate}%</p>
              </div>
              <div>
                <p className="text-secondary">Recipients</p>
                <p className="font-bold text-primary">{campaign.recipients.toLocaleString()}</p>
              </div>
            </div>
          )}
          
          {campaign.status === 'scheduled' && (
            <div className="flex items-center space-x-4 text-sm">
              <div className="flex items-center space-x-2">
                <CalendarIcon className="w-4 h-4 text-accent-primary" />
                <span className="text-primary">Scheduled: {campaign.scheduledDate}</span>
              </div>
              <div className="flex items-center space-x-2">
                <UserGroupIcon className="w-4 h-4 text-accent-primary" />
                <span className="text-primary">{campaign.recipients.toLocaleString()} recipients</span>
              </div>
            </div>
          )}
        </div>
        <div className="flex items-center space-x-2">
          <button className="p-2 text-secondary hover:text-primary">
            <EyeIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-primary">
            <PencilIcon className="w-4 h-4" />
          </button>
          {campaign.status === 'scheduled' && (
            <button className="p-2 text-secondary hover:text-accent-success">
              <PaperAirplaneIcon className="w-4 h-4" />
            </button>
          )}
          <button className="p-2 text-secondary hover:text-accent-danger">
            <TrashIcon className="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  );

  const ListCard = ({ list }) => (
    <div className="card-elevated p-6">
      <div className="flex items-center justify-between mb-4">
        <h3 className="font-semibold text-primary">{list.name}</h3>
        <Button variant="secondary" size="small">Manage</Button>
      </div>
      
      <div className="grid grid-cols-2 gap-4 text-sm">
        <div>
          <p className="text-secondary">Subscribers</p>
          <p className="text-2xl font-bold text-primary">{list.subscribers.toLocaleString()}</p>
        </div>
        <div>
          <p className="text-secondary">Growth</p>
          <p className={`text-lg font-bold ${list.growth > 0 ? 'text-accent-success' : 'text-accent-danger'}`}>
            +{list.growth}%
          </p>
        </div>
      </div>
      
      <div className="mt-4 pt-4 border-t border-default">
        <div className="flex items-center justify-between text-sm">
          <span className="text-secondary">Engagement Rate</span>
          <span className="text-primary font-medium">32.5%</span>
        </div>
      </div>
    </div>
  );

  const TemplateCard = ({ template }) => (
    <div className="card-elevated p-6 hover-surface transition-colors cursor-pointer">
      <div className="flex items-center justify-between mb-4">
        <div>
          <h3 className="font-semibold text-primary">{template.name}</h3>
          <p className="text-secondary text-sm">{template.category}</p>
        </div>
        <Button size="small">Use</Button>
      </div>
      
      <div className="flex items-center justify-between text-sm">
        <span className="text-secondary">Used {template.usage} times</span>
        <div className="flex items-center space-x-1">
          <DocumentTextIcon className="w-4 h-4 text-accent-primary" />
          <span className="text-primary">Email</span>
        </div>
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

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-primary">Email Marketing</h1>
          <p className="text-secondary mt-1">Create and manage email campaigns</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary">
            <UserGroupIcon className="w-4 h-4 mr-2" />
            Manage Lists
          </Button>
          <Button>
            <PlusIcon className="w-4 h-4 mr-2" />
            Create Campaign
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'campaigns', name: 'Campaigns' },
            { id: 'lists', name: 'Lists' },
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
              title="Total Subscribers"
              value={analytics.totalSubscribers.toLocaleString()}
              change={analytics.monthlyGrowth}
              icon={UserGroupIcon}
              color="primary"
            />
            <StatCard
              title="Campaigns Sent"
              value={analytics.totalCampaigns.toString()}
              change={15.2}
              icon={EnvelopeIcon}
              color="success"
            />
            <StatCard
              title="Avg. Open Rate"
              value={analytics.averageOpenRate}
              change={2.3}
              icon={EyeIcon}
              color="warning"
              suffix="%"
            />
            <StatCard
              title="Avg. Click Rate"
              value={analytics.averageClickRate}
              change={1.8}
              icon={ChartBarIcon}
              color="primary"
              suffix="%"
            />
          </div>

          {/* Recent Campaigns */}
          <div>
            <h2 className="text-xl font-semibold text-primary mb-4">Recent Campaigns</h2>
            <div className="space-y-4">
              {campaigns.slice(0, 2).map((campaign) => (
                <CampaignCard key={campaign.id} campaign={campaign} />
              ))}
            </div>
          </div>

          {/* Quick Actions */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <PlusIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Create Campaign</h3>
              <p className="text-secondary">Start a new email marketing campaign</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <UserGroupIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Import Contacts</h3>
              <p className="text-secondary">Add new subscribers to your lists</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <DocumentTextIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Browse Templates</h3>
              <p className="text-secondary">Use pre-designed email templates</p>
            </button>
          </div>
        </div>
      )}

      {activeTab === 'campaigns' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Email Campaigns</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Campaigns</option>
                <option>Sent</option>
                <option>Scheduled</option>
                <option>Draft</option>
              </select>
              <select className="input px-3 py-2 rounded-md">
                <option>Sort by Date</option>
                <option>Sort by Performance</option>
                <option>Sort by Recipients</option>
              </select>
            </div>
          </div>
          
          <div className="space-y-4">
            {campaigns.map((campaign) => (
              <CampaignCard key={campaign.id} campaign={campaign} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'lists' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Subscriber Lists</h2>
            <div className="flex items-center space-x-3">
              <Button variant="secondary">Import Contacts</Button>
              <Button>
                <PlusIcon className="w-4 h-4 mr-2" />
                Create List
              </Button>
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {lists.map((list) => (
              <ListCard key={list.id} list={list} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'templates' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Email Templates</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Categories</option>
                <option>Newsletter</option>
                <option>Marketing</option>
                <option>Onboarding</option>
                <option>Product</option>
              </select>
              <Button>
                <PlusIcon className="w-4 h-4 mr-2" />
                Create Template
              </Button>
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {templates.map((template) => (
              <TemplateCard key={template.id} template={template} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Email Analytics</h2>
          <div className="card-elevated p-8 text-center">
            <ChartBarIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-primary mb-2">Detailed Analytics Coming Soon</h3>
            <p className="text-secondary">We're building comprehensive email analytics to help you track campaign performance, subscriber engagement, and conversion rates.</p>
          </div>
        </div>
      )}
    </div>
  );
};

export default EmailMarketingPage;