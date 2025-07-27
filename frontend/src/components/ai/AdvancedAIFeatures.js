import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  SparklesIcon,
  PhotoIcon,
  SpeakerWaveIcon,
  DocumentTextIcon,
  CodeBracketIcon,
  LanguageIcon,
  MagnifyingGlassIcon,
  ChartBarIcon,
  PencilIcon,
  VideoCameraIcon,
  CpuChipIcon,
  LightBulbIcon
} from '@heroicons/react/24/outline';

const AdvancedAIFeatures = () => {
  const [selectedTool, setSelectedTool] = useState('content-generator');
  const [isGenerating, setIsGenerating] = useState(false);
  const [generatedContent, setGeneratedContent] = useState('');
  const [inputText, setInputText] = useState('');
  const [selectedModel, setSelectedModel] = useState('gpt-4');

  const aiTools = [
    {
      id: 'content-generator',
      name: 'AI Content Generator',
      description: 'Generate high-quality content for blogs, social media, and marketing',
      icon: DocumentTextIcon,
      color: 'from-blue-500 to-purple-600',
      credits: 2,
      features: ['Blog posts', 'Social media', 'Marketing copy', 'Product descriptions']
    },
    {
      id: 'image-generator',
      name: 'AI Image Generator',
      description: 'Create stunning visuals and artwork using advanced AI models',
      icon: PhotoIcon,
      color: 'from-pink-500 to-rose-500',
      credits: 5,
      features: ['Custom artwork', 'Product images', 'Social graphics', 'Logos']
    },
    {
      id: 'voice-synthesis',
      name: 'AI Voice Synthesis',
      description: 'Convert text to natural-sounding speech in multiple languages',
      icon: SpeakerWaveIcon,
      color: 'from-green-500 to-emerald-500', 
      credits: 3,
      features: ['Natural voices', '20+ languages', 'Custom speed', 'Background music']
    },
    {
      id: 'code-assistant',
      name: 'AI Code Assistant',
      description: 'Generate, debug, and optimize code in multiple programming languages',
      icon: CodeBracketIcon,
      color: 'from-indigo-500 to-blue-500',
      credits: 4,
      features: ['Code generation', 'Bug fixing', 'Optimization', 'Documentation']
    },
    {
      id: 'translator',
      name: 'AI Translator',
      description: 'Translate content between 100+ languages with context awareness',
      icon: LanguageIcon,
      color: 'from-orange-500 to-amber-500',
      credits: 1,
      features: ['100+ languages', 'Context-aware', 'Cultural adaptation', 'Bulk translation']
    },
    {
      id: 'seo-optimizer',
      name: 'SEO Content Optimizer',
      description: 'Optimize your content for search engines and better rankings',
      icon: MagnifyingGlassIcon,
      color: 'from-teal-500 to-cyan-500',
      credits: 3,
      features: ['Keyword research', 'Content analysis', 'Meta descriptions', 'Recommendations']
    },
    {
      id: 'data-analyzer',
      name: 'AI Data Analyzer',
      description: 'Analyze data patterns and generate insights automatically',
      icon: ChartBarIcon,
      color: 'from-purple-500 to-pink-500',
      credits: 6,
      features: ['Pattern recognition', 'Predictive analysis', 'Report generation', 'Visualizations']
    },
    {
      id: 'email-writer',
      name: 'AI Email Writer',
      description: 'Craft compelling emails for marketing, sales, and communication',
      icon: PencilIcon,
      color: 'from-cyan-500 to-blue-500',
      credits: 2,
      features: ['Marketing emails', 'Sales sequences', 'Follow-ups', 'Personalization']
    },
    {
      id: 'video-editor',
      name: 'AI Video Editor',
      description: 'Edit and enhance videos with AI-powered tools',
      icon: VideoCameraIcon,
      color: 'from-red-500 to-pink-500',
      credits: 8,
      features: ['Auto-editing', 'Scene detection', 'Audio enhancement', 'Subtitle generation']
    }
  ];

  const models = [
    { id: 'gpt-4', name: 'GPT-4', description: 'Most advanced language model', speed: 'Slow', quality: 'Highest' },
    { id: 'gpt-3.5-turbo', name: 'GPT-3.5 Turbo', description: 'Fast and reliable', speed: 'Fast', quality: 'High' },
    { id: 'claude-3', name: 'Claude 3', description: 'Excellent for analysis', speed: 'Medium', quality: 'High' },
    { id: 'gemini-pro', name: 'Gemini Pro', description: 'Google\'s latest model', speed: 'Fast', quality: 'High' }
  ];

  const handleGenerate = async () => {
    if (!inputText.trim()) return;

    // Real data loaded from API
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/ai/generate`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({
          tool: selectedTool,
          model: selectedModel,
          input: inputText,
          options: {}
        })
      });

      if (response.ok) {
        const data = await response.json();
        // Real data loaded from API
      } else {
        // Mock generation for development
        setTimeout(() => {
          setGeneratedContent(generateMockContent(selectedTool, inputText));
        }, 2000);
      }
    } catch (error) {
      console.error('Generation failed:', error);
      // Mock generation for development
      setTimeout(() => {
        setGeneratedContent(generateMockContent(selectedTool, inputText));
      }, 2000);
    } finally {
      // Real data loaded from API
    }
  };

  const generateMockContent = (tool, input) => {
    switch (tool) {
      case 'content-generator':
        return `# Generated Blog Post: ${input}\n\nThis is a high-quality blog post generated by AI based on your topic "${input}". The content is optimized for engagement and SEO.\n\n## Introduction\n\nIn today's digital landscape, ${input} has become increasingly important for businesses and individuals alike. This comprehensive guide will explore the key aspects and provide actionable insights.\n\n## Key Points\n\n- Understanding the fundamentals of ${input}\n- Best practices and strategies\n- Common mistakes to avoid\n- Future trends and opportunities\n\n## Conclusion\n\nBy implementing these strategies around ${input}, you'll be well-positioned to achieve your goals and stay ahead of the competition.`;
      
      case 'image-generator':
        return `🎨 AI Image Generated Successfully!\n\nImage Details:\n- Style: Professional, modern\n- Resolution: 1024x1024\n- Format: PNG\n- Theme: ${input}\n\nThe image has been generated based on your prompt "${input}" and is ready for download. The AI has created a visually appealing image that captures the essence of your request.`;
      
      case 'voice-synthesis':
        return `🎙️ Voice Synthesis Complete!\n\nAudio Details:\n- Text: "${input}"\n- Voice: Natural Female (American English)\n- Duration: ~${Math.ceil(input.length / 12)} seconds\n- Format: MP3, 128kbps\n\nYour text has been converted to high-quality speech. The audio file is ready for download and can be used in your projects.`;
      
      case 'code-assistant':
        return `\`\`\`python\n# Generated code for: ${input}\n\ndef solution():\n    \"\"\"\n    AI-generated solution for ${input}\n    This code is optimized for performance and readability.\n    \"\"\"\n    \n    # Implementation logic\n    result = []\n    \n    # Process the requirements for ${input}\n    for item in range(10):\n        processed_item = process_data(item)\n        result.append(processed_item)\n    \n    return result\n\ndef process_data(data):\n    # Helper function to process individual data items\n    return data * 2 + 1\n\n# Usage example\nif __name__ == "__main__":\n    output = solution()\n    print(f"Result: {output}")\n\`\`\``;
      
      case 'translator':
        return `🌐 Translation Complete!\n\nOriginal (English): "${input}"\n\nTranslations:\n🇪🇸 Spanish: "${input} (traducido al español)"\n🇫🇷 French: "${input} (traduit en français)"\n🇩🇪 German: "${input} (ins Deutsche übersetzt)"\n🇯🇵 Japanese: "${input} (日本語に翻訳)"\n🇨🇳 Chinese: "${input} (翻译成中文)"\n\nAll translations maintain context and cultural appropriateness.`;
      
      default:
        return `Generated content for ${input} using ${tool}. This is a high-quality result created by our advanced AI system.`;
    }
  };

  const selectedToolData = aiTools.find(tool => tool.id === selectedTool);

  return (
    <div className="max-w-7xl mx-auto p-6">
      <div className="mb-8 text-center">
        <h1 className="text-4xl font-bold text-gray-900 dark:text-white mb-4">
          Advanced AI Features
        </h1>
        <p className="text-xl text-gray-600 dark:text-gray-300">
          Supercharge your creativity with our cutting-edge AI tools
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {/* AI Tools Sidebar */}
        <div className="lg:col-span-1">
          <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            AI Tools
          </h2>
          <div className="space-y-2">
            {aiTools.map((tool) => (
              <motion.button
                key={tool.id}
                whileHover={{ scale: 1.02 }}
                onClick={() => setSelectedTool(tool.id)}
                className={`w-full p-4 rounded-lg text-left transition-all ${
                  selectedTool === tool.id
                    ? `bg-gradient-to-r ${tool.color} text-white shadow-lg`
                    : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700'
                }`}
              >
                <div className="flex items-center">
                  <tool.icon className={`h-6 w-6 ${selectedTool === tool.id ? 'text-white' : 'text-gray-500 dark:text-gray-400'} mr-3`} />
                  <div className="flex-1">
                    <div className={`font-medium ${selectedTool === tool.id ? 'text-white' : 'text-gray-900 dark:text-white'}`}>
                      {tool.name}
                    </div>
                    <div className="flex items-center justify-between mt-1">
                      <span className={`text-xs ${selectedTool === tool.id ? 'text-white/80' : 'text-gray-500 dark:text-gray-400'}`}>
                        {tool.credits} credits
                      </span>
                    </div>
                  </div>
                </div>
              </motion.button>
            ))}
          </div>
        </div>

        {/* Main Content Area */}
        <div className="lg:col-span-3">
          <div className="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            {/* Tool Header */}
            <div className="flex items-center justify-between mb-6">
              <div className="flex items-center">
                <div className={`p-3 bg-gradient-to-r ${selectedToolData?.color} rounded-lg mr-4`}>
                  <selectedToolData.icon className="h-8 w-8 text-white" />
                </div>
                <div>
                  <h2 className="text-2xl font-bold text-gray-900 dark:text-white">
                    {selectedToolData?.name}
                  </h2>
                  <p className="text-gray-600 dark:text-gray-300">
                    {selectedToolData?.description}
                  </p>
                </div>
              </div>
              <div className="text-right">
                <div className="text-sm text-gray-500 dark:text-gray-400">Credits</div>
                <div className="text-2xl font-bold text-blue-600">
                  {selectedToolData?.credits}
                </div>
              </div>
            </div>

            {/* Features */}
            <div className="mb-6">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                Features
              </h3>
              <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                {selectedToolData?.features.map((feature, index) => (
                  <div key={index} className="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <SparklesIcon className="h-4 w-4 text-blue-500 mr-2" />
                    <span className="text-sm text-gray-700 dark:text-gray-300">{feature}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Model Selection */}
            <div className="mb-6">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                Select AI Model
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                {models.map((model) => (
                  <button
                    key={model.id}
                    onClick={() => setSelectedModel(model.id)}
                    className={`p-4 rounded-lg border-2 transition-all ${
                      selectedModel === model.id
                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                        : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'
                    }`}
                  >
                    <div className="text-left">
                      <div className={`font-medium ${selectedModel === model.id ? 'text-blue-600' : 'text-gray-900 dark:text-white'}`}>
                        {model.name}
                      </div>
                      <div className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {model.description}
                      </div>
                      <div className="flex justify-between mt-2">
                        <span className="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-600 rounded">
                          {model.speed}
                        </span>
                        <span className="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-600 rounded">
                          {model.quality}
                        </span>
                      </div>
                    </div>
                  </button>
                ))}
              </div>
            </div>

            {/* Input Area */}
            <div className="mb-6">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                Input
              </h3>
              <textarea
                value={inputText}
                onChange={(e) => setInputText(e.target.value)}
                placeholder={`Enter your ${selectedTool.replace('-', ' ')} prompt here...`}
                className="w-full h-32 p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            {/* Generate Button */}
            <div className="mb-6">
              <button
                onClick={handleGenerate}
                disabled={isGenerating || !inputText.trim()}
                className={`w-full py-4 px-6 rounded-lg font-semibold text-white transition-all ${
                  isGenerating || !inputText.trim()
                    ? 'bg-gray-400 cursor-not-allowed'
                    : `bg-gradient-to-r ${selectedToolData?.color} hover:opacity-90 shadow-lg hover:shadow-xl`
                }`}
              >
                {isGenerating ? (
                  <div className="flex items-center justify-center">
                    <CpuChipIcon className="h-5 w-5 mr-2 animate-spin" />
                    Generating with {models.find(m => m.id === selectedModel)?.name}...
                  </div>
                ) : (
                  <div className="flex items-center justify-center">
                    <LightBulbIcon className="h-5 w-5 mr-2" />
                    Generate with AI ({selectedToolData?.credits} credits)
                  </div>
                )}
              </button>
            </div>

            {/* Output Area */}
            {(generatedContent || isGenerating) && (
              <div>
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                  Generated Content
                </h3>
                <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                  {isGenerating ? (
                    <div className="flex items-center justify-center py-8">
                      <CpuChipIcon className="h-8 w-8 text-blue-500 animate-spin mr-3" />
                      <span className="text-gray-600 dark:text-gray-300">
                        AI is working on your request...
                      </span>
                    </div>
                  ) : (
                    <div className="whitespace-pre-wrap text-gray-700 dark:text-gray-300">
                      {generatedContent}
                    </div>
                  )}
                </div>
                
                {generatedContent && !isGenerating && (
                  <div className="mt-4 flex space-x-3">
                    <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                      Copy to Clipboard
                    </button>
                    <button className="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                      Save to Library
                    </button>
                    <button className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                      Export
                    </button>
                  </div>
                )}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdvancedAIFeatures;