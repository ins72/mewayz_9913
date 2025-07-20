import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  SparklesIcon,
  ChatBubbleLeftRightIcon,
  PhotoIcon,
  DocumentTextIcon,
  HashtagIcon,
  LightBulbIcon,
  AcademicCapIcon,
  EnvelopeIcon,
  ChartBarIcon,
  PlayIcon,
  StopIcon,
  ClipboardDocumentIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon
} from '@heroicons/react/24/outline';
import {
  SparklesIcon as SparklesIconSolid,
  StarIcon as StarIconSolid,
  BoltIcon as BoltIconSolid
} from '@heroicons/react/24/solid';

const UltraAdvancedAIFeaturesPage = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  const [activeFeature, setActiveFeature] = useState('content_generation');
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState(null);
  
  // Form states for different AI features
  const [contentForm, setContentForm] = useState({
    prompt: '',
    content_type: 'social_post',
    tone: 'professional',
    max_tokens: 500
  });
  
  const [analysisForm, setAnalysisForm] = useState({
    content: '',
    analysis_type: 'sentiment'
  });
  
  const [hashtagForm, setHashtagForm] = useState({
    content: '',
    platform: 'instagram',
    count: 10
  });
  
  const [improvementForm, setImprovementForm] = useState({
    content: '',
    improvement_type: 'engagement'
  });
  
  const [courseForm, setCourseForm] = useState({
    topic: '',
    lesson_title: '',
    difficulty: 'beginner',
    duration: 15
  });
  
  const [emailForm, setEmailForm] = useState({
    purpose: '',
    audience: '',
    sequence_length: 5
  });
  
  const [ideasForm, setIdeasForm] = useState({
    industry: '',
    content_type: 'social_media',
    count: 10
  });
  
  // AI usage analytics
  const [analytics, setAnalytics] = useState(null);
  const [tokenBalance, setTokenBalance] = useState(null);
  
  useEffect(() => {
    fetchAIAnalytics();
    fetchTokenBalance();
  }, []);
  
  const fetchTokenBalance = async () => {
    try {
      // Get workspace first
      const workspacesResponse = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/workspaces`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      
      if (workspacesResponse.ok) {
        const workspacesData = await workspacesResponse.json();
        if (workspacesData.success && workspacesData.data.workspaces.length > 0) {
          const workspaceId = workspacesData.data.workspaces[0].id;
          
          // Get token balance
          const tokenResponse = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/tokens/workspace/${workspaceId}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          if (tokenResponse.ok) {
            const tokenData = await tokenResponse.json();
            if (tokenData.success) {
              setTokenBalance(tokenData.data);
            }
          }
        }
      }
    } catch (err) {
      console.error('Failed to fetch token balance:', err);
    }
  };
  
  const fetchAIAnalytics = async () => {
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/ai/usage-analytics`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      
      if (response.ok) {
        const data = await response.json();
        setAnalytics(data.data);
      }
    } catch (err) {
      console.error('Failed to fetch AI analytics:', err);
    }
  };
  
  const handleAIRequest = async (endpoint, requestData) => {
    setLoading(true);
    setResult(null);
    
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/ai/${endpoint}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify(requestData)
      });
      
      if (response.ok) {
        const data = await response.json();
        if (data.success) {
          setResult(data.data);
          const tokensConsumed = data.tokens_consumed;
          success(`AI request completed successfully! ${tokensConsumed ? `(${tokensConsumed} tokens consumed)` : ''}`);
          fetchAIAnalytics(); // Refresh analytics
        } else {
          // Handle insufficient tokens error
          if (data.error === 'insufficient_tokens') {
            error(`Insufficient tokens: ${data.message}. Need ${data.tokens_needed} tokens.`);
          } else {
            error(data.message || 'AI request failed');
          }
        }
      } else {
        const errorData = await response.json();
        if (errorData.error === 'insufficient_tokens') {
          error(`Insufficient tokens: ${errorData.message}. Need ${errorData.tokens_needed} tokens.`);
        } else {
          error(errorData.detail || 'AI request failed');
        }
      }
    } catch (err) {
      error('Failed to process AI request');
      console.error('AI request error:', err);
    } finally {
      setLoading(false);
    }
  };
  
  const aiFeatures = [
    {
      id: 'content_generation',
      name: 'Content Generation',
      icon: DocumentTextIcon,
      description: 'Generate high-quality content for any purpose',
      color: 'blue'
    },
    {
      id: 'content_analysis',
      name: 'Content Analysis',
      icon: ChartBarIcon,
      description: 'Analyze content sentiment, SEO, and engagement',
      color: 'purple'
    },
    {
      id: 'hashtag_generation',
      name: 'Smart Hashtags',
      icon: HashtagIcon,
      description: 'Generate trending hashtags for social media',
      color: 'pink'
    },
    {
      id: 'content_improvement',
      name: 'Content Enhancement',
      icon: SparklesIcon,
      description: 'Improve existing content for better performance',
      color: 'yellow'
    },
    {
      id: 'course_content',
      name: 'Course Creation',
      icon: AcademicCapIcon,
      description: 'Generate educational content and lesson plans',
      color: 'green'
    },
    {
      id: 'email_sequences',
      name: 'Email Marketing',
      icon: EnvelopeIcon,
      description: 'Create automated email sequences',
      color: 'red'
    },
    {
      id: 'content_ideas',
      name: 'Idea Generator',
      icon: LightBulbIcon,
      description: 'Get creative content ideas for any industry',
      color: 'orange'
    }
  ];
  
  const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    success('Copied to clipboard!');
  };
  
  const renderFeatureContent = () => {
    switch (activeFeature) {
      case 'content_generation':
        return (
          <div className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Prompt</label>
                <textarea
                  value={contentForm.prompt}
                  onChange={(e) => setContentForm({...contentForm, prompt: e.target.value})}
                  placeholder="Describe what content you want to generate..."
                  className="input h-24"
                />
              </div>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Content Type</label>
                  <select
                    value={contentForm.content_type}
                    onChange={(e) => setContentForm({...contentForm, content_type: e.target.value})}
                    className="input"
                  >
                    <option value="social_post">Social Media Post</option>
                    <option value="blog_article">Blog Article</option>
                    <option value="email_campaign">Email Campaign</option>
                    <option value="product_description">Product Description</option>
                    <option value="website_copy">Website Copy</option>
                    <option value="seo_content">SEO Content</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Tone</label>
                  <select
                    value={contentForm.tone}
                    onChange={(e) => setContentForm({...contentForm, tone: e.target.value})}
                    className="input"
                  >
                    <option value="professional">Professional</option>
                    <option value="casual">Casual</option>
                    <option value="friendly">Friendly</option>
                    <option value="authoritative">Authoritative</option>
                    <option value="conversational">Conversational</option>
                    <option value="humorous">Humorous</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Max Tokens</label>
                  <input
                    type="number"
                    value={contentForm.max_tokens}
                    onChange={(e) => setContentForm({...contentForm, max_tokens: parseInt(e.target.value)})}
                    className="input"
                    min="100"
                    max="2000"
                  />
                </div>
              </div>
            </div>
            <button
              onClick={() => handleAIRequest('generate-content', contentForm)}
              disabled={loading || !contentForm.prompt}
              className="btn btn-primary"
            >
              {loading ? <ArrowPathIcon className="h-4 w-4 animate-spin mr-2" /> : <SparklesIcon className="h-4 w-4 mr-2" />}
              Generate Content
            </button>
          </div>
        );
        
      case 'content_analysis':
        return (
          <div className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Content to Analyze</label>
                <textarea
                  value={analysisForm.content}
                  onChange={(e) => setAnalysisForm({...analysisForm, content: e.target.value})}
                  placeholder="Paste your content here for analysis..."
                  className="input h-32"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Analysis Type</label>
                <select
                  value={analysisForm.analysis_type}
                  onChange={(e) => setAnalysisForm({...analysisForm, analysis_type: e.target.value})}
                  className="input"
                >
                  <option value="sentiment">Sentiment Analysis</option>
                  <option value="seo">SEO Analysis</option>
                  <option value="engagement">Engagement Potential</option>
                  <option value="readability">Readability Score</option>
                  <option value="brand_voice">Brand Voice Analysis</option>
                </select>
              </div>
            </div>
            <button
              onClick={() => handleAIRequest('analyze-content', analysisForm)}
              disabled={loading || !analysisForm.content}
              className="btn btn-primary"
            >
              {loading ? <ArrowPathIcon className="h-4 w-4 animate-spin mr-2" /> : <ChartBarIcon className="h-4 w-4 mr-2" />}
              Analyze Content
            </button>
          </div>
        );
        
      case 'hashtag_generation':
        return (
          <div className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Content/Topic</label>
                <textarea
                  value={hashtagForm.content}
                  onChange={(e) => setHashtagForm({...hashtagForm, content: e.target.value})}
                  placeholder="Describe your content or topic..."
                  className="input h-24"
                />
              </div>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Platform</label>
                  <select
                    value={hashtagForm.platform}
                    onChange={(e) => setHashtagForm({...hashtagForm, platform: e.target.value})}
                    className="input"
                  >
                    <option value="instagram">Instagram</option>
                    <option value="twitter">Twitter</option>
                    <option value="linkedin">LinkedIn</option>
                    <option value="tiktok">TikTok</option>
                    <option value="facebook">Facebook</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Number of Hashtags</label>
                  <input
                    type="number"
                    value={hashtagForm.count}
                    onChange={(e) => setHashtagForm({...hashtagForm, count: parseInt(e.target.value)})}
                    className="input"
                    min="1"
                    max="30"
                  />
                </div>
              </div>
            </div>
            <button
              onClick={() => handleAIRequest('generate-hashtags', hashtagForm)}
              disabled={loading || !hashtagForm.content}
              className="btn btn-primary"
            >
              {loading ? <ArrowPathIcon className="h-4 w-4 animate-spin mr-2" /> : <HashtagIcon className="h-4 w-4 mr-2" />}
              Generate Hashtags
            </button>
          </div>
        );
        
      case 'content_improvement':
        return (
          <div className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Content to Improve</label>
                <textarea
                  value={improvementForm.content}
                  onChange={(e) => setImprovementForm({...improvementForm, content: e.target.value})}
                  placeholder="Paste your existing content here..."
                  className="input h-32"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Improvement Type</label>
                <select
                  value={improvementForm.improvement_type}
                  onChange={(e) => setImprovementForm({...improvementForm, improvement_type: e.target.value})}
                  className="input"
                >
                  <option value="engagement">Engagement</option>
                  <option value="clarity">Clarity</option>
                  <option value="seo">SEO Optimization</option>
                  <option value="professional">Professional Tone</option>
                  <option value="casual">Casual Tone</option>
                  <option value="persuasive">Persuasiveness</option>
                </select>
              </div>
            </div>
            <button
              onClick={() => handleAIRequest('improve-content', improvementForm)}
              disabled={loading || !improvementForm.content}
              className="btn btn-primary"
            >
              {loading ? <ArrowPathIcon className="h-4 w-4 animate-spin mr-2" /> : <SparklesIcon className="h-4 w-4 mr-2" />}
              Improve Content
            </button>
          </div>
        );
        
      case 'course_content':
        return (
          <div className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Topic</label>
                  <input
                    type="text"
                    value={courseForm.topic}
                    onChange={(e) => setCourseForm({...courseForm, topic: e.target.value})}
                    placeholder="e.g., Digital Marketing"
                    className="input"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Lesson Title</label>
                  <input
                    type="text"
                    value={courseForm.lesson_title}
                    onChange={(e) => setCourseForm({...courseForm, lesson_title: e.target.value})}
                    placeholder="e.g., Introduction to Social Media Analytics"
                    className="input"
                  />
                </div>
              </div>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Difficulty Level</label>
                  <select
                    value={courseForm.difficulty}
                    onChange={(e) => setCourseForm({...courseForm, difficulty: e.target.value})}
                    className="input"
                  >
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Duration (minutes)</label>
                  <input
                    type="number"
                    value={courseForm.duration}
                    onChange={(e) => setCourseForm({...courseForm, duration: parseInt(e.target.value)})}
                    className="input"
                    min="5"
                    max="120"
                  />
                </div>
              </div>
            </div>
            <button
              onClick={() => handleAIRequest('generate-course-content', courseForm)}
              disabled={loading || !courseForm.topic || !courseForm.lesson_title}
              className="btn btn-primary"
            >
              {loading ? <ArrowPathIcon className="h-4 w-4 animate-spin mr-2" /> : <AcademicCapIcon className="h-4 w-4 mr-2" />}
              Generate Course Content
            </button>
          </div>
        );
        
      case 'email_sequences':
        return (
          <div className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Purpose/Campaign Type</label>
                  <input
                    type="text"
                    value={emailForm.purpose}
                    onChange={(e) => setEmailForm({...emailForm, purpose: e.target.value})}
                    placeholder="e.g., Welcome Series, Product Launch, Course Promotion"
                    className="input"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Target Audience</label>
                  <input
                    type="text"
                    value={emailForm.audience}
                    onChange={(e) => setEmailForm({...emailForm, audience: e.target.value})}
                    placeholder="e.g., New subscribers, E-commerce customers, Course students"
                    className="input"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Sequence Length</label>
                <input
                  type="number"
                  value={emailForm.sequence_length}
                  onChange={(e) => setEmailForm({...emailForm, sequence_length: parseInt(e.target.value)})}
                  className="input"
                  min="2"
                  max="10"
                />
              </div>
            </div>
            <button
              onClick={() => handleAIRequest('generate-email-sequence', emailForm)}
              disabled={loading || !emailForm.purpose || !emailForm.audience}
              className="btn btn-primary"
            >
              {loading ? <ArrowPathIcon className="h-4 w-4 animate-spin mr-2" /> : <EnvelopeIcon className="h-4 w-4 mr-2" />}
              Generate Email Sequence
            </button>
          </div>
        );
        
      case 'content_ideas':
        return (
          <div className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Industry</label>
                  <input
                    type="text"
                    value={ideasForm.industry}
                    onChange={(e) => setIdeasForm({...ideasForm, industry: e.target.value})}
                    placeholder="e.g., Technology, Healthcare, Fashion, Food"
                    className="input"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-secondary mb-2">Content Type</label>
                  <select
                    value={ideasForm.content_type}
                    onChange={(e) => setIdeasForm({...ideasForm, content_type: e.target.value})}
                    className="input"
                  >
                    <option value="social_media">Social Media Posts</option>
                    <option value="blog_articles">Blog Articles</option>
                    <option value="video_content">Video Content</option>
                    <option value="email_campaigns">Email Campaigns</option>
                    <option value="infographics">Infographics</option>
                    <option value="podcasts">Podcast Episodes</option>
                  </select>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Number of Ideas</label>
                <input
                  type="number"
                  value={ideasForm.count}
                  onChange={(e) => setIdeasForm({...ideasForm, count: parseInt(e.target.value)})}
                  className="input"
                  min="3"
                  max="20"
                />
              </div>
            </div>
            <button
              onClick={() => handleAIRequest('get-content-ideas', ideasForm)}
              disabled={loading || !ideasForm.industry}
              className="btn btn-primary"
            >
              {loading ? <ArrowPathIcon className="h-4 w-4 animate-spin mr-2" /> : <LightBulbIcon className="h-4 w-4 mr-2" />}
              Generate Ideas
            </button>
          </div>
        );
        
      default:
        return null;
    }
  };
  
  const renderResult = () => {
    if (!result) return null;
    
    return (
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="mt-8 p-6 bg-surface border border-default rounded-xl"
      >
        <div className="flex items-center justify-between mb-4">
          <h3 className="text-lg font-semibold text-primary">Generated Result</h3>
          <button
            onClick={() => copyToClipboard(JSON.stringify(result, null, 2))}
            className="btn btn-secondary btn-sm"
          >
            <ClipboardDocumentIcon className="h-4 w-4 mr-2" />
            Copy
          </button>
        </div>
        
        {result.success ? (
          <div className="space-y-4">
            <div className="flex items-center text-green-600">
              <CheckCircleIcon className="h-5 w-5 mr-2" />
              <span>Successfully generated!</span>
            </div>
            
            {result.content && (
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Generated Content:</label>
                <div className="p-4 bg-surface-elevated rounded-lg">
                  <pre className="whitespace-pre-wrap text-primary text-sm">{result.content}</pre>
                </div>
              </div>
            )}
            
            {result.hashtags && (
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Generated Hashtags:</label>
                <div className="flex flex-wrap gap-2">
                  {result.hashtags.map((tag, index) => (
                    <span key={index} className="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                      {tag}
                    </span>
                  ))}
                </div>
              </div>
            )}
            
            {result.ideas && (
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Content Ideas:</label>
                <ul className="list-disc list-inside space-y-1">
                  {result.ideas.map((idea, index) => (
                    <li key={index} className="text-primary text-sm">{idea}</li>
                  ))}
                </ul>
              </div>
            )}
            
            {result.analysis && (
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Analysis Result:</label>
                <div className="p-4 bg-surface-elevated rounded-lg">
                  <pre className="whitespace-pre-wrap text-primary text-sm">{result.analysis}</pre>
                </div>
              </div>
            )}
            
            {result.improved_content && (
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Improved Content:</label>
                <div className="p-4 bg-surface-elevated rounded-lg">
                  <pre className="whitespace-pre-wrap text-primary text-sm">{result.improved_content}</pre>
                </div>
              </div>
            )}
            
            {result.emails && (
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">Email Sequence:</label>
                <div className="p-4 bg-surface-elevated rounded-lg">
                  <pre className="whitespace-pre-wrap text-primary text-sm">{result.emails}</pre>
                </div>
              </div>
            )}
          </div>
        ) : (
          <div className="flex items-center text-red-600">
            <ExclamationTriangleIcon className="h-5 w-5 mr-2" />
            <span>Error: {result.error}</span>
          </div>
        )}
      </motion.div>
    );
  };
  
  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="bg-gradient-to-r from-purple-600 via-blue-600 to-cyan-600 rounded-xl shadow-default p-8 text-white">
        <div className="flex items-center justify-between">
          <div>
            <div className="flex items-center mb-4">
              <SparklesIconSolid className="h-10 w-10 mr-4" />
              <h1 className="text-4xl font-bold">Ultra-Advanced AI Features</h1>
            </div>
            <p className="text-white/80 text-lg">Harness the power of OpenAI GPT-4 to supercharge your content creation and business growth</p>
          </div>
          <div className="flex space-x-4">
            {tokenBalance && (
              <div className="bg-white/10 rounded-xl p-6 backdrop-blur-sm">
                <div className="text-center">
                  <div className="flex items-center justify-center mb-2">
                    <BoltIconSolid className="h-6 w-6 mr-2 text-yellow-400" />
                    <div className="text-3xl font-bold">{(tokenBalance.balance || 0)}</div>
                  </div>
                  <div className="text-sm text-white/70">Available Tokens</div>
                </div>
                <div className="text-center mt-4">
                  <div className="text-lg font-semibold mb-1">
                    {tokenBalance.allowance_remaining || 0} / {tokenBalance.monthly_allowance || 0}
                  </div>
                  <div className="text-xs text-white/70">Monthly Allowance</div>
                </div>
              </div>
            )}
            {analytics && (
              <div className="bg-white/10 rounded-xl p-6 backdrop-blur-sm">
                <div className="text-center">
                  <div className="text-3xl font-bold mb-1">{analytics.total_requests}</div>
                  <div className="text-sm text-white/70">AI Requests</div>
                </div>
                <div className="text-center mt-4">
                  <div className="text-2xl font-bold mb-1">{analytics.success_rate.toFixed(1)}%</div>
                  <div className="text-sm text-white/70">Success Rate</div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
      
      {/* AI Features Grid */}
      <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        {aiFeatures.map((feature) => (
          <motion.button
            key={feature.id}
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            whileHover={{ scale: 1.02 }}
            onClick={() => setActiveFeature(feature.id)}
            className={`p-6 rounded-xl border-2 transition-all text-left ${
              activeFeature === feature.id
                ? `border-${feature.color}-500 bg-${feature.color}-50 dark:bg-${feature.color}-900/20`
                : 'border-default bg-surface hover:bg-surface-hover'
            }`}
          >
            <div className={`inline-flex p-3 rounded-xl mb-4 ${
              activeFeature === feature.id
                ? `bg-${feature.color}-100 dark:bg-${feature.color}-800/30`
                : 'bg-surface-elevated'
            }`}>
              <feature.icon className={`h-6 w-6 ${
                activeFeature === feature.id
                  ? `text-${feature.color}-600 dark:text-${feature.color}-400`
                  : 'text-secondary'
              }`} />
            </div>
            <h3 className={`font-semibold mb-2 ${
              activeFeature === feature.id ? 'text-primary' : 'text-primary'
            }`}>
              {feature.name}
            </h3>
            <p className="text-sm text-secondary">{feature.description}</p>
          </motion.button>
        ))}
      </div>
      
      {/* Active Feature Content */}
      <div className="bg-surface-elevated rounded-xl shadow-default p-8">
        <div className="flex items-center mb-6">
          {aiFeatures.find(f => f.id === activeFeature)?.icon && (
            <div className="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl mr-4">
              {React.createElement(aiFeatures.find(f => f.id === activeFeature).icon, {
                className: "h-6 w-6 text-blue-600 dark:text-blue-400"
              })}
            </div>
          )}
          <div>
            <h2 className="text-2xl font-bold text-primary">
              {aiFeatures.find(f => f.id === activeFeature)?.name}
            </h2>
            <p className="text-secondary">
              {aiFeatures.find(f => f.id === activeFeature)?.description}
            </p>
          </div>
        </div>
        
        {renderFeatureContent()}
        {renderResult()}
      </div>
      
      {/* AI Usage Analytics */}
      {analytics && (
        <div className="bg-surface-elevated rounded-xl shadow-default p-6">
          <h3 className="text-xl font-semibold text-primary mb-6">AI Usage Analytics</h3>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div className="text-center">
              <div className="text-2xl font-bold text-primary mb-1">{analytics.total_requests}</div>
              <div className="text-sm text-secondary">Total Requests</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-green-600 mb-1">{analytics.successful_requests}</div>
              <div className="text-sm text-secondary">Successful</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-blue-600 mb-1">{analytics.total_tokens_used.toLocaleString()}</div>
              <div className="text-sm text-secondary">Tokens Used</div>
            </div>
            <div className="text-center">
              <div className="text-2xl font-bold text-purple-600 mb-1">{Object.keys(analytics.feature_usage || {}).length}</div>
              <div className="text-sm text-secondary">Features Used</div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default UltraAdvancedAIFeaturesPage;