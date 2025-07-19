import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
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
  StopIcon,
  ArrowPathIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  RocketLaunchIcon,
  BoltIcon,
  EyeIcon,
  DocumentArrowDownIcon,
  ShareIcon,
  BookmarkIcon,
  ClipboardDocumentIcon,
  LanguageIcon,
  PaintBrushIcon,
  MegaphoneIcon,
  ChatBubbleBottomCenterTextIcon,
  CameraIcon,
  FilmIcon,
  SpeakerWaveIcon
} from '@heroicons/react/24/outline';
import toast from 'react-hot-toast';

const AIFeaturesPageV2 = () => {
  const { user } = useAuth();
  const [activeCategory, setActiveCategory] = useState('content');
  const [aiTools, setAiTools] = useState(null);
  const [loading, setLoading] = useState(true);
  const [processing, setProcessing] = useState({});
  const [results, setResults] = useState({});
  
  // Content Generation Form
  const [contentForm, setContentForm] = useState({
    type: 'blog_post',
    topic: '',
    tone: 'professional',
    length: 'medium',
    language: 'english',
    keywords: '',
    audience: 'general'
  });

  // Image Generation Form
  const [imageForm, setImageForm] = useState({
    prompt: '',
    style: 'photorealistic',
    size: '1024x1024',
    count: 1
  });

  useEffect(() => {
    fetchAITools();
  }, []);

  const fetchAITools = async () => {
    try {
      setLoading(true);
      // Fetch comprehensive AI tools data
      const mockAITools = {
        categories: {
          content: {
            name: 'Content Creation',
            icon: DocumentTextIcon,
            color: 'bg-blue-500',
            tools: [
              {
                id: 'blog-writer',
                name: 'AI Blog Writer',
                description: 'Generate high-quality blog posts with SEO optimization',
                icon: DocumentTextIcon,
                credits: 2,
                features: ['SEO Optimization', 'Multiple Languages', 'Custom Tone', 'Plagiarism Check']
              },
              {
                id: 'social-posts',
                name: 'Social Media Posts',
                description: 'Create engaging posts for all social platforms',
                icon: MegaphoneIcon,
                credits: 1,
                features: ['Platform Optimization', 'Hashtag Suggestions', 'Trending Topics', 'Scheduling']
              },
              {
                id: 'email-campaigns',
                name: 'Email Campaigns',
                description: 'Write compelling email marketing campaigns',
                icon: ChatBubbleBottomCenterTextIcon,
                credits: 2,
                features: ['A/B Testing', 'Personalization', 'Subject Line Optimization', 'CTA Generation']
              },
              {
                id: 'copywriting',
                name: 'Sales Copywriting',
                description: 'Generate high-converting sales copy and ads',
                icon: PaintBrushIcon,
                credits: 3,
                features: ['Conversion Focused', 'Psychology-Based', 'Multiple Variants', 'Industry Specific']
              }
            ]
          },
          visual: {
            name: 'Visual Content',
            icon: PhotoIcon,
            color: 'bg-purple-500',
            tools: [
              {
                id: 'image-generator',
                name: 'AI Image Generator',
                description: 'Create stunning images from text descriptions',
                icon: PhotoIcon,
                credits: 5,
                features: ['Multiple Styles', 'High Resolution', 'Commercial License', 'Brand Consistency']
              },
              {
                id: 'logo-creator',
                name: 'Logo Creator',
                description: 'Design professional logos with AI assistance',
                icon: PaintBrushIcon,
                credits: 10,
                features: ['Vector Format', 'Multiple Variations', 'Brand Guidelines', 'Font Pairing']
              },
              {
                id: 'video-generator',
                name: 'Video Content',
                description: 'Generate video content and animations',
                icon: FilmIcon,
                credits: 15,
                features: ['Motion Graphics', 'Voice Synthesis', 'Music Integration', 'Multiple Formats']
              },
              {
                id: 'thumbnail-maker',
                name: 'Thumbnail Maker',
                description: 'Create eye-catching thumbnails for videos',
                icon: CameraIcon,
                credits: 3,
                features: ['Click-Optimized', 'A/B Testing', 'Text Overlay', 'Brand Templates']
              }
            ]
          },
          voice: {
            name: 'Voice & Audio',
            icon: MicrophoneIcon,
            color: 'bg-green-500',
            tools: [
              {
                id: 'voice-synthesis',
                name: 'Voice Synthesis',
                description: 'Convert text to natural-sounding speech',
                icon: SpeakerWaveIcon,
                credits: 1,
                features: ['Multiple Voices', '30+ Languages', 'SSML Support', 'Emotion Control']
              },
              {
                id: 'voice-cloning',
                name: 'Voice Cloning',
                description: 'Clone your voice for content creation',
                icon: MicrophoneIcon,
                credits: 20,
                features: ['Personal Voice', 'High Quality', 'Fast Processing', 'Secure Storage']
              },
              {
                id: 'podcast-assistant',
                name: 'Podcast Assistant',
                description: 'Generate podcast scripts and show notes',
                icon: ChatBubbleLeftRightIcon,
                credits: 5,
                features: ['Script Writing', 'Show Notes', 'Topic Research', 'Interview Questions']
              }
            ]
          },
          analysis: {
            name: 'Analysis & Insights',
            icon: ChartBarIcon,
            color: 'bg-orange-500',
            tools: [
              {
                id: 'content-analyzer',
                name: 'Content Analyzer',
                description: 'Analyze content performance and optimization',
                icon: ChartBarIcon,
                credits: 2,
                features: ['SEO Analysis', 'Readability Score', 'Engagement Prediction', 'Improvement Suggestions']
              },
              {
                id: 'competitor-analysis',
                name: 'Competitor Analysis',
                description: 'Analyze competitor content strategies',
                icon: EyeIcon,
                credits: 5,
                features: ['Content Gaps', 'Keyword Analysis', 'Performance Metrics', 'Strategy Insights']
              },
              {
                id: 'trend-predictor',
                name: 'Trend Predictor',
                description: 'Predict upcoming trends in your industry',
                icon: RocketLaunchIcon,
                credits: 8,
                features: ['Industry Trends', 'Keyword Trends', 'Content Opportunities', 'Timing Insights']
              }
            ]
          }
        },
        usage_stats: {
          total_credits: 150,
          used_credits: 47,
          remaining_credits: 103,
          this_month_usage: 47,
          popular_tool: 'AI Blog Writer',
          total_content_generated: 234,
          time_saved_hours: 89
        },
        recent_generations: [
          {
            id: 1,
            tool: 'AI Blog Writer',
            title: 'The Future of Digital Marketing',
            created_at: '2 hours ago',
            status: 'completed',
            credits_used: 2
          },
          {
            id: 2,
            tool: 'Image Generator',
            title: 'Product Launch Campaign Images',
            created_at: '1 day ago',
            status: 'completed',
            credits_used: 5
          },
          {
            id: 3,
            tool: 'Social Media Posts',
            title: 'Weekly Social Content Batch',
            created_at: '2 days ago',
            status: 'completed',
            credits_used: 3
          }
        ]
      };
      
      setAiTools(mockAITools);
    } catch (error) {
      console.error('Failed to fetch AI tools:', error);
      toast.error('Failed to load AI tools');
    } finally {
      setLoading(false);
    }
  };

  const handleToolUse = async (toolId, formData = {}) => {
    setProcessing(prev => ({ ...prev, [toolId]: true }));
    
    try {
      // Simulate AI processing
      await new Promise(resolve => setTimeout(resolve, 3000));
      
      const mockResults = {
        'blog-writer': {
          title: formData.topic || 'Generated Blog Post',
          content: `# ${formData.topic || 'Generated Content'}\n\nThis is a professionally generated blog post using advanced AI technology. The content is optimized for SEO and tailored to your specified tone and audience.\n\n## Key Points\n\n1. **Engaging Introduction**: Hook your readers from the start\n2. **Valuable Content**: Provide actionable insights\n3. **Strong Conclusion**: End with a clear call-to-action\n\n## SEO Optimization\n\n- **Readability Score**: 92/100\n- **SEO Score**: 87/100\n- **Word Count**: 1,247 words\n- **Keywords**: Naturally integrated\n\n*Generated with Mewayz AI - Professional Content Creation*`,
          metrics: {
            word_count: 1247,
            readability_score: 92,
            seo_score: 87,
            estimated_read_time: '5 minutes'
          }
        },
        'image-generator': {
          images: [
            { url: 'https://via.placeholder.com/512x512?text=AI+Generated+Image+1', prompt: formData.prompt },
            { url: 'https://via.placeholder.com/512x512?text=AI+Generated+Image+2', prompt: formData.prompt }
          ],
          generation_time: '12 seconds',
          style_applied: formData.style
        }
      };
      
      setResults(prev => ({ 
        ...prev, 
        [toolId]: mockResults[toolId] || { success: true, message: 'Content generated successfully!' }
      }));
      
      toast.success(`${aiTools.categories[activeCategory].tools.find(t => t.id === toolId)?.name} completed!`);
    } catch (error) {
      toast.error('Generation failed. Please try again.');
    } finally {
      setProcessing(prev => ({ ...prev, [toolId]: false }));
    }
  };

  const handleDownloadContent = (content, filename) => {
    const blob = new Blob([content], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    toast.success('Content downloaded successfully!');
  };

  const handleCopyToClipboard = (content) => {
    navigator.clipboard.writeText(content);
    toast.success('Content copied to clipboard!');
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-accent-primary mx-auto mb-4"></div>
          <p className="text-secondary">Loading AI tools...</p>
        </div>
      </div>
    );
  }

  const categories = aiTools?.categories || {};
  const currentCategory = categories[activeCategory];

  return (
    <div className="space-y-6">
      {/* Header with Credits */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="mb-8"
      >
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-primary mb-2 flex items-center">
              <SparklesIcon className="h-8 w-8 text-accent-primary mr-3" />
              AI Content Studio
            </h1>
            <p className="text-secondary">
              Generate high-quality content with advanced AI tools
            </p>
          </div>
          
          <div className="flex items-center space-x-4">
            <div className="bg-surface-elevated p-4 rounded-lg">
              <div className="text-2xl font-bold text-accent-primary">
                {aiTools?.usage_stats?.remaining_credits || 103}
              </div>
              <div className="text-sm text-secondary">Credits Remaining</div>
            </div>
            <button className="btn-primary flex items-center">
              <PlusIcon className="h-5 w-5 mr-2" />
              Buy Credits
            </button>
          </div>
        </div>
      </motion.div>

      {/* Usage Statistics */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.1 }}
        className="grid grid-cols-1 md:grid-cols-4 gap-6"
      >
        <div className="bg-surface-elevated p-6 rounded-lg shadow-default">
          <div className="flex items-center">
            <div className="p-3 bg-blue-500/10 rounded-lg">
              <BoltIcon className="h-6 w-6 text-blue-500" />
            </div>
            <div className="ml-4">
              <div className="text-2xl font-bold text-primary">{aiTools?.usage_stats?.this_month_usage || 47}</div>
              <div className="text-sm text-secondary">This Month</div>
            </div>
          </div>
        </div>
        
        <div className="bg-surface-elevated p-6 rounded-lg shadow-default">
          <div className="flex items-center">
            <div className="p-3 bg-green-500/10 rounded-lg">
              <DocumentTextIcon className="h-6 w-6 text-green-500" />
            </div>
            <div className="ml-4">
              <div className="text-2xl font-bold text-primary">{aiTools?.usage_stats?.total_content_generated || 234}</div>
              <div className="text-sm text-secondary">Content Generated</div>
            </div>
          </div>
        </div>
        
        <div className="bg-surface-elevated p-6 rounded-lg shadow-default">
          <div className="flex items-center">
            <div className="p-3 bg-purple-500/10 rounded-lg">
              <ClockIcon className="h-6 w-6 text-purple-500" />
            </div>
            <div className="ml-4">
              <div className="text-2xl font-bold text-primary">{aiTools?.usage_stats?.time_saved_hours || 89}h</div>
              <div className="text-sm text-secondary">Time Saved</div>
            </div>
          </div>
        </div>
        
        <div className="bg-surface-elevated p-6 rounded-lg shadow-default">
          <div className="flex items-center">
            <div className="p-3 bg-orange-500/10 rounded-lg">
              <RocketLaunchIcon className="h-6 w-6 text-orange-500" />
            </div>
            <div className="ml-4">
              <div className="text-2xl font-bold text-primary">98.5%</div>
              <div className="text-sm text-secondary">Success Rate</div>
            </div>
          </div>
        </div>
      </motion.div>

      {/* Category Navigation */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.2 }}
        className="flex space-x-1 bg-surface p-1 rounded-lg"
      >
        {Object.entries(categories).map(([key, category]) => (
          <button
            key={key}
            onClick={() => setActiveCategory(key)}
            className={`flex items-center space-x-2 px-4 py-3 rounded-md transition-all font-medium ${
              activeCategory === key
                ? 'bg-accent-primary text-white shadow-md'
                : 'text-secondary hover:text-primary hover:bg-surface-hover'
            }`}
          >
            <category.icon className="h-5 w-5" />
            <span>{category.name}</span>
          </button>
        ))}
      </motion.div>

      {/* AI Tools Grid */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.3 }}
        className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"
      >
        {currentCategory?.tools?.map((tool, index) => (
          <div key={tool.id} className="bg-surface-elevated p-6 rounded-lg shadow-default hover:shadow-lg transition-all">
            <div className="flex items-start justify-between mb-4">
              <div className="flex items-center space-x-3">
                <div className={`p-3 ${currentCategory.color}/10 rounded-lg`}>
                  <tool.icon className={`h-6 w-6 text-${currentCategory.color.replace('bg-', '').replace('-500', '-500')}`} />
                </div>
                <div>
                  <h3 className="text-lg font-semibold text-primary">{tool.name}</h3>
                  <p className="text-sm text-secondary">{tool.description}</p>
                </div>
              </div>
              <div className="text-right">
                <div className="text-sm text-secondary">Credits</div>
                <div className="text-lg font-bold text-accent-primary">{tool.credits}</div>
              </div>
            </div>

            <div className="mb-4">
              <div className="flex flex-wrap gap-2">
                {tool.features.map((feature, idx) => (
                  <span key={idx} className="px-2 py-1 bg-surface text-xs text-secondary rounded-full">
                    {feature}
                  </span>
                ))}
              </div>
            </div>

            {/* Tool-specific forms */}
            {tool.id === 'blog-writer' && (
              <div className="space-y-3 mb-4">
                <input
                  type="text"
                  placeholder="Enter blog topic..."
                  value={contentForm.topic}
                  onChange={(e) => setContentForm({...contentForm, topic: e.target.value})}
                  className="w-full input rounded-lg focus-ring"
                />
                <div className="grid grid-cols-2 gap-3">
                  <select
                    value={contentForm.tone}
                    onChange={(e) => setContentForm({...contentForm, tone: e.target.value})}
                    className="input rounded-lg focus-ring"
                  >
                    <option value="professional">Professional</option>
                    <option value="casual">Casual</option>
                    <option value="friendly">Friendly</option>
                    <option value="authoritative">Authoritative</option>
                  </select>
                  <select
                    value={contentForm.length}
                    onChange={(e) => setContentForm({...contentForm, length: e.target.value})}
                    className="input rounded-lg focus-ring"
                  >
                    <option value="short">Short (~500 words)</option>
                    <option value="medium">Medium (~1000 words)</option>
                    <option value="long">Long (~2000 words)</option>
                  </select>
                </div>
              </div>
            )}

            {tool.id === 'image-generator' && (
              <div className="space-y-3 mb-4">
                <textarea
                  placeholder="Describe the image you want to generate..."
                  value={imageForm.prompt}
                  onChange={(e) => setImageForm({...imageForm, prompt: e.target.value})}
                  className="w-full input rounded-lg focus-ring h-20 resize-none"
                />
                <div className="grid grid-cols-2 gap-3">
                  <select
                    value={imageForm.style}
                    onChange={(e) => setImageForm({...imageForm, style: e.target.value})}
                    className="input rounded-lg focus-ring"
                  >
                    <option value="photorealistic">Photorealistic</option>
                    <option value="digital-art">Digital Art</option>
                    <option value="oil-painting">Oil Painting</option>
                    <option value="cartoon">Cartoon</option>
                  </select>
                  <select
                    value={imageForm.count}
                    onChange={(e) => setImageForm({...imageForm, count: parseInt(e.target.value)})}
                    className="input rounded-lg focus-ring"
                  >
                    <option value="1">1 Image</option>
                    <option value="2">2 Images</option>
                    <option value="4">4 Images</option>
                  </select>
                </div>
              </div>
            )}

            <button
              onClick={() => handleToolUse(tool.id, tool.id === 'blog-writer' ? contentForm : imageForm)}
              disabled={processing[tool.id] || (tool.id === 'blog-writer' && !contentForm.topic) || (tool.id === 'image-generator' && !imageForm.prompt)}
              className="w-full btn-primary flex items-center justify-center disabled:opacity-50"
            >
              {processing[tool.id] ? (
                <>
                  <ArrowPathIcon className="h-5 w-5 mr-2 animate-spin" />
                  Generating...
                </>
              ) : (
                <>
                  <PlayIcon className="h-5 w-5 mr-2" />
                  Generate with AI
                </>
              )}
            </button>

            {/* Results Display */}
            {results[tool.id] && (
              <motion.div
                initial={{ opacity: 0, height: 0 }}
                animate={{ opacity: 1, height: 'auto' }}
                className="mt-4 p-4 bg-surface rounded-lg border border-green-200"
              >
                <div className="flex items-center mb-3">
                  <CheckCircleIcon className="h-5 w-5 text-green-500 mr-2" />
                  <span className="text-green-600 font-medium">Generation Complete!</span>
                </div>

                {tool.id === 'blog-writer' && results[tool.id].content && (
                  <div>
                    <div className="grid grid-cols-3 gap-4 mb-3 text-sm">
                      <div>
                        <div className="text-secondary">Words</div>
                        <div className="font-semibold">{results[tool.id].metrics?.word_count}</div>
                      </div>
                      <div>
                        <div className="text-secondary">SEO Score</div>
                        <div className="font-semibold text-green-500">{results[tool.id].metrics?.seo_score}/100</div>
                      </div>
                      <div>
                        <div className="text-secondary">Readability</div>
                        <div className="font-semibold text-blue-500">{results[tool.id].metrics?.readability_score}/100</div>
                      </div>
                    </div>
                    
                    <div className="bg-surface-hover p-3 rounded text-sm text-secondary max-h-32 overflow-y-auto mb-3">
                      {results[tool.id].content.substring(0, 200)}...
                    </div>
                    
                    <div className="flex space-x-2">
                      <button
                        onClick={() => handleCopyToClipboard(results[tool.id].content)}
                        className="btn-secondary flex items-center text-sm"
                      >
                        <ClipboardDocumentIcon className="h-4 w-4 mr-1" />
                        Copy
                      </button>
                      <button
                        onClick={() => handleDownloadContent(results[tool.id].content, `${results[tool.id].title}.md`)}
                        className="btn-secondary flex items-center text-sm"
                      >
                        <DocumentArrowDownIcon className="h-4 w-4 mr-1" />
                        Download
                      </button>
                    </div>
                  </div>
                )}

                {tool.id === 'image-generator' && results[tool.id].images && (
                  <div>
                    <div className="grid grid-cols-2 gap-3 mb-3">
                      {results[tool.id].images.map((image, idx) => (
                        <img key={idx} src={image.url} alt={image.prompt} className="w-full h-32 object-cover rounded-lg" />
                      ))}
                    </div>
                    <div className="text-sm text-secondary mb-3">
                      Generated in {results[tool.id].generation_time} • Style: {results[tool.id].style_applied}
                    </div>
                  </div>
                )}
              </motion.div>
            )}
          </div>
        ))}
      </motion.div>

      {/* Recent Generations */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, delay: 0.4 }}
        className="bg-surface-elevated p-6 rounded-lg shadow-default"
      >
        <h3 className="text-lg font-semibold text-primary mb-4">Recent Generations</h3>
        <div className="space-y-3">
          {aiTools?.recent_generations?.map((generation) => (
            <div key={generation.id} className="flex items-center justify-between p-4 bg-surface rounded-lg">
              <div className="flex items-center space-x-3">
                <div className="w-10 h-10 bg-accent-primary/10 rounded-lg flex items-center justify-center">
                  <SparklesIcon className="h-5 w-5 text-accent-primary" />
                </div>
                <div>
                  <div className="font-medium text-primary">{generation.title}</div>
                  <div className="text-sm text-secondary">{generation.tool} • {generation.created_at}</div>
                </div>
              </div>
              <div className="flex items-center space-x-3">
                <span className="text-sm text-secondary">{generation.credits_used} credits</span>
                <button className="text-blue-600 hover:text-blue-800">
                  <EyeIcon className="h-4 w-4" />
                </button>
              </div>
            </div>
          ))}
        </div>
      </motion.div>
    </div>
  );
};

export default AIFeaturesPageV2;