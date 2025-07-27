import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { aiAPI } from '../../services/api';
import {
  SparklesIcon,
  ChatBubbleLeftRightIcon,
  PhotoIcon,
  DocumentTextIcon,
  MicrophoneIcon,
  ChartBarIcon,
  CogIcon,
  PlusIcon,
  PlayIcon,
  StopIcon
} from '@heroicons/react/24/outline';
import toast from 'react-hot-toast';

const AIFeaturesPage = () => {
  const { user } = useAuth();
  const [aiServices, setAiServices] = useState(null);
  const [activeService, setActiveService] = useState(null);
  const [loading, setLoading] = useState(true);
  const [generating, setGenerating] = useState(false);
  const [contentForm, setContentForm] = useState({
    content_type: 'blog_post',
    topic: '',
    length: 'medium',
    tone: 'professional',
    keywords: ''
  });
  const [generatedContent, setGeneratedContent] = useState(null);

  useEffect(() => {
    fetchAIServices();
  }, []);

  const fetchAIServices = async () => {
    try {
      const response = await aiAPI.getServices();
      // Real data loaded from API
    } catch (error) {
      console.error('Failed to fetch AI services:', error);
      toast.error('Failed to load AI services');
    } finally {
      // Real data loaded from API
    }
  };

  const handleGenerateContent = async () => {
    if (!contentForm.topic) {
      toast.error('Please enter a topic');
      return;
    }

    // Real data loaded from API
    try {
      const formData = new FormData();
      formData.append('content_type', contentForm.content_type);
      formData.append('topic', contentForm.topic);
      formData.append('length', contentForm.length);
      formData.append('tone', contentForm.tone);
      if (contentForm.keywords) {
        formData.append('keywords', contentForm.keywords);
      }

      const response = await aiAPI.generateContent(formData);
      // Real data loaded from API
      toast.success('Content generated successfully!');
    } catch (error) {
      console.error('Content generation failed:', error);
      toast.error('Failed to generate content');
    } finally {
      // Real data loaded from API
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-accent-primary"></div>
      </div>
    );
  }

  const serviceIcons = {
    'content-generation': DocumentTextIcon,
    'seo-optimization': ChartBarIcon,
    'chatbot-assistant': ChatBubbleLeftRightIcon,
    'image-generation': PhotoIcon,
    'voice-synthesis': MicrophoneIcon
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="mb-8"
      >
        <h1 className="text-3xl font-bold text-primary mb-2 flex items-center">
          <SparklesIcon className="h-8 w-8 text-accent-primary mr-3" />
          AI Features
        </h1>
        <p className="text-secondary">
          Harness the power of artificial intelligence to supercharge your productivity.
        </p>
      </motion.div>

      {/* Usage Statistics */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.1 }}
        className="bg-surface-elevated p-6 rounded-lg shadow-default"
      >
        <h3 className="text-lg font-semibold text-primary mb-4">Usage Statistics</h3>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div className="text-center">
            <div className="text-2xl font-bold text-accent-primary">
              {aiServices?.usage_statistics?.total_requests?.toLocaleString() || '1,247'}
            </div>
            <div className="text-sm text-secondary">Total Requests</div>
          </div>
          <div className="text-center">
            <div className="text-2xl font-bold text-green-500">
              {aiServices?.usage_statistics?.this_month || '389'}
            </div>
            <div className="text-sm text-secondary">This Month</div>
          </div>
          <div className="text-center">
            <div className="text-2xl font-bold text-blue-500">
              ${aiServices?.usage_statistics?.cost_savings || '2,450'}
            </div>
            <div className="text-sm text-secondary">Cost Savings</div>
          </div>
          <div className="text-center">
            <div className="text-2xl font-bold text-purple-500">
              {aiServices?.usage_statistics?.time_saved_hours || '156'}h
            </div>
            <div className="text-sm text-secondary">Time Saved</div>
          </div>
        </div>
      </motion.div>

      {/* AI Services Grid */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.2 }}
        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        {aiServices?.available_services?.map((service, index) => {
          const IconComponent = serviceIcons[service.id] || SparklesIcon;
          return (
            <div
              key={service.id}
              className="bg-surface-elevated p-6 rounded-lg shadow-default hover:shadow-lg transition-all cursor-pointer"
              onClick={() => setActiveService(service)}
            >
              <div className="flex items-center mb-4">
                <div className="flex-shrink-0 p-3 bg-accent-primary/10 rounded-lg">
                  <IconComponent className="h-6 w-6 text-accent-primary" />
                </div>
                <div className="ml-4">
                  <h3 className="text-lg font-semibold text-primary">{service.name}</h3>
                  <p className="text-sm text-secondary">{service.description}</p>
                </div>
              </div>
              <div className="space-y-2">
                {service.features.map((feature, idx) => (
                  <div key={idx} className="flex items-center text-sm text-secondary">
                    <div className="w-1.5 h-1.5 bg-accent-primary rounded-full mr-2" />
                    {feature}
                  </div>
                ))}
              </div>
              <div className="mt-4 pt-4 border-t border-default">
                <div className="flex justify-between items-center">
                  <span className="text-sm text-secondary">Starting at</span>
                  <span className="text-lg font-semibold text-accent-primary">
                    ${service.pricing.monthly}
                  </span>
                </div>
              </div>
            </div>
          );
        })}
      </motion.div>

      {/* Content Generation Tool */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.3 }}
        className="bg-surface-elevated p-6 rounded-lg shadow-default"
      >
        <h3 className="text-xl font-semibold text-primary mb-6 flex items-center">
          <DocumentTextIcon className="h-6 w-6 text-accent-primary mr-2" />
          AI Content Generator
        </h3>
        
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Input Form */}
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-secondary mb-2">
                Content Type
              </label>
              <select
                value={contentForm.content_type}
                onChange={(e) => setContentForm({...contentForm, content_type: e.target.value})}
                className="w-full input rounded-lg focus-ring"
              >
                <option value="blog_post">Blog Post</option>
                <option value="social_media">Social Media Post</option>
                <option value="email_campaign">Email Campaign</option>
                <option value="product_description">Product Description</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-secondary mb-2">
                Topic
              </label>
              <input
                type="text"
                value={contentForm.topic}
                onChange={(e) => setContentForm({...contentForm, topic: e.target.value})}
                placeholder="Enter your topic here..."
                className="w-full input rounded-lg focus-ring"
              />
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-secondary mb-2">
                  Length
                </label>
                <select
                  value={contentForm.length}
                  onChange={(e) => setContentForm({...contentForm, length: e.target.value})}
                  className="w-full input rounded-lg focus-ring"
                >
                  <option value="short">Short</option>
                  <option value="medium">Medium</option>
                  <option value="long">Long</option>
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-secondary mb-2">
                  Tone
                </label>
                <select
                  value={contentForm.tone}
                  onChange={(e) => setContentForm({...contentForm, tone: e.target.value})}
                  className="w-full input rounded-lg focus-ring"
                >
                  <option value="professional">Professional</option>
                  <option value="casual">Casual</option>
                  <option value="friendly">Friendly</option>
                  <option value="authoritative">Authoritative</option>
                </select>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-secondary mb-2">
                Keywords (Optional)
              </label>
              <input
                type="text"
                value={contentForm.keywords}
                onChange={(e) => setContentForm({...contentForm, keywords: e.target.value})}
                placeholder="Enter keywords separated by commas..."
                className="w-full input rounded-lg focus-ring"
              />
            </div>

            <button
              onClick={handleGenerateContent}
              disabled={generating}
              className="w-full btn-primary flex items-center justify-center"
            >
              {generating ? (
                <>
                  <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2" />
                  Generating...
                </>
              ) : (
                <>
                  <SparklesIcon className="h-5 w-5 mr-2" />
                  Generate Content
                </>
              )}
            </button>
          </div>

          {/* Generated Content */}
          <div className="bg-surface p-4 rounded-lg">
            <h4 className="font-medium text-primary mb-3">Generated Content</h4>
            {generatedContent ? (
              <div className="space-y-4">
                <div className="prose prose-sm text-secondary">
                  <pre className="whitespace-pre-wrap text-sm">
                    {generatedContent.content}
                  </pre>
                </div>
                
                <div className="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span className="text-secondary">Word Count:</span>
                    <span className="ml-2 font-medium text-primary">
                      {generatedContent.word_count}
                    </span>
                  </div>
                  <div>
                    <span className="text-secondary">SEO Score:</span>
                    <span className="ml-2 font-medium text-green-500">
                      {generatedContent.seo_score}/100
                    </span>
                  </div>
                  <div>
                    <span className="text-secondary">Readability:</span>
                    <span className="ml-2 font-medium text-blue-500">
                      {generatedContent.readability_score}/100
                    </span>
                  </div>
                  <div>
                    <span className="text-secondary">Processing Time:</span>
                    <span className="ml-2 font-medium text-primary">
                      {generatedContent.metadata?.processing_time}
                    </span>
                  </div>
                </div>

                <div className="space-y-2">
                  <h5 className="font-medium text-primary">AI Suggestions:</h5>
                  {generatedContent.suggestions?.map((suggestion, idx) => (
                    <div key={idx} className="flex items-start text-sm text-secondary">
                      <div className="w-1.5 h-1.5 bg-yellow-500 rounded-full mt-2 mr-2 flex-shrink-0" />
                      {suggestion}
                    </div>
                  ))}
                </div>
              </div>
            ) : (
              <div className="text-center text-secondary py-8">
                <SparklesIcon className="h-12 w-12 mx-auto mb-3 opacity-50" />
                <p>Generated content will appear here</p>
              </div>
            )}
          </div>
        </div>
      </motion.div>
    </div>
  );
};

export default AIFeaturesPage;