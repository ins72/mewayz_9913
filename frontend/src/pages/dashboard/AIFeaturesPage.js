import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  SparklesIcon, 
  PlusIcon, 
  PlayIcon,
  StopIcon,
  CogIcon,
  DocumentTextIcon,
  PhotoIcon,
  ChatBubbleLeftIcon,
  ChartBarIcon,
  LightBulbIcon,
  MagicWandIcon,
  RocketLaunchIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  ClockIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const AIFeaturesPage = () => {
  const [aiServices, setAiServices] = useState([]);
  const [workflows, setWorkflows] = useState([]);
  const [contentGenerated, setContentGenerated] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');
  const [chatInput, setChatInput] = useState('');
  const [chatHistory, setChatHistory] = useState([]);

  useEffect(() => {
    loadAIData();
  }, []);

  const loadAIData = async () => {
    try {
      // Mock data for now - replace with actual API calls
      setAiServices([
        { id: 'content-generation', name: 'Content Generation', status: 'active', usage: 89, limit: 100 },
        { id: 'seo-optimization', name: 'SEO Optimization', status: 'active', usage: 67, limit: 100 },
        { id: 'competitor-analysis', name: 'Competitor Analysis', status: 'active', usage: 34, limit: 50 },
        { id: 'sentiment-analysis', name: 'Sentiment Analysis', status: 'active', usage: 12, limit: 25 },
        { id: 'pricing-optimization', name: 'Pricing Optimization', status: 'inactive', usage: 0, limit: 10 },
        { id: 'lead-scoring', name: 'Lead Scoring', status: 'active', usage: 156, limit: 200 }
      ]);

      setWorkflows([
        {
          id: 1,
          name: 'Blog Content Automation',
          description: 'Automatically generate, optimize, and schedule blog posts',
          status: 'running',
          lastRun: '2025-07-19 14:30',
          frequency: 'Daily',
          completedTasks: 23
        },
        {
          id: 2,
          name: 'Social Media Content',
          description: 'Generate social media posts based on trending topics',
          status: 'paused',
          lastRun: '2025-07-18 09:15',
          frequency: 'Every 4 hours',
          completedTasks: 156
        },
        {
          id: 3,
          name: 'Lead Qualification',
          description: 'Automatically score and categorize new leads',
          status: 'running',
          lastRun: '2025-07-19 15:45',
          frequency: 'Real-time',
          completedTasks: 89
        }
      ]);

      setContentGenerated([
        {
          id: 1,
          type: 'Blog Post',
          title: '10 Digital Marketing Trends for 2025',
          platform: 'Blog',
          generated: '2025-07-19 14:30',
          status: 'published',
          engagement: 245
        },
        {
          id: 2,
          type: 'Social Media',
          title: 'Summer marketing campaign ideas...',
          platform: 'Instagram',
          generated: '2025-07-19 13:15',
          status: 'scheduled',
          engagement: 0
        },
        {
          id: 3,
          type: 'Email Subject',
          title: 'Your exclusive summer discount awaits!',
          platform: 'Email Marketing',
          generated: '2025-07-19 12:00',
          status: 'draft',
          engagement: 0
        }
      ]);

      setAnalytics({
        totalGenerations: 1247,
        timesSaved: 186,
        contentPublished: 89,
        engagementBoost: 34.5,
        costSavings: 2340,
        accuracyRate: 92.4
      });

      setChatHistory([
        { type: 'user', message: 'Generate a blog post about AI in marketing', time: '14:30' },
        { type: 'ai', message: 'I\'ll help you create a comprehensive blog post about AI in marketing. Here\'s what I can generate for you:\n\n**Title**: "How AI is Revolutionizing Marketing in 2025"\n\n**Outline**:\n1. Introduction to AI in Marketing\n2. Current AI Marketing Tools\n3. Benefits and ROI\n4. Future Predictions\n\nWould you like me to proceed with the full content?', time: '14:31' }
      ]);
    } catch (error) {
      console.error('Failed to load AI data:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleChatSubmit = (e) => {
    e.preventDefault();
    if (!chatInput.trim()) return;

    // Add user message
    const newMessage = { type: 'user', message: chatInput, time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) };
    setChatHistory(prev => [...prev, newMessage]);

    // Simulate AI response
    setTimeout(() => {
      const aiResponse = { 
        type: 'ai', 
        message: `I can help you with "${chatInput}". Let me analyze this request and provide you with the best solution...`, 
        time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
      };
      setChatHistory(prev => [...prev, aiResponse]);
    }, 1000);

    setChatInput('');
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
              {change > 0 ? '+' : ''}{change}% improvement
            </p>
          )}
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const ServiceCard = ({ service }) => (
    <div className="card-elevated p-6">
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center space-x-3">
          <div className="w-10 h-10 bg-gradient-primary rounded-lg flex items-center justify-center">
            <SparklesIcon className="w-5 h-5 text-white" />
          </div>
          <div>
            <h3 className="font-semibold text-primary">{service.name}</h3>
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${
              service.status === 'active'
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
            }`}>
              {service.status}
            </span>
          </div>
        </div>
        <Button variant="secondary" size="small">
          <CogIcon className="w-4 h-4 mr-1" />
          Settings
        </Button>
      </div>

      <div className="space-y-3">
        <div className="flex justify-between text-sm">
          <span className="text-secondary">Usage this month</span>
          <span className="font-medium text-primary">{service.usage} / {service.limit}</span>
        </div>
        <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div 
            className="bg-accent-primary h-2 rounded-full transition-all duration-300"
            style={{ width: `${(service.usage / service.limit) * 100}%` }}
          ></div>
        </div>
        <div className="flex justify-between">
          <span className="text-xs text-secondary">
            {service.limit - service.usage} remaining
          </span>
          <span className="text-xs text-secondary">
            {Math.round((service.usage / service.limit) * 100)}% used
          </span>
        </div>
      </div>
    </div>
  );

  const WorkflowCard = ({ workflow }) => (
    <div className="card-elevated p-6">
      <div className="flex items-start justify-between mb-4">
        <div className="flex-1">
          <div className="flex items-center space-x-2 mb-2">
            <h3 className="font-semibold text-primary">{workflow.name}</h3>
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${
              workflow.status === 'running'
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            }`}>
              {workflow.status}
            </span>
          </div>
          <p className="text-secondary text-sm mb-3">{workflow.description}</p>
          
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div>
              <p className="text-secondary">Frequency</p>
              <p className="font-medium text-primary">{workflow.frequency}</p>
            </div>
            <div>
              <p className="text-secondary">Completed Tasks</p>
              <p className="font-medium text-primary">{workflow.completedTasks}</p>
            </div>
          </div>
        </div>
        <div className="flex items-center space-x-2">
          <button className="p-2 text-secondary hover:text-primary">
            <EyeIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-primary">
            <PencilIcon className="w-4 h-4" />
          </button>
          <button className={`p-2 ${
            workflow.status === 'running' 
              ? 'text-accent-danger hover:text-red-700' 
              : 'text-accent-success hover:text-green-700'
          }`}>
            {workflow.status === 'running' ? <StopIcon className="w-4 h-4" /> : <PlayIcon className="w-4 h-4" />}
          </button>
        </div>
      </div>
      
      <div className="pt-4 border-t border-default">
        <p className="text-xs text-secondary">Last run: {workflow.lastRun}</p>
      </div>
    </div>
  );

  const ContentCard = ({ content }) => (
    <div className="card p-4">
      <div className="flex items-start justify-between">
        <div className="flex-1">
          <div className="flex items-center space-x-2 mb-2">
            <span className={`px-2 py-1 rounded text-xs font-medium ${
              content.type === 'Blog Post' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
              content.type === 'Social Media' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' :
              'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
            }`}>
              {content.type}
            </span>
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${
              content.status === 'published'
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : content.status === 'scheduled'
                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            }`}>
              {content.status}
            </span>
          </div>
          <h4 className="font-medium text-primary mb-1">{content.title}</h4>
          <p className="text-sm text-secondary mb-2">{content.platform}</p>
          <div className="flex items-center justify-between text-xs text-secondary">
            <span>Generated: {content.generated}</span>
            {content.engagement > 0 && (
              <span>Engagement: {content.engagement}</span>
            )}
          </div>
        </div>
        <div className="flex items-center space-x-2">
          <button className="p-2 text-secondary hover:text-primary">
            <EyeIcon className="w-4 h-4" />
          </button>
          <button className="p-2 text-secondary hover:text-primary">
            <PencilIcon className="w-4 h-4" />
          </button>
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
          <h1 className="text-3xl font-bold text-primary">AI Features</h1>
          <p className="text-secondary mt-1">Harness the power of AI to automate and optimize your business</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary">
            <LightBulbIcon className="w-4 h-4 mr-2" />
            AI Suggestions
          </Button>
          <Button>
            <PlusIcon className="w-4 h-4 mr-2" />
            Create Workflow
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'chat', name: 'AI Chat' },
            { id: 'workflows', name: 'Workflows' },
            { id: 'services', name: 'AI Services' },
            { id: 'content', name: 'Generated Content' }
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
              title="AI Generations"
              value={analytics.totalGenerations.toLocaleString()}
              change={45.2}
              icon={SparklesIcon}
              color="primary"
            />
            <StatCard
              title="Hours Saved"
              value={analytics.timesSaved.toString()}
              change={67.8}
              icon={ClockIcon}
              color="success"
            />
            <StatCard
              title="Content Published"
              value={analytics.contentPublished.toString()}
              change={23.4}
              icon={DocumentTextIcon}
              color="warning"
            />
            <StatCard
              title="Accuracy Rate"
              value={analytics.accuracyRate.toString()}
              icon={ChartBarIcon}
              color="primary"
              suffix="%"
            />
          </div>

          {/* Quick Actions */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <ChatBubbleLeftIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">AI Chat Assistant</h3>
              <p className="text-secondary">Get instant help with content creation and optimization</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <DocumentTextIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Generate Content</h3>
              <p className="text-secondary">Create blog posts, social media, and marketing content</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <RocketLaunchIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Automate Workflows</h3>
              <p className="text-secondary">Set up AI-powered automation for repetitive tasks</p>
            </button>
          </div>

          {/* Recent Activity */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <h2 className="text-xl font-semibold text-primary mb-4">Active Workflows</h2>
              <div className="space-y-4">
                {workflows.filter(w => w.status === 'running').map((workflow) => (
                  <div key={workflow.id} className="card p-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <h4 className="font-medium text-primary">{workflow.name}</h4>
                        <p className="text-sm text-secondary">{workflow.frequency}</p>
                      </div>
                      <div className="flex items-center space-x-2">
                        <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span className="text-sm text-accent-success">Running</span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div>
              <h2 className="text-xl font-semibold text-primary mb-4">Recent AI Content</h2>
              <div className="space-y-4">
                {contentGenerated.slice(0, 3).map((content) => (
                  <div key={content.id} className="card p-4">
                    <div className="flex items-center justify-between">
                      <div>
                        <h4 className="font-medium text-primary">{content.title}</h4>
                        <p className="text-sm text-secondary">{content.type} • {content.platform}</p>
                      </div>
                      <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                        content.status === 'published'
                          ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                          : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                      }`}>
                        {content.status}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'chat' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">AI Chat Assistant</h2>
          
          <div className="card-elevated">
            <div className="h-96 p-6 overflow-y-auto border-b border-default">
              <div className="space-y-4">
                {chatHistory.map((msg, index) => (
                  <div key={index} className={`flex ${msg.type === 'user' ? 'justify-end' : 'justify-start'}`}>
                    <div className={`max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                      msg.type === 'user' 
                        ? 'bg-accent-primary text-white' 
                        : 'bg-gray-100 dark:bg-gray-800 text-primary'
                    }`}>
                      <p className="text-sm">{msg.message}</p>
                      <p className={`text-xs mt-1 ${
                        msg.type === 'user' ? 'text-blue-100' : 'text-secondary'
                      }`}>
                        {msg.time}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
            
            <form onSubmit={handleChatSubmit} className="p-6">
              <div className="flex space-x-3">
                <input
                  type="text"
                  value={chatInput}
                  onChange={(e) => setChatInput(e.target.value)}
                  placeholder="Ask AI to help with content creation, optimization, or any business task..."
                  className="flex-1 input px-4 py-3 rounded-lg"
                />
                <Button type="submit" disabled={!chatInput.trim()}>
                  <ChatBubbleLeftIcon className="w-4 h-4 mr-2" />
                  Send
                </Button>
              </div>
            </form>
          </div>
        </div>
      )}

      {activeTab === 'workflows' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">AI Workflows</h2>
            <Button>
              <PlusIcon className="w-4 h-4 mr-2" />
              Create Workflow
            </Button>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {workflows.map((workflow) => (
              <WorkflowCard key={workflow.id} workflow={workflow} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'services' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">AI Services</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {aiServices.map((service) => (
              <ServiceCard key={service.id} service={service} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'content' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Generated Content</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Content Types</option>
                <option>Blog Posts</option>
                <option>Social Media</option>
                <option>Email Content</option>
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
            {contentGenerated.map((content) => (
              <ContentCard key={content.id} content={content} />
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

export default AIFeaturesPage;