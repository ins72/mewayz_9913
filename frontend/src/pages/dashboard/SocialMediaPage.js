import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  ChartBarIcon, 
  PlusIcon, 
  PencilIcon, 
  TrashIcon,
  CalendarIcon,
  HashtagIcon,
  PhotoIcon,
  FilmIcon,
  EyeIcon,
  HeartIcon,
  ChatBubbleLeftIcon,
  ShareIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const SocialMediaPage = () => {
  const [accounts, setAccounts] = useState([]);
  const [posts, setPosts] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadSocialMediaData();
  }, []);

  const loadSocialMediaData = async () => {
    try {
      // Mock data for now - replace with actual API calls
      setAccounts([
        { id: 1, platform: 'Instagram', username: '@mybusiness', followers: 15420, connected: true },
        { id: 2, platform: 'Facebook', username: 'My Business Page', followers: 8950, connected: true },
        { id: 3, platform: 'Twitter', username: '@mybusiness', followers: 3200, connected: false },
        { id: 4, platform: 'LinkedIn', username: 'My Business', followers: 1890, connected: true },
      ]);

      setPosts([
        {
          id: 1,
          content: 'Just launched our new product line! 🚀 #newproduct #launch',
          platform: 'Instagram',
          scheduled: '2025-07-20 10:00',
          status: 'scheduled',
          engagement: { likes: 0, comments: 0, shares: 0 }
        },
        {
          id: 2,
          content: 'Behind the scenes at our office today! Working hard to bring you the best experience.',
          platform: 'Facebook',
          published: '2025-07-19 14:30',
          status: 'published',
          engagement: { likes: 45, comments: 12, shares: 8 }
        }
      ]);

      setAnalytics({
        totalFollowers: 29460,
        totalEngagement: 1250,
        reachThisWeek: 15670,
        topPerformingPost: 'Product launch announcement',
        engagementRate: 4.2
      });
    } catch (error) {
      console.error('Failed to load social media data:', error);
    } finally {
      setLoading(false);
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
              {change > 0 ? '+' : ''}{change}% vs last week
            </p>
          )}
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const AccountCard = ({ account }) => (
    <div className="card-elevated p-6">
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center space-x-3">
          <div className="w-10 h-10 bg-gradient-primary rounded-lg flex items-center justify-center">
            <span className="text-white font-bold">{account.platform[0]}</span>
          </div>
          <div>
            <h3 className="font-semibold text-primary">{account.platform}</h3>
            <p className="text-secondary">{account.username}</p>
          </div>
        </div>
        <div className={`w-3 h-3 rounded-full ${account.connected ? 'bg-accent-success' : 'bg-accent-danger'}`}></div>
      </div>
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm text-secondary">Followers</p>
          <p className="text-xl font-bold text-primary">{account.followers.toLocaleString()}</p>
        </div>
        <Button variant={account.connected ? 'secondary' : 'primary'} size="small">
          {account.connected ? 'Manage' : 'Connect'}
        </Button>
      </div>
    </div>
  );

  const PostCard = ({ post }) => (
    <div className="card-elevated p-6">
      <div className="flex items-start justify-between mb-4">
        <div className="flex items-center space-x-3">
          <div className="w-8 h-8 bg-gradient-primary rounded-lg flex items-center justify-center">
            <span className="text-white text-sm font-bold">{post.platform[0]}</span>
          </div>
          <div>
            <h4 className="font-medium text-primary">{post.platform}</h4>
            <p className="text-sm text-secondary">
              {post.status === 'scheduled' ? `Scheduled for ${post.scheduled}` : `Published ${post.published}`}
            </p>
          </div>
        </div>
        <div className="flex items-center space-x-2">
          <button className="p-2 text-secondary hover:text-primary">
            <EyeIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-primary">
            <PencilIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-accent-danger">
            <TrashIcon className="w-4 h-4" />
          </button>
        </div>
      </div>
      
      <p className="text-primary mb-4">{post.content}</p>
      
      <div className="flex items-center justify-between pt-4 border-t border-default">
        <div className="flex items-center space-x-4 text-sm text-secondary">
          <div className="flex items-center space-x-1">
            <HeartIcon className="w-4 h-4" />
            <span>{post.engagement.likes}</span>
          </div>
          <div className="flex items-center space-x-1">
            <ChatBubbleLeftIcon className="w-4 h-4" />
            <span>{post.engagement.comments}</span>
          </div>
          <div className="flex items-center space-x-1">
            <ShareIcon className="w-4 h-4" />
            <span>{post.engagement.shares}</span>
          </div>
        </div>
        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
          post.status === 'scheduled' 
            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
        }`}>
          {post.status}
        </span>
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
          <h1 className="text-3xl font-bold text-primary">Social Media Management</h1>
          <p className="text-secondary mt-1">Manage all your social media accounts from one place</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary">
            <CalendarIcon className="w-4 h-4 mr-2" />
            Schedule Post
          </Button>
          <Button>
            <PlusIcon className="w-4 h-4 mr-2" />
            Create Post
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'posts', name: 'Posts' },
            { id: 'analytics', name: 'Analytics' },
            { id: 'accounts', name: 'Accounts' }
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
              title="Total Followers"
              value={analytics.totalFollowers.toLocaleString()}
              change={5.2}
              icon={ChartBarIcon}
              color="primary"
            />
            <StatCard
              title="Total Engagement"
              value={analytics.totalEngagement.toLocaleString()}
              change={12.8}
              icon={HeartIcon}
              color="success"
            />
            <StatCard
              title="Reach This Week"
              value={analytics.reachThisWeek.toLocaleString()}
              change={-2.1}
              icon={EyeIcon}
              color="warning"
            />
            <StatCard
              title="Engagement Rate"
              value={`${analytics.engagementRate}%`}
              change={1.3}
              icon={ShareIcon}
              color="primary"
            />
          </div>

          {/* Quick Actions */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <PhotoIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Create Image Post</h3>
              <p className="text-secondary">Share photos with your audience</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <FilmIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Create Video Post</h3>
              <p className="text-secondary">Share videos and stories</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <HashtagIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Hashtag Research</h3>
              <p className="text-secondary">Find trending hashtags</p>
            </button>
          </div>
        </div>
      )}

      {activeTab === 'posts' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Recent Posts</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Platforms</option>
                <option>Instagram</option>
                <option>Facebook</option>
                <option>Twitter</option>
                <option>LinkedIn</option>
              </select>
              <select className="input px-3 py-2 rounded-md">
                <option>All Status</option>
                <option>Published</option>
                <option>Scheduled</option>
                <option>Draft</option>
              </select>
            </div>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {posts.map((post) => (
              <PostCard key={post.id} post={post} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'accounts' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Connected Accounts</h2>
            <Button>
              <PlusIcon className="w-4 h-4 mr-2" />
              Connect Account
            </Button>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {accounts.map((account) => (
              <AccountCard key={account.id} account={account} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Analytics Dashboard</h2>
          <div className="card-elevated p-8 text-center">
            <ChartBarIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-primary mb-2">Detailed Analytics Coming Soon</h3>
            <p className="text-secondary">We're building comprehensive analytics to help you understand your social media performance.</p>
          </div>
        </div>
      )}
    </div>
  );
};

export default SocialMediaPage;