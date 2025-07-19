import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  PlusIcon,
  XMarkIcon,
  PhotoIcon,
  VideoCameraIcon,
  CalendarIcon,
  ChartBarIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  LinkIcon,
  PaperAirplaneIcon,
  ClockIcon,
  EyeIcon,
  HeartIcon,
  ChatBubbleLeftIcon,
  ArrowPathRoundedSquareIcon
} from '@heroicons/react/24/outline';

const EnhancedSocialMediaManager = () => {
  const [activeTab, setActiveTab] = useState('composer');
  const [connectedAccounts, setConnectedAccounts] = useState([]);
  const [availableIntegrations, setAvailableIntegrations] = useState({});
  const [loading, setLoading] = useState(true);
  const [posts, setPosts] = useState([]);
  const [activities, setActivities] = useState([]);
  const [emailStats, setEmailStats] = useState({});

  // Composer state
  const [postContent, setPostContent] = useState({
    text: '',
    platforms: [],
    media: [],
    schedule_date: null,
    schedule_time: null
  });
  
  const [emailCampaign, setEmailCampaign] = useState({
    subject: '',
    body: '',
    recipients: '',
    sender_name: '',
    sender_email: ''
  });

  useEffect(() => {
    fetchAvailableIntegrations();
    fetchActivities();
    fetchEmailStats();
  }, []);

  const fetchAvailableIntegrations = async () => {
    try {
      const token = localStorage.getItem('token');
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/integrations/available`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });

      if (response.ok) {
        const result = await response.json();
        if (result.success) {
          setAvailableIntegrations(result.integrations);
        }
      }
    } catch (error) {
      console.error('Failed to fetch integrations:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchActivities = async () => {
    try {
      const token = localStorage.getItem('token');
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/integrations/social/activities`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });

      if (response.ok) {
        const result = await response.json();
        if (result.success) {
          setActivities(result.activities);
        }
      }
    } catch (error) {
      console.error('Failed to fetch activities:', error);
    }
  };

  const fetchEmailStats = async () => {
    try {
      const token = localStorage.getItem('token');
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/integrations/email/stats`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });

      if (response.ok) {
        const result = await response.json();
        if (result.success) {
          setEmailStats(result);
        }
      }
    } catch (error) {
      console.error('Failed to fetch email stats:', error);
    }
  };

  const connectPlatform = async (platform) => {
    try {
      const token = localStorage.getItem('token');
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/integrations/social/auth`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          platform: platform,
          callback_url: `${window.location.origin}/dashboard/social-media/callback/${platform}`
        })
      });

      if (response.ok) {
        const result = await response.json();
        if (result.success && result.auth_url) {
          // Open authentication popup
          const popup = window.open(
            result.auth_url,
            `${platform}_auth`,
            'width=600,height=600,scrollbars=yes,resizable=yes'
          );

          // Listen for popup close or message
          const checkClosed = setInterval(() => {
            if (popup.closed) {
              clearInterval(checkClosed);
              // Refresh integrations
              fetchAvailableIntegrations();
            }
          }, 1000);
        }
      }
    } catch (error) {
      console.error(`Failed to connect ${platform}:`, error);
    }
  };

  const postToSocialMedia = async () => {
    if (!postContent.text.trim() || postContent.platforms.length === 0) {
      alert('Please enter content and select at least one platform');
      return;
    }

    try {
      const token = localStorage.getItem('token');
      
      // For demo purposes, we'll simulate posting to X
      if (postContent.platforms.includes('x')) {
        const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/integrations/social/post`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            platform: 'x',
            content: {
              text: postContent.text,
              media_ids: postContent.media.map(m => m.id)
            },
            access_token: 'demo_access_token',
            access_token_secret: 'demo_access_token_secret'
          })
        });

        if (response.ok) {
          const result = await response.json();
          if (result.success) {
            alert('Post published successfully!');
            setPostContent({ text: '', platforms: [], media: [], schedule_date: null, schedule_time: null });
            fetchActivities(); // Refresh activities
          }
        }
      }
    } catch (error) {
      console.error('Failed to post:', error);
      alert('Failed to post content. Please try again.');
    }
  };

  const sendEmailCampaign = async () => {
    if (!emailCampaign.subject || !emailCampaign.body || !emailCampaign.recipients) {
      alert('Please fill in all required fields');
      return;
    }

    try {
      const token = localStorage.getItem('token');
      const recipients = emailCampaign.recipients.split(',').map(email => email.trim()).filter(email => email);
      
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/integrations/email/send`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          recipients: recipients,
          subject: emailCampaign.subject,
          body: emailCampaign.body,
          sender_name: emailCampaign.sender_name,
          sender_email: emailCampaign.sender_email
        })
      });

      if (response.ok) {
        const result = await response.json();
        if (result.success) {
          alert(`Email campaign sent successfully to ${recipients.length} recipients!`);
          setEmailCampaign({ subject: '', body: '', recipients: '', sender_name: '', sender_email: '' });
          fetchEmailStats(); // Refresh stats
        } else {
          alert(`Failed to send campaign: ${result.error || 'Unknown error'}`);
        }
      } else {
        alert('Failed to send email campaign');
      }
    } catch (error) {
      console.error('Failed to send email campaign:', error);
      alert('Failed to send email campaign. Please try again.');
    }
  };

  const togglePlatform = (platform) => {
    setPostContent(prev => ({
      ...prev,
      platforms: prev.platforms.includes(platform)
        ? prev.platforms.filter(p => p !== platform)
        : [...prev.platforms, platform]
    }));
  };

  const getPlatformIcon = (platform) => {
    switch (platform) {
      case 'x':
        return (
          <div className="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
            <span className="text-white font-bold text-sm">X</span>
          </div>
        );
      case 'tiktok':
        return (
          <div className="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
            <VideoCameraIcon className="w-5 h-5 text-white" />
          </div>
        );
      case 'instagram':
        return (
          <div className="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
            <PhotoIcon className="w-5 h-5 text-white" />
          </div>
        );
      default:
        return (
          <div className="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center">
            <LinkIcon className="w-5 h-5 text-white" />
          </div>
        );
    }
  };

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-app flex items-center justify-center">
        <div className="animate-pulse text-primary">Loading integrations...</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-app">
      <div className="max-w-7xl mx-auto px-4 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold text-primary">Social Media & Email Manager</h1>
            <p className="text-secondary mt-2">Manage your social media posts and email campaigns</p>
          </div>
          
          <div className="flex items-center space-x-4">
            <button 
              onClick={() => window.location.reload()}
              className="p-2 text-secondary hover:text-primary rounded-lg transition-colors"
            >
              <ArrowPathIcon className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Integration Status */}
        <div className="bg-card border border-default rounded-xl p-6 mb-8">
          <h3 className="text-lg font-semibold text-primary mb-4">Available Integrations</h3>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className={`p-4 rounded-lg border ${availableIntegrations.x_twitter ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-orange-500 bg-orange-50 dark:bg-orange-900/20'}`}>
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  {getPlatformIcon('x')}
                  <div>
                    <h4 className="font-medium text-primary">X (Twitter)</h4>
                    <p className="text-xs text-secondary">Social media posting</p>
                  </div>
                </div>
                {availableIntegrations.x_twitter ? (
                  <CheckCircleIcon className="w-5 h-5 text-green-600" />
                ) : (
                  <button 
                    onClick={() => connectPlatform('x')}
                    className="text-xs bg-orange-600 text-white px-2 py-1 rounded"
                  >
                    Connect
                  </button>
                )}
              </div>
            </div>

            <div className={`p-4 rounded-lg border ${availableIntegrations.tiktok ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-orange-500 bg-orange-50 dark:bg-orange-900/20'}`}>
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  {getPlatformIcon('tiktok')}
                  <div>
                    <h4 className="font-medium text-primary">TikTok</h4>
                    <p className="text-xs text-secondary">Video content</p>
                  </div>
                </div>
                {availableIntegrations.tiktok ? (
                  <CheckCircleIcon className="w-5 h-5 text-green-600" />
                ) : (
                  <button 
                    onClick={() => connectPlatform('tiktok')}
                    className="text-xs bg-orange-600 text-white px-2 py-1 rounded"
                  >
                    Connect
                  </button>
                )}
              </div>
            </div>

            <div className={`p-4 rounded-lg border ${availableIntegrations.elasticmail ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-orange-500 bg-orange-50 dark:bg-orange-900/20'}`}>
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <span className="text-white font-bold text-sm">@</span>
                  </div>
                  <div>
                    <h4 className="font-medium text-primary">ElasticMail</h4>
                    <p className="text-xs text-secondary">Email campaigns</p>
                  </div>
                </div>
                {availableIntegrations.elasticmail ? (
                  <CheckCircleIcon className="w-5 h-5 text-green-600" />
                ) : (
                  <ExclamationCircleIcon className="w-5 h-5 text-orange-600" />
                )}
              </div>
            </div>
          </div>
        </div>

        {/* Tabs */}
        <div className="flex space-x-1 bg-card border border-default rounded-lg p-1 mb-8">
          {['composer', 'activities', 'email', 'analytics'].map(tab => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`flex-1 px-4 py-2 rounded text-sm font-medium transition-colors ${
                activeTab === tab 
                  ? 'bg-accent-primary text-white' 
                  : 'text-secondary hover:text-primary'
              }`}
            >
              {tab.charAt(0).toUpperCase() + tab.slice(1)}
            </button>
          ))}
        </div>

        {/* Tab Content */}
        <div className="space-y-6">
          {activeTab === 'composer' && (
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
              {/* Social Media Composer */}
              <div className="bg-card border border-default rounded-xl p-6">
                <h3 className="text-xl font-semibold text-primary mb-4">Social Media Post</h3>
                
                <div className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Content</label>
                    <textarea
                      value={postContent.text}
                      onChange={(e) => setPostContent({ ...postContent, text: e.target.value })}
                      rows={4}
                      className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none resize-none"
                      placeholder="What's happening?"
                    />
                    <div className="text-xs text-secondary mt-1">
                      {postContent.text.length}/280 characters
                    </div>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Platforms</label>
                    <div className="flex space-x-2">
                      {['x', 'tiktok', 'instagram'].map(platform => (
                        <button
                          key={platform}
                          onClick={() => togglePlatform(platform)}
                          className={`flex items-center space-x-2 px-3 py-2 rounded-lg border transition-colors ${
                            postContent.platforms.includes(platform)
                              ? 'border-accent-primary bg-accent-primary/10 text-accent-primary'
                              : 'border-default hover:border-accent-primary text-secondary'
                          }`}
                        >
                          {getPlatformIcon(platform)}
                          <span className="text-sm font-medium capitalize">{platform}</span>
                        </button>
                      ))}
                    </div>
                  </div>

                  <div className="flex space-x-3">
                    <button className="flex-1 bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center space-x-2">
                      <PaperAirplaneIcon className="w-4 h-4" />
                      <span>Post Now</span>
                    </button>
                    <button className="px-4 py-2 border border-default rounded-lg hover:bg-hover transition-colors flex items-center space-x-2">
                      <ClockIcon className="w-4 h-4" />
                      <span>Schedule</span>
                    </button>
                  </div>
                </div>
              </div>

              {/* Preview */}
              <div className="bg-card border border-default rounded-xl p-6">
                <h3 className="text-xl font-semibold text-primary mb-4">Preview</h3>
                
                {postContent.text ? (
                  <div className="space-y-4">
                    {postContent.platforms.map(platform => (
                      <div key={platform} className="border border-default rounded-lg p-4">
                        <div className="flex items-center space-x-3 mb-3">
                          {getPlatformIcon(platform)}
                          <div>
                            <h4 className="text-sm font-medium text-primary capitalize">{platform} Post</h4>
                            <p className="text-xs text-secondary">Preview</p>
                          </div>
                        </div>
                        <p className="text-sm text-primary">{postContent.text}</p>
                        
                        <div className="flex items-center space-x-4 mt-3 text-xs text-secondary">
                          <div className="flex items-center space-x-1">
                            <HeartIcon className="w-4 h-4" />
                            <span>0</span>
                          </div>
                          <div className="flex items-center space-x-1">
                            <ChatBubbleLeftIcon className="w-4 h-4" />
                            <span>0</span>
                          </div>
                          <div className="flex items-center space-x-1">
                            <ArrowPathRoundedSquareIcon className="w-4 h-4" />
                            <span>0</span>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                ) : (
                  <div className="text-center py-8 text-secondary">
                    <PlusIcon className="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p>Start typing to see preview</p>
                  </div>
                )}
              </div>
            </div>
          )}

          {activeTab === 'activities' && (
            <div className="bg-card border border-default rounded-xl p-6">
              <h3 className="text-xl font-semibold text-primary mb-4">Recent Activities</h3>
              
              {activities.length > 0 ? (
                <div className="space-y-3">
                  {activities.map(activity => (
                    <div key={activity._id} className="flex items-start space-x-3 p-3 hover:bg-hover rounded-lg transition-colors">
                      {getPlatformIcon(activity.platform)}
                      <div className="flex-1 min-w-0">
                        <p className="text-sm text-primary">
                          Posted to <span className="font-medium capitalize">{activity.platform}</span>
                        </p>
                        <p className="text-xs text-secondary mt-1 truncate">
                          "{activity.content.text}"
                        </p>
                        <p className="text-xs text-secondary mt-1">
                          {formatDate(activity.created_at)}
                        </p>
                      </div>
                      {activity.post_url && (
                        <a 
                          href={activity.post_url}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-accent-primary hover:opacity-80 text-sm"
                        >
                          <EyeIcon className="w-4 h-4" />
                        </a>
                      )}
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-8 text-secondary">
                  <ChartBarIcon className="w-12 h-12 mx-auto mb-2 opacity-50" />
                  <p>No activities yet</p>
                </div>
              )}
            </div>
          )}

          {activeTab === 'email' && (
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
              {/* Email Composer */}
              <div className="bg-card border border-default rounded-xl p-6">
                <h3 className="text-xl font-semibold text-primary mb-4">Email Campaign</h3>
                
                {availableIntegrations.elasticmail ? (
                  <div className="space-y-4">
                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Subject</label>
                      <input
                        type="text"
                        value={emailCampaign.subject}
                        onChange={(e) => setEmailCampaign({ ...emailCampaign, subject: e.target.value })}
                        className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                        placeholder="Email subject..."
                      />
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Recipients (comma separated)</label>
                      <textarea
                        value={emailCampaign.recipients}
                        onChange={(e) => setEmailCampaign({ ...emailCampaign, recipients: e.target.value })}
                        rows={3}
                        className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none resize-none"
                        placeholder="email1@example.com, email2@example.com"
                      />
                    </div>

                    <div className="grid grid-cols-2 gap-3">
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Sender Name</label>
                        <input
                          type="text"
                          value={emailCampaign.sender_name}
                          onChange={(e) => setEmailCampaign({ ...emailCampaign, sender_name: e.target.value })}
                          className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                          placeholder="Your Name"
                        />
                      </div>
                      <div>
                        <label className="block text-sm font-medium text-secondary mb-2">Sender Email</label>
                        <input
                          type="email"
                          value={emailCampaign.sender_email}
                          onChange={(e) => setEmailCampaign({ ...emailCampaign, sender_email: e.target.value })}
                          className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                          placeholder="your@email.com"
                        />
                      </div>
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-secondary mb-2">Email Content (HTML)</label>
                      <textarea
                        value={emailCampaign.body}
                        onChange={(e) => setEmailCampaign({ ...emailCampaign, body: e.target.value })}
                        rows={6}
                        className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none resize-none"
                        placeholder="<p>Your email content here...</p>"
                      />
                    </div>

                    <button 
                      onClick={sendEmailCampaign}
                      className="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center space-x-2"
                    >
                      <PaperAirplaneIcon className="w-4 h-4" />
                      <span>Send Campaign</span>
                    </button>
                  </div>
                ) : (
                  <div className="text-center py-8 text-secondary">
                    <ExclamationCircleIcon className="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p>ElasticMail integration not available</p>
                  </div>
                )}
              </div>

              {/* Email Stats */}
              <div className="bg-card border border-default rounded-xl p-6">
                <h3 className="text-xl font-semibold text-primary mb-4">Email Statistics</h3>
                
                {emailStats.user_stats ? (
                  <div className="space-y-4">
                    <div className="grid grid-cols-1 gap-4">
                      <div className="bg-surface border border-default rounded-lg p-4 text-center">
                        <div className="text-2xl font-bold text-primary">{emailStats.user_stats.total_campaigns}</div>
                        <div className="text-sm text-secondary">Total Campaigns</div>
                      </div>
                      <div className="bg-surface border border-default rounded-lg p-4 text-center">
                        <div className="text-2xl font-bold text-primary">{emailStats.user_stats.total_emails_sent}</div>
                        <div className="text-sm text-secondary">Emails Sent</div>
                      </div>
                      <div className="bg-surface border border-default rounded-lg p-4 text-center">
                        <div className="text-2xl font-bold text-primary">{emailStats.user_stats.total_contacts}</div>
                        <div className="text-sm text-secondary">Total Contacts</div>
                      </div>
                    </div>
                    
                    {emailStats.account_stats && emailStats.account_stats.credits && (
                      <div className="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div className="text-sm text-blue-800 dark:text-blue-200">
                          <strong>Account Credits:</strong> {emailStats.account_stats.credits}
                        </div>
                        {emailStats.account_stats.reputation && (
                          <div className="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Reputation:</strong> {emailStats.account_stats.reputation}%
                          </div>
                        )}
                      </div>
                    )}
                  </div>
                ) : (
                  <div className="text-center py-8 text-secondary">
                    <ChartBarIcon className="w-12 h-12 mx-auto mb-2 opacity-50" />
                    <p>No email statistics available</p>
                  </div>
                )}
              </div>
            </div>
          )}

          {activeTab === 'analytics' && (
            <div className="bg-card border border-default rounded-xl p-6">
              <h3 className="text-xl font-semibold text-primary mb-4">Analytics & Insights</h3>
              
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-surface border border-default rounded-lg p-4 text-center">
                  <div className="text-3xl font-bold text-primary">{activities.length}</div>
                  <div className="text-sm text-secondary">Total Posts</div>
                </div>
                <div className="bg-surface border border-default rounded-lg p-4 text-center">
                  <div className="text-3xl font-bold text-primary">
                    {emailStats.user_stats ? emailStats.user_stats.total_campaigns : 0}
                  </div>
                  <div className="text-sm text-secondary">Email Campaigns</div>
                </div>
                <div className="bg-surface border border-default rounded-lg p-4 text-center">
                  <div className="text-3xl font-bold text-primary">
                    {Object.values(availableIntegrations).filter(Boolean).length}
                  </div>
                  <div className="text-sm text-secondary">Connected Platforms</div>
                </div>
              </div>

              <div className="mt-6 text-center text-secondary">
                <ChartBarIcon className="w-16 h-16 mx-auto mb-4 opacity-50" />
                <p>Advanced analytics coming soon...</p>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default EnhancedSocialMediaManager;