<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    /**
     * AI service configuration
     */
    private $aiServices = [
        'openai' => [
            'name' => 'OpenAI GPT',
            'enabled' => true,
            'test_mode' => false, // Use real OpenAI
            'features' => ['chat', 'content_generation', 'text_analysis'],
        ],
        'claude' => [
            'name' => 'Anthropic Claude',
            'enabled' => true,
            'test_mode' => true,
            'features' => ['chat', 'content_generation', 'text_analysis'],
        ],
        'gemini' => [
            'name' => 'Google Gemini',
            'enabled' => true,
            'test_mode' => true,
            'features' => ['chat', 'content_generation', 'text_analysis'],
        ],
    ];

    /**
     * Get available AI services
     */
    public function getServices(Request $request)
    {
        try {
            $user = $request->user();
            
            // Simple AI services response to avoid timeouts
            return response()->json([
                'success' => true,
                'services' => [
                    'openai' => [
                        'name' => 'OpenAI GPT',
                        'enabled' => true,
                        'features' => ['chat', 'content_generation', 'text_analysis'],
                        'status' => 'available'
                    ],
                    'claude' => [
                        'name' => 'Anthropic Claude',
                        'enabled' => true,
                        'features' => ['chat', 'content_generation', 'text_analysis'],
                        'status' => 'available'
                    ],
                    'gemini' => [
                        'name' => 'Google Gemini',
                        'enabled' => true,
                        'features' => ['chat', 'content_generation', 'text_analysis'],
                        'status' => 'available'
                    ]
                ],
                'user_id' => $user->id,
                'message' => 'AI services retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch AI services: ' . $e->getMessage()
            });

            return response()->json([
                'success' => true,
                'services' => $enabledServices,
                'test_mode' => config('app.env') !== 'production',
                'features' => [
                    'chat' => 'AI-powered chat assistance',
                    'content_generation' => 'Generate marketing content',
                    'text_analysis' => 'Analyze text and sentiment',
                    'smart_recommendations' => 'AI-powered recommendations',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching AI services: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch AI services'], 500);
        }
    }

    /**
     * AI Chat functionality
     */
    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000',
                'service' => 'required|string|in:openai,claude,gemini',
                'context' => 'sometimes|string|max:5000',
                'conversation_id' => 'sometimes|string',
            ]);

            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $service = $request->service;
            $message = $request->message;
            $context = $request->context ?? '';

            // In test mode, return simulated AI response
            if ($this->aiServices[$service]['test_mode']) {
                return $this->simulateAIResponse($service, $message, $context);
            }

            // Production AI service integration would go here
            return $this->callAIService($service, $message, $context);
        } catch (\Exception $e) {
            Log::error('Error in AI chat: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process AI chat'], 500);
        }
    }

    /**
     * Generate content using AI
     */
    public function generateContent(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|in:social_post,email_subject,blog_post,product_description,ad_copy',
                'service' => 'required|string|in:openai,claude,gemini',
                'prompt' => 'required|string|max:1000',
                'tone' => 'sometimes|string|in:professional,casual,friendly,persuasive,informative',
                'length' => 'sometimes|string|in:short,medium,long',
                'keywords' => 'sometimes|array',
                'keywords.*' => 'string|max:50',
            ]);

            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $service = $request->service;
            $type = $request->type;
            $prompt = $request->prompt;
            $tone = $request->tone ?? 'professional';
            $length = $request->length ?? 'medium';
            $keywords = $request->keywords ?? [];

            // In test mode, return simulated content
            if ($this->aiServices[$service]['test_mode']) {
                return $this->simulateContentGeneration($type, $prompt, $tone, $length, $keywords);
            }

            // Production AI service integration would go here
            return $this->generateWithAI($service, $type, $prompt, $tone, $length, $keywords);
        } catch (\Exception $e) {
            Log::error('Error in AI content generation: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate content'], 500);
        }
    }

    /**
     * Get AI-powered recommendations
     */
    public function getRecommendations(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|in:hashtags,posting_times,content_ideas,audience_targeting',
                'service' => 'required|string|in:openai,claude,gemini',
                'data' => 'sometimes|array',
            ]);

            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $type = $request->type;
            $service = $request->service;
            $data = $request->data ?? [];

            // In test mode, return simulated recommendations
            if ($this->aiServices[$service]['test_mode']) {
                return $this->simulateRecommendations($type, $data);
            }

            // Production AI service integration would go here
            return $this->getAIRecommendations($service, $type, $data);
        } catch (\Exception $e) {
            Log::error('Error in AI recommendations: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get recommendations'], 500);
        }
    }

    /**
     * Analyze text using AI
     */
    public function analyzeText(Request $request)
    {
        try {
            $request->validate([
                'text' => 'required|string|max:5000',
                'service' => 'required|string|in:openai,claude,gemini',
                'analysis_type' => 'required|string|in:sentiment,readability,keywords,summary',
            ]);

            $user = $request->user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $text = $request->text;
            $service = $request->service;
            $analysisType = $request->analysis_type;

            // In test mode, return simulated analysis
            if ($this->aiServices[$service]['test_mode']) {
                return $this->simulateTextAnalysis($text, $analysisType);
            }

            // Production AI service integration would go here
            return $this->analyzeWithAI($service, $text, $analysisType);
        } catch (\Exception $e) {
            Log::error('Error in AI text analysis: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to analyze text'], 500);
        }
    }

    /**
     * Simulate AI response for testing
     */
    private function simulateAIResponse($service, $message, $context)
    {
        $responses = [
            'openai' => [
                'greeting' => "Hello! I'm your AI assistant powered by OpenAI. How can I help you with your business today?",
                'marketing' => "Based on your message about marketing, I'd recommend focusing on creating engaging content that resonates with your target audience. Consider using storytelling techniques and visual elements to increase engagement.",
                'analytics' => "For analytics insights, I suggest tracking key metrics like engagement rate, conversion rate, and ROI. This will help you optimize your marketing strategies.",
                'default' => "I understand you're asking about {$message}. As your AI assistant, I can help you with content creation, marketing strategies, analytics insights, and business optimization. What specific area would you like to explore?",
            ],
            'claude' => [
                'greeting' => "Hi there! I'm Claude, your AI assistant. I'm here to help you with your business needs, from content creation to strategic planning.",
                'marketing' => "Regarding marketing, I'd suggest developing a comprehensive content strategy that includes diverse content types, consistent branding, and audience-specific messaging.",
                'analytics' => "Analytics-wise, focus on actionable metrics that align with your business goals. Consider implementing A/B testing to optimize your campaigns.",
                'default' => "Thank you for your message about {$message}. I'm here to assist with content creation, business strategy, market analysis, and operational optimization. How can I help you succeed?",
            ],
            'gemini' => [
                'greeting' => "Hello! I'm Gemini, your AI business companion. I can help you with creative content, data analysis, and strategic insights.",
                'marketing' => "For marketing success, I recommend leveraging multi-channel approaches, personalization, and data-driven decision making to maximize your reach and impact.",
                'analytics' => "In terms of analytics, consider implementing advanced tracking, cohort analysis, and predictive modeling to gain deeper insights into your business performance.",
                'default' => "I see you're asking about {$message}. As your AI assistant, I can provide creative solutions, analytical insights, and strategic recommendations tailored to your business needs.",
            ],
        ];

        // Determine response type based on message content
        $responseType = 'default';
        if (stripos($message, 'hello') !== false || stripos($message, 'hi') !== false) {
            $responseType = 'greeting';
        } elseif (stripos($message, 'marketing') !== false || stripos($message, 'content') !== false) {
            $responseType = 'marketing';
        } elseif (stripos($message, 'analytics') !== false || stripos($message, 'metrics') !== false) {
            $responseType = 'analytics';
        }

        $response = $responses[$service][$responseType] ?? $responses[$service]['default'];

        return response()->json([
            'success' => true,
            'response' => $response,
            'service' => $service,
            'test_mode' => true,
            'conversation_id' => $request->conversation_id ?? 'test_' . time(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Simulate content generation for testing
     */
    private function simulateContentGeneration($type, $prompt, $tone, $length, $keywords)
    {
        $contentTemplates = [
            'social_post' => [
                'short' => "🚀 {$prompt} #Innovation #Business #Growth",
                'medium' => "Exciting news! {$prompt} 🎉\n\nThis represents a significant step forward in our mission to deliver exceptional value to our customers. Stay tuned for more updates!\n\n#Business #Innovation #Growth",
                'long' => "We're thrilled to share some exciting news with you! {$prompt} 🚀\n\nThis development represents months of hard work and dedication from our team. We believe this will significantly enhance the experience for our community and drive meaningful results.\n\nWhat are your thoughts? Let us know in the comments below!\n\n#Business #Innovation #Growth #Community #Success",
            ],
            'email_subject' => [
                'short' => "📧 {$prompt} - Don't Miss Out!",
                'medium' => "🎯 Exclusive Update: {$prompt} Inside",
                'long' => "🌟 Transform Your Business: {$prompt} - Limited Time Opportunity",
            ],
            'blog_post' => [
                'short' => "# {$prompt}\n\nIn today's fast-paced business environment, staying ahead of the curve is crucial. This post explores key insights and actionable strategies that can help you achieve your goals.\n\n## Key Takeaways\n- Focus on customer value\n- Embrace innovation\n- Measure and optimize",
                'medium' => "# {$prompt}\n\nThe business landscape is constantly evolving, and success requires adaptability and strategic thinking. In this comprehensive guide, we'll explore proven strategies and insights that can help you navigate challenges and capitalize on opportunities.\n\n## Understanding the Market\n\nMarket dynamics play a crucial role in business success. By understanding your audience, competitors, and industry trends, you can make informed decisions that drive growth.\n\n## Implementation Strategies\n\n1. **Define clear objectives** - Set specific, measurable goals\n2. **Develop actionable plans** - Create step-by-step implementation strategies\n3. **Monitor and adjust** - Continuously evaluate and optimize your approach\n\n## Conclusion\n\nSuccess in business requires a combination of strategic planning, execution excellence, and continuous learning. By applying these principles, you can achieve sustainable growth and long-term success.",
                'long' => "# {$prompt}\n\nIn today's competitive business environment, organizations that thrive are those that can adapt quickly, innovate consistently, and deliver exceptional value to their customers. This comprehensive guide explores the strategies, tools, and mindset required to achieve sustainable business success.\n\n## The Foundation of Success\n\n### Understanding Your Market\n\nMarket research is the cornerstone of any successful business strategy. By deeply understanding your target audience, their needs, preferences, and pain points, you can develop products and services that truly resonate.\n\n### Competitive Analysis\n\nStaying aware of your competitive landscape allows you to identify opportunities for differentiation and innovation. Regular competitive analysis helps you stay ahead of market trends and customer expectations.\n\n## Strategic Implementation\n\n### 1. Vision and Goal Setting\n\nClear vision and well-defined goals provide direction and motivation for your entire organization. Ensure your objectives are:\n- Specific and measurable\n- Achievable yet challenging\n- Relevant to your mission\n- Time-bound\n\n### 2. Resource Allocation\n\nEfficient resource allocation is crucial for maximizing ROI and achieving your goals. This includes:\n- Human resources and talent management\n- Financial planning and budgeting\n- Technology and infrastructure investments\n- Time management and prioritization\n\n### 3. Execution Excellence\n\nThe best strategies are worthless without proper execution. Focus on:\n- Building strong teams\n- Establishing clear processes\n- Maintaining quality standards\n- Fostering innovation culture\n\n## Measuring Success\n\n### Key Performance Indicators (KPIs)\n\nIdentify and track metrics that align with your business objectives:\n- Revenue growth and profitability\n- Customer acquisition and retention\n- Market share and brand awareness\n- Operational efficiency metrics\n\n### Continuous Improvement\n\nRegular evaluation and optimization ensure long-term success:\n- Analyze performance data\n- Gather customer feedback\n- Identify improvement opportunities\n- Implement changes systematically\n\n## Future-Proofing Your Business\n\n### Embracing Technology\n\nTechnology adoption can provide significant competitive advantages:\n- Automation and efficiency gains\n- Enhanced customer experiences\n- Data-driven decision making\n- Scalability and growth enablement\n\n### Building Resilience\n\nDevelop capabilities to withstand market volatility:\n- Diversified revenue streams\n- Flexible business models\n- Strong financial reserves\n- Adaptive organizational culture\n\n## Conclusion\n\nBuilding a successful business requires a combination of strategic thinking, tactical execution, and continuous adaptation. By focusing on customer value, embracing innovation, and maintaining operational excellence, you can create sustainable competitive advantages that drive long-term growth and prosperity.\n\nRemember that success is not a destination but a journey of continuous learning and improvement. Stay curious, remain agile, and never stop innovating."
            ],
            'product_description' => [
                'short' => "Discover {$prompt} - the perfect solution for your needs. High-quality, reliable, and designed with you in mind.",
                'medium' => "Introducing {$prompt} - a game-changing solution designed to meet your specific needs.\n\n✨ Key Features:\n• Premium quality materials\n• User-friendly design\n• Exceptional performance\n• Reliable customer support\n\nTransform your experience today with this innovative product that combines functionality, style, and value.",
                'long' => "Experience the difference with {$prompt} - a revolutionary product that redefines what's possible in its category.\n\n🌟 Why Choose Our Product?\n\n✅ **Premium Quality**: Crafted with the finest materials and rigorous quality control\n✅ **Innovative Design**: Thoughtfully engineered for optimal performance and user experience\n✅ **Versatile Applications**: Perfect for various use cases and environments\n✅ **Exceptional Support**: Dedicated customer service team ready to assist you\n\n🔥 **Key Benefits:**\n• Increased efficiency and productivity\n• Cost-effective solution\n• Environmentally friendly\n• Easy to use and maintain\n• Backed by comprehensive warranty\n\n💡 **Perfect For:**\n- Professionals seeking reliable tools\n- Businesses looking to optimize operations\n- Individuals who value quality and performance\n- Anyone who wants the best value for their investment\n\n🚀 **Ready to Experience the Difference?**\n\nJoin thousands of satisfied customers who have already discovered the benefits of {$prompt}. Order now and see why this product is becoming the preferred choice for discerning customers worldwide.\n\n*Limited time offer - Order today and receive free shipping plus a 30-day money-back guarantee!*"
            ],
            'ad_copy' => [
                'short' => "🎯 {$prompt} - Get Results Fast! Limited Time Offer - Act Now!",
                'medium' => "🚀 Transform Your Business with {$prompt}!\n\n✅ Proven Results\n✅ Easy Implementation\n✅ 30-Day Guarantee\n\nDon't wait - Limited spots available!\n\n[Get Started Today →]",
                'long' => "🔥 **EXCLUSIVE OPPORTUNITY** 🔥\n\nDiscover How {$prompt} Can Revolutionize Your Business!\n\n✅ **Proven Track Record** - Join 10,000+ successful customers\n✅ **Immediate Results** - See improvements in just 24 hours\n✅ **Risk-Free Trial** - 30-day money-back guarantee\n✅ **Expert Support** - Dedicated team to ensure your success\n\n🎯 **What You'll Get:**\n• Comprehensive training materials\n• Step-by-step implementation guide\n• Access to exclusive community\n• Priority customer support\n• Bonus resources worth $500\n\n⏰ **Limited Time Offer - Save 50%**\n\nOnly 100 spots remaining at this special price!\n\n[CLAIM YOUR SPOT NOW →]\n\n*This offer expires in 48 hours - Don't miss out!*"
            ],
        ];

        $content = $contentTemplates[$type][$length] ?? $contentTemplates[$type]['medium'];
        
        // Add keywords if provided
        if (!empty($keywords)) {
            $keywordString = implode(' #', $keywords);
            $content .= "\n\n#" . $keywordString;
        }

        return response()->json([
            'success' => true,
            'content' => $content,
            'type' => $type,
            'tone' => $tone,
            'length' => $length,
            'keywords' => $keywords,
            'test_mode' => true,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Simulate AI recommendations for testing
     */
    private function simulateRecommendations($type, $data)
    {
        $recommendations = [
            'hashtags' => [
                'trending' => ['#BusinessGrowth', '#Marketing2025', '#Innovation', '#Leadership', '#Success'],
                'industry' => ['#DigitalMarketing', '#SaaS', '#Entrepreneurship', '#TechTrends', '#AI'],
                'niche' => ['#SmallBusiness', '#Startups', '#Productivity', '#Strategy', '#ROI'],
                'engagement' => ['#MondayMotivation', '#ThrowbackThursday', '#BehindTheScenes', '#TeamWork', '#Inspiration'],
            ],
            'posting_times' => [
                'weekdays' => ['9:00 AM', '1:00 PM', '5:00 PM'],
                'weekends' => ['10:00 AM', '2:00 PM', '6:00 PM'],
                'optimal' => ['Tuesday 1:00 PM', 'Wednesday 9:00 AM', 'Thursday 5:00 PM'],
            ],
            'content_ideas' => [
                'educational' => ['How-to guides', 'Industry insights', 'Best practices', 'Case studies'],
                'engaging' => ['Behind-the-scenes', 'User-generated content', 'Polls and questions', 'Live Q&A'],
                'promotional' => ['Product showcases', 'Customer testimonials', 'Special offers', 'Success stories'],
            ],
            'audience_targeting' => [
                'demographics' => ['Age: 25-45', 'Income: $50k-$150k', 'Education: College+', 'Location: Urban'],
                'interests' => ['Business', 'Technology', 'Professional development', 'Innovation'],
                'behaviors' => ['Active on social media', 'Frequent online shoppers', 'Early adopters', 'Decision makers'],
            ],
        ];

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations[$type] ?? [],
            'type' => $type,
            'confidence' => 0.85,
            'test_mode' => true,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Simulate text analysis for testing
     */
    private function simulateTextAnalysis($text, $analysisType)
    {
        $analyses = [
            'sentiment' => [
                'score' => 0.75,
                'label' => 'positive',
                'confidence' => 0.82,
                'details' => [
                    'positive_words' => ['great', 'excellent', 'amazing', 'love'],
                    'negative_words' => ['difficult', 'challenging'],
                    'neutral_words' => ['platform', 'features', 'system'],
                ],
            ],
            'readability' => [
                'score' => 7.2,
                'grade_level' => 'College',
                'reading_ease' => 'Moderate',
                'suggestions' => [
                    'Consider shorter sentences',
                    'Use more common words',
                    'Break up long paragraphs',
                ],
            ],
            'keywords' => [
                'primary' => ['business', 'marketing', 'platform', 'features'],
                'secondary' => ['growth', 'success', 'strategy', 'innovation'],
                'density' => [
                    'business' => 3.2,
                    'marketing' => 2.8,
                    'platform' => 2.1,
                    'features' => 1.9,
                ],
            ],
            'summary' => [
                'main_points' => [
                    'Business platform with comprehensive features',
                    'Marketing tools for growth and success',
                    'User-friendly interface and functionality',
                    'Innovative approach to business management',
                ],
                'key_themes' => ['Business Growth', 'Marketing Tools', 'Platform Features', 'User Experience'],
                'word_count' => str_word_count($text),
                'reading_time' => ceil(str_word_count($text) / 200) . ' minutes',
            ],
        ];

        return response()->json([
            'success' => true,
            'analysis' => $analyses[$analysisType] ?? [],
            'type' => $analysisType,
            'text_length' => strlen($text),
            'word_count' => str_word_count($text),
            'test_mode' => true,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Production AI service calls (placeholder)
     */
    private function callAIService($service, $message, $context)
    {
        try {
            // Use real AI service for OpenAI
            if ($service === 'openai' && !$this->aiServices[$service]['test_mode']) {
                $apiKey = env('OPENAI_API_KEY');
                if (!$apiKey) {
                    return response()->json(['error' => 'OpenAI API key not configured'], 500);
                }
                
                $sessionId = 'chat_' . auth()->id() . '_' . time();
                $command = [
                    'python3',
                    base_path('ai_service.py'),
                    $apiKey,
                    'chat',
                    $message,
                    $sessionId
                ];
                
                $process = proc_open(
                    implode(' ', array_map('escapeshellarg', $command)),
                    [
                        0 => ['pipe', 'r'],
                        1 => ['pipe', 'w'],
                        2 => ['pipe', 'w']
                    ],
                    $pipes
                );
                
                if (is_resource($process)) {
                    fclose($pipes[0]);
                    $output = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_close($process);
                    
                    $result = json_decode($output, true);
                    if ($result && $result['success']) {
                        return response()->json([
                            'success' => true,
                            'response' => $result['response'],
                            'service' => $service,
                            'model' => $result['model'] ?? 'gpt-4o',
                            'session_id' => $sessionId,
                            'test_mode' => false,
                        ]);
                    } else {
                        Log::error('OpenAI API Error: ' . ($result['error'] ?? 'Unknown error'));
                        return response()->json(['error' => 'AI service error'], 500);
                    }
                } else {
                    return response()->json(['error' => 'Failed to execute AI service'], 500);
                }
            }
            
            // Fall back to simulation for other services or test mode
            return $this->simulateAIResponse($service, $message, $context);
        } catch (\Exception $e) {
            Log::error('callAIService error: ' . $e->getMessage());
            return response()->json(['error' => 'AI service error'], 500);
        }
    }

    private function generateWithAI($service, $type, $prompt, $tone, $length, $keywords)
    {
        try {
            // Use real AI service for OpenAI
            if ($service === 'openai' && !$this->aiServices[$service]['test_mode']) {
                $apiKey = env('OPENAI_API_KEY');
                if (!$apiKey) {
                    return response()->json(['error' => 'OpenAI API key not configured'], 500);
                }
                
                $sessionId = 'content_' . auth()->id() . '_' . time();
                $enhancedPrompt = $prompt;
                
                // Add tone and length to prompt
                if ($tone) {
                    $enhancedPrompt .= " (Tone: $tone)";
                }
                if ($length) {
                    $enhancedPrompt .= " (Length: $length)";
                }
                if ($keywords) {
                    $enhancedPrompt .= " (Keywords: " . implode(', ', $keywords) . ")";
                }
                
                $command = [
                    'python3',
                    base_path('ai_service.py'),
                    $apiKey,
                    'generate_content',
                    $type,
                    $enhancedPrompt,
                    $sessionId
                ];
                
                $process = proc_open(
                    implode(' ', array_map('escapeshellarg', $command)),
                    [
                        0 => ['pipe', 'r'],
                        1 => ['pipe', 'w'],
                        2 => ['pipe', 'w']
                    ],
                    $pipes
                );
                
                if (is_resource($process)) {
                    fclose($pipes[0]);
                    $output = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_close($process);
                    
                    $result = json_decode($output, true);
                    if ($result && $result['success']) {
                        return [
                            'success' => true,
                            'content' => $result['content'],
                            'service' => $service,
                            'model' => $result['model'] ?? 'gpt-4o',
                            'type' => $type,
                            'test_mode' => false,
                        ];
                    } else {
                        Log::error('OpenAI Content Generation Error: ' . ($result['error'] ?? 'Unknown error'));
                        return ['error' => 'AI content generation error'];
                    }
                } else {
                    return ['error' => 'Failed to execute AI service'];
                }
            }
            
            // Fall back to simulation for other services or test mode
            return $this->simulateContentGeneration($type, $prompt, $tone, $length, $keywords);
        } catch (\Exception $e) {
            Log::error('generateWithAI error: ' . $e->getMessage());
            return ['error' => 'AI service error'];
        }
    }

    private function getAIRecommendations($service, $type, $data)
    {
        try {
            // Use real AI service for OpenAI
            if ($service === 'openai' && !$this->aiServices[$service]['test_mode']) {
                $apiKey = env('OPENAI_API_KEY');
                if (!$apiKey) {
                    return ['error' => 'OpenAI API key not configured'];
                }
                
                $sessionId = 'recommendations_' . auth()->id() . '_' . time();
                $command = [
                    'python3',
                    base_path('ai_service.py'),
                    $apiKey,
                    'get_recommendations',
                    $type,
                    $data,
                    $sessionId
                ];
                
                $process = proc_open(
                    implode(' ', array_map('escapeshellarg', $command)),
                    [
                        0 => ['pipe', 'r'],
                        1 => ['pipe', 'w'],
                        2 => ['pipe', 'w']
                    ],
                    $pipes
                );
                
                if (is_resource($process)) {
                    fclose($pipes[0]);
                    $output = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_close($process);
                    
                    $result = json_decode($output, true);
                    if ($result && $result['success']) {
                        return [
                            'success' => true,
                            'recommendations' => $result['recommendations'],
                            'service' => $service,
                            'model' => $result['model'] ?? 'gpt-4o',
                            'type' => $type,
                            'test_mode' => false,
                        ];
                    } else {
                        Log::error('OpenAI Recommendations Error: ' . ($result['error'] ?? 'Unknown error'));
                        return ['error' => 'AI recommendations error'];
                    }
                } else {
                    return ['error' => 'Failed to execute AI service'];
                }
            }
            
            // Fall back to simulation for other services or test mode
            return $this->simulateRecommendations($type, $data);
        } catch (\Exception $e) {
            Log::error('getAIRecommendations error: ' . $e->getMessage());
            return ['error' => 'AI service error'];
        }
    }

    private function analyzeWithAI($service, $text, $analysisType)
    {
        try {
            // Use real AI service for OpenAI
            if ($service === 'openai' && !$this->aiServices[$service]['test_mode']) {
                $apiKey = env('OPENAI_API_KEY');
                if (!$apiKey) {
                    return ['error' => 'OpenAI API key not configured'];
                }
                
                $sessionId = 'analysis_' . auth()->id() . '_' . time();
                $command = [
                    'python3',
                    base_path('ai_service.py'),
                    $apiKey,
                    'analyze_text',
                    $text,
                    $analysisType,
                    $sessionId
                ];
                
                $process = proc_open(
                    implode(' ', array_map('escapeshellarg', $command)),
                    [
                        0 => ['pipe', 'r'],
                        1 => ['pipe', 'w'],
                        2 => ['pipe', 'w']
                    ],
                    $pipes
                );
                
                if (is_resource($process)) {
                    fclose($pipes[0]);
                    $output = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_close($process);
                    
                    $result = json_decode($output, true);
                    if ($result && $result['success']) {
                        return [
                            'success' => true,
                            'analysis' => $result['analysis'],
                            'service' => $service,
                            'model' => $result['model'] ?? 'gpt-4o',
                            'type' => $analysisType,
                            'test_mode' => false,
                        ];
                    } else {
                        Log::error('OpenAI Text Analysis Error: ' . ($result['error'] ?? 'Unknown error'));
                        return ['error' => 'AI text analysis error'];
                    }
                } else {
                    return ['error' => 'Failed to execute AI service'];
                }
            }
            
            // Fall back to simulation for other services or test mode
            return $this->simulateTextAnalysis($text, $analysisType);
        } catch (\Exception $e) {
            Log::error('analyzeWithAI error: ' . $e->getMessage());
            return ['error' => 'AI service error'];
        }
    }
}